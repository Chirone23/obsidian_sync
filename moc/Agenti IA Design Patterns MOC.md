# Agenti IA — Design Patterns MOC

Mappa dei 21 design pattern per costruire sistemi agentici autonomi e intelligenti. Framework, ottimizzazione prompting, ricerca di impatto.

**Fonte Principale:** NotebookLM https://notebooklm.google.com/notebook/09a0fe85-b23d-4cb1-830e-ce83bfe690ff

### Fonti Originali (5)

1. **"Agentic_Design_Patterns.pdf"** — Antonio Gulli (424 pagine)
   - 21 pattern di progettazione per agenti autonomi
   - Guida tecnica hands-on

2. **"GPT-5 Prompting Guide"** (URL) — OpenAI, 7 agosto 2025
   - Ottimizzazione prestazioni GPT-5
   - Responses API, metaprompting

3. **"GPT-5 Prompting Guide"** (Markdown) — OpenAI
   - Versione Markdown della guida
   - Context engineering, reasoning_effort

4. **"Labor Market Impacts of AI"** (URL) — Anthropic, 5 marzo 2026
   - Ricerca economica impatto IA
   - Maxim Massenkoff, Peter McCrory

5. **"Nowcasting_Econ-Report-v16.pdf"** — Anthropic
   - Metrica "observed exposure"
   - Analisi impatto mercato lavoro

---

## 21 Design Pattern per Agenti IA

Organizzati in 4 categorie fondamentali che descrivono come trasformare LLM in sistemi autonomi capaci di pianificare, agire e collaborare.

### 1. Esecuzione Core e Decomposizione dei Compiti (Pattern 1-7)

| # | Pattern | Descrizione | Use Case |
|---|---------|-------------|----------|
| **1** | **Prompt Chaining** | Suddivide compito complesso in sequenza lineare di step (output uno = input successivo) | Generazione report: riassunto → estrazione dati → bozza email |
| **2** | **Routing** | Logica condizionale per dirigere flusso verso diversi strumenti/sub-agenti | Customer support: smistamento query tecniche vs commerciali |
| **3** | **Parallelization** | Esecuzione simultanea di compiti indipendenti per ridurre latenza | Ricerca: notizie + dati borsa + social parallelamente |
| **4** | **Reflection** | Valutazione e correzione del proprio lavoro via feedback loop (Produttore-Critico) | Debugging codice: scrivi → testa → analizza errori → correggi |
| **5** | **Tool Use** | Interazione con API, database, software esterni via Function Calling | Assistente finanziario: calcolo profitti + recupero prezzi real-time |
| **6** | **Planning** | Formulazione autonoma sequenza azioni per obiettivo complesso non noto a priori | Organizzazione viaggi: capire budget → cercare voli → prenotare hotel |
| **7** | **Multi-Agent** | Squadra agenti specializzati collabora su obiettivo comune via divisione lavoro | Team sviluppo: analista + programmatore + tester + documentatore |

### 2. Contesto e Apprendimento (Pattern 8-11)

| # | Pattern | Descrizione | Use Case |
|---|---------|-------------|----------|
| **8** | **Memory Management** | Memoria breve termine (context chat) + lunga termine (database vettoriali) per coerenza | Personalizzazione ricordando preferenze passate |
| **9** | **Learning and Adaptation** | Miglioramento strategie tramite feedback o auto-modifica del codice | Robotica/trading che adattano parametri a condizioni mutevoli |
| **10** | **Model Context Protocol (MCP)** | Standard aperto per connettere LLM a risorse e strumenti esterni universalmente | Integrazione database aziendali o dispositivi IoT |
| **11** | **Goal Setting and Monitoring** | Definisce obiettivi SMART e monitora progressi per evitare derailing agente | Automazione workflow aziendali con self-assessment |

### 3. Conoscenza e Robustezza (Pattern 12-14)

| # | Pattern | Descrizione | Use Case |
|---|---------|-------------|----------|
| **12** | **Exception Handling and Recovery** | Gestisce errori strumenti/API via retry, fallback, escalation umana | Bot scraping che gestisce CAPTCHA e cambi strutturali siti |
| **13** | **Human-in-the-Loop (HITL)** | Integra intervento umano per decisioni critiche, revisioni, feedback formativi | Approvazione prestiti bancari o moderazione contenuti sensibili |
| **14** | **Knowledge Retrieval (RAG)** | Consulta fonti esterne pre-risposta per evitare allucinazioni | Chatbot aziendali su manuali interni solo |

### 4. Comunicazione e Ragionamento Avanzato (Pattern 15-21)

| # | Pattern | Descrizione | Use Case |
|---|---------|-------------|----------|
| **15** | **Inter-Agent Communication (A2A)** | Protocollo standard agenti su framework diversi collaborino | Orchestrazione workflow cross-piattaforma (Google ADK + CrewAI) |
| **16** | **Resource-Aware Optimization** | Agente sceglie modelli/percorsi basato su budget costo/tempo/energia | Usare modelli piccoli per query semplici, "Pro" per complessi |
| **17** | **Reasoning Techniques** | Chain-of-Thought / Tree-of-Thought per ragionamento esplicito agente | Risoluzione problemi matematici, analisi legali profonde |
| **18** | **Guardrails/Safety Patterns** | Filtri input/output e vincoli comportamentali per risposte sicure etiche | Prevenire jailbreak e linguaggi offensivi |
| **19** | **Evaluation and Monitoring** | Sistemi continui misurare accuratezza/latenza/helpfulness via LLM-as-a-Judge | A/B testing versioni agente in produzione |
| **20** | **Prioritization** | Ordinare compiti per urgenza, importanza, dipendenze | Gestione ticket: guasti critici prima di reset password |
| **21** | **Exploration and Discovery** | Ricerca attiva informazioni per identificare "unknown unknowns" | Automazione ricerca scientifica (scoperta farmaci, ipotesi) |

---

## Framework e Implementazione

### Framework Principali

| Framework | Quando usare | Specialità |
|-----------|-------------|-----------|
| **LangChain** | Sequenze lineari e DAG semplici | Catene di azioni, composizione step lineari |
| **LangGraph** | Agenti che ragionano in loop o riflettono | Grafi ciclici, gestione stato, reflection patterns |
| **CrewAI** | Team agenti con ruoli e personalità | Collaborazione gerarchica/sequenziale, persona e backstory |
| **Google Agent Developer Kit (ADK)** | Produzione con Google Cloud | Pattern pre-costruiti (Sequential/Parallel), infrastruttura Google, protocolli A2A |

### Memoria e Orchestrazione

**Memoria:**
- **Short-Term:** Context window dell'LLM (sessione corrente)
- **Long-Term:** Database vettoriali o Memory Bank persistenti
- **In Google ADK:** Session (stato singola chat) + MemoryService (repository ricercabile conversazioni)

**Orchestrazione:**
- **Gerarchica:** Supervisore delega compiti
- **Rete:** Comunicazione peer-to-peer tra agenti
- **Agent as Tool:** Un agente invoca altro agente come funzione

---

## GPT-5: Prompting Avanzato per Agenti

### Responses API e Persistenza Ragionamento

Sostituire Chat Completions classiche con **Responses API**:
- Persiste Chain-of-Thought tra una tool call e l'altra
- Conserva token ragionamento, evita ricostruire piano da zero
- **Guadagno accuratezza:** +4,3% (da 73,9% a 78,2% in test Tau-Bench Retail)

### Metaprompting

GPT-5 come **meta-prompter di se stesso:**
- Fornisci prompt che ha fallito + chiedi cosa aggiungere/rimuovere
- Modello suggerisce miglioramenti automatici
- Evita riscrittura manuale iterativa

### Context Engineering Avanzato

Spostamento dal semplice prompt engineering all'ingegneria del **contesto dinamico:**
- System instructions (direttive base)
- Documenti recuperati (RAG)
- Output strumenti precedenti
- Dati impliciti dell'utente
- Costruire ambiente informativo completo prima della query

### Controllo Eagerness (Proattività)

Calibrare quanto agente è esplorativo prima di rispondere:
- Parametro `reasoning_effort`
- Tag `<context_gathering>`, `<persistence>`
- Bilanciare proattività vs risposta diretta

---

## Ricerca Anthropic: "Observed Exposure" al Mercato Lavoro

### Metrica di Esposizione Osservata

A differenza di misure puramente teoriche, combina:
- Capacità tecniche LLM
- Dati utilizzo reale in contesti professionali
- Peso maggiore a compiti **completamente automatizzati** (API) vs assistivi

### Occupazioni Più Esposte

| Occupazione                   | Esposizione Osservata | Impatto          |
| ----------------------------- | --------------------- | ---------------- |
| **Programmatori informatici** | 75%                   | Più esposti      |
| **Addetti customer service**  | 70,1%                 | Alta esposizione |
| **Addetti inserimento dati**  | 67,1%                 | Alta esposizione |
| Analisti finanziari           | Elevata               | Medio-alta       |
| Specialisti cartelle cliniche | Elevata               | Medio-alta       |

### Metriche di Impatto Economico

- **Riduzione crescita occupazionale:** -0,6% per ogni incremento del 10% di esposizione (proiezione decennale)
- **Disoccupazione totale:** Nessun aumento sistematico ancora rilevato
- **Primo segnale:** Rallentamento assunzioni giovani lavoratori (22-25 anni) nelle professioni più esposte

---

## Casi Avanzati: Google Co-Scientist e SICA

### Google Co-Scientist

Sistema multi-agente per generazione e validazione ipotesi scientifiche:

**Architettura:**
- **Agente Generazione:** Esplora letteratura, propone idee nuove
- **Agente Riflessione:** Peer reviewer, critica ipotesi
- **Agente Ranking:** Tornei Elo per prioritizzare idee migliori
- **Agente Evoluzione:** Raffina continuamente ipotesi migliori

**Successi Pratici:**
- KIRA6: Nuovo candidato farmaco per leucemia mieloide acuta
- Target epigenetici: Scoperta nuovi target per fibrosi epatica

### SICA (Self-Improving Coding Agent)

Agente che **auto-modifica il proprio codice sorgente** per migliorare capacità:

**Cicli di Miglioramento:**
1. Analizza versioni passate di se stesso
2. Valuta benchmark relativi
3. Seleziona versione più performante
4. Altera direttamente base di codice

**Sviluppi Autonomi:**
- Smart Editor (tool personalizzato)
- Localizzatori simboli basati su AST (Abstract Syntax Trees)

**Sicurezza:** Esecuzione in container Docker per isolare host da shell command che agente potrebbe generare

---

## Connessioni

- [[Imprenditoria MOC]] — Compliance e regolamentazione AI (EU AI Act, FRIA, DDL settori)
- [[Progettistica AI MOC]] — Specifiche progettuale sistemi AI
- [[Skill MOC]] — n8n Avanzato, Agent Builder, orchestrazione
- [[Knowledge MOC]] — AI e automazione
- [[Index MOC]]

---

## Strumenti di Valutazione Reale

### Petri — AI Alignment Testing

**Nota completa:** [[../knowledge/Petri - AI Alignment Testing|Petri - AI Alignment Testing]]
**Sviluppato da:** Anthropic → donato a **Meridian Labs** (no-profit) + UK AISI Red Team
**Fonti:** https://www.anthropic.com/research/donating-open-source-petri · https://meridianlabs-ai.github.io/inspect_petri/

Implementazione pratica dei Pattern 18 (Guardrails) + 19 (Evaluation and Monitoring): sistema automatizzato di red-teaming per LLM con 3 ruoli (Auditor / Target / Judge), 170+ scenari (Seeds), 38 dimensioni comportamentali misurate dal Judge.

**Caratteristica chiave:** il componente "Dish" usa prompt e infrastrutture reali per impedire al modello di riconoscere di essere testato (`eval_awareness` è una delle dimensioni misurate — se alta, il test è compromesso).

```bash
pip install inspect-petri
inspect eval inspect_petri/audit \
  -T seed_instructions=tags:sycophancy \
  --model-role auditor=anthropic/claude-sonnet-4-6 \
  --model-role target=openai/gpt-5-mini \
  --model-role judge=anthropic/claude-opus-4-6
```
