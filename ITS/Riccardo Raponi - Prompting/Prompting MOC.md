# Prompting MOC

Materia ITS — Prompting e comunicazione con AI.
**Docente:** Riccardo Raponi
**Fonte:** `ITS/Riccardo Raponi - Prompting/`
**NotebookLM:** 14 fonti (PDF, slide di lezione, manuali, infografiche)

---

## Fondamenti: dalla Magia alla Meccanica

L'IA generativa **non comprende** — è uno strumento statistico basato su probabilità, addestrato su un corpus planetario e compresso in relazioni vettoriali.

### Limiti tecnici da conoscere

| Concetto | Cosa significa |
|----------|----------------|
| **Token** | Pezzetti di parole: l'unità minima che il modello processa |
| **Finestra di Contesto** | Analogia della "lavagna piccola" — oltre un certo limite, il modello dimentica |
| **Pappagallo Stocastico** | Il modello ripete pattern statistici senza vera comprensione |
| **Allucinazioni** | Risposte plausibili ma false — rischio critico in contesti business |
| **Bias** | Il modello eredita i bias dei dati di training |

**Implicazione operativa:** LLM = "neo-laureato veloce" — colmo di conoscenza generica, privo di contesto aziendale. Il prompt serve a dargli quel contesto.

---

## Livelli di Maestria

### Modello a 3 livelli (Raponi)

| Livello | Descrizione |
|---------|-------------|
| **1 — Interazione Diretta** | Domande semplici, output generico |
| **2 — Ottimizzazione Guidata** | Struttura + vincoli, output mirato |
| **3 — Ragionamento Complesso** | Chain-of-Thought, task scomposti, auto-valutazione |

### Modello a 4 livelli (Guida Strategica)

1. **Diretto/Contestuale** — Chi, Dove, Perché
2. **Esempi + Gioco di Ruolo** — Persona esplicita
3. **Limitazioni + Iterazione** — vincoli progressivi
4. **Strategico** — CoT e scenari multipli

**Regola d'oro:** Se la risposta dell'IA è generica, il problema è nel prompt — non nel modello.

---

## Framework C.I.A.R.E.

| Lettera | Elemento | Cosa fare |
|---------|----------|-----------|
| **C** | Contesto | Background e situazione di partenza |
| **I** | Intento | Obiettivo finale e uso dell'output |
| **A** | Audience + Output | A chi si rivolge + formato (tabella, JSON, lista) |
| **R** | Regole | Vincoli di stile, lunghezza, tono |
| **E** | Esempi | Modelli da imitare (Few-Shot) |

---

## 5 Pilastri della Padronanza

1. **Dare Direzione** — ruolo/persona specifica → sblocca vocabolario tecnico ed empatia
2. **Specificare il Formato** — lunghezza, struttura Markdown, tono
3. **Fornire Esempi (Few-Shot)** — mostra input/output attesi per "sintonizzare" il modello
4. **Valutare la Qualità** — chiedi all'IA di auto-valutarsi (score 1-5)
5. **Suddividere il Lavoro** — scomponi task complessi in sotto-compiti sequenziali

---

## Checklist 10 Controlli Pre-Invio

Prima di inviare un prompt critico, verifica:

- [ ] **Ruolo** — persona/expertise è dichiarata?
- [ ] **Obiettivo** — cosa deve produrre l'output?
- [ ] **Contesto** — background e situazione sono presenti?
- [ ] **Destinatario** — chi leggerà il risultato?
- [ ] **Formato** — struttura, lunghezza, tipo (tabella/JSON/prosa)?
- [ ] **Vincoli** — limiti di stile, tono, tempo?
- [ ] **Specificità** — sono stati eliminati termini vaghi?
- [ ] **Esempi** — c'è almeno uno shot di riferimento?
- [ ] **Esclusioni** — cosa NON deve fare?
- [ ] **Chiarezza** — un estraneo capirebbe la richiesta?

---

## Matrice delle Strategie

| Strategia | Quando usarla |
|-----------|---------------|
| **Zero-Shot** | Task comuni, no esempi — veloce ma rischioso |
| **One-Shot / Few-Shot** | Output con formato specifico — allinea lo stile |
| **Role-Based** | Serve vocabolario e prospettiva di dominio |
| **Chain-of-Thought (CoT)** | Logica/matematica/analisi multi-step |
| **RAG** (Retrieval-Augmented) | Risposte ancorate a fonti esterne affidabili |

---

## Tecniche Avanzate

| Tecnica | Formula / Uso |
|---------|--------------|
| **Chain-of-Thought (CoT)** | *"Pensiamo passo dopo passo"* — aumenta accuratezza logica |
| **Prompt Chaining** | Output prompt 1 → input prompt 2 (scompone task massivi) |
| **Priming** | Prepara il modello con contesto/esempi prima della richiesta |
| **Reverse Prompting** | Dai un testo, chiedi di ricostruire il prompt originale |
| **Prompting Ricorsivo** | L'IA raffina iterativamente il proprio output |
| **Sollecitazione Condizionale** | *"Se X allora Y, altrimenti Z"* — logica a rami |
| **Control Codes** | `[FORMALE]` `[TECNICO]` `[CONCISO]` per guidare lo stile |
| **Active Learning** | L'IA fa domande di chiarimento prima di rispondere |
| **Perspective Prompting** | Analizza da più punti di vista (es. medico vs paziente) |
| **Constructive Critic** | Chiedi critiche esperte + suggerimenti di miglioramento |
| **Esplorativa** | Scenari ipotetici e futuri ("immagina che...") |

---

## Edge Cases — Quando il Framework C.I.A.R.E. Fallisce

Estratto dalle fonti del notebook (Prompting_Strategico_Aziendale, il piccolo manuale, Fare la domanda giusta).

| Edge case | Sintomo | Mitigazione |
|-----------|---------|-------------|
| **Soggettività interpretativa** | Termini come "interessante", "accattivante", "professionale" non sono interpretabili univocamente dall'IA | Sostituire con criteri misurabili (lunghezza, tono specifico, lessico vincolato) |
| **Mancanza di Ground Truth** | L'Intento (I) non è ancorato a documenti di riferimento → allucinazioni verosimili ma false | Allegare fonti o usare RAG; vincolare con "rispondi solo se presente nelle fonti" |
| **Contesto ambiguo** | Background (C) contraddittorio → output incoerente | Esplicitare priorità tra informazioni in conflitto |
| **Sovrapposizione informativa** | Troppe Regole (R) o Esempi (E) contrastanti → "deriva" del modello | Limitare regole a 3-5 max; esempi coerenti tra loro |

**Regola operativa:** dopo 5-6 round di raffinamento senza miglioramento, ripartire da prompt pulito basato sui feedback raccolti (Sentinelli/Placa).

---

## Esempi Prima/Dopo (5 Casi Concreti)

| Dominio | Prompt mal scritto | Prompt riscritto | Fonte |
|---------|-------------------|------------------|-------|
| **Marketing** | *"Scrivi un post social sull'AI."* | *"Agisci come Social Media Manager fintech. Post LinkedIn (max 150 parole) su come la funzione X risolve Y per team accounting. 1 emoji, 3 hashtag (#Fintech #AI #Automazione), link placeholder."* | Prompting_Strategico_Aziendale |
| **Sales** | *"Dammi idee per una chiamata di vendita."* | *"Script di 5 domande aperte per discovery call con Direttore Marketing. Obiettivo: capire sfide su lead generation. Domande sequenziali a narrazione."* | idem |
| **Customer Care** | *"Come rispondo a un cliente insoddisfatto?"* | *"Cliente si lamenta che funzione X non funziona. Policy: assistenza tecnica → rimborso. Risposta in 3 passaggi, partendo da validazione del problema."* | idem |
| **Image Gen** | *"Crea un'immagine di un gatto."* | *"Foto realistica di gatto soriano arancione acciambellato su lino bianco, luce dorata mattino da finestra. Stile cinematografico, focus superficiale, 35mm."* | Prompt_Engineering_AI_Results |
| **Legale** | *"Precedenti giudiziari su violazione brevetti in casi simili."* | *"Precedenti su violazione brevetti settore tecnologico, prodotti simili a quello dell'azienda A."* | Fare la domanda giusta (Sentinelli/Placa) |

**Metrica trasversale (studio BCG citato):** prompting strutturato → +20-40% produttività individuale, +40% qualità output.

---

## Tensioni tra Fonti (Senso Critico)

Le fonti del notebook non concordano su tutto. Riconoscere le tensioni evita applicazione meccanica.

| Tema | Posizione A | Posizione B | Quando applicare A vs B |
|------|------------|------------|-------------------------|
| **Ruolo assegnato all'IA** | Ruolo tecnico/professionale specifico (es. "avvocato d'impresa") per sbloccare cluster di vocabolario (Raponi, Sentinelli) | Relazione "collaboratore personale" / assistente del Direttore Creativo (Gemini Guide) | A → output verticale tecnico; B → workflow creativi e iterativi |
| **Iterazione e raffinamento** | Fermarsi dopo 5-6 round, ripartire da prompt pulito (Sentinelli/Placa) | Continuare ricorsivamente fino al risultato perfetto (Active Learning, manuali base) | A → task con criteri misurabili; B → esplorazione creativa |

---

## Protocollo Triage (Master Prompt Engineer)

**Fase 1 — Classificazione istantanea**
- **Critico/Business** → domande preventive obbligatorie, no assunzioni
- **Creativo/Personale** → procedi, chiedi solo chiarimenti di stile

**Fase 2 — Costruzione Master Prompt** (obbligatorio per task critici)
- Persona esplicita
- Contesto completo
- Vincoli di formato
- Few-Shot con **edge cases** inclusi
- Chain-of-Thought attivata

**Fase 3 — Score Confidence 1-5**
Prima di rilasciare l'output, l'IA si auto-valuta sulla chiarezza della richiesta.

---

## Multimodalità (Gemini / GPT-5)

Modelli moderni processano **testo + immagini + audio + video**. Tre strategie operative:

| Strategia | Uso tipico |
|-----------|------------|
| **Condensare** | Sintesi di report lunghi, estrazione punti chiave |
| **Espandere** | Ricerche di mercato, brainstorming a partire da seed |
| **Ripetere** | Adattare tono/formato per target diversi (es. tecnico → divulgativo) |

**GPT-5 agentic:** persistenza nel problem-solving, esecuzione autonoma di flussi multi-step (Terminal-Bench). Richiede prompt con **obiettivo finale chiaro + criteri di stop**.

**Reattivo vs Orchestrator (GPT-5 prompting guide):**
- **Modelli tradizionali** = reattivi: scambio domanda → risposta testuale, l'utente coordina i passi
- **GPT-5 agentic** = AI Orchestrator: interagisce autonomamente con Web, API, Database, strumenti di comunicazione per raggiungere l'obiettivo
- **Steerability:** GPT-5 risponde meglio a istruzioni dirette ed esplicite; permette Custom Rules profonde → il prompt diventa "configurazione operativa", non più solo "domanda"

---

## Workflow Aziendale: Human-in-the-Loop

**Ciclo sicuro per uso business:**

```
Draft (IA) → Review umana → Finalize → Archivio in Prompt Library
```

**Costi del prompting inefficace:**
- Allucinazioni in documenti legali/contabili
- Violazione GDPR (dati personali nei prompt)
- Bias nei processi decisionali (HR, credito, selezione)

**Mitigazione:** Prompt Library aziendale + template validati + revisione umana obbligatoria sui deliverable.

---

## Casi Studio Verticali

| Dominio | Prompt tipico |
|---------|--------------|
| **PM** | *"Agisci come PM senior. Crea una WBS per un'app mobile in formato tabella."* |
| **Marketing** | *"Agisci come consulente. Identifica 3 rischi e 3 opportunità. Valuta 1-5."* |
| **Legale** | *"Mettiti nei panni di un avvocato d'impresa. Scrivimi un NDA."* |
| **Commercialista** | *"Struttura il piano fiscale di una startup innovativa nel primo triennio."* |
| **Architettura** | *"Progetta linee guida per un edificio sostenibile in clima mediterraneo."* |
| **Lead Generation** | *"Genera 10 sequenze email per qualificare lead B2B nel settore X."* |
| **Didattica** | *"Genera un piano lezione a ritroso partendo dagli obiettivi di apprendimento."* |

---

## Fonti del Notebook (14 elementi)

**Immagini / Infografiche**
- `5 pilastri.png` — le 5 colonne della padronanza
- `5_passi_prompt-perfetto` — framework C.I.A.R.E. visuale
- `checklist_prompt_perfetto` — 10 controlli pre-invio

**PDF docente (Raponi)**
- `AI_data_science_metodologie_09.12.025.pdf` — fondamenti tecnici IA
- `Evoluzione_Aziendale_Project_Management_IA.16.12.025.pdf` — PM + IA, 3V Big Data
- `Prompt_Engineering_AI_Results.pdf` — matrice strategie + chaining
- `Prompt_Engineering_Guida_Strategica.pdf` — 4 livelli operativi
- `Prompting_Strategico_Aziendale.pdf` — workflow Human-in-the-Loop

**Manuali / Libri**
- `Fare la domanda giusta` (Sentinelli, Placa) — Prompt Engineering professionale
- `Il piccolo manuale per dominare l'IA` — principi base + esercizi
- `IT_The_Art_Of_Prompting.pdf` — multimodalità Gemini

**Guide tecniche**
- `GPT-5 prompting guide` — comportamento agentico
- `Istruzioni Master Prompt Engineer` — meta-prompt di triage
- `Testo` (appunti) — note su Chain-of-Thought

---

## Connessioni

- [[ITS MOC]]
- [[Knowledge MOC]] — Context Engineering, framework AI trasversali
- [[Progettistica AI MOC]] — applicazione al design di sistemi AI
- [[NotebookLM Query Playbook]] — estrarre contenuti dai notebook online applicando questi framework

- [[Bracketed Headers - Convention Markdown]] — convenzione di formattazione complementare a C.I.A.R.E.: chiudere ogni heading con gli stessi `#` per delimitare visivamente i blocchi e ridurre il sanguinamento di contesto tra sezioni
- [[Creazione di scenari con AI]] — Prompt Architecture per Decision Making Strategico: Board Virtuali, What-If, Control Codes, caso TechLogistics

---

## Prompt Library

Raccolta di prompt pronti all'uso strutturati con C.I.A.R.E. → [[Prompt Library]]

| # | Prompt | Dove usarlo | Caso d'uso |
|---|--------|-------------|------------|
| 01 | Validazione Lista Fonti/Link | Perplexity (web search) | Verificare che siti/newsletter/canali trovati siano reali, attivi e pertinenti |
| 02 | Board di 3 Agenti — Team di Sviluppo Ideale | ChatGPT/Claude/Gemini (in inglese) | 3 agenti (tecnica/consegna/budget) dibattono e progettano il team di sviluppo per un progetto |
