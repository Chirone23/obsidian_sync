# PROGETTO CORSO AI Projects Development, ITS ICT Academy Roma

**Developer:** Christian Giordano  
**Started:** 2026-05-20  
**Project:** SpecterAI — AI Contract Analyzer per Non-Avvocati  
**Agente usato:** Claude Code CLI (terminale) — scelto al posto di Cursor per zero costo token e zero drift di modello (vedi Round 3)

---

## Round 1 — Christian (2026-05-20, ACADEMY):

Ho la specifica tecnica v3.1 confermata dalla prof a 95/100 e il system prompt già testato su 8 contratti PDF reali con 3 patch anti-allucinazione applicate (patch v2, v2.1, v2.2). Voglio iniziare il building. Come mi organizzo per il setup del progetto? Devo creare `.cursorrules`, `requirements.txt` e `.gitignore` per poi entrare in Cursor.

---

## Round 2 — CLAUDE CODE (2026-05-20, ACADEMY):

Setup creato:
- `requirements.txt` con le 8 dipendenze della specifica (fastapi, uvicorn, pymupdf, spacy, pydantic, jinja2, python-multipart, python-dotenv)
- `.gitignore` con `.env`, `__pycache__/`, `venv/`, `.tmp/`
- `.cursorrules` con le regole Pydantic v2 + Python 3.12 ricavate da Perplexity (Pydantic v2 usa `model_validate` non `parse_obj`, i validators sono `@field_validator` non `@validator`)

Commit: `e2ff7d1 Add: requirements.txt + .gitignore + .cursorrules`

---

## Round 3 — Christian (2026-05-20, ACADEMY):

Ho provato ad aprire Cursor ma ho visto che Claude Code CLI ha la stessa capacità di generare codice senza consumare token API a pagamento. Voglio abbandonare Cursor e costruire tutto con Claude Code CLI per rispettare la strategia token zero-cost che ho documentato nella spec v3.1 §7. Ha senso?

---

## Round 4 — CLAUDE CODE (2026-05-20, ACADEMY):

Sì, la scelta è coerente con la specifica: Claude Code CLI usa l'abbonamento esistente, non consuma crediti API Anthropic, e usa lo stesso modello del runtime (Sonnet 4.6). Nessun rischio di drift comportamentale tra testing e produzione.

Rimosso `.cursorrules` e `.cursor/rules/` — non servono fuori da Cursor.

Commit: `f7190b7 Remove: .cursor/rules/ — abbandonato Cursor, build con Claude Code CLI`

---

## Round 5 — Christian (2026-05-20, ACADEMY):

Inizia da `schemas.py`. Lo schema deve supportare la patch v2.1: `raw_excerpt` deve essere `list[str]` non `str`. E devo avere validatori che blocchino ellissi (`[...]`) dentro gli excerpt.

---

## Round 6 — CLAUDE CODE (2026-05-20, ACADEMY):

Scritto `schemas.py` con Pydantic v2:
- `CategoryResult`: `present: bool`, `risk_level: Literal["low","medium","high"] | None`, `raw_excerpt: list[str]`, `plain_language: str`
- Validator `@field_validator("raw_excerpt")`: blocca ellissi `[...]`, forza ogni span ≥ 20 caratteri, blocca lista vuota se `present=True`
- `ContractAnalysis`: 7 categorie fisse come chiavi, `language_detected`, normalizer che accetta anche `str` legacy e lo converte in `list[str]`

Test manuale: `ContractAnalysis.model_validate(json_esempio)` — PASS.

Commit: `40eb0f8 Add: schemas.py — Pydantic v2, CategoryResult + ContractAnalysis, validatori ellissi/span/7-categorie`

---

## Round 7 — Christian (2026-05-20, ACADEMY):

Ora `pdf_processor.py`. Deve estrarre testo con PyMuPDF, troncare a 40k caratteri e rifiutare i PDF scansionati con un messaggio chiaro (no OCR nel MVP).

---

## Round 8 — CLAUDE CODE (2026-05-20, ACADEMY):

Scritto `pdf_processor.py`:
- `extract_text(pdf_bytes)` → apre con `fitz.open(stream=...)`, concatena il testo di ogni pagina
- Se il testo estratto è < 100 caratteri totali → `raise ValueError("PDF scansionato o senza layer testo — OCR non supportato in questa versione")`
- Troncamento a 40.000 caratteri con taglio al whitespace più vicino per non spezzare parole

Test su `Contratto firmato.pdf` (scansionato): errore corretto, messaggio chiaro.

Commit: `19c15fc Add: pdf_processor.py — PyMuPDF 1.27, estrazione testo + troncamento 40k + gestione PDF corrotti/scansionati`

---

## Round 9 — Christian (2026-05-20, ACADEMY):

`regex_layer.py` — estrai date nel formato italiano (gg/mm/aaaa e gg mese aaaa), importi in euro, e scadenze con parole chiave tipo "entro", "termine", "scade".

---

## Round 10 — CLAUDE CODE (2026-05-20, ACADEMY):

Scritto `regex_layer.py` con `re` stdlib:
- Date numeriche: `\d{1,2}/\d{1,2}/\d{4}` e varianti con trattino
- Date letterali: `\d{1,2}\s+(gennaio|febbraio|...|dicembre)\s+\d{4}`
- Importi: `€\s*[\d.,]+|\d[\d.,]*\s*euro` con deduplicazione
- Scadenze: finestre con `(?:entro|termine|scade|scadenza).{0,50}` per catturare il contesto

Output: `dict` con chiavi `dates`, `amounts`, `deadlines` — passato come metadati al LLM.

Commit: `e903c97 Add: regex_layer.py — estrazione date/importi/scadenze con re stdlib`

---

## Round 11 — Christian (2026-05-20, ACADEMY):

Prima di `llm_client.py` voglio aggiungere un filtro privacy. La specifica §7 diceva che i dati personali non devono essere inviati ad Anthropic. Come implemento il redact/restore?

---

## Round 12 — CLAUDE CODE (2026-05-20, ACADEMY):

Prima ho verificato su Perplexity che l'approccio "on-premise filter" fosse il più adatto (non serve un servizio esterno). Ho proposto due opzioni: regex puro (veloce, 0 dipendenze extra) o hybrid regex + spaCy (più accurato su NER italiano).

Ho scelto hybrid dopo smoke test: spaCy `it_core_news_sm` (13MB) funziona, `it_core_news_lg` (550MB) andava in timeout su Windows.

`privacy_filter.py` implementa:
- Regex IT per CF, PIVA, IBAN, email, telefoni, date di nascita
- spaCy NER per PER (nomi), ORG, LOC, GPE
- `redact(text)` → `(testo_redatto, mapping)` dove mapping è `{PLACEHOLDER: valore_reale}`
- `restore(text, mapping)` → ripristina i placeholder nell'output LLM

Test su CF e PIVA reali: redact + restore round-trip OK.

Commit: `1e6a762 Add: privacy_filter.py — Hybrid regex IT + spaCy sm, redact/restore testati`

---

## Round 13 — Christian (2026-05-20, ACADEMY):

Ora `llm_client.py`. Deve chiamare Claude tramite Claude Code CLI, passare il system prompt da file, e gestire il retry se il JSON non è valido.

---

## Round 14 — CLAUDE CODE (2026-05-20, ACADEMY):

Scritto `llm_client.py`:
- Carica `prompts/system_prompt.md` ed estrae il blocco dentro i backtick dopo `## SYSTEM PROMPT`
- Chiama `subprocess.run(["claude", "-p", "--system-prompt", ..., "--output-format", "json"], input=user_message, ...)`
- Parsa `result.stdout` come JSON, estrae `data["result"]`
- Se `ContractAnalysis.model_validate_json()` fallisce → retry 1 volta con prompt più restrittivo
- Dopo l'analisi: `_restore_excerpts()` ripristina i dati personali redatti

Test E2E su `ContrattoCOCOCO.pdf` (co.co.co. ERSU Messina): JSON conforme, 7 categorie, `language_detected: italian`. PASS.

Commit: `28c1f81 Add: llm_client.py (Claude Code CLI) + fix schemas.py str->list[str] normalizer`

---

## Round 15 — Christian (2026-05-20, ACADEMY):

Scrivi `main.py` con FastAPI, l'endpoint `POST /analyze` che riceve un PDF e restituisce il report, e le due template HTML per la pagina di upload e il report.

---

## Round 16 — CLAUDE CODE (2026-05-20, ACADEMY):

Scritti `main.py`, `templates/index.html`, `templates/report.html`:
- `POST /analyze`: riceve `UploadFile`, valida MIME `application/pdf` e dimensione ≤ 10MB, chiama la pipeline `extract_text → extract_metadata → analyze`, restituisce `TemplateResponse` con il report
- `GET /`: pagina upload con form drag-and-drop
- `report.html`: 7 categorie con semaforo colore (verde/giallo/rosso secondo `risk_level`), `raw_excerpt` collassabile, disclaimer AI Act Art. 50 visibile senza scroll

Commit: `61a2de5 Add: main.py + templates/index.html + templates/report.html — MVP completo`

---

## Round 17 — Christian (2026-05-20, ACADEMY):

Testa il sistema su `Consip_CondizioniGeneraliRelativeAllaFornituraDiProdottiAgosto2018-A.pdf` e `210701_DRE_Capitolato-Tecnico-di-Appalto.pdf` — i due contratti più lunghi (40k char) che in un'altra sessione avevano fallito con WinError 206.

---

## Round 18 — CLAUDE CODE (2026-05-20, ACADEMY):

Diagnosi: il messaggio utente veniva passato come argv (`["claude", "-p", user_message, ...]`). Su Windows, la riga di comando ha un limite di ~32k caratteri — i contratti da 40k superavano il limite e causavano `WinError 206: The filename or extension is too long`.

**Fix (2 righe):** rimosso `user_message` dall'argv, aggiunto `input=user_message` come stdin al `subprocess.run()`.

Risultati post-fix:
- Consip (40k char) → 268 secondi, `language: italian`, 7/7 categorie parse OK ✅
- Capitolato Demanio (40k char) → 163 secondi, `language: italian`, 5/7 categorie (atteso: appalto pubblico senza IP né governing_law esplicita) ✅

**Score finale: 7/8 PDF passano. 1 rifiutato correttamente (scansionato, no OCR).**

Fix documentato come INC-004. Commit: `d2dc4ba Fix: llm_client.py — stdin invece di argv per evitare WinError 206 su Windows`

---

*Documento creato per il corso AI Projects Development — ITS ICT Academy Roma*
