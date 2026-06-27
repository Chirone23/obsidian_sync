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
**Dipende dalla profondità della piattaforma — e qui la risposta semplice "no" è sbagliata.** Vanno distinti due tipi di "saper usare":

1. **Meccanico (come si chiama il tool)** → lo dà lo **schema MCP**: nome + descrizione + schema dei parametri. Sufficiente per tool semplici (`meteo(città)`).
2. **Competenza di dominio (come costruire input validi e buoni)** → serve la **doc della piattaforma**. Indispensabile quando il payload è di fatto *un intero programma* (es. n8n: node-type, forma dei parametri per nodo, sintassi delle espressioni, regole di connessione). Lo schema descrive il *contenitore*, non la *lingua* che ci va dentro.

> **Lezione concreta (n8n, 2026-06).** Collegando n8n via MCP, l'agente sapeva *chiamare* `create_workflow` ma non sapeva *comporre* il workflow finché non gli è stata collegata anche la **documentazione n8n** (sempre via MCP). A quel punto le performance sono schizzate. La doc era la conoscenza di dominio mancante.

### Il meccanismo: i docs come fonte *interrogabile* (= retrieval)
I docs n8n erano dati via MCP **non come secondo set di azioni, ma come fonte di conoscenza cercabile on-demand**. Cioè era **retrieval (§2.3) travestito da MCP**: non "manuale incollato nel prompt" (saturerebbe il contesto), ma "manuale che l'agente interroga quando serve".

### Regola pratica per NOA
Collegando una piattaforma **profonda** via MCP, pianifica **due** cose, non una:
- **Server di azioni** → i tool per *fare* (firma, argomenti).
- **Fonte docs interrogabile** → il loro docs-MCP, oppure i loro docs indicizzati nella memoria retrieval di NOA.

Per piattaforme **semplici** basta lo schema; per il "quando/perché/policy" si aggiunge una **direttiva in linguaggio naturale** nel vault (filosofia "direttive" del CLAUDE.md). La direttiva è la versione leggera; i docs cercabili sono la versione per piattaforme grosse.

### Implicazioni pratiche
- Servono **descrizioni di tool buone** lato server (è la vera UX dell'agente).
- Ogni tool MCP **occupa token** nel system prompt: molti server = contesto gonfio → selezione/caricamento on-demand.
- Verificare che **`v4-flash` regga il tool-calling** (è OpenAI-compatibile, ma il tier piccolo è più debole nella scelta dello strumento).

---

[[progetti/Serverino/README]] • [[progetti/Serverino/DEFINIZIONE_ASSISTENTE]] • [[progetti/Serverino/SKILL_AUTONOME]] • [[moc/Index MOC]]
