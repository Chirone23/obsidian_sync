# NOA — Gap e Roadmap (vs assistenti frontier)

**Data:** 2026-06-27
**Status:** 🟢 Analisi condivisa — documento vivo
**Scopo:** Fotografia onesta di cosa NOA è oggi e cosa gli manca rispetto ad assistenti come Claude Code, Gemini, Perplexity, DeepSeek/Qwen/Kimi. Base per decidere le priorità.

> Collega: [[progetti/Serverino/DEFINIZIONE_ASSISTENTE]] • [[progetti/Serverino/REALITY_CHECK]] • [[progetti/Serverino/SKILL_AUTONOME]] • [[progetti/Serverino/bot-architecture]] • [[moc/Index MOC]]

---

## 0. Inquadramento — categoria diversa, non "versione debole"

NOA **non** è "un Claude più scarso". È un'altra categoria: maggiordomo **personale, privato, single-tenant**, che gira su hardware tuo, conosce il tuo vault, si auto-estende con skill e **agisce di sua iniziativa** (scheduler). I frontier model sono cervelli generalisti enormi ma **passivi, multi-tenant, senza memoria del tuo mondo**. Confrontarli sulla "potenza" è la metrica sbagliata.

---

## 1. Cosa NOA è oggi (ancorato al codice)

- Bot Telegram **single-user** (auth su un solo `chat_id`).
- Cervello: **DeepSeek `v4-flash`** (cloud, tier economico, nessun retry per design §8).
- Contesto: legge file fissi del vault (`system/padrone/memory/MOC`) e li infila nel prompt.
- Storage SQLite: `logs, stats, tasks, settings, memory_suggestions, drafts, skills`.
- **Scheduler proattivo**: task `daily HH:MM` / `every Nh`, flusso proponi→`/conferma`→esegui.
- Working memory = ultimi 10 messaggi in RAM (persi al riavvio).
- Long-term memory = `memory.md`, manuale (`/ricorda`→`/salva`); manutenzione L1/L2 **ancora TODO**.
- Skill: `meteo` + sottosistema **self-build** (contratto→genera→test in sandbox Docker→auto-attiva→handoff `.md`).
- Esecuzione: **app Windows in tray** (venv + `pythonw`, autostart, backend sandbox Docker).

---

## 2. Cosa gli manca, in ordine di impatto

### 1. Tool-calling vero — *il buco architetturale più grosso*
Oggi NOA **non decide** quali strumenti usare: `on_message` fa **match di keyword** (prima parola = skill; "programma/pianifica" = task), altrimenti chatta. La promessa di design "l'LLM sceglie la skill e il payload" **non è implementata**. I frontier fanno *function calling*: il modello ragiona, sceglie il tool, passa gli argomenti, **concatena** chiamate, reagisce ai risultati. È ciò che separa un chatbot da un agente — e il gancio che sblocca il resto: **la ricerca internet è solo un altro tool**. Lo standard per collegare strumenti/piattaforme esterne è **MCP** (→ §6), ma MCP è inutile senza questo loop.

### 2. Ricerca + retrieval
- *Web search* → zero accesso a internet (mestiere di Perplexity, grounding con citazioni).
- *RAG sul vault* → NOA **non cerca** nel vault: legge file fissi e li mette interi nel prompt. Niente embedding, niente recupero semantico → non scala con un vault grande.

### 3. Memoria che scala
Working memory in RAM (persa al riavvio); long-term append-only manuale; motore L1/L2 TODO; nessun recupero (l'intera `memory.md` finisce nel system prompt). Primitivo rispetto a thread persistenti + memoria semantica dei grandi.

### 4. Multimodalità
Solo testo. Niente voce (eppure Telegram = vocali continui), niente PDF/immagini, niente vision. Fuori scope per scelta (Fase 2), ma è il gap più **percepibile** nell'uso da telefono.

### 5. Profondità di ragionamento
`v4-flash` è veloce e quasi gratis, ma è il tier piccolo: su coding/ragionamento multi-step/analisi lunga è lontano da Opus / Gemini-Ultra / DeepSeek-R1. Scelta di costo deliberata, non un difetto — ma va detto.

### 6. Capacità di agire sul mondo
NOA può solo rispondere su Telegram ed eseguire skill pre-approvate. Niente loop agentico generale (a parte il self-build, stretto): non manda email, non tocca il calendario, non apre il browser, non scrive file.

### Minori ma reali
Niente streaming (aspetti il completamento intero); single point of failure (DeepSeek giù = muto, nessun retry); nessuna difesa da prompt-injection dal contenuto del vault.

---

## 3. Cosa NOA ha che i frontier NON hanno

- **Proattività** — inizia lui (scheduler), loro aspettano sempre te.
- **Privacy reale** — dati che non escono, un solo padrone.
- **Integrazione nativa del tuo secondo cervello** (il vault).
- **Auto-estensione** con skill in sandbox.
- **Costo marginale ~zero** oltre l'API.

Differenziatori veri, non consolazioni.

---

## 4. Prossimi 3, in fila

1. **Tool-calling loop** — converte NOA da "bot a keyword" ad agente. Sblocca il resto.
2. **Web search come prima skill di quel loop** — chiude il gap più sentito e dimostra l'architettura.
3. **Memoria con retrieval** (embedding sul vault + recupero) — NOA *cerca* cosa serve invece di dumpare tutto.

Voce e multimodalità **dopo**: alto impatto percepito, ma nessuno dei tre punti sopra dipende da loro.

---

## 5. Bivio aperto (da decidere)

NOA deve diventare più **agente** (fa cose: mail, calendario, ricerche, file) o più **cervello/oracolo** (risponde meglio, sa di più, ragiona di più)?

- Strada **agente** → parte dal punto **1** (tool-calling).
- Strada **oracolo** → parte da **modello migliore + memoria con retrieval** (punti 5 e 3).

Decisione non ancora presa.

---

## 6. Collegamenti MCP — come NOA imparerà a usare le piattaforme

**Premessa:** MCP sta *a valle* del tool-calling (§2.1). È lo standard di trasporto per esporre strumenti; senza il loop di function-calling NOA non può consumarli.

### NOA legge la doc della piattaforma per sapere come usarla?
**No.** Il modello **non** va a leggere la documentazione/sito della piattaforma. Il *server MCP* dichiara i suoi strumenti come funzioni — **nome + descrizione + schema dei parametri** (JSON) — e sono quelle a finire nel contesto del modello. **Quello è il suo unico "manuale".** Descrizioni buone → uso corretto; descrizioni vaghe → NOA brancola.

### Il "doc" operativo lo scriviamo noi (direttiva nel vault)
Lo schema MCP dice *come si chiama* il tool (firma, argomenti), non *quando/perché/con che policy* usarlo. Quel giudizio operativo va messo in una **direttiva in linguaggio naturale** nel vault, che NOA legge come contesto (filosofia "direttive" del CLAUDE.md).

- **MCP (server)** → il "come chiamare": firma, argomenti, schema.
- **Direttiva (vault)** → il "quando/perché/policy/edge case".
- È ibrido: lo schema è automatico, la direttiva la **scriviamo noi** — NOA non la recupera da sola.

### Implicazioni pratiche
- Servono **descrizioni di tool buone** lato server (è la vera UX dell'agente).
- Ogni tool MCP **occupa token** nel system prompt: molti server = contesto gonfio → selezione/caricamento on-demand.
- Verificare che **`v4-flash` regga il tool-calling** (è OpenAI-compatibile, ma il tier piccolo è più debole nella scelta dello strumento).

---

[[progetti/Serverino/README]] • [[progetti/Serverino/DEFINIZIONE_ASSISTENTE]] • [[progetti/Serverino/SKILL_AUTONOME]] • [[moc/Index MOC]]
