"""Estrae testo dai PDF preventivi noleggio e salva un dump JSON+TXT."""
import json
from pathlib import Path
import pdfplumber

SRC = Path(r"C:/Users/Chirone/Documents/Secondo_Cervello/ITS/Lorenzo - Imprenditoriale/noleggio vs acquisto")
OUT_DIR = Path(r"C:/Users/Chirone/Documents/Secondo_Cervello/.tmp/noleggio_extract")
OUT_DIR.mkdir(parents=True, exist_ok=True)

result = {}
for pdf_path in sorted(SRC.glob("*.pdf")):
    pages_text = []
    with pdfplumber.open(pdf_path) as pdf:
        for page in pdf.pages:
            pages_text.append(page.extract_text() or "")
    full = "\n\n--- PAGE ---\n\n".join(pages_text)
    result[pdf_path.name] = full
    (OUT_DIR / (pdf_path.stem + ".txt")).write_text(full, encoding="utf-8")

(OUT_DIR / "all.json").write_text(json.dumps(result, ensure_ascii=False, indent=2), encoding="utf-8")
print(f"OK - {len(result)} pdf estratti in {OUT_DIR}")
for name, txt in result.items():
    print(f"  {name}: {len(txt)} chars")
