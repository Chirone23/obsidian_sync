import fitz


MAX_CHARS = 40_000
MIN_READABLE_CHARS = 100


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

    truncated = len(text) > MAX_CHARS
    return text[:MAX_CHARS], truncated
