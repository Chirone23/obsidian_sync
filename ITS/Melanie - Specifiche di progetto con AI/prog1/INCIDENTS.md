# INCIDENTS — SpecterAI

**Progetto:** SpecterAI (AI Contract Analyzer per Non-Avvocati in Italiano)  
**Data inizio:** 2026-05-04  

---

## Tabella Incidents

| ID | Data | Componente | Severità | Status |
|---|---|---|---|---|
| INC-001 | TBD | PyMuPDF text extraction | Critical | Open |
| INC-002 | TBD | Claude API timeout | High | Open |
| INC-003 | TBD | JSON parsing / malformed response | High | Open |

---

## INC-001 — PyMuPDF Text Extraction

**Data:** TBD (Expected durante Fase 2 — PDF Input Layer)

**Componente:** PDF parsing layer

**Descrizione:** PyMuPDF potrebbe non estrarre correttamente testo da:
- PDF scansionati (OCR-required)
- PDF con form fields nascosti
- PDF con encoding non-standard
- PDF multi-colonna con layout complesso

**Severità:** Critical (blocca core functionality)

**Root cause:** [TBD — sarà analizzato durante testing]

**Soluzione:** [TBD]

**Lezioni apprese:** [TBD]

**Aggiornamenti Specifica:** [Modifiche applicate alla v2 se necessario]

**Status:** Open

---

## INC-002 — Claude API Timeout

**Data:** TBD (Expected durante Fase 4 — Claude Integration)

**Componente:** Claude API integration layer

**Descrizione:** Timeout del modello Claude su contratti lunghi (>8000 token), rate limiting API su batch analysis.

**Severità:** High

**Root cause:** [TBD — sarà analizzato durante testing]

**Soluzione:** [TBD — possibili mitigazioni: chunking, streaming, batch API]

**Lezioni apprese:** [TBD]

**Aggiornamenti Specifica:** [Modifiche applicate alla v2 se necessario]

**Status:** Open

---

## INC-003 — JSON Parsing / Malformed Response

**Data:** TBD (Expected durante Fase 4 — Claude Integration)

**Componente:** JSON validation layer

**Descrizione:** Claude potrebbe generare JSON malformato in edge case (clausole ambigue, contratti molto lunghi, linguaggio non-standard).

**Severità:** High

**Root cause:** [TBD — sarà analizzato durante testing]

**Soluzione:** [TBD — possibili mitigazioni: response validation, fallback parsing, structured output API]

**Lezioni apprese:** [TBD]

**Aggiornamenti Specifica:** [Modifiche applicate alla v2 se necessario]

**Status:** Open

---

## Protocollo di Gestione Strutturata degli Errori

Quando un incident viene riscontrato durante il building:

1. **Leggere il messaggio di errore e stack trace** — acquisire tutte le informazioni
2. **Capire la causa radice** — non coprire il sintomo, capire cosa ha causato il comportamento
3. **Correggere e testare** — fix + validazione con caso di test specifico
4. **Documentare su INCIDENTS.md** — aggiungere entry nella tabella + sezione dettagliata
5. **Aggiornare la Specifica v2 se necessario** — se il fix implica cambio architetturale

---

## Template per Nuovi Incidents

```
## INC-XXX — [Nome breve]

**Data:** [YYYY-MM-DD]

**Componente:** [Layer / Modulo]

**Descrizione:** [Cosa è successo, quando è stato notato]

**Severità:** [Critical / High / Medium / Low]

**Root cause:** [Analisi della causa radice]

**Soluzione:** [Cosa è stato cambiato/testato]

**Lezioni apprese:** [Cosa abbiamo imparato per futuro]

**Aggiornamenti Specifica:** [Modifiche applicate alla v2]

**Status:** [Open / Resolved]
```
