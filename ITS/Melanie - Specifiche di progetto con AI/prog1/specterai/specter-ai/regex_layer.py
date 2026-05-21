import re


_DATE_PATTERNS = [
    r'\b\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{4}\b',
    r'\b\d{1,2}\s+(?:gennaio|febbraio|marzo|aprile|maggio|giugno|luglio|agosto|settembre|ottobre|novembre|dicembre)\s+\d{4}\b',
    r'\bentro\s+il\s+\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{4}\b',
]

# loose validation, downstream-safe: questi pattern alimentano solo i metadati
# passati all'LLM come contesto, non vengono usati per redazione PII.
_AMOUNT_PATTERNS = [
    r'€\s*\d[\d\.,]+',
    r'\d[\d\.,]+\s*€',
    r'\bEUR\s+\d[\d\.,]+',
]

_DEADLINE_PATTERNS = [
    r'\bentro\s+\d+\s+giorni\b',
    r'\bentro\s+\d+\s+mesi\b',
    r'\bpreavviso\s+di\s+\d+\s+giorni\b',
    r'\bpreavviso\s+di\s+\d+\s+mesi\b',
    r'\bentro\s+\d+\s+\(?\w+\)?\s+giorni\b',
]


def extract_metadata(text: str) -> dict:
    dates = set()
    for pattern in _DATE_PATTERNS:
        dates.update(re.findall(pattern, text, re.IGNORECASE))

    amounts = set()
    for pattern in _AMOUNT_PATTERNS:
        amounts.update(re.findall(pattern, text))

    deadlines = set()
    for pattern in _DEADLINE_PATTERNS:
        deadlines.update(re.findall(pattern, text, re.IGNORECASE))

    return {
        "dates": list(dates),
        "amounts": list(amounts),
        "deadlines": list(deadlines),
    }
