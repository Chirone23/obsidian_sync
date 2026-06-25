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

# INC-006 — distinzione toponimo pubblico vs indirizzo (PII).
# spaCy `sm` etichetta sia "Roma"/"Foro" sia "Via Garibaldi 12" come LOC.
# Una città/foro è dato pubblico (non PII): va lasciata leggere a Claude,
# altrimenti la prosa dice "foro non leggibile" mentre la citazione mostra
# "Roma" (report contraddittorio). Un indirizzo di residenza è invece PII
# (T15) e va censurato. Distinzione context-aware, stesso pattern di CF/PIVA.
#
# Un'entità LOC/GPE è indirizzo se:
#  (a) il testo dell'entità contiene un tipo-strada (via, piazza, corso…), oppure
#  (b) è immediatamente preceduta da una keyword di residenza/domicilio/sede.
# Altrimenti è un toponimo nudo (città, foro) → passa.
_STREET_RE = re.compile(
    r'(?i)\b(?:via|viale|v\.?le|piazza|p\.?zza|corso|c\.?so|strada|largo|'
    r'vicolo|borgo|lungomare|contrada|località|fraz\.?|frazione)\b'
)
_RESIDENCE_RE = re.compile(
    r'(?i)(?:residen|domicili|abita|dimora|con\s+sede|sede\s+(?:legale\s+)?in)\s*$'
)


def _is_address(text: str, start: int, ent_text: str) -> bool:
    """True se l'entità LOC/GPE è un indirizzo (PII), non un toponimo nudo."""
    if _STREET_RE.search(ent_text):
        return True
    # Finestra di ~30 char prima dell'entità per la keyword di residenza.
    prefix = text[max(0, start - 30):start]
    return _RESIDENCE_RE.search(prefix) is not None


def redact(text: str) -> tuple[str, dict[str, str]]:
    mapping: dict[str, str] = {}
    counters: dict[str, int] = {}

    def _make_placeholder(label: str, val: str) -> str:
        counters[label] = counters.get(label, 0) + 1
        placeholder = f"[{label}_{counters[label]}]"
        mapping[placeholder] = val
        return placeholder

    # Passo 1 — spaCy NER sul testo ORIGINALE.
    #
    # Deve precedere le regex. Se le regex girassero prima, i placeholder
    # inseriti (es. [CF_1]) modificherebbero gli offset e i byte count
    # del testo: un'entità spaCy che inizia a offset 5 nel testo originale
    # potrebbe finire a cavallo di un placeholder nel testo modificato,
    # producendo placeholder annidati tipo [PER[CF_1]_2].
    #
    # Con spaCy prima: offset sempre validi, nessun placeholder nel testo
    # al momento della sostituzione → corruzione impossibile per costruzione.
    if _SPACY_AVAILABLE:
        doc = _nlp(text)
        entities = [
            (ent.start_char, ent.end_char, ent.label_, ent.text)
            for ent in doc.ents
            if ent.label_ in ("PER", "PERSON", "ORG")
            or (
                ent.label_ in ("LOC", "GPE")
                and _is_address(text, ent.start_char, ent.text)
            )
        ]
        # Reverse per preservare gli offset delle sostituzioni successive
        for start, end, label, val in sorted(entities, key=lambda e: e[0], reverse=True):
            if not val.strip():
                continue
            text = text[:start] + _make_placeholder(label, val) + text[end:]

    # Passo 2 — Regex deterministiche sul testo post-NER.
    #
    # I pattern non matchano dentro placeholder [LABEL_N]: cercano strutture
    # specifiche (16-char CF alfanumerico, 11 cifre Luhn, IT+27-char, @, +39)
    # che non compaiono nelle stringhe "[LABEL_N]".
    def _replace(match: re.Match, label: str) -> str:
        return _make_placeholder(label, match.group(0))

    def _replace_cf(match: re.Match) -> str:
        prefix_len = match.start(1) - match.start(0)
        return match.group(0)[:prefix_len] + _make_placeholder("CF", match.group(1))

    def _replace_piva(match: re.Match) -> str:
        val = match.group(0)
        return _make_placeholder("PIVA", val) if validate_piva_luhn(val) else val

    def _replace_iban(match: re.Match) -> str:
        val = match.group(0)
        return _make_placeholder("IBAN", val) if validate_iban_it(val) else val

    text = _CF_RE.sub(_replace_cf, text)
    text = _PIVA_RE.sub(_replace_piva, text)
    text = _IBAN_RE.sub(_replace_iban, text)
    text = _EMAIL_RE.sub(lambda m: _replace(m, "EMAIL"), text)
    text = _PHONE_RE.sub(lambda m: _replace(m, "TEL"), text)

    return text, mapping


def restore(text: str, mapping: dict[str, str]) -> str:
    for placeholder, value in mapping.items():
        text = text.replace(placeholder, value)
    return text
