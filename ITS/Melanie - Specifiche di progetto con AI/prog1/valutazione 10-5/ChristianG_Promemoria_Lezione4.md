# Christian Giordano — Promemoria pre-Lezione 4

**Progetto:** SpecterAI — AI Contract Analyzer for Non-Lawyers  
**Data:** 10/05/2026  
**Valutazione 1:** 95/100 — Eccellente (confermata, nessun materiale nuovo consegnato)

---

## Stato

La tua spec v2 è la più completa della classe: prompt scritto con few-shot, changelog documentato, ambiente pronto con tutte le dipendenze installate. Non hai consegnato materiali nuovi — non ne avevi bisogno. Il punteggio resta 95/100.

Da domani si costruisce. Questo promemoria ti dà il breakdown operativo per partire.

---

## Checklist pre-building

Prima di aprire Cursor domani, verifica di avere:

- [ ] Prompt di sistema testato in Claude.ai con almeno 1 contratto reale
- [ ] Output JSON verificato: tutte e 7 le categorie presenti, `raw_excerpt` con citazioni reali, `risk_level` solo "low"/"medium"/"high"
- [ ] 3 contratti PDF di test pronti (servizi, NDA, fornitura)
- [ ] Struttura cartelle del progetto creata (vedi sotto)
- [ ] API key Anthropic funzionante in `.env`
- [ ] PROMPT_LOG.md e INCIDENTS.md creati (vuoti, si riempiono durante il building)

---

## Breakdown in task di sviluppo

Il tuo progetto ha un flusso lineare: PDF → testo → regex → LLM → validazione → report. Ogni modulo dipende dal precedente. L'ordine di costruzione segue il flusso.

### Fase 1 — Fondamenta (Lezione 4)

| Task | File | Dipende da | Come testare |
|------|------|------------|-------------|
| Schema Pydantic | `schemas.py` | Niente — è il primo | Crea un JSON di esempio a mano, validalo con `ContractAnalysis.model_validate(json)`. Se passa, lo schema è corretto. |
| Estrazione testo da PDF | `pdf_processor.py` | Niente | Carica un PDF reale, stampa il testo estratto. Deve essere completo e leggibile. Se il PDF ha layout complessi, accetta il fallimento con messaggio chiaro. |
| Layer regex | `regex_layer.py` | `pdf_processor.py` | Estrai testo da un contratto reale, passa al regex. Deve trovare: date (gg/mm/aaaa), importi (€), scadenze. Stampa i risultati e verifica a occhio. |

### Fase 2 — Core AI (Lezione 4-5)

| Task | File | Dipende da | Come testare |
|------|------|------------|-------------|
| Prompt in file separato | `prompts/system_prompt.md` | Niente — è già scritto nella spec v2 | Copialo dalla spec, incollalo in Claude.ai con un contratto. Verifica JSON valido. |
| Client LLM | `llm_client.py` | `schemas.py`, `prompts/` | Chiama Claude API con testo di un contratto, ricevi JSON, valida con Pydantic. Se validazione fallisce → retry 1 volta con prompt più restrittivo. |
| Integrazione regex + LLM | `llm_client.py` | `regex_layer.py`, `llm_client.py` | Il JSON finale deve contenere sia i dati regex (date, importi) sia l'analisi LLM (7 categorie). Confronta con il contratto originale. |

### Fase 3 — Interfaccia (Lezione 5)

| Task | File | Dipende da | Come testare |
|------|------|------------|-------------|
| Endpoint FastAPI | `main.py` | `pdf_processor.py`, `llm_client.py`, `schemas.py` | POST `/analyze` con file PDF → ricevi JSON con analisi completa. Testa con `curl` o Postman prima del frontend. |
| Pagina upload | `templates/index.html` | `main.py` | Form con drag-and-drop PDF, click "Analizza", vedi spinner. |
| Pagina report | `templates/report.html` | `main.py` | 7 categorie con semaforo colore (verde/giallo/rosso), disclaimer visibile senza scroll, `raw_excerpt` per ogni categoria. |

### Fase 4 — Test e documentazione (Lezione 5-6)

| Task | File | Dipende da | Come testare |
|------|------|------------|-------------|
| Test con 5 contratti | — | Tutto | 1 servizi, 1 NDA, 1 fornitura, 1 collaborazione, 1 locazione. Per ognuno: output corretto? Categorie giuste? Excerpt reali? |
| Test edge case | — | Tutto | PDF corrotto, PDF >10MB, PDF protetto, PDF scansione (immagine). Il sistema deve gestire con messaggio chiaro, non crash. |
| Test contratto inglese | — | Tutto | Output in italiano anche se il contratto è in inglese? Verifica. |
| PROMPT_LOG | `PROMPT_LOG.md` | Building | Ogni iterazione del prompt durante lo sviluppo: cosa hai cambiato, perché, risultato. |
| INCIDENTS | `INCIDENTS.md` | Building | Ogni errore concreto: cosa è successo, causa, soluzione. |

### Fase 5 — Rifinitura e demo (Lezione 6-7)

| Task | Note |
|------|------|
| Template HTML curato | Colori semaforo, layout responsive, footer con disclaimer |
| Download report | PDF o Markdown scaricabile (nice-to-have) |
| Selezione demo | 2-3 contratti che producono output impressionante |
| Preparazione presentazione | 2 minuti su come funziona il prompt + demo live |

---

## Struttura progetto

```
specter-ai/
├── main.py                # FastAPI app, endpoint upload + analisi
├── pdf_processor.py       # PyMuPDF: estrazione testo da PDF
├── regex_layer.py         # Estrazione date, importi, scadenze
├── llm_client.py          # Chiamata Claude API + gestione retry
├── schemas.py             # Schema Pydantic per validazione JSON
├── templates/
│   ├── index.html         # Pagina upload
│   └── report.html        # Template Jinja2 per il report
├── prompts/
│   └── system_prompt.md   # Il prompt (già scritto nella spec v2)
├── tests/
│   └── contratti/         # PDF di test
├── PROMPT_LOG.md
├── INCIDENTS.md
├── .env                   # ANTHROPIC_API_KEY
├── .gitignore
├── requirements.txt
└── README.md
```

---

## Strategia token

| Fase | Strumento | Perché |
|------|-----------|--------|
| Testare/iterare il prompt | Claude.ai (gratis) | Verificare output su contratti reali senza costi |
| Scrivere il codice | Cursor con Sonnet | Generazione moduli singoli, contesto ridotto |
| Test runtime | Claude API (Haiku) | ~€0,02 per analisi |
| Debug | Cursor con Sonnet | Fix nel contesto del progetto |

**Consiglio:** il tuo prompt è già scritto e testabile. Prima di aprire Cursor per ogni modulo, testa la logica in Claude.ai: "Ho una funzione che riceve X, deve restituire Y, gestire Z." Poi in Cursor chiedi di implementarla con la specifica già chiara. Risparmi cicli di fix.

---

## Domani alla Lezione 4

1. **Ascolta le indicazioni generali** a inizio lezione (gestione progetto, GitHub, token)
2. **Crea la struttura del progetto** se non l'hai già fatto
3. **Parti da schemas.py** — è il fondamento, tutto il resto lo valida contro
4. **Poi pdf_processor.py + regex_layer.py** — la pipeline di input
5. **Se arrivi al llm_client.py** nella stessa lezione, sei in vantaggio

---

*Promemoria pre-Lezione 4 — Corso AI Projects Development, ITS ICT Academy Roma*
