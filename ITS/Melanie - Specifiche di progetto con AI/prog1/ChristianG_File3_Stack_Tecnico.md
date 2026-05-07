# Christian G. — Stack Tecnico e Percorso Consigliato

**Progetto:** SpecterAI — AI Contract Analyzer for Non-Lawyers  
**Data:** 06/05/2026

---

## Analisi dello stack scelto

| Componente | Scelta | Adeguato? |
|-----------|--------|-----------|
| Linguaggio | Python 3.12+ | Sì |
| Backend | FastAPI | Sì — leggero, asincrono, perfetto per un endpoint upload+analisi |
| Template HTML | Jinja2 | Sì — integrato in FastAPI, zero frontend framework |
| PDF parsing | PyMuPDF (fitz) | Sì — veloce, affidabile su PDF digitali |
| Layer deterministico | regex (stdlib) | Sì — scelta intelligente per date/importi senza LLM |
| Validazione output | Pydantic v2 | Sì — schema enforcement sul JSON di Claude |
| LLM | Claude Sonnet | Sì — ottimo per testo legale, pricing sostenibile |
| Deploy | localhost (MVP) | Sì |

**Verdetto: stack eccellente.** Nessuna dipendenza superflua, nessun framework pesante, ogni componente ha una motivazione chiara. Lo stack è lineare e testabile modulo per modulo.

---

## Compatibilità con Cursor free plan + LLM gratuiti

**Cursor:** perfettamente compatibile. Il progetto è Python puro con pochi file — Cursor con Sonnet sarà molto efficace. La struttura a moduli separati (pdf_processor, regex_layer, llm_client, schemas) facilita il lavoro: puoi chiedere a Cursor di generare un modulo alla volta con contesto ridotto.

**LLM per il runtime:** il progetto usa Claude API a pagamento, ma il costo è trascurabile (~€0,02 per analisi). Per lo sviluppo:
- Testa il prompt in **Claude.ai** (gratis) prima di codificare
- Usa **Google AI Studio** (Gemini Flash, gratis) per test ripetuti di validazione JSON
- Riserva le chiamate Claude API per i test finali con contratti reali

**Hardware:** 16GB RAM, AMD Ryzen 5 PRO 6-core — ampiamente sufficiente. Non ci sono modelli locali né database vettoriali: tutto il carico pesante è su Claude API.

---

## Ordine di costruzione consigliato

### Modulo 1 — schemas.py + pdf_processor.py (Lezione 3, prima metà)
La base: definisci lo schema di output e l'estrazione testo.

```
Componenti:
- schemas.py: CategoryAnalysis + ContractAnalysis (Pydantic)
- pdf_processor.py: funzione extract_text(file_bytes) → str
  - Usa fitz.open(stream=file_bytes, filetype="pdf")
  - Gestisci edge case: PDF vuoto (<100 char), troppo grande (>10MB), protetto
- Test: carica un PDF reale, verifica che il testo estratto sia completo
```

### Modulo 2 — regex_layer.py (Lezione 3, seconda metà)
Il layer deterministico — estrazione senza LLM.

```
Componenti:
- Funzione extract_metadata(text) → dict con: date trovate, importi (€), 
  scadenze, nomi di parti contraenti
- Pattern regex per date italiane (gg/mm/aaaa, "entro 30 giorni", ecc.)
- Pattern regex per importi (€ 1.000,00, 1000 euro, ecc.)
- Test: testo con 3 date e 2 importi → verifica estrazione corretta
```

### Modulo 3 — llm_client.py (Lezione 4, prima metà)
La chiamata a Claude con retry e validazione.

```
Componenti:
- Funzione analyze_contract(text, metadata) → ContractAnalysis
- Carica system prompt da file prompts/system_prompt.md
- Chiamata Claude API con temperature=0, max_tokens=2048
- Parse JSON response → validazione Pydantic
- Retry logic: se JSON malformato, retry 1 volta con prompt più restrittivo
- Se retry fallisce: raise con errore strutturato
- Test: testo contratto reale → output JSON valido con tutte e 7 le categorie
```

### Modulo 4 — main.py + template Jinja2 (Lezione 4, seconda metà)
L'endpoint FastAPI e la pagina HTML.

```
Componenti:
- Endpoint POST /analyze: riceve file upload, orchestra il flusso
- Validazione input: MIME type, dimensione, densità testo
- Flusso: extract_text → extract_metadata → analyze_contract → render HTML
- Template report.html: 7 categorie con semaforo colore, disclaimer visibile
- Pagina upload index.html: form semplice con drag-and-drop PDF
- Test: upload PDF dal browser → report visualizzato correttamente
```

### Modulo 5 — Test end-to-end + documentazione (Lezione 5)
Test con contratti reali e documentazione.

```
Componenti:
- Test con 5 contratti diversi (servizi, locazione, fornitura, NDA, collaborazione)
- Test con contratto in inglese → output in italiano
- Test edge case: PDF corrotto, >10MB, protetto, scansione
- Compilazione PROMPT_LOG.md e INCIDENTS.md
- Verifica disclaimer visibile senza scroll
```

### Modulo 6 — Rifinitura e demo (Lezione 6)
Polish e preparazione presentazione.

```
Componenti:
- Miglioramento template HTML (colori semaforo, layout responsive)
- Eventuali fix da INCIDENTS
- Aggiornamento specifica se necessario (SPEC_ERRATA o v3)
- Preparazione demo: 2-3 contratti selezionati per la presentazione
```

---

## Strategia token

| Fase | Strumento | Perché |
|------|-----------|--------|
| Testare il system prompt | Claude.ai (gratis) | Iterare sul prompt senza costi |
| Test ripetuti validazione JSON | Google AI Studio (Gemini Flash, gratis) | Verificare schema output in serie |
| Scrivere il codice Python | Cursor con Sonnet | Generazione moduli singoli, contesto ridotto |
| Test end-to-end runtime | Claude API (pay-per-use) | ~€0,02 per analisi, budget totale <€1 per tutti i test |
| Debug e fix | Cursor con Sonnet | Fix nel contesto del progetto |

**Consiglio pratico:** il tuo prompt di sistema è già scritto nella spec v2 — è pronto per essere copiato in `prompts/system_prompt.md`. Testa subito in Claude.ai con un contratto reale. Se l'output è corretto, il 50% del progetto è già fatto concettualmente.

---

## Rischi e semplificazioni

**Se PyMuPDF fallisce su layout complessi:**
- Per l'MVP, accetta il fallimento con messaggio chiaro. Non tentare di risolvere ogni layout — i contratti standard funzionano, quelli esotici sono fuori scope dichiarato.

**Se il template HTML diventa troppo complesso:**
- Per la Lezione 4, un report HTML minimale (lista testuale con colori inline per i semafori) è sufficiente. Il polish estetico si fa alla Lezione 6.

**Se il retry JSON fallisce troppo spesso:**
- Aggiungi nel prompt di retry: "Your previous response was not valid JSON. Return ONLY the JSON object, no markdown fences, no explanation." Questo risolve il 90% dei casi.
- Se continua a fallire, verifica che il testo del contratto non sia troppo lungo — il troncamento a 40k char che hai previsto è la mitigazione giusta.

**Se il tempo avanza (probabile):**
- Aggiungi il download del report in PDF (con weasyprint o un semplice "print to PDF" dal browser)
- Migliora il template HTML con CSS più curato
- Aggiungi un indicatore di confidence per il risk_level

---

## Roadmap per le 4 lezioni

| Lezione | Obiettivo | Deliverable |
|---------|-----------|-------------|
| Lezione 3 | Modulo 1 + 2: estrazione testo + layer regex | PDF → testo pulito + metadati estratti, testato su 2 contratti |
| Lezione 4 | Modulo 3 + 4: Claude API + FastAPI + Jinja2 | Flusso completo: upload PDF → report HTML nel browser |
| Lezione 5 | Modulo 5: test con 5 contratti + documentazione | PROMPT_LOG e INCIDENTS compilati, edge case testati |
| Lezione 6 | Modulo 6: rifinitura + demo | Template HTML curato, demo pronta con 2-3 contratti |

---

*Percorso tecnico — Corso AI Projects Development, ITS ICT Academy Roma*
