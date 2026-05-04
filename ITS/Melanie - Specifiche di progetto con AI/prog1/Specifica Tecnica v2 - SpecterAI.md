# Specifica Tecnica v2 — SpecterAI

**Progetto:** SpecterAI — AI Contract Analyzer for Non-Lawyers
**Versione:** 2.0
**Data:** 2026-05-04
**Corso:** AI Projects Development · [[Progettistica AI MOC]]
**Stato:** Revisione post-analisi critica v1

---

## Changelog v1 → v2

| Sezione | Modifica |
|---|---|
| §6 Comportamento AI | Aggiunto prompt di sistema completo (Ruolo + Task + Formato + Vincoli + Esclusioni) |
| §6 Comportamento AI | Aggiunti few-shot examples (Regola #4 prompt engineering) |
| §6 Comportamento AI | Aggiunta strategia multi-model routing e parametri modello |
| §7 Privacy e Vincoli | Aggiunta sezione AI Sustainability / Green AI (assente in v1) |
| §3 Flusso Operativo | Formalizzati gli edge case degli input |
| §12 (nuovo) | Aggiunta sezione Documentazione di Progetto (PROMPT_LOG, INCIDENTS, SESSION_HANDOFF) |

---

## 1. Sintesi del Progetto

**Problema:** Freelance, autonomi e piccoli imprenditori ricevono contratti che non capiscono appieno e spesso firmano senza sapere cosa rischiano. Gli strumenti esistenti (Spellbook, Harvey AI, Docusign IAM) sono pensati per avvocati e legal team enterprise: output in linguaggio giuridico, pricing fuori portata, workflow complessi.

**Soluzione:** SpecterAI è un sistema che analizza contratti in formato PDF e restituisce un report in linguaggio plain con i punti critici da verificare prima di firmare. Non dà consulenza legale — evidenzia i rischi e suggerisce le domande da porre.

**Utenti target:** Freelance, liberi professionisti, piccoli imprenditori italiani senza legal team interno. Stima mercato: 2,7M soggetti in Italia (Statista 2019).

**Valore prodotto:** Ridurre il rischio di firmare contratti con clausole sfavorevoli senza averle comprese, senza pagare un avvocato per ogni documento.

**Posizionamento AI Act:** Sistema di *decision-support*, non di consulenza legale. Ogni output include disclaimer esplicito. L'utente mantiene piena responsabilità decisionale.

---

## 2. MVP e Funzionalità

### In scope (MVP)
- Upload di un PDF contratto (digitale, non scansione)
- Estrazione e analisi di 7 categorie di red flag
- Output web strutturato in italiano con spiegazione plain-language per ogni categoria
- Disclaimer AI Act visibile in ogni output
- Supporto contratti in italiano e inglese (processing interno in inglese)
- Nessuna persistenza dei dati (processing on-request)

### Fuori scope (versioni future)
- OCR per PDF scansionati
- Confronto tra versioni del contratto
- Download output in PDF
- Storico analisi / account utente
- Integrazione con firma digitale
- Dashboard multi-documento
- Notifiche scadenze contrattuali

---

## 3. Flusso Operativo

```
[Utente] → Upload PDF
         → FastAPI: validazione input (dimensione, MIME type, densità testo)
         → PyMuPDF estrae il testo grezzo
         → Layer deterministico estrae date, importi, scadenze (regex)
         → Rilevamento lingua (heuristica, no LLM)
         → Testo inviato a Claude API (claude-sonnet-4-6, prompt in inglese)
         → Claude restituisce JSON strutturato con analisi per categoria
         → Pydantic valida lo schema JSON
         → FastAPI renderizza la risposta via Jinja2
         → [Utente] visualizza il report nel browser
         → File eliminato dalla memoria (nessuna persistenza)
```

### Requisiti funzionali
- Il sistema deve processare un PDF entro 30 secondi
- L'output deve coprire tutte e 7 le categorie (anche se non presenti nel contratto: `"present": false`)
- Il disclaimer deve essere visibile senza scroll nella pagina di output
- In caso di PDF non leggibile, il sistema deve restituire un messaggio di errore chiaro

### Input — edge case formali

| Condizione | Comportamento atteso |
|---|---|
| PDF digitale standard (≤10MB, >100 char testo) | Flusso normale |
| PDF >10MB | Errore bloccante prima dell'elaborazione |
| PDF scansionato (testo <100 char estratto) | Errore con spiegazione: "supporta solo PDF digitali" |
| PDF con campi compilabili non riempiti | Estrazione parziale, avviso nell'output |
| PDF protetto da password | Errore: "il file è protetto, rimuovi la password" |
| File non PDF (MIME type errato) | Errore: "formato non supportato" |
| Testo estratto >50.000 caratteri | Troncamento agli ultimi 40.000 char + avviso in output |
| Contratto in lingua non IT/EN | Elaborazione comunque, avviso "lingua non ottimale" |

### Requisiti non funzionali
- Nessun dato persistito su disco o database
- Il file PDF non deve mai uscire dal server (no forward a terze parti eccetto Claude API via HTTPS)
- Dimensione massima file accettata: 10 MB

---

## 4. Stack Tecnologico

| Componente | Tecnologia | Motivazione |
|---|---|---|
| Linguaggio | Python 3.12+ | Standard del corso, ecosistema ricco per PDF e LLM |
| Web framework | FastAPI | Leggero, asincrono, nativo per API, consigliato dal corso |
| Template HTML | Jinja2 | Integrato in FastAPI, zero frontend framework per MVP |
| PDF parsing | PyMuPDF (fitz) | Open source, veloce, affidabile su PDF digitali |
| Layer deterministico | re (regex stdlib) | Estrazione date/importi/scadenze senza dipendere dall'LLM |
| Validazione output AI | Pydantic v2 | Schema enforcement sul JSON restituito da Claude |
| LLM principale | Claude API (claude-sonnet-4-6) | Migliore accuratezza su testo legale, pricing sostenibile |
| AI code editor | Cursor | Ambiente di sviluppo con AI integrata |
| Version control | Git + GitHub | Backup, tracciabilità, consegna corso |
| Deploy MVP | Locale (localhost) | Sufficiente per demo corso |

### Dipendenze Python principali
```
fastapi
uvicorn
pymupdf
anthropic
pydantic
jinja2
python-multipart
python-dotenv
```

---

## 5. Architettura e Flusso Dati

### Componenti
```
┌─────────────┐    PDF upload    ┌──────────────┐
│   Browser   │ ───────────────► │   FastAPI    │
│  (Jinja2)   │ ◄─────────────── │   Server     │
└─────────────┘    HTML report   └──────┬───────┘
                                        │
                              ┌─────────▼────────┐
                              │  Input Validator  │
                              │  (MIME, size,     │
                              │   text density)  │
                              └─────────┬────────┘
                                        │
                              ┌─────────▼────────┐
                              │  PDF Processor   │
                              │  (PyMuPDF)       │
                              └─────────┬────────┘
                                        │ testo grezzo
                              ┌─────────▼────────┐
                              │ Regex Layer      │
                              │ (date, importi)  │
                              └─────────┬────────┘
                                        │ testo + metadati
                              ┌─────────▼────────┐
                              │  Claude API      │
                              │ (claude-sonnet)  │
                              └─────────┬────────┘
                                        │ JSON
                              ┌─────────▼────────┐
                              │ Pydantic Schema  │
                              │ Validation       │
                              └──────────────────┘
```

### Flusso dati e privacy
- Il PDF viene caricato in memoria RAM, non scritto su disco
- Il testo estratto viene inviato a Claude API via HTTPS
- Nessun dato viene salvato dopo la risposta
- Ogni richiesta è stateless e indipendente

---

## 6. Comportamento AI

### Multi-model routing

| Task | Modello | Motivazione |
|---|---|---|
| Validazione input (testo sufficiente?) | Nessun LLM — heuristica Python | Task deterministico, zero token consumati |
| Rilevamento lingua (IT vs EN) | Nessun LLM — libreria `langdetect` | Task semplice, non serve modello avanzato |
| Analisi contratto (7 categorie) | claude-sonnet-4-6 | Task critico e ambiguo, richiede modello bilanciato |
| Retry su JSON malformato | claude-sonnet-4-6 con prompt più restrittivo | Stessa classe, non declassare su task già fallito |

### Parametri modello
```python
response = client.messages.create(
    model="claude-sonnet-4-6",
    max_tokens=2048,        # cap output per ridurre costi e latenza
    temperature=0,          # task tecnico: massima determinismo
    system=SYSTEM_PROMPT,
    messages=[{"role": "user", "content": contract_text}]
)
```

**Nota temperatura:** 0 (non 1) perché l'analisi contrattuale è un task tecnico che richiede coerenza e riproducibilità, non creatività.

### Token optimization
- `max_tokens=2048` — sufficiente per 7 categorie JSON, evita output verbosi inutili
- Testo contratto troncato a 40.000 char prima dell'invio (risparmio ~30% sui contratti lunghi)
- Il prompt di sistema è fisso e conciso (< 600 token)
- Nessuna history conversazionale inviata — ogni analisi è una chiamata singola stateless

---

### Prompt di Sistema (System Prompt)

Il prompt segue la struttura: **Ruolo → Task → Formato output → Vincoli → Esclusioni**.

```
You are a contract analysis assistant specialized in identifying risks for 
non-lawyers: freelancers, self-employed professionals, and small business owners.

TASK
Analyze the provided contract text and extract critical information in exactly 
7 categories. For each category, extract:
- whether the clause is explicitly present in the text
- the verbatim excerpt from the contract (empty string if absent)
- a plain-language explanation in Italian (max 50 words)
- a risk level: "low", "medium", or "high"
- one concrete question the user should ask before signing (in Italian)

OUTPUT FORMAT
Return ONLY a valid JSON object. No prose, no markdown fences, no explanation 
outside the JSON. Match this schema exactly:

{
  "language_detected": "italian" | "english",
  "categories": {
    "payment_terms":        { "present": bool, "raw_excerpt": str, "plain_language": str, "risk_level": str, "question_to_ask": str },
    "auto_renewal":         { "present": bool, "raw_excerpt": str, "plain_language": str, "risk_level": str, "question_to_ask": str },
    "penalties":            { "present": bool, "raw_excerpt": str, "plain_language": str, "risk_level": str, "question_to_ask": str },
    "liability_limitation": { "present": bool, "raw_excerpt": str, "plain_language": str, "risk_level": str, "question_to_ask": str },
    "termination":          { "present": bool, "raw_excerpt": str, "plain_language": str, "risk_level": str, "question_to_ask": str },
    "governing_law":        { "present": bool, "raw_excerpt": str, "plain_language": str, "risk_level": str, "question_to_ask": str },
    "intellectual_property":{ "present": bool, "raw_excerpt": str, "plain_language": str, "risk_level": str, "question_to_ask": str }
  },
  "top_3_risks": [str, str, str],
  "disclaimer": "Questo report non costituisce consulenza legale. Prima di firmare, consulta un professionista qualificato."
}

CONSTRAINTS
- If a category is absent from the contract: "present": false, "raw_excerpt": ""
- risk_level must be exactly one of: "low", "medium", "high" — no other values
- plain_language must be in Italian, plain language, max 50 words
- question_to_ask must be in Italian, phrased as a direct question
- top_3_risks must reference the 3 highest-risk categories actually present in the contract
- raw_excerpt must be a verbatim quote from the contract text, not a paraphrase

DO NOT
- Invent clauses not explicitly present in the contract text
- Use legal jargon in plain_language
- Advise whether to sign or not to sign the contract
- Return any text, explanation, or formatting outside the JSON object
```

---

### Few-Shot Examples

I few-shot vengono iniettati come `user/assistant` messages prima del contratto reale, per ancorare il modello sul formato esatto dell'output.

**Esempio 1 — clausola presente, rischio alto**

*Input excerpt:*
```
Art. 5 — Rinnovo del contratto
Il presente contratto si rinnova automaticamente per ulteriori 12 mesi 
salvo disdetta da comunicarsi almeno 90 giorni prima della scadenza 
a mezzo raccomandata A/R.
```

*Output atteso (solo categoria `auto_renewal`):*
```json
"auto_renewal": {
  "present": true,
  "raw_excerpt": "Il presente contratto si rinnova automaticamente per ulteriori 12 mesi salvo disdetta da comunicarsi almeno 90 giorni prima della scadenza a mezzo raccomandata A/R.",
  "plain_language": "Il contratto si rinnova da solo ogni anno. Per uscire devi avvisare 90 giorni prima con raccomandata — quasi 3 mesi di anticipo.",
  "risk_level": "high",
  "question_to_ask": "Possiamo ridurre il preavviso di disdetta da 90 a 30 giorni?"
}
```

**Esempio 2 — clausola assente**

*Input excerpt:* (nessun riferimento a proprietà intellettuale nel testo)

*Output atteso (solo categoria `intellectual_property`):*
```json
"intellectual_property": {
  "present": false,
  "raw_excerpt": "",
  "plain_language": "Il contratto non specifica chi possiede i materiali prodotti al termine del rapporto. Questa assenza è un rischio.",
  "risk_level": "medium",
  "question_to_ask": "Chi è proprietario dei materiali, del codice o dei contenuti che produco nell'ambito di questo contratto?"
}
```

---

### Schema output Claude (JSON completo)
```json
{
  "language_detected": "italian|english",
  "categories": {
    "payment_terms": {
      "present": true,
      "raw_excerpt": "...",
      "plain_language": "...",
      "risk_level": "low|medium|high",
      "question_to_ask": "..."
    },
    "auto_renewal":         { "...": "..." },
    "penalties":            { "...": "..." },
    "liability_limitation": { "...": "..." },
    "termination":          { "...": "..." },
    "governing_law":        { "...": "..." },
    "intellectual_property":{ "...": "..." }
  },
  "top_3_risks": ["...", "...", "..."],
  "disclaimer": "Questo report non costituisce consulenza legale..."
}
```

### Le 7 categorie analizzate
1. **Termini di pagamento** — scadenze, modalità, interessi di mora
2. **Rinnovo automatico** — clausole di rinnovo tacito, tempistiche disdetta
3. **Penali e ritardi** — sanzioni per ritardi di consegna o pagamento
4. **Limitazione di responsabilità** — cap sui danni, esclusioni
5. **Recesso e disdetta** — condizioni e tempistiche per uscire dal contratto
6. **Foro competente** — dove si risolvono le controversie
7. **Proprietà intellettuale** — chi possiede cosa al termine del contratto

---

## 7. Dati, Privacy e Vincoli Normativi

### GDPR
- Nessun dato personale persistito
- Il PDF è processato in memoria e scartato dopo l'analisi
- Nessuna registrazione utente, nessun cookie di tracciamento
- Il testo del contratto viene inviato a Claude API (Anthropic): Anthropic garantisce no-training su chiamate API (verificare ToS prima del deploy pubblico)

### AI Act
- Classificazione: sistema potenzialmente **alto rischio** (analisi documenti legali)
- Mitigazione: posizionamento come *decision-support tool*, non consulenza legale
- Disclaimer obbligatorio in ogni output
- Human-in-the-loop: l'utente legge il report e decide autonomamente
- Nessuna decisione automatizzata che impatta diritti o obbligazioni

### AI Sustainability / Green AI

> Ogni chiamata API consuma energia. Ottimizzare i token è una scelta di sostenibilità progettuale, destinata a diventare sempre più rilevante nei criteri di valutazione dei sistemi AI. — Lezione 2

| Scelta progettuale | Impatto sostenibilità |
|---|---|
| `max_tokens=2048` (cap esplicito) | Riduce energia consumata per generare output inutilmente lunghi |
| Troncamento testo a 40.000 char | Riduce token input del ~30% sui contratti lunghi |
| Routing: task semplici → no LLM | Evita chiamate API per operazioni deterministiche (lingua, validazione) |
| Stateless: nessuna history | Ogni richiesta è una singola chiamata, nessun contesto accumulato inutilmente |
| Temperature=0 | Riduce il campionamento probabilistico: meno "tentativi" interni del modello |

**Stima consumo per analisi:** ~2.500–4.000 token input + ~1.500–2.000 token output = ~4.000–6.000 token/analisi. Con claude-sonnet-4-6, il costo medio è <0,02 €/analisi.

### Disclaimer (testo UI)
> SpecterAI è uno strumento di supporto alla lettura dei documenti. Le informazioni fornite non costituiscono consulenza legale. Prima di firmare qualsiasi contratto, consulta un professionista qualificato.

---

## 8. Validazione e Quality Control

### Metriche di qualità MVP
- **Copertura categorie**: 100% delle 7 categorie presenti nell'output (anche se `"present": false`)
- **Latenza**: risposta entro 30 secondi per contratti fino a 10 pagine
- **Accuratezza estrattiva**: date e importi estratti dal layer deterministico devono corrispondere al testo originale
- **Disclaimer visibilità**: presente senza scroll nella pagina di output
- **Schema compliance**: 0 errori Pydantic sull'output di Claude in condizioni normali

### Test pianificati
- Test con 5 contratti reali di tipo diverso (servizi, locazione, fornitura, NDA, collaborazione)
- Test con contratto in inglese → verifica output in italiano
- Test con PDF corrotto → verifica messaggio di errore
- Test con contratto senza clausola IP → verifica `"present": false` nell'output
- Test con PDF protetto da password → verifica errore chiaro
- Test con PDF >10MB → verifica blocco prima dell'elaborazione

### Validazione output AI
- Il JSON restituito da Claude deve matchare lo schema Pydantic
- Se JSON malformato: retry una volta con prompt più restrittivo (`DO NOT return any text outside the JSON`)
- Se retry fallisce: errore generico all'utente, log dell'incident in INCIDENTS.md
- Il `risk_level` deve essere uno tra: `"low"`, `"medium"`, `"high"` — Pydantic lo valida con `Literal`

---

## 9. Gestione Errori e Fallback

| Errore | Comportamento |
|---|---|
| PDF non leggibile / corrotto | "Il file non è leggibile. Assicurati che sia un PDF digitale, non una scansione." |
| PDF troppo grande (>10MB) | "Il file supera i 10MB. Carica un documento più leggero." |
| PDF protetto da password | "Il file è protetto da password. Rimuovi la protezione e riprova." |
| Testo estratto < 100 caratteri | "Il documento sembra vuoto o è una scansione. SpecterAI supporta solo PDF digitali." |
| Testo estratto > 50.000 char | Elaborazione con troncamento + avviso "Contratto lungo: analizzate le prime 40.000 parole" |
| Tipo file non PDF (MIME errato) | "Formato non supportato. Carica un file PDF." |
| Claude API timeout | Retry automatico (1 volta), poi: "Analisi temporaneamente non disponibile. Riprova tra qualche minuto." |
| JSON malformato da Claude | Retry con prompt restrittivo (1 volta), poi errore generico + log in INCIDENTS.md |
| Pydantic validation error | Come JSON malformato — retry, poi errore generico |
| Errore generico server | "Qualcosa è andato storto. Nessun tuo dato è stato salvato." |

---

## 10. Deploy, Manutenzione e Aggiornamenti

### MVP (corso)
- Deploy: localhost con `uvicorn main:app --reload`
- Variabili d'ambiente: `ANTHROPIC_API_KEY` in `.env` (mai committato)
- Avvio: `python -m uvicorn main:app --host 0.0.0.0 --port 8000`

### Futuro (post-corso)
- Deploy su Render o Railway (free tier, ~5-8 €/mese)
- Aggiunta rate limiting per prevenire abusi
- Monitoring errori con Sentry

### Aggiornamenti modello
- Il sistema è provider-agnostic: la logica di estrazione è separata dall'LLM
- Switch da Claude a GPT-4o richiede solo cambio client e adattamento del prompt (< 1 ora)
- Il routing multi-modello permette di aggiornare singoli step senza toccare gli altri

---

## 11. Rischi, Assunzioni e Checklist Pre-Build

### Assunzioni dichiarate
- Il freelance medio gestisce 10-20 contratti/anno (nessun dato pubblico disponibile — assunzione conservativa)
- I contratti target sono PDF digitali standard (non scansioni, non PDF con campi compilabili complessi)
- Anthropic non usa le chiamate API per training (verificare ToS prima del deploy pubblico)
- Il troncamento a 40.000 char copre le clausole critiche nella maggior parte dei contratti standard

### Rischi identificati

| Rischio | Probabilità | Impatto | Mitigazione |
|---|---|---|---|
| PDF parsing fallisce su layout non standard | Media | Alto | Layer deterministico + messaggio errore chiaro |
| Claude allucina clausole inesistenti | Bassa | Alto | Pydantic validation + `raw_excerpt` testuale obbligatorio nel JSON |
| AI Act: classificazione alto rischio | Presente | Medio | Disclaimer + posizionamento decision-support |
| Dipendenza da Claude API (costi/disponibilità) | Bassa | Medio | Architettura provider-agnostic, switch facile |
| Scope creep durante sviluppo | Alta | Medio | Specifica congelata — nessuna feature non listata in scope |
| Troncamento a 40.000 char taglia clausola critica | Bassa | Alto | Avviso in output + futura versione con chunking semantico |
| JSON retry fallisce → utente senza output | Molto bassa | Medio | Log in INCIDENTS.md, errore trasparente all'utente |

### Checklist pre-build
- [x] Obiettivo principale definito in una frase
- [x] Perimetro: cosa fa / cosa non fa
- [x] Utenti target identificati
- [x] Input formalmente descritti (tipo, formato, vincoli, edge case)
- [x] Output formalmente descritti (struttura, formato, range)
- [x] Almeno 3 metriche di qualità quantitative definite
- [x] Architettura a componenti schematizzata
- [x] Dipendenze esterne elencate
- [x] Minimo 3 rischi identificati con strategia di mitigazione
- [x] Assunzioni esplicitate
- [x] Vincoli GDPR e AI Act inseriti
- [x] AI Sustainability / Green AI inserita
- [x] Prompt di sistema completo (Ruolo + Task + Formato + Vincoli + Esclusioni)
- [x] Few-shot examples specificati
- [x] Multi-model routing definito
- [x] Token optimization strategy definita
- [x] File di documentazione di progetto referenziati

---

## 12. Documentazione di Progetto

Durante lo sviluppo vengono mantenuti i seguenti file nella cartella `prog1/`:

| File | Scopo | Quando aggiornarlo |
|---|---|---|
| `PROMPT_LOG.md` | Diario delle iterazioni: prompt testati, cosa ha funzionato e perché | Dopo ogni test significativo del prompt |
| `INCIDENTS.md` | Registro errori: cosa ha fallito, causa, soluzione applicata | Ogni volta che un errore inatteso si verifica |
| `SESSION_HANDOFF.md` | Stato del progetto tra sessioni: decisioni prese, problemi aperti, prossimi step | All'inizio e alla fine di ogni sessione di lavoro |

Questi file fanno parte della valutazione del corso — documentare il processo di revisione (v1, v2, v3…) ha lo stesso peso del prodotto finale.

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Brainstorming - Validazione Idea]]
- [[Contract Analyzer - Validazione Idea]]
- [[Lezione 2 - Specifica Tecnica e Prompt Engineering]]
- [[Template - Specifica Tecnica]]
- [[Template - PROMPT_LOG]]
- [[Template - INCIDENTS]]
