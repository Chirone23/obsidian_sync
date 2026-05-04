# Lezione 1 - Case Study e Setup

**Corso:** AI Projects Development · [[Progettistica AI MOC]]
**Tema:** Presentazione del corso, filosofia didattica, case study reale, strumenti e setup

---

## Il Corso — Visione d'Insieme

**Obiettivo:** formare professionisti capaci di progettare, sviluppare e validare sistemi AI lungo l'intero ciclo di vita — dall'idea alla produzione.

**Struttura:** 5 lezioni con progressione cumulativa. Ogni lezione si fonda sulla precedente.

**Metodo:** teoria + caso di studio reale (sistema multi-agente LinkedIn) + progetto personale (MVP).

### Perché esiste questo corso

La maggior parte dei corsi AI si concentra su due estremi: matematica dei modelli (deep learning, algebra lineare) oppure utilizzo superficiale di tool consumer. Manca la **zona centrale del progetto applicato**: come si costruisce un sistema AI funzionante, robusto, validato, sicuro ed economicamente sostenibile?

- Corsi ML tradizionali trascurano il ciclo di vita del prodotto
- Contenuti online si limitano all'uso degli strumenti, senza progettazione e validazione
- Bootcamp tecnici saltano la fase critica di specifica e governance
- Percorsi esistenti ignorano vincoli normativi (**GDPR**, **AI Act**) e **AI Sustainability**

---

## Le 5 Aree del Corso

| # | Area | Equivalente professionale |
|---|---|---|
| 1 | Valutazione dell'idea | Product Discovery & Feasibility |
| 2 | Specifica tecnica | Technical Design Document (TDD) |
| 3 | AI come co-sviluppatore | AI Orchestration & Engineering |
| 4 | Validazione e quality control | Quality Assurance (QA) & Evaluation |
| 5 | Deploy, monitoring e manutenzione | Deployment & Lifecycle Management |

---

## Filosofia Didattica

> L'intelligenza artificiale è uno strumento potente, non un sostituto del pensiero. Il giudizio critico trasforma le risposte AI in decisioni efficaci.

### Principi fondanti
- **L'errore come risorsa didattica** — esposizione deliberata a scenari di errore per sviluppare capacità diagnostica autentica
- **La complessità reale come contesto** — un progetto reale impone vincoli, ambiguità e compromessi che nessun esercizio simulato replica
- **Il pensiero critico come competenza trasversale** — ogni decisione tecnica va analizzata nei presupposti, nelle alternative e nelle conseguenze
- **La specifica prima del codice** — nessun sistema funziona bene se non è stato prima definito con chiarezza
- **L'indipendenza dagli strumenti** — progettare sistemi che non dipendono da un singolo tool o provider

### I 3 Pilastri Metodologici

**Pilastro I — Specifica prima del codice**
La qualità del progetto finale è determinata in larga misura dalla qualità della definizione iniziale: obiettivi, vincoli, metriche di successo e criteri di accettazione. La specifica tecnica è un documento professionale vincolante, non una formalità burocratica.

**Pilastro II — Iterazione come metodo**
Lo sviluppo con AI non è lineare. È un processo di raffinamento continuo in cui prompt, architetture e validatori evolvono insieme. Ogni ciclo produce un apprendimento documentato: cosa ha funzionato, perché, e come ottimizzare il passo successivo.

**Pilastro III — Supervisione umana critica**
L'AI può generare codice, testi, architetture e soluzioni, ma non può valutare autonomamente la propria adeguatezza al contesto. La supervisione non è un controllo passivo, ma una partecipazione attiva al processo di sviluppo.

---

## Obiettivi Formativi

1. **Analisi preliminare del progetto** — valutazione strategica (opportunità, originalità, scalabilità) + analisi di fattibilità (tecnica, economica, normativa)
2. **Redigere la specifica tecnica** — documento completo con requisiti funzionali, tecnici e UI/UX, riferimento vincolante per tutto lo sviluppo
3. **Collaborare con strumenti AI** — usare AI come co-sviluppatori in processo strutturato, mantenendo supervisione umana critica e costante
4. **Validazione come pratica costante** — quality control integrato in ogni fase, per rilevare errori, allucinazioni, derive di qualità, non conformità
5. **Deploy e ciclo di vita** — portare un sistema AI dalla prototipazione alla produzione con strategie di monitoraggio e manutenzione

---

## Competenze e Profilo Professionale

| Area | Competenze |
|---|---|
| **Progettazione** | Architetture AI adeguate al contesto · Specifiche tecniche professionali · Vincoli tecnici ed economici reali |
| **Tecniche** | Prompt engineering iterativo e avanzato · Collaborazione con LLM e AI builder · Integrazione API in pipeline operative |
| **Validazione** | Sistemi di quality control · Identificazione allucinazioni, bias, non conformità · Test sistematico e documentazione |
| **Critiche e strategiche** | Valutazione critica degli output AI · Supervisione umana strutturata · Comunicazione tecnica verso stakeholder |

---

## Il Case Study Reale

**Sistema:** LinkedIn Social Agents — multi-agente AI operativo in agenzia

**Funzionalità:**
- Generazione automatizzata di contenuti e immagini su LinkedIn
- Workflow di approvazione via Slack
- Pubblicazione diretta via API
- Dashboard di gestione per Social Media Manager

**Stack tecnologico:**
`Python` · `FastAPI` · `Claude API` · `Claude Agents SDK` · `SQLAlchemy` · `SQLite` · `httpx` · `Slack` · `Playwright` · `Docker` · `Cursor`

**API esterne:** LinkedIn API · Higgsfield API · Fal.ai · Tavily · Sentry

**Perché è fondamentale come riferimento:**
- Credibilità — decisioni con conseguenze vere, non scenari ipotetici
- Trasparenza — errori, revisioni, costi e compromessi discussi apertamente
- Vincoli realistici — budget limitato, compromessi architetturali reali
- Metodo replicabile — il percorso è applicabile a qualsiasi progetto AI

**Cosa viene mostrato del case study:**
- Architettura del sistema e stack tecnologico motivato
- Prompt e storia delle iterazioni (impatto sui costi operativi)
- Errori incontrati e come sono stati diagnosticati e risolti
- Budget reale, costi di inference, strategie di ottimizzazione

---

## Framework di Valutazione dell'Idea

Prima di costruire qualsiasi sistema AI, rispondere onestamente su 5 dimensioni:

| Dimensione | Domande chiave | Errori comuni |
|---|---|---|
| **Tecnica** | Il problema è ben definito? L'AI lo risolve meglio delle alternative? | Sovrastimare le capacità AI |
| **Economica** | I costi di sviluppo + operativi sono sostenibili? Il valore li giustifica? | Ignorare i costi operativi (API + infrastruttura + manutenzione) |
| **Complessità** | È gestibile con le risorse disponibili? L'impatto giustifica lo sforzo? | Definire male il problema |
| **Rischio e Compliance** | Dipendenze critiche? Rispetta GDPR/AI Act? | Sottovalutare dati e compliance |
| **Sostenibilità tecnologica** | Cosa succede se il provider cambia API o prezzi? | Dipendenza da singolo provider |

---

## Struttura della Specifica Tecnica

| # | Sezione | Contenuto |
|---|---|---|
| 1 | **Obiettivi e perimetro** | Cosa fa il sistema, cosa NON fa, utenti target, valore misurabile |
| 2 | **Input e output** | Definizione formale: tipo, formato, vincoli, casi limite → diventano i criteri di test |
| 3 | **Requisiti di qualità** | Accuratezza minima, latenza massima, tasso errore ammissibile. Senza metriche, la validazione è impossibile |
| 4 | **Architettura del sistema** | Componenti, flussi operativi, dipendenze esterne, interfacce |
| 5 | **Rischi e assunzioni** | Assunzioni esplicite + rischi tecnici/normativi + strategie di mitigazione |

---

## Strumenti e Tecnologie

| Categoria | Strumenti | Utilizzo |
|---|---|---|
| LLM e AI generativa | OpenAI, Claude, Gemini, Grok, Mistral, Meta Llama | Chiamate API programmatiche nel progetto |
| AI code editor | Cursor | Ambiente di sviluppo principale, Agents |
| Interfaccia AI | Claude, ChatGPT, Gemini, Grok | Brainstorming, specifica, validazione decisioni |
| Linguaggio | Python 3.12+ | Sviluppo del progetto |
| Web framework | FastAPI (consigliato), Flask | API, interfaccia web, rendering server-side |
| Database e validazione | SQLAlchemy, Pydantic | Modelli dati, validazione, query strutturate |
| Client HTTP | httpx | Chiamate API asincrone |
| Database | SQLite | Persistenza dei dati, gestione dello stato |
| Version control | Git, GitHub | Controllo versioni, backup, collaborazione |
| Deploy | Render, Railway, PythonAnywhere, Hetzner VPS | Esecuzione e distribuzione online (5-8 €/mese) |

---

## Setup Tecnico Lezione 1

- [ ] Installazione Cursor, Python 3.12+, Git
- [ ] Creazione repository GitHub
- [ ] Configurazione Sentry (error tracking)
- [ ] Richiesta accessi API: LinkedIn, Higgsfield, Fal.ai, Tavily

---

## Criteri di Valutazione

| Dimensione | Cosa si valuta | Peso |
|---|---|---|
| Progettazione e metodo | Specifica tecnica, scelte motivate, analisi vincoli | 30% |
| Documentazione di processo | PROMPT_LOG, INCIDENTS, tracciabilità decisioni, repo organizzato | 30% |
| Sviluppo e implementazione | MVP funzionante, validatori, interfaccia, approvazione umana | 20% |
| Presentazione e riflessione critica | Demo, analisi errori, consapevolezza limiti, comunicazione | 20% |

**Scala:** 90-100 Eccellente · 80-89 Ottimo · 70-79 Buono · 50-69 Sufficiente · 0-49 Non sufficiente

**Materiali da consegnare:**
- Repository GitHub con README e istruzioni di avvio
- Specifica tecnica (versione finale)
- PROMPT_LOG.md con iterazioni documentate
- INCIDENTS.md (min. 2 voci secondo la rubrica)
- Presentazione con demo del sistema funzionante

---

## Note Personali

*(spazio per appunti durante la lezione)*

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Lezione 2 - Specifica Tecnica e Prompt Engineering]]
- [[Template - Specifica Tecnica]]
- [[Template - PROMPT_LOG]]
- [[Template - INCIDENTS]]
