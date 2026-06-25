import fitz


MAX_CHARS = 40_000
MIN_READABLE_CHARS = 100

# Soglia detector testo corrotto. I caratteri Latin Extended-A/B (U+0100–U+024F)
# sono di fatto assenti nell'italiano: una loro densità alta segnala una tabella
# ToUnicode del font rotta (es. font subset Calibri-Identity-H), che produce
# mojibake tipo "Đoŵplessivo" = "complessivo". Calibrato su PDF reali: contratti
# puliti = 0,00%, Consip corrotto = 4,42%. Soglia all'1% nel mezzo.
GARBLED_RATIO_THRESHOLD = 0.01


def _garbled_ratio(text: str) -> float:
    """Frazione di lettere nei range Latin Extended-A/B sul totale delle lettere."""
    letters = [c for c in text if c.isalpha()]
    if not letters:
        return 0.0
    suspicious = sum(1 for c in letters if 0x0100 <= ord(c) <= 0x024F)
    return suspicious / len(letters)


def extract_text(pdf_bytes: bytes) -> str:
    try:
        doc = fitz.open(stream=pdf_bytes, filetype="pdf")
    except Exception:
        raise ValueError(
            "PDF non leggibile: file corrotto, protetto da password, o formato non supportato."
        )
    pages = [page.get_text() for page in doc]
    doc.close()

    text = "\n".join(pages)

    if len(text.strip()) < MIN_READABLE_CHARS:
        raise ValueError(
            "PDF non leggibile: potrebbe essere scansionato o protetto da password. "
            "SpecterAI richiede un PDF con testo selezionabile."
        )

    # Detector testo corrotto: meglio rifiutare onestamente che citare spazzatura.
    # Qui in futuro si innesta il fallback OCR (vedi docs/OCR_fallback_decisione.md).
    if _garbled_ratio(text) > GARBLED_RATIO_THRESHOLD:
        raise ValueError(
            "PDF non estraibile in modo affidabile: il documento usa font con codifica "
            "corrotta (ToUnicode), quindi il testo estratto sarebbe illeggibile. "
            "SpecterAI non analizza testo non verificabile. Riprova con un altro PDF "
            "(il supporto OCR per questi casi è in roadmap)."
        )

    truncated = len(text) > MAX_CHARS
    return text[:MAX_CHARS], truncated
