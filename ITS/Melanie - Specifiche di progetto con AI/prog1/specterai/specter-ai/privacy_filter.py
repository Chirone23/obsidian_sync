import re

try:
    import spacy
    _nlp = spacy.load("it_core_news_sm")
except OSError:
    raise RuntimeError(
        "Modello spaCy 'it_core_news_sm' non trovato. "
        "Esegui: python -m spacy download it_core_news_sm"
    )


# Regex deterministici per PII strutturate italiane
_CF_RE = re.compile(r'\b[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]\b', re.IGNORECASE)
_PIVA_RE = re.compile(r'\b\d{11}\b')
_IBAN_RE = re.compile(r'\bIT\d{2}[A-Z0-9]{23}\b', re.IGNORECASE)
_EMAIL_RE = re.compile(r'\b[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}\b')
_PHONE_RE = re.compile(r'(?:\+39|39)?[\s\-]?(?:0\d{1,4}[\s\-]?\d{4,8}|3\d{2}[\s\-]?\d{6,7})\b')


def redact(text: str) -> tuple[str, dict[str, str]]:
    mapping: dict[str, str] = {}
    counters: dict[str, int] = {}

    def replace(match: re.Match, label: str) -> str:
        val = match.group(0)
        counters[label] = counters.get(label, 0) + 1
        placeholder = f"[{label}_{counters[label]}]"
        mapping[placeholder] = val
        return placeholder

    text = _CF_RE.sub(lambda m: replace(m, "CF"), text)
    text = _PIVA_RE.sub(lambda m: replace(m, "PIVA"), text)
    text = _IBAN_RE.sub(lambda m: replace(m, "IBAN"), text)
    text = _EMAIL_RE.sub(lambda m: replace(m, "EMAIL"), text)
    text = _PHONE_RE.sub(lambda m: replace(m, "TEL"), text)

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
