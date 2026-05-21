import logging
import re

try:
    import spacy
    _nlp = spacy.load("it_core_news_sm")
    _SPACY_AVAILABLE = True
except (OSError, ImportError):
    logging.warning(
        "spaCy o il modello 'it_core_news_sm' non disponibili — "
        "NER disabilitato, solo redazione regex attiva."
    )
    _nlp = None
    _SPACY_AVAILABLE = False


def validate_piva_luhn(piva: str) -> bool:
    """Algoritmo Luhn IT per Partita IVA (11 cifre)."""
    if not re.fullmatch(r'\d{11}', piva):
        return False
    odd_sum = sum(int(piva[i]) for i in range(0, 10, 2))
    even_sum = 0
    for i in range(1, 10, 2):
        doubled = int(piva[i]) * 2
        even_sum += doubled if doubled <= 9 else doubled - 9
    check = (10 - (odd_sum + even_sum) % 10) % 10
    return check == int(piva[10])


def validate_iban_it(iban: str) -> bool:
    """Checksum mod-97 per IBAN italiano (27 caratteri, inizia con IT)."""
    iban = iban.upper().replace(" ", "")
    if not re.fullmatch(r'IT\d{2}[A-Z0-9]{23}', iban):
        return False
    rearranged = iban[4:] + iban[:4]
    numeric = "".join(str(ord(c) - 55) if c.isalpha() else c for c in rearranged)
    return int(numeric) % 97 == 1


# Regex deterministici per PII strutturate italiane.
# CF: context-aware — matcha solo se preceduto da keyword identificativa.
# Il CF stesso è case-sensitive uppercase (nessun IGNORECASE globale).
_CF_RE = re.compile(
    r'(?i:codice\s+fiscale|C\.?F\.?)\s*[:/-]?\s*([A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z])\b'
)
_PIVA_RE = re.compile(r'\b\d{11}\b')
_IBAN_RE = re.compile(r'\bIT\d{2}[A-Z0-9]{23}\b', re.IGNORECASE)
_EMAIL_RE = re.compile(r'\b[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}\b')
# Prefisso +39/0039 obbligatorio: evita falsi positivi su numeri civici e protocolli.
_PHONE_RE = re.compile(r'(?:\+39|0039)[\s\-]?(?:0\d{1,4}[\s\-]?\d{4,8}|3\d{2}[\s\-]?\d{6,7})\b')


def redact(text: str) -> tuple[str, dict[str, str]]:
    mapping: dict[str, str] = {}
    counters: dict[str, int] = {}

    def replace(match: re.Match, label: str) -> str:
        val = match.group(0)
        counters[label] = counters.get(label, 0) + 1
        placeholder = f"[{label}_{counters[label]}]"
        mapping[placeholder] = val
        return placeholder

    def replace_cf(match: re.Match) -> str:
        cf_val = match.group(1)
        counters['CF'] = counters.get('CF', 0) + 1
        placeholder = f"[CF_{counters['CF']}]"
        mapping[placeholder] = cf_val
        prefix_len = match.start(1) - match.start(0)
        return match.group(0)[:prefix_len] + placeholder

    def replace_if_valid_piva(match: re.Match) -> str:
        val = match.group(0)
        if not validate_piva_luhn(val):
            return val
        return replace(match, "PIVA")

    def replace_if_valid_iban(match: re.Match) -> str:
        val = match.group(0)
        if not validate_iban_it(val):
            return val
        return replace(match, "IBAN")

    text = _CF_RE.sub(replace_cf, text)
    text = _PIVA_RE.sub(replace_if_valid_piva, text)
    text = _IBAN_RE.sub(replace_if_valid_iban, text)
    text = _EMAIL_RE.sub(lambda m: replace(m, "EMAIL"), text)
    text = _PHONE_RE.sub(lambda m: replace(m, "TEL"), text)

    if _SPACY_AVAILABLE:
        doc = _nlp(text)
        # Processa entità in ordine inverso per non spostare gli offset
        _placeholder_re = re.compile(r'^\[?[A-Z]+_\d+\]?$')
        entities = [(ent.start_char, ent.end_char, ent.label_) for ent in doc.ents
                    if ent.label_ in ("PER", "PERSON", "LOC", "GPE", "ORG")]
        for start, end, label in sorted(entities, reverse=True):
            val = text[start:end]
            # Salta se è già un placeholder o parte di uno
            if not val.strip() or _placeholder_re.match(val.strip()):
                continue
            # Salta se cade dentro una sequenza [...] già inserita
            before = text[max(0, start-1):start]
            if before == "[":
                continue
            counters[label] = counters.get(label, 0) + 1
            placeholder = f"[{label}_{counters[label]}]"
            mapping[placeholder] = val
            text = text[:start] + placeholder + text[end:]

    return text, mapping


def restore(text: str, mapping: dict[str, str]) -> str:
    for placeholder, value in mapping.items():
        text = text.replace(placeholder, value)
    return text
