# Lezione 2 - Specifica Tecnica e Prompt Engineering

**Corso:** AI Projects Development · [[Progettistica AI MOC]]
**Tema:** Cos'è un progetto, GDPR/AI Act, MVP, prompt engineering, economia dei token, specifica tecnica

---

## Cos'è un Progetto e Come Nasce

Un progetto non è solo un'idea che prende forma. È un **percorso strutturato** che parte dall'identificazione di un problema reale, passa per decisioni documentate e arriva a una soluzione funzionante.

> Dal case study: il sistema LinkedIn Social Agents è nato da una conversazione, non da un piano. La maggior parte dei progetti reali nasce così.

### Il Ciclo di Vita del Progetto — 7 Fasi

```
01 Problem Discovery
02 Ricerca e Analisi
03 Studio di Fattibilità
04 Specifica Tecnica
05 Development
06 Validazione & Testing
07 Deploy & Monitoring
```

Un progetto non segue una linea retta ma un processo iterativo: si testa, si corregge, si migliora. Saltare una fase significa compromettere la qualità del risultato finale.

---

## GDPR · AI Act · AI Sustainability

Tre vincoli **non opzionali**. Vanno inseriti nella specifica tecnica prima di scrivere una riga di codice.

| Vincolo | Domande da porsi | Natura |
|---|---|---|
| **GDPR** | Dove vanno i dati degli utenti? Chi li processa? Per quanto tempo? | Requisito **architetturale**, non solo legale |
| **AI Act** | Qual è il livello di rischio del mio sistema? (pratiche vietate / alto rischio / trasparenza) | Requisito **normativo** europeo |
| **Green AI / AI Sustainability** | Ogni chiamata API consuma energia. Ottimizzare i token è una scelta di sostenibilità progettuale | Destinato a diventare sempre più centrale |

---

## Validare l'Idea: le 5 Dimensioni

Questa fase prepara la stesura della Specifica Tecnica. Serve a trasformare un'intuizione in decisioni progettuali chiare, verificabili e documentabili.

> Saltare questo passaggio è il motivo per cui molti progetti falliscono: non per mancanza di competenze tecniche, ma perché i sistemi sono fragili, poco definiti o economicamente insostenibili.

| Dimensione | Domande chiave |
|---|---|
| **Fattibilità Tecnica** | L'AI aggiunge valore reale rispetto alle alternative esistenti? Il problema è definito con chiarezza? |
| **Sostenibilità Economica** | Il valore generato giustifica i costi di sviluppo e inference (API)? Il modello economico è scalabile? |
| **Complessità** | Il problema è gestibile con il tempo e le risorse disponibili? L'impatto giustifica lo sforzo? |
| **Rischio e Compliance** | Dipendenze critiche? Rispetta GDPR/AI Act e principi etici? |
| **Sostenibilità Tecnologica** | Cosa succede se il provider cambia API o prezzi? Il sistema è abbastanza indipendente? |

### Pratica: Brainstorming con AI

**Step 1 — Analisi dell'Idea**
```
Voglio costruire un sistema AI che faccia [X] per risolvere [Y].
Analizza questa idea come un consulente esperto del settore.
```

**Step 2 — Criteri di Analisi**
Chiedere all'AI di analizzare sulle 5 dimensioni: fattibilità tecnica, sostenibilità economica, complessità, rischio e compliance, sostenibilità tecnologica.

**Step 3 — Vincoli di Progetto**
L'idea deve: essere realizzabile nel corso · mantenere i costi quasi a zero · risolvere un problema reale · essere originale (non derivata da altri corsi o soluzioni copiate)

---

## Idea → MVP → Prodotto

**Cos'è un MVP**
Il Minimum Viable Product è la versione minima del sistema, capace di funzionare e dimostrare il valore dell'idea. Non è un prototipo incompleto, ma un prodotto finito con scope ridotto all'essenziale.

**Dal Case Study**
Il sistema è partito con un singolo account, senza dashboard o processi di approvazione. Le funzionalità avanzate (immagini AI, integrazione Slack, ricerca automatizzata) sono state implementate solo dopo aver validato l'efficacia del nucleo centrale.

**L'obiettivo del Corso**
Realizzare un MVP validato e documentato. La padronanza del metodo progettuale ha più valore del prodotto stesso: significa costruire sistemi affidabili, documentare le scelte tecniche e acquisire competenze scalabili, trasferibili a ogni progetto futuro.

---

## 2026: Year of the Truth — Agentic Reality

| Tendenza | Cosa significa |
|---|---|
| **AI come infrastruttura** | Per Capgemini il 2026 è l'anno in cui l'AI diventa infrastruttura portante delle aziende |
| **Realtà Agentica** | Superiamo l'era dei chatbot passivi. Sistemi multi-agente capaci di pianificare, ragionare e agire autonomamente |
| **AI-Native Development** | Il software non viene più scritto con l'aiuto dell'AI, ma nasce all'interno di piattaforme AI-native |
| **Figure emergenti** | Chief AI Transformation Officer, Head of AI — il mercato cerca chi padroneggia metodo e governance, non solo programmazione |
| **Indipendenza dagli strumenti** | Lo standard di oggi sarà obsoleto domani. Progettare sistemi agnostici è una competenza fondamentale |

---

## I 3 Livelli di Competenza AI

L'interazione con l'AI si articola su tre livelli progressivi:

| Livello | Territorio | Descrizione |
|---|---|---|
| **1 — Recupero** | AI | Accedere a informazioni, sintetizzare, generare output. Delegare il recupero è una scelta efficiente |
| **2 — Giudizio** | Umano + AI | Formulare domande di qualità, filtrare output, capire di cosa fidarsi. Qui si crea la differenza tra utilizzo superficiale e competente |
| **3 — Decisione** | Umano | Valutare conseguenze reali, riconoscere segnali di rottura, mantenere la responsabilità. Richiede esperienza e contesto |

### Il Metodo Socratico (Maieutica)
Socrate non dava risposte: faceva domande. Con un LLM funziona in entrambe le direzioni:
- Chiedere il **ragionamento**, non la risposta
- Spingere l'AI a fare domande scomode
- Non cercare conferme, cercare **resistenza**

---

## Prompt Engineering #1

Un prompt efficace non è una domanda generica: è una **specifica operativa**. Definisce con chiarezza ruolo, compito, formato dell'output, vincoli e criteri di esclusione.

### Struttura Base
```
You are a [ROLE].
Your task is to [TASK].
Output format: [FORMAT].
Constraints: [CONSTRAINTS].
Do not: [EXCLUSIONS].
```

### Le 6 Regole Consigliate

| # | Regola | Perché |
|---|---|---|
| 1 | **Inglese** | Output spesso più precisi, meno token, maggiore coerenza con documentazione tecnica |
| 2 | **Struttura** | Usare esplicitamente la sequenza Ruolo + Task + Formato + Vincoli |
| 3 | **Negative Prompting** | Dire cosa NON fare è spesso più efficace che spiegare solo cosa fare. Riduce derive, ambiguità e allucinazioni |
| 4 | **Few-Shot** | Fornire 2-3 esempi reali è più efficace che descrivere lo schema a parole. Esempi pertinenti e diversificati (evitare effetto ancoraggio) |
| 5 | **File .md** | Usare file markdown strutturati invece di chat lunghe per mantenere coerenza e ridurre i costi |
| 6 | **Generatori gratuiti** | PROMPTCOWBOY.AI, GENERATEPROMPT.AI, AIPRM.COM — punto di partenza, non fidarsi al 100%, correggere sempre |

---

## Come Ragionano Davvero gli LLM

4 comportamenti strutturali che influenzano qualità, affidabilità e processo decisionale:

| Comportamento | Descrizione | Come gestirlo |
|---|---|---|
| **Lost in the Middle** | Nei contesti lunghi i modelli danno più peso all'inizio e alla fine. Le informazioni nel mezzo vengono trascurate | Mettere i vincoli chiave all'inizio e/o alla fine del prompt |
| **Temperatura ≠ Creatività** | La temperatura controlla la casualità, non la qualità creativa. Alta temperatura = più imprevedibilità, non necessariamente risultati migliori | Usare temperatura bassa per task tecnici e precisi |
| **Bias temporali** | I modelli non sono aggiornati continuamente e possono produrre risposte plausibili ma temporalmente non corrette | Esplicitare sempre il contesto temporale della richiesta |
| **Accondiscendenza** | Un LLM tende a collaborare con l'ipotesi proposta e a renderla coerente, più che a metterla in discussione | Richiedere esplicitamente obiezioni, contro-esempi e stress-test |

---

## File Markdown come Metodo di Lavoro

**Il problema della chat**
A ogni nuovo messaggio, l'LLM rielabora il contesto. Nelle sessioni lunghe: aumento dei costi, perdita di coerenza, difficoltà di riprendere il lavoro, assenza di tracciabilità.

**La soluzione: i Discussion File**
Usare file `.md` strutturati in round. Ogni round ha un obiettivo, un output e una decisione.

| File | Scopo | Utilizzo |
|---|---|---|
| **DISCUSSION_FILE.md** | Chat strutturata con l'LLM, organizzata in round di dialogo | Sostituisce la chat lunga in Cursor: ogni round registra richiesta, risposta, decisione |
| **SESSION_HANDOFF.md** | File di passaggio tra sessioni di lavoro | Contiene stato del progetto, decisioni prese, problemi aperti |
| **PROMPT_LOG.md** | Diario delle iterazioni: cosa ha funzionato e perché | Utile per la presentazione finale e per progetti futuri |
| **INCIDENTS.md** | Registro degli errori | Documentare cosa non ha funzionato → evitare di ripetere gli stessi sbagli |

---

## Errori Comuni con gli LLM

| Errore | Descrizione | Soluzione |
|---|---|---|
| **Date obsolete** | Il modello può richiamare informazioni non aggiornate o riferimenti temporali non corretti | Verificare sempre le date |
| **Incoerenza tra prompt successivi** | Ogni nuovo prompt modifica il contesto e può ridurre la coerenza delle richieste successive | Organizzare la sessione in round distinti con obiettivi espliciti |
| **Perdita di contesto** | Nelle sessioni lunghe, le istruzioni iniziali perdono peso o vengono reinterpretate | Ripetere e fissare i vincoli chiave nei file .md |
| **Allucinazioni** | Il modello può produrre dati inventati, riferimenti inesistenti o soluzioni non verificabili, mantenendo un tono sicuro e plausibile | Validare sempre gli output critici |

---

## Economia dei Token: Costi e Ottimizzazione

**Cos'è un Token**
Unità con cui il modello processa il testo. Non equivale a una parola: può essere una parola intera, una sua parte o un segno di punteggiatura. In inglese 100 token ≈ 60-80 parole. In italiano il rapporto tende a essere leggermente più alto. Si paga sia l'input che l'output.

### Cosa Fa Aumentare i Costi
- Prompt troppo lunghi
- Risposte troppo verbose
- Chat molto lunghe (riprocessano sempre più contesto)
- Usare un modello avanzato per task semplici

### Principio Operativo — Routing Multi-Modello
```
Task semplice     → modello leggero
Analisi intermedia → modello bilanciato
Task critico/ambiguo → modello avanzato
```

### Ottimizzazione
- Ridurre il contesto → meno token inutili
- Limitare l'output → risposte più efficienti
- Separare i task → maggiore controllo del costo

---

## La Specifica Tecnica

> Il documento più importante del progetto. Senza una specifica precisa, è impossibile valutare se un output dell'AI è corretto, parzialmente corretto o completamente sbagliato.

**Dal Case Study:**
- 6 versioni della specifica generate prima di arrivare a quella definitiva
- 2.852 righe nella versione finale
- Specifica congelata prima del deploy: le correzioni minori successive documentate in SPEC_ERRATA.md

### Sezioni della Specifica

| Sezione | Contenuto |
|---|---|
| Sintesi del progetto | Obiettivo, problema e utenti |
| MVP e funzionalità fuori scope | Cosa è dentro, cosa è fuori |
| Flusso operativo e requisiti | Come funziona il sistema |
| Stack tecnologico e dipendenze | Ogni scelta motivata |
| Architettura e flusso dati | Componenti e interfacce |
| Comportamento AI | Come si comporta il modello |
| Dati, privacy e vincoli normativi | GDPR, AI Act |
| Validazione e quality control | Metriche e criteri di accettazione |
| Gestione errori e fallback | Cosa succede quando qualcosa va storto |
| Deploy, manutenzione e aggiornamenti | Ciclo di vita del sistema |
| Rischi, assunzioni e checklist pre-build | Tutto ciò che potrebbe andare storto |

### Checklist Specifica Tecnica

- [ ] Obiettivo principale definito in una frase
- [ ] Perimetro: cosa fa / cosa non fa
- [ ] Utenti target identificati
- [ ] Input formalmente descritti (tipo, formato, vincoli, edge case)
- [ ] Output formalmente descritti (struttura, formato, range)
- [ ] Almeno 3 metriche di qualità quantitative definite
- [ ] Architettura a componenti schematizzata
- [ ] Dipendenze esterne elencate
- [ ] Minimo 3 rischi identificati con strategia di mitigazione
- [ ] Assunzioni esplicitate
- [ ] Vincoli GDPR/AI Act inseriti

---

## Homework Lezione 2 — Da Portare alla Lezione 3

| Deliverable | Descrizione |
|---|---|
| **Validazione dell'Idea** | Brainstorming iniziale, idee scartate, idea scelta, motivazione e 5 dimensioni di validazione |
| **Specifica Tecnica** | Tutte le versioni (v1, v2, v3…) — il processo di revisione fa parte della valutazione |
| **Studiare PDF Lezione 1 + 2** | La parte pratica si basa sui concetti chiave già introdotti |
| **Verifica dei PC** | Sistema operativo, processore, Python, Git, Cursor e dipendenze principali (sia a casa che in Academy) |

---

## Materiale Didattico Allegato

| File | Contenuto | A cosa serve |
|---|---|---|
| Prompt_Engineering_Guide.md | Guida completa al Prompt Engineering | Migliorare interazioni con l'AI |
| DISCUSSION_FILE_1_Template.md | Template dialogo strutturato con agente | Organizzare e documentare conversazioni |
| SESSION_HANDOFF_Template.md | Template stato progetto e decisioni | Passaggio consegne tra sessioni |
| PROMPT_LOG_Template.md | Template diario iterazioni e risultati | Tracciare test e miglioramenti |
| INCIDENTS_Template.md | Template registro errori e criticità | Evitare ripetizione di sbagli |
| Technical_Spec_Guide.md | Guida alla compilazione della Specifica | Redazione professionale della specifica |
| TECHNICAL_SPEC_Template.md | Template Specifica Tecnica | Redigere tutte le versioni |
| Brainstorming_Idea_Validation_Guide.md | Guida alla validazione dell'idea | Cosa documentare e come valutare |
| Brainstorming_Idea_Validation_Template.md | Template validazione dell'idea | Consegnare brainstorming e idea scelta |

---

## Note Personali

*(spazio per appunti durante la lezione)*

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Lezione 1 - Case Study e Setup]]
- [[Lezione 3 - Costruire il Sistema]]
- [[Template - Specifica Tecnica]]
- [[Template - PROMPT_LOG]]
- [[Template - INCIDENTS]]
