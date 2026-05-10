# Specifica Tecnica v1 — SpecterAI

**Progetto:** SpecterAI — AI Contract Analyzer for Non-Lawyers
**Versione:** 1.0
**Data:** 2026-05-04
**Corso:** AI Projects Development · [[Progettistica AI MOC]]
**Stato:** Prima bozza completa

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
         → FastAPI riceve il file in memoria
         → PyMuPDF estrae il testo grezzo
         → Layer deterministico estrae date, importi, scadenze (regex)
         → Testo inviato a Claude API (con prompt strutturato in inglese)
         → Claude restituisce JSON strutturato con analisi per categoria
         → FastAPI renderizza la risposta via Jinja2
         → [Utente] visualizza il report nel browser
         → File eliminato dalla memoria (nessuna persistenza)
```

### Requisiti funzionali
- Il sistema deve processare un PDF entro 30 secondi
- L'output deve coprire tutte e 7 le categorie (anche se non presenti nel contratto: segnalare "clausola assente")
- Il disclaimer deve essere visibile senza scroll nella pagina di output
- In caso di PDF non leggibile, il sistema deve restituire un errore chiaro

### Requisiti non funzionali
- Nessun dato persistito su disco o database
- Il file PDF non deve mai uscire dal server (no forward a terze parti eccetto Claude API via HTTPS)
- Dimensione massima file accettata: 10 MB
- Lingua contratto supportata: italiano, inglese

---

## 4. Stack Tecnologico

| Componente | Tecnologia | Motivazione |
|---|---|---|
| Linguaggio | Python 3.12+ | Standard del corso, ecosistema ricco per PDF e LLM |
| Web framework | FastAPI | Leggero, asincrono, nativo per API, consigliato dal corso |
| Template HTML | Jinja2 | Integrato in FastAPI, zero frontend framework per MVP |
| PDF parsing | PyMuPDF (fitz) | Open source, veloce, affidabile su PDF digitali |
| Layer deterministico | re (regex stdlib) | Estrazione date/importi/scadenze senza dipendere dall'LLM |
| LLM | Claude API (claude-sonnet-4-6) | Migliore accuratezza su testo legale, pricing sostenibile |
| AI code editor | Cursor | Ambiente di sviluppo con AI integrata |
| Version control | Git + GitHub | Backup, tracciabilità, consegna corso |
| Deploy MVP | Locale (localhost) | Sufficiente per demo corso |

### Dipendenze Python principali
```
fastapi
uvicorn
pymupdf
anthropic
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
                              │  (analisi JSON)  │
                              └──────────────────┘
```

### Flusso dati e privacy
- Il PDF viene caricato in memoria RAM, non scritto su disco
- Il testo estratto viene inviato a Claude API via HTTPS
- Nessun dato viene salvato dopo la risposta
- Ogni richiesta è stateless e indipendente

---

## 6. Comportamento AI

### Prompt strategy
- Lingua del prompt: inglese (migliore accuratezza LLM su testo legale)
- Tecnica: structured output JSON con schema fisso (7 categorie)
- Negative prompting: il modello non deve formulare giudizi "firma / non firmare"
- Il modello deve segnalare esplicitamente quando una clausola è assente

### Schema output Claude (JSON)
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
    "auto_renewal": { ... },
    "penalties": { ... },
    "liability_limitation": { ... },
    "termination": { ... },
    "governing_law": { ... },
    "intellectual_property": { ... }
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
- Il testo del contratto viene inviato a Claude API (Anthropic): da verificare che il piano API non usi i dati per training (Anthropic garantisce no-training su API calls)

### AI Act
- Classificazione: sistema potenzialmente **alto rischio** (analisi documenti legali)
- Mitigazione: posizionamento come *decision-support tool*, non consulenza legale
- Disclaimer obbligatorio in ogni output
- Human-in-the-loop: l'utente legge il report e decide autonomamente
- Nessuna decisione automatizzata che impatta diritti o obbligazioni

### Disclaimer (testo)
> SpecterAI è uno strumento di supporto alla lettura dei documenti. Le informazioni fornite non costituiscono consulenza legale. Prima di firmare qualsiasi contratto, consulta un professionista qualificato.

---

## 8. Validazione e Quality Control

### Metriche di qualità MVP
- **Copertura categorie**: 100% delle 7 categorie presenti nell'output (anche se "assente")
- **Latenza**: risposta entro 30 secondi per contratti fino a 10 pagine
- **Accuratezza estrattiva**: date e importi estratti dal layer deterministico devono corrispondere al testo originale
- **Disclaimer visibilità**: presente senza scroll nella pagina di output

### Test pianificati
- Test con 5 contratti reali di tipo diverso (servizi, locazione, fornitura, NDA, collaborazione)
- Test con contratto in inglese → verifica output in italiano
- Test con PDF corrotto → verifica messaggio di errore
- Test con contratto senza clausola IP → verifica segnalazione "clausola assente"

### Validazione output AI
- Il JSON restituito da Claude deve matchare lo schema atteso (validazione con Pydantic)
- Se il JSON è malformato: retry una volta, poi fallback con messaggio di errore
- Il `risk_level` deve essere uno tra: `low`, `medium`, `high` (nessun valore libero)

---

## 9. Gestione Errori e Fallback

| Errore | Comportamento |
|---|---|
| PDF non leggibile / corrotto | Messaggio: "Il file non è leggibile. Assicurati che sia un PDF digitale, non una scansione." |
| PDF troppo grande (>10MB) | Messaggio: "Il file supera i 10MB. Carica un documento più leggero." |
| Testo estratto < 100 caratteri | Messaggio: "Il documento sembra vuoto o è una scansione. SpecterAI supporta solo PDF digitali." |
| Claude API timeout | Retry automatico (1 volta), poi messaggio: "Analisi temporaneamente non disponibile. Riprova tra qualche minuto." |
| JSON malformato da Claude | Retry automatico (1 volta) con prompt più restrittivo, poi errore generico |
| Errore generico server | Messaggio: "Qualcosa è andato storto. Nessun tuo dato è stato salvato." |

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

---

## 11. Rischi, Assunzioni e Checklist Pre-Build

### Assunzioni dichiarate
- Il freelance medio gestisce 10-20 contratti/anno (nessun dato pubblico disponibile — assunzione conservativa)
- I contratti target sono PDF digitali standard (non scansioni, non PDF con campi compilabili complessi)
- Anthropic non usa le chiamate API per training (verificare ToS prima del deploy pubblico)

### Rischi identificati

| Rischio | Probabilità | Impatto | Mitigazione |
|---|---|---|---|
| PDF parsing fallisce su layout non standard | Media | Alto | Layer deterministico + messaggio errore chiaro |
| Claude allucina clausole inesistenti | Bassa | Alto | Pydantic validation + excerpt testuale obbligatorio nel JSON |
| AI Act: classificazione alto rischio | Presente | Medio | Disclaimer + posizionamento decision-support |
| Dipendenza da Claude API (costi/disponibilità) | Bassa | Medio | Architettura provider-agnostic, switch facile |
| Scope creep durante sviluppo | Alta | Medio | Specifica congelata — nessuna feature non listata in scope |

### Checklist pre-build
- [x] Obiettivo principale definito in una frase
- [x] Perimetro: cosa fa / cosa non fa
- [x] Utenti target identificati
- [x] Input formalmente descritti (PDF digitale, max 10MB, IT/EN)
- [x] Output formalmente descritto (JSON → HTML, 7 categorie, disclaimer)
- [x] Metriche di qualità quantitative definite (latenza, copertura, accuratezza)
- [x] Architettura a componenti schematizzata
- [x] Dipendenze esterne elencate
- [x] Rischi identificati con strategia di mitigazione
- [x] Assunzioni esplicitate
- [x] Vincoli GDPR e AI Act inseriti

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Brainstorming - Validazione Idea]]
- [[Contract Analyzer - Validazione Idea]]
- [[Lezione 2 - Specifica Tecnica e Prompt Engineering]]
- [[Template - Specifica Tecnica]]
