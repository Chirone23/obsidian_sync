# SpecterAI — Breakdown in Task di Sviluppo

**Developer:** Christian Giordano  
**Progetto:** SpecterAI — AI Contract Analyzer per Non-Avvocati  
**Data:** 2026-05-20  
**Corso:** AI Projects Development — ITS ICT Academy Roma  

> Il piano che scompone il progetto in fasi da costruire in ordine preciso.  
> Il flusso del sistema è lineare: PDF → testo → regex → LLM → validazione → report.  
> Ogni modulo dipende dal precedente. L'ordine di costruzione segue il flusso.

---

## Fase 1 — Fondamenta

**Obiettivo:** Pipeline di input funzionante — da PDF grezzo a testo + metadati strutturati.  
**Dipendenza:** Nessuna — è il punto di partenza.  
**Deliverable:** PDF → testo leggibile + date/importi/scadenze estratti su 2 contratti reali.

| Task | File | Come testare | Stato |
|------|------|--------------|-------|
| Schema Pydantic | `schemas.py` | `ContractAnalysis.model_validate(json_esempio)` — se passa, lo schema è corretto | ✅ Completato |
| Estrazione testo da PDF | `pdf_processor.py` | Carica PDF reale, stampa testo estratto. Deve essere leggibile. PDF scansionato → errore chiaro | ✅ Completato |
| Layer regex | `regex_layer.py` | Estrai testo da contratto reale, passa al regex. Verifica a occhio: date, importi €, scadenze | ✅ Completato |
| Filtro privacy | `privacy_filter.py` | `redact()` oscura CF/PIVA/email/nomi. `restore()` li ripristina. Test round-trip | ✅ Completato |

**Note di build:**  
- `schemas.py` è il primo — tutto il resto valida contro di lui.  
- `raw_excerpt` implementato come `list[str]` (non `str`) per supportare la patch v2.1 anti-cross-article.  
- Validator che blocca ellissi `[...]` e span < 20 caratteri.  
- `privacy_filter.py` usa `spaCy it_core_news_sm` (13MB) — il modello `lg` (550MB) andava in timeout su Windows.

---

## Fase 2 — Core AI

**Obiettivo:** Integrazione LLM funzionante — da testo a JSON analisi validato.  
**Dipendenza:** Fase 1 completata.  
**Deliverable:** Flusso E2E `upload → analisi → report HTML` funzionante su almeno 1 contratto reale.

| Task | File | Come testare | Stato |
|------|------|--------------|-------|
| Prompt in file separato | `prompts/system_prompt.md` | Già testato su Claude.ai con 8 contratti reali pre-Cursor — JSON valido | ✅ Completato |
| Client LLM | `llm_client.py` | Chiama Claude CLI con testo contratto, riceve JSON, valida con Pydantic. Retry se fallisce | ✅ Completato |
| Integrazione regex + LLM | `llm_client.py` | JSON finale contiene metadati regex (date, importi) + analisi LLM (7 categorie) | ✅ Completato |

**Note di build:**  
- LLM backend: Claude Code CLI via `subprocess.run()` con `input=user_message` (stdin).  
- **Non usare argv per il messaggio** — WinError 206 su Windows con testi > 32k caratteri (INC-004).  
- Comando corretto: `["claude", "-p", "--system-prompt", ..., "--output-format", "json"]` + `input=user_message`.  
- Retry 1 volta con prompt più restrittivo se `model_validate_json()` fallisce.

---

## Fase 3 — Interfaccia

**Obiettivo:** Applicazione web accessibile da browser.  
**Dipendenza:** Fasi 1 e 2 completate.  
**Deliverable:** Form upload → spinner → report con semafori colore, disclaimer visibile.

| Task | File | Come testare | Stato |
|------|------|--------------|-------|
| Endpoint FastAPI | `main.py` | `POST /analyze` con PDF → JSON analisi. Valida MIME e dimensione (≤ 10MB) | ✅ Completato |
| Pagina upload | `templates/index.html` | Form con drag-and-drop, click "Analizza", spinner di attesa | ✅ Completato |
| Pagina report | `templates/report.html` | 7 categorie con semaforo (verde/giallo/rosso), `raw_excerpt` collassabile, disclaimer AI | ✅ Completato |

**Note di build:**  
- Semaforo colore: `risk_level=low` → verde, `medium` → giallo, `high` → rosso, `present=false` → grigio.  
- Disclaimer AI Act Art. 50 visibile senza scroll (requisito spec v3.1 §3).

---

## Fase 4 — Test e Documentazione

**Obiettivo:** Validare il sistema su contratti reali diversi e documentare tutto.  
**Dipendenza:** Fasi 1-3 completate.  
**Deliverable:** Test plan §8 (T1-T14) eseguiti, PROMPT_LOG e INCIDENTS aggiornati con dati reali.

| Task | Come testare | Stato |
|------|--------------|-------|
| Test 7 tipologie (7/8 PDF) | 1 appalto pubblico, 1 co.co.co., 1 fornitura PA, 1 NDA, 1 locazione PA, 1 Condizioni Generali, 1 Capitolato — output corretto? excerpt reali? | ✅ 7/8 PASS |
| Test edge case PDF scansionato | `Contratto firmato.pdf` → errore chiaro, no crash | ✅ Gestito correttamente |
| Test contratti lunghi (40k char) | Consip + Capitolato Demanio → analisi completa senza WinError | ✅ OK dopo fix INC-004 |
| PROMPT_LOG aggiornato | Iterazioni prompt + segnali S-01/S-02/S-03 documentati | ✅ Aggiornato |
| INCIDENTS aggiornato | INC-004 WinError 206 documentato e risolto | ✅ Aggiornato |
| Test plan §8 (T1-T12) formale | Esecuzione sistematica con pass/fail loggati | ⏳ Lezione 5 |
| T13: grep speculazione=0 | Cerca `(probabil|presumibil|potrebbe)` nell'output — deve dare 0 match | ⏳ Lezione 5 |
| T14: grounding plain↔raw_excerpt | Ogni numero/data/qualificatore modale del `plain_language` deve essere in almeno uno span di `raw_excerpt` | ⏳ Lezione 5 |

---

## Fase 5 — Rifinitura e Demo

**Obiettivo:** Sistema pronto per la presentazione finale.  
**Dipendenza:** Fase 4 completata.  
**Deliverable:** Demo live su 2-3 contratti, presentazione 2 minuti.

| Task | Stato |
|------|-------|
| Selezione 2-3 contratti per demo | ⏳ Lezione 6 |
| Polish HTML (layout responsive, footer disclaimer) | ⏳ Lezione 6 |
| README con istruzioni avvio (`uvicorn main:app --reload`) | ⏳ Lezione 6 |
| Preparazione presentazione (prompt + demo live) | ⏳ Lezione 6-7 |

---

## Dipendenze tra Moduli

```
schemas.py
    ↓
pdf_processor.py   privacy_filter.py   regex_layer.py
         ↓                ↓                  ↓
              llm_client.py  ←  prompts/system_prompt.md
                    ↓
                main.py
                    ↓
         templates/index.html + report.html
```

---

## Score Finale Build (2026-05-20)

| Metrica | Target spec | Risultato |
|---------|-------------|-----------|
| PDF analizzabili | 7/8 tipologie | ✅ 7/8 (1 scansionato rifiutato by design) |
| Latenza contratto lungo | < 5 min | ✅ 163-268s |
| JSON valido schema | 100% | ✅ 100% sui 7 PDF analizzabili |
| Categorie rilevate | 7/7 sempre presenti | ✅ (presenti nel JSON anche se `present=false`) |
| WinError 206 su Windows | 0 occorrenze | ✅ risolto con stdin fix |

---

*Documento creato per il corso AI Projects Development — ITS ICT Academy Roma*
