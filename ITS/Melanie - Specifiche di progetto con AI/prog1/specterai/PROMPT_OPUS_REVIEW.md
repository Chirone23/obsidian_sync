# Prompt — Code Review SpecterAI per Claude Opus

> Copia tutto il blocco sotto e incollalo in una chat Claude Opus nuova (o `claude -p --model claude-opus-4-7`).

---

## [C — CONTESTO]

Sei un senior software architect con specializzazione in sistemi legal-AI e Python production-grade. Ti viene sottoposto SpecterAI, un MVP costruito da uno studente ITS per un corso di AI Projects Development. Il sistema analizza contratti PDF in italiano e segnala clausole rischiose in 7 categorie, restituendo un report HTML con semaforo di rischio.

**Stack:** Python 3.12, FastAPI, PyMuPDF 1.27, spaCy it_core_news_sm, Pydantic v2, Jinja2, Claude Code CLI via subprocess (stdin).

**Risultati test E2E al momento della review:**
- 7/8 PDF contrattuali superano il test (1 scansionato rifiutato by design, comportamento corretto)
- 5/5 contratti di noleggio auto analizzati correttamente (tipologia aggiuntiva non prevista nella spec)
- Latenza: 163–268s per contratti da 40k caratteri
- Bug risolto in produzione: WinError 206 su Windows (messaggio argv troppo lungo → migrato a stdin)

**Codice sorgente completo (6 moduli):**

---

### `schemas.py`
```python
from typing import Annotated, Literal
from pydantic import BaseModel, BeforeValidator, model_validator, field_validator


def _normalize_excerpt(v: object) -> list[str]:
    if isinstance(v, str):
        return [v] if v else []
    return v  # type: ignore[return-value]


RawExcerpt = Annotated[list[str], BeforeValidator(_normalize_excerpt)]


class CategoryResult(BaseModel):
    present: bool
    raw_excerpt: RawExcerpt
    plain_language: str
    risk_level: Literal["low", "medium", "high"]
    question_to_ask: str

    @model_validator(mode="after")
    def validate_excerpts(self) -> "CategoryResult":
        if not self.present:
            return self
        if not self.raw_excerpt:
            raise ValueError("raw_excerpt non può essere vuoto quando present=true")
        for span in self.raw_excerpt:
            if len(span) < 20:
                raise ValueError(f"Ogni span di raw_excerpt deve essere ≥20 caratteri: '{span}'")
            if "[...]" in span or "..." in span:
                raise ValueError(f"raw_excerpt non può contenere ellissi: '{span}'")
        return self


class ContractAnalysis(BaseModel):
    language_detected: Literal["italian", "english"]
    categories: dict[str, CategoryResult]
    top_3_risks: list[str]
    disclaimer: str = "Questo report non costituisce consulenza legale. Prima di firmare, consulta un professionista qualificato."

    @field_validator("top_3_risks")
    @classmethod
    def validate_top3(cls, v: list[str]) -> list[str]:
        if len(v) != 3:
            raise ValueError(f"top_3_risks deve contenere esattamente 3 elementi, trovati {len(v)}")
        return v

    @model_validator(mode="after")
    def validate_categories(self) -> "ContractAnalysis":
        expected = {
            "payment_terms", "auto_renewal", "penalties",
            "liability_limitation", "termination", "governing_law", "intellectual_property"
        }
        missing = expected - set(self.categories.keys())
        if missing:
            raise ValueError(f"Categorie mancanti nel JSON: {missing}")
        return self
```

---

### `pdf_processor.py`
```python
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
    return text[:MAX_CHARS]
```

---

### `regex_layer.py`
```python
import re

_DATE_PATTERNS = [
    r'\b\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{4}\b',
    r'\b\d{1,2}\s+(?:gennaio|febbraio|marzo|aprile|maggio|giugno|luglio|agosto|settembre|ottobre|novembre|dicembre)\s+\d{4}\b',
    r'\bentro\s+il\s+\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{4}\b',
]

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
    return {"dates": list(dates), "amounts": list(amounts), "deadlines": list(deadlines)}
```

---

### `privacy_filter.py`
```python
import re

try:
    import spacy
    _nlp = spacy.load("it_core_news_sm")
except OSError:
    raise RuntimeError("Modello spaCy 'it_core_news_sm' non trovato.")

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
    _placeholder_re = re.compile(r'^\[?[A-Z]+_\d+\]?$')
    entities = [(ent.start_char, ent.end_char, ent.label_) for ent in doc.ents
                if ent.label_ in ("PER", "PERSON", "LOC", "GPE", "ORG")]
    for start, end, label in sorted(entities, reverse=True):
        val = text[start:end]
        if not val.strip() or _placeholder_re.match(val.strip()):
            continue
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
```

---

### `llm_client.py`
```python
import json
import re
import subprocess
from pathlib import Path

from privacy_filter import redact, restore
from schemas import ContractAnalysis

_SYSTEM_PROMPT_PATH = Path(__file__).parent / "prompts" / "system_prompt.md"
_MODEL = "claude-sonnet-4-6"

def _load_system_prompt() -> str:
    raw = _SYSTEM_PROMPT_PATH.read_text(encoding="utf-8")
    match = re.search(r"## SYSTEM PROMPT\s*```(.*?)```", raw, re.DOTALL)
    if not match:
        raise ValueError("System prompt non trovato in prompts/system_prompt.md")
    return match.group(1).strip()

def _call_claude(system_prompt: str, user_message: str) -> str:
    result = subprocess.run(
        ["claude", "-p",
         "--system-prompt", system_prompt,
         "--model", _MODEL,
         "--output-format", "json"],
        input=user_message,
        capture_output=True,
        text=True,
        encoding="utf-8",
    )
    if result.returncode != 0:
        raise RuntimeError(f"Claude CLI error: {result.stderr[:300]}")
    data = json.loads(result.stdout)
    if data.get("is_error"):
        raise RuntimeError(f"Claude API error: {data}")
    return data["result"]

def _restore_excerpts(analysis: ContractAnalysis, mapping: dict) -> ContractAnalysis:
    for cat in analysis.categories.values():
        cat.raw_excerpt = [restore(span, mapping) for span in cat.raw_excerpt]
    return analysis

def analyze(contract_text: str, metadata: dict) -> ContractAnalysis:
    text_redacted, mapping = redact(contract_text)
    system_prompt = _load_system_prompt()
    user_message = (
        f"Analizza il seguente testo contrattuale e restituisci SOLO il JSON richiesto.\n\n"
        f"METADATI STRUTTURATI (date/importi estratti pre-analisi):\n{json.dumps(metadata, ensure_ascii=False)}\n\n"
        f"TESTO CONTRATTO:\n{text_redacted}"
    )
    raw_response = _call_claude(system_prompt, user_message)
    json_match = re.search(r'\{.*\}', raw_response, re.DOTALL)
    if not json_match:
        raise ValueError("Nessun JSON trovato nella risposta")
    json_str = json_match.group(0)
    try:
        analysis = ContractAnalysis.model_validate_json(json_str)
    except Exception as e:
        retry_message = user_message + "\n\nRisposta precedente non valida. Restituisci ESCLUSIVAMENTE il JSON, nessun testo aggiuntivo."
        raw_response = _call_claude(system_prompt, retry_message)
        json_match = re.search(r'\{.*\}', raw_response, re.DOTALL)
        if not json_match:
            raise ValueError("Analisi non riuscita dopo retry") from e
        analysis = ContractAnalysis.model_validate_json(json_match.group(0))
    if analysis.language_detected not in ("italian", "english"):
        raise ValueError(f"language_detected non valido: {analysis.language_detected}")
    return _restore_excerpts(analysis, mapping)
```

---

### `main.py`
```python
from pathlib import Path
from fastapi import FastAPI, File, Request, UploadFile
from fastapi.responses import HTMLResponse
from fastapi.templating import Jinja2Templates
from llm_client import analyze
from pdf_processor import extract_text
from regex_layer import extract_metadata

app = FastAPI()
templates = Jinja2Templates(directory=str(Path(__file__).parent / "templates"))
MAX_SIZE = 10 * 1024 * 1024  # 10MB

@app.get("/", response_class=HTMLResponse)
async def index(request: Request):
    return templates.TemplateResponse("index.html", {"request": request})

@app.post("/analyze", response_class=HTMLResponse)
async def analyze_contract(request: Request, file: UploadFile = File(...)):
    if file.content_type != "application/pdf":
        return HTMLResponse("<h2>Errore: carica un file PDF valido.</h2><a href='/'>Torna indietro</a>", status_code=422)
    pdf_bytes = await file.read()
    if len(pdf_bytes) > MAX_SIZE:
        return HTMLResponse("<h2>Errore: il file supera i 10MB.</h2><a href='/'>Torna indietro</a>", status_code=422)
    try:
        contract_text = extract_text(pdf_bytes)
    except ValueError as e:
        return HTMLResponse(f"<h2>PDF non leggibile</h2><p>{e}</p><a href='/'>Torna indietro</a>", status_code=422)
    metadata = extract_metadata(contract_text)
    try:
        result = analyze(contract_text, metadata)
    except RuntimeError:
        return HTMLResponse("<h2>Servizio temporaneamente non disponibile.</h2><p>Riprova tra qualche minuto.</p><a href='/'>Torna indietro</a>", status_code=503, headers={"Retry-After": "30"})
    except ValueError:
        return HTMLResponse("<h2>Analisi non riuscita.</h2><p>Il documento potrebbe essere in un formato non supportato.</p><a href='/'>Torna indietro</a>", status_code=500)
    return templates.TemplateResponse("report.html", {"request": request, "analysis": result, "filename": file.filename})
```

---

## [I — INTENTO]

Voglio una **code review critica e onesta** di questo MVP. Non mi interessa una valutazione scolastica — voglio sapere cosa è fatto bene, cosa è fragile, cosa fallirebbe in produzione reale, e cosa migliorerei subito se dovessi consegnare questo sistema a un cliente pagante.

---

## [A — AUDIENCE + OUTPUT]

**Chi legge:** uno studente ITS con 6 mesi di Python, buona comprensione dell'architettura, che vuole capire dove il suo codice è professionale e dove no.

**Formato output richiesto:**

1. **Giudizio sintetico** (3-5 righe): il sistema è production-ready? Qual è il livello complessivo?
2. **Pregi** (max 5): cosa è fatto genuinamente bene, con riferimento a file e riga specifica
3. **Problemi critici** (priorità ALTA): cosa fallirebbe in produzione, con file + riga + spiegazione del perché
4. **Problemi minori** (priorità MEDIA/BASSA): code smell, edge case non gestiti, debito tecnico
5. **Top 3 miglioramenti** che otterrebbero il maggior impatto con il minor sforzo
6. **Auto-valutazione confidence** 1-5 sulla tua review (quanto sei certo delle osservazioni?)

---

## [R — REGOLE]

- Usa **Chain-of-Thought**: ragiona esplicitamente su ogni modulo prima di formulare il giudizio finale
- Analizza da **3 prospettive**: (a) software engineer, (b) esperto di sicurezza/GDPR, (c) utente finale non tecnico
- Cita sempre **file + numero di riga** per ogni osservazione
- **Non essere generoso**: se c'è un bug reale o un rischio, dillo chiaramente
- **Non inventare problemi**: se qualcosa è corretto, dì che è corretto
- Considera il contesto: è un MVP da corso ITS, non un sistema enterprise — valuta proporzionalmente

---

## [E — ESEMPI]

Esempio del formato atteso per un problema critico:
> **`privacy_filter.py:15` — False positive PIVA** — il pattern `\b\d{11}\b` cattura qualsiasi sequenza di 11 cifre, inclusi numeri di telefono o importi. In un contratto con "11000000000 euro" verrebbe redatto come PIVA. Rischio: perdita di dati legittimi nel testo inviato al LLM.

Esempio di pregio:
> **`schemas.py:21-32` — Validazione ellissi nel validator Pydantic** — scelta corretta mettere la validazione dello schema nel layer dati invece che nel client LLM. Fallisce velocemente e in modo esplicito.

---

Pensa passo dopo passo. Inizia dal modulo più critico per la correttezza del sistema, poi procedi verso l'esterno.
