---
docente: Lorenzo
fonte_notebooklm: https://notebooklm.google.com/notebook/09a0fe85-b23d-4cb1-830e-ce83bfe690ff
n_fonti: 9
tags: [its, imprenditoria, ai, agentic-patterns, gpt-5, mcp, a2a, proptech, healthcare, retail]
---

# Ecosistema AI Moderno — Panoramica

Sintesi delle 9 fonti del NotebookLM di Lorenzo: design di sistemi agentici, prompting GPT-5, impatto AI sul lavoro, applicazioni verticali e protocolli emergenti. Estratta seguendo il [[NotebookLM Query Playbook]] (4 round: Discovery → Deep Dive → Esempi → Contraddizioni).

---

## Mappa delle 9 Fonti

| # | Titolo | Tipo | Argomento |
|---|--------|------|-----------|
| 1 | Agentic Design Patterns (Antonio Gulli) | PDF (424 pp.) | 21 pattern di design per agenti autonomi |
| 2 | Come creare un GPT personalizzato | Slide PDF | Teoria + caso automotive |
| 3 | GPT-5 Prompting Guide | Articolo MD | Ottimizzazione prompting GPT-5 |
| 4 | Google Just Explained How to Use AI | Video YouTube | Tecniche prompting da corsi Google |
| 5 | Impatto dell'AI sul mercato del lavoro | Slide PDF | Analisi Anthropic 2026 sul lavoro |
| 6 | Labor market impacts of AI: Anthropic | Articolo web | Studio scientifico esposizione AI |
| 7 | Nowcasting_Econ-Report-v16.pdf | Report PDF | Versione documentale del report Anthropic |
| 8 | Smart_Living_PropTech.pdf | Pitch deck | Ecosistema PropTech Smart Living |
| 9 | Testo incollato | MD/JSON | Repository system prompt GPT-5 |

---

## 1. Framework e Modelli

### Ciclo di Problem-Solving Agentico (5 fasi)
1. **Ricezione Missione**
2. **Analisi della Scena**
3. **Ragionamento / Pianificazione**
4. **Azione**
5. **Apprendimento**

### Livelli di Complessità Agentica (Gulli)

| Livello | Descrizione |
|---------|-------------|
| **0** | LLM Core — solo modello linguistico |
| **1** | Connesso — uso di tool esterni |
| **2** | Strategico — pianificazione |
| **3** | Collaborativo — multi-agente |

### 21 Agentic Design Patterns (Gulli)
Toolkit completo: Prompt Chaining, Routing, Parallelization, Reflection, Tool Use, Planning, Multi-Agent, ReAct, RAG, MCP, A2A, ecc.

### Smart Living Score (PropTech)
3 pilastri: **Analisi Elio-Termica** · **Analisi Acustica** · **Analisi Ambientale** (PM10/PM2.5). Scala 0-100.

### Observed Exposure (Anthropic)
Metrica che incrocia fattibilità teorica del task e uso reale osservato (Anthropic Economic Index ↔ database O*NET). Pesi: **automation = 1.0**, **augmentation = 0.5**.

### Motore Lead Generation AI (PropTech, 4 fasi)
1. Attrazione (Hook / anteprima 3D)
2. Cattura (Gating insight critici)
3. Qualificazione (Match AI profilo psicografico)
4. Monetizzazione (Routing al CRM)

### Modello Contractor (4 pilastri)
1. Contratto formalizzato
2. Negoziazione/feedback
3. Esecuzione iterativa
4. Decomposizione in sub-contratti

Per task critici ad alto rischio che richiedono determinismo.

---

## 2. Tecniche Operative

| Tecnica | Logica | Output atteso |
|---------|--------|---------------|
| **Chain-of-Thought (CoT)** | "Think step by step" — scomposizione esplicita | Accuratezza in task logico-matematici |
| **Tree-of-Thought (ToT)** | Branching con backtrack su più percorsi | Soluzione ottimale tra alternative |
| **Prompt Chaining** | Output di A = input di B | Flussi modulari affidabili |
| **Routing** | Classificazione intento → percorso specialistico | Esecuzione dinamica context-aware |
| **ReAct** | Pensiero → Azione (tool) → Osservazione → loop | Agenti che interagiscono con ambiente |
| **Reflection** | Producer + Critic con feedback loop | Output raffinato iterativamente |
| **Context Engineering** | Prompt sistema + RAG + dati impliciti + cronologia | Risposte radicate e personalizzate |
| **Meta Prompting** | Usare AI per scrivere/migliorare prompt | Prompt ottimizzati |

### Parametri GPT-5 (nuovi)
- `reasoning_effort` — intensità del ragionamento prima dell'azione
- `verbosity` — lunghezza della risposta (separato dal tempo di riflessione)
- **Tool Preambles** — l'agente spiega cosa fa con i tool e perché
- **Agentic Eagerness** — bilanciamento tra autonomia decisionale e attesa istruzioni

---

## 3. Definizioni Tecniche

- **Agente IA** — sistema che percepisce l'ambiente e agisce per raggiungere obiettivi (pianificazione + tool use).
- **Tool / Function Calling** — meccanismo per cui l'LLM decide quando invocare funzioni esterne (API, DB, codice).
- **RAG (Retrieval-Augmented Generation)** — accesso a basi di conoscenza esterne prima della generazione, ancora la risposta a fatti verificabili.
- **Agentic** — sistemi che agiscono autonomamente in cicli di analisi-pianificazione-esecuzione.
- **MCP (Model Context Protocol)** — standard aperto, "adattatore universale" LLM ↔ sistemi esterni senza integrazioni custom.
- **A2A (Agent-to-Agent)** — protocollo aperto per comunicazione tra agenti di framework diversi (es. LangGraph ↔ CrewAI).
- **Observed Exposure** — fattibilità teorica AI × uso reale professionale.
- **Augmentation** — AI supporta l'uomo (peso 0.5).
- **Automation** — AI esegue interamente il task (peso 1.0).

---

## 4. Checklist e Regole d'Oro

### Per GPT Personalizzati
- [ ] Obiettivo chiaro (cosa fa **e cosa NON fa**)
- [ ] Target utente identificato (calibra il tono)
- [ ] Istruzioni operative non ambigue
- [ ] Formato output fisso (tabelle, elenchi)
- [ ] Guardrail espliciti
- [ ] File di knowledge aggiornati

### Criteri SMART per obiettivi agente
Specifici · Misurabili · Raggiungibili · Rilevanti · Temporalmente definiti.

### Rubrica LLM-as-a-Judge
Chiarezza · Neutralità/bias · Rilevanza · Completezza · Appropriatezza per il pubblico.

### Standard Coding GPT-5
- Modularità e riuso
- Coerenza design system (color tokens, padding multipli di 4)
- Gerarchia tipografica limitata a 4-5 font sizes
- **Primacy of Context** — performance ∝ qualità del briefing; evitare black-box context retrieval.

---

## 5. Anti-Pattern

| Da evitare | Conseguenza |
|------------|-------------|
| Prompt generici | L'agente perde specializzazione, diventa chatbot standard |
| Mancanza di perimetro | Allucinazioni su domande fuori competenza |
| Aspettarsi che "indovini" | Se i dati mancano, deve dichiararlo, non inventare |
| Prompt monolitici | Instruction neglect, deriva contestuale |
| Istruzioni contraddittorie | GPT-5 spreca token cercando di conciliare |
| Over-searching | Inefficienza quando la conoscenza interna è sufficiente |
| Intervento umano totale | Collo di bottiglia, perdita scalabilità |
| Least Privilege Violation | Permessi eccessivi → raggio errore/attacco maggiore |

---

## 6. Casi Pratici e Template

### Automotive Rental Competitor GPT
> "Assistente specializzato nell'analisi della concorrenza nel settore automotive con focus sul noleggio auto. Supporta benchmark tra competitor, confronto offerte, analisi pricing, servizi inclusi, posizionamento di brand, funnel digitale e insight strategici."

**Guardrail:** "Distingue fatti, inferenze e ipotesi usando etichette chiare. Non inventa dati: se un'informazione manca, lo dichiara. Segnala confronti non omogenei. Non fornisce consulenza legale."

**Conversation Starters:**
- Confronta i principali competitor del noleggio lungo termine in Italia
- Analizza il posizionamento di Arval, Ayvens e Leasys nel segmento business
- Crea un benchmark delle offerte di noleggio per SUV compatti

**Mockup:** Arval € 389/mese, anticipo € 2.500 (NLT 36 mesi, 30.000 km).

### Smart Living (PropTech)
**Empatia dei dati:** dato grezzo `35 dB` → consiglio: *"Le camere sul retro garantiscono standard acustici eccellenti. Potrai dormire con le finestre aperte senza essere disturbato."*

**Modello B2B2C:** SaaS per agenzie immobiliari + API per portali.

### CareFlow Assistant (Healthcare)
> "You are CareFlow Assistant, a virtual admin for a healthcare startup... Triage requests, match patients to providers... Map symptoms to priority: Red (2h), Orange (24h), Yellow (3d), Green (7d). Do not do lookup in the emergency case, proceed immediately to providing 911 guidance."

### Taubench-Retail
> "As a retail agent, you can help users cancel/modify orders... Authenticate user identity via email or name + zip code... Obtain explicit user confirmation (yes) before updating database... Transfer to human if request is out of scope."

### Template Coding GPT-5

**`<context_gathering>`**
> "Goal: Get enough context fast. Parallelize discovery and stop as soon as you can act. Method: Start broad, fan out to subqueries... Avoid over searching... Early stop if you can name exact content to change."

**`<persistence>`**
> "You are an agent — please keep going until the user's query is completely resolved... Never stop or hand back when you encounter uncertainty — research or deduce the most reasonable approach."

**`<code_editing_rules>`**
> "Clarity and Reuse: Every component should be modular. Consistency: Adhere to design system (color tokens, spacing). Visual Hierarchy: Limit typography to 4–5 font sizes. Spacing: Multiples of 4 for padding/margins."

**`<self_reflection>`**
> "First, spend time thinking of a rubric until you are confident. Then, think deeply about what makes for a world-class one-shot web app... Iterate until response hits top marks."

**`<tool_preambles>`**
> "Always begin by rephrasing the user's goal... mark progress clearly... Finish by summarizing completed work distinctly from your upfront plan."

### Ricerca Scientifica

**Google Co-Scientist** — sistema multi-agente che ha identificato target epigenetici per la fibrosi epatica (validati in lab). Agenti: *Generation* (ipotesi), *Reflection* (peer review), *Ranking* (Elo tournament), *Evolution* (raffinamento).

**OpenAI Deep Research API:**
> "You are a professional researcher preparing a structured, data-driven report. Focus on data-rich insights, use reliable sources, and include inline citations."

### Project Manager AI (LangChain)
> "You are a focused Project Manager LLM agent... 1. Create task using create_new_task tool to get task_id. 2. Analyze priority ('urgent' = P0). 3. If missing, default to P1 and 'Worker A'."

---

## 7. Dati Numerici Chiave

| Dato | Valore | Contesto |
|------|--------|----------|
| Pagine guida Gulli | 424 | Agentic Design Patterns |
| Pattern agentici | 21 | Catalogati da Gulli |
| Compravendite immobiliari Italia 2025 | 767.000 (+6,6%) | Mercato Smart Living |
| Esposizione "Computer & Math" | 29% reale vs 85% potenziale | Anthropic 2026 |
| Computer Programmers — copertura osservata | 74,5% | Massima esposizione |
| Job-finding rate giovani 22-25 ruoli esposti | -14% vs 2022 | Segnale entry-level |
| GPQA "diamond set" — Co-Scientist | 78,4% accuratezza | Benchmark scientifico |
| Smart Living Score | scala 0-100 | Indice predittivo immobili |

---

## 8. Contraddizioni e Note Critiche

- **Livelli di complessità AI** — Gulli definisce 4 livelli rigorosi (0-3); le slide GPT personalizzati e Google YT sono più granulari, senza piramide formale.
- **Definizione di "tool"** — Gulli: interfaccia esterna definita via codice. GPT-5: estesa a "budget di chiamate" e "preamboli" obbligatori.
- **Pianificazione** — Gulli la tratta come pattern dedicato (cap. 6); GPT-5 la considera intrinseca, attivabile via `reasoning_effort`.
- **Esposizione lavoro** — le slide semplificano; il Nowcasting Report distingue tra metrica β teorica di Eloundou et al. e nuova misura basata su uso reale.

---

## 9. Riferimenti Esterni Citati

**Istituzioni & ricerca:** Anthropic · BLS (Bureau of Labor Statistics) · O*NET · Google Research (Project Astra, Co-Scientist).

**Paper & libri:** Eloundou et al. (2023) *GPTs are GPTs* · Sutton & Barto (2018) *Reinforcement Learning* · Gulli et al. *Agent Companion*.

**Framework & software:** LangChain · LangGraph · CrewAI · Vertex AI · Cursor · OpenRouter · Aider · Google ADK.

**Dataset:** HotpotQA · DROP · GPQA.

**Autori citati:** Antonio Gulli · Marco Argenti (CIO Goldman Sachs) · Patti Maes (MIT, pioniera agenti software) · Maxim Massenkoff & Peter McCrory (Anthropic).

---

## Connessioni

- [[Imprenditoria MOC]]
- [[ITS MOC]]
- [[Agenti IA Design Patterns MOC]] — pattern e implementazione tecnica
- [[Progettistica AI MOC]] — specifiche di progetto AI
- [[Prompting MOC]] — tecniche CoT/ToT/Context Engineering
- [[NotebookLM Query Playbook]] — metodologia di estrazione usata
