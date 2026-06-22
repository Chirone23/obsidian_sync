# Serverino Bot — SPECIFICHE ESATTE
> ⚠️ **PARZIALMENTE SUPERATO (2026-06-22).** Dove questo documento contraddice [[progetti/Serverino/DEFINIZIONE_ASSISTENTE]] §8 (Riconciliazione v2 / opzione B), **vale §8**. In particolare: §3 (state machine → loop asyncio singolo), §4.3 e §18.2 (memoria → `/ricorda` manuale, no consolidamento LLM), §6.2/§8 (`deepseek-chat` → `deepseek-v4-flash`), §18.3 (no retry-3). Vedi tabella ERRATA in DEFINIZIONE §8.3.


**Versione:** 1.0 MVP  
**Data:** 2026-06-15  
**Status:** Blueprint finale — niente codice, solo schemi

---

## 1. DATABASE SCHEMA

### Table: `logs`
```sql
CREATE TABLE logs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  level TEXT NOT NULL,  -- 'INFO', 'DEBUG', 'WARNING', 'ERROR'
  message TEXT NOT NULL,
  context TEXT  -- JSON per dati aggiuntivi (tokens, response_time_ms, etc)
);

CREATE INDEX idx_logs_timestamp ON logs(timestamp);
CREATE INDEX idx_logs_level_timestamp ON logs(level, timestamp DESC);
```

### Table: `stats`
```sql
CREATE TABLE stats (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  date DATE UNIQUE NOT NULL,
  messages_count INTEGER DEFAULT 0,
  tokens_in INTEGER DEFAULT 0,
  tokens_out INTEGER DEFAULT 0,
  errors_count INTEGER DEFAULT 0,
  avg_response_time_ms REAL DEFAULT 0
);

CREATE INDEX idx_stats_date ON stats(date DESC);
```

### Retention Policy
- **Auto-clean ogni 1° del mese:** DELETE FROM logs WHERE timestamp < DATE('now', '-30 days')
- Stats non vengono cancellate

---

## 2. MESSAGE FLOW — Diagramma

```
┌─────────────────┐
│  Telegram User  │
└────────┬────────┘
         │ sends message
         ▼
┌─────────────────────────────────────┐
│   Telegram Polling (python-telegram) │
│   - Extract: text, chat_id, user_id  │
│   - Validate chat_id == TELEGRAM_CHAT_ID
└────────┬────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────┐
│   Message Queue (in-memory list)     │
│   - If bot is BUSY → append to queue │
│   - If bot is IDLE → send to handler │
└────────┬────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────┐
│   Message Handler                    │
│   ├─ Set bot state = BUSY            │
│   └─ Load context from Obsidian      │
└────────┬────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────┐
│   Obsidian Reader (filesystem)       │
│   ├─ Read skill/bot-persona.md       │
│   ├─ Read skill/bot-padrone.md       │
│   ├─ Read idee/bot-memory.md         │
│   └─ Extract plain text              │
└────────┬────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────┐
│   Prompt Builder                     │
│   ├─ Combine files + last 10 msgs    │
│   ├─ Format system prompt            │
│   └─ Count tokens (estimate)         │
└────────┬────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────┐
│   DeepSeek API Call                  │
│   ├─ POST /v1/chat/completions       │
│   ├─ Timeout: 60s (retry 3x)         │
│   ├─ Log response time               │
│   └─ Extract: text, tokens_in, ...   │
└────────┬────────────────────────────┘
         │
         ├─────────────────────────────────┐
         │                                 │
         ▼ (success)                  ▼ (error after retries)
  ┌──────────────────┐          ┌──────────────────────┐
  │ Store in SQLite  │          │ Return error message │
  │ - Insert log     │          │ + log error          │
  │ - Update stats   │          │ + set state = IDLE   │
  └────────┬─────────┘          └──────────────────────┘
           │
           ▼
┌──────────────────────────────────┐
│  Build Response with Metadata     │
│  ├─ Text                          │
│  └─ <!-- tokens:X in, Y out|...→ │
└────────┬─────────────────────────┘
         │
         ▼
┌──────────────────────────────────┐
│  Send to Telegram                │
│  └─ Retry 3x if fails            │
└────────┬─────────────────────────┘
         │
         ▼
┌──────────────────────────────────┐
│  Check Message Queue             │
│  ├─ If queue not empty           │
│  │  └─ Add queued msgs to prompt │
│  │  └─ Repeat from "DeepSeek API"│
│  └─ Else: Set state = IDLE       │
└──────────────────────────────────┘
```

---

## 3. STATE MACHINE — Bot States

```
┌────────┐
│  IDLE  │ (waiting for message)
└────┬───┘
     │ /start or message received
     ▼
┌────────────────┐
│  LOADING_CTX   │ (reading Obsidian files)
└────┬───────────┘
     │ loaded
     ▼
┌────────────────┐
│  BUILDING_PROMPT│ (formatting context + message)
└────┬───────────┘
     │ built
     ▼
┌────────────────┐
│  BUSY          │ (API call in progress)
│ (can receive   │
│  messages)     │
└────┬───────────┘
     │ response received
     ▼
┌────────────────┐
│  SENDING       │ (sending response to Telegram)
└────┬───────────┘
     │ sent
     ▼
┌────────────────┐
│  CHECK_QUEUE   │ (any messages buffered?)
└────┬───────────┘
     │ yes
     ├──→ back to LOADING_CTX
     │
     │ no
     ▼
   IDLE
```

---

## 4. OBSIDIAN FILES

### 4.1 `skill/bot-persona.md`
**Contenuto:** Instruzione di sistema per il bot
```
# [Persona Name]

## Chi sei
[Descrizione ruolo, funzioni, specializzazioni]

## Come rispondi
- Linguaggio: [italiano/english/misto]
- Stile: [formal/casual/technical]
- Lunghezza: [brief/medium/detailed]
- Tone: [helpful/direct/funny/empathetic]

## Vincoli
[Qualunque limitazione, cosa non fare, edge case]

## Expertise
[Domini in cui sei esperto, aree di focus]
```

**Uso:** Intero file letto come system prompt prefix.

---

### 4.2 `skill/bot-padrone.md`
**Contenuto:** Profilo utente
```
# Profilo di Chirone

## Chi sei
[Descrizione di te, ruolo, contesto di lavoro, timezone]

## Come lavori con il bot
[Stile di interazione, frequenza, aspettative, preferenze di risposta]

## Contesto di dominio
[Aree di interesse, progetti attuali, background, conoscenze specifiche]

## Preferenze
[Niente X, preferisci Y, chiedi prima di Z]
```

**Uso:** Intero file letto come system prompt (sezione "I am:").

---

### 4.3 `idee/bot-memory.md`
**Contenuto:** Episodic memory (per sessione corrente o tra sessioni se salvato)
```
# Bot Memory — Session Log

## Metadati sessione
- Start: 2026-06-15 20:30 UTC
- Topic: [tema principale conversazione]
- Key decisions: [decisioni prese, conclusioni importanti]

## Conversazione
Formato libero markdown con:
- Timestamp di checkpoint importanti
- Riassunti di subtopic
- Insights o decizoni salvate

---

## Previous Session (se caricata)
[Se user salva una recap da sessione precedente, va qui]
```

**Uso:** 
- Letto all'inizio sesssione se esiste
- Durante `/start` → reset (file non cambia, ma context window lo ignora)
- User decide cosa salvare via `/recap` command (manual dump)

---

## 5. CONFIGURATION

### 5.1 `.env` Structure
```bash
# DeepSeek API
DEEPSEEK_API_KEY=sk_...
DEEPSEEK_MODEL=deepseek-v4-flash
DEEPSEEK_TEMPERATURE=0.7
DEEPSEEK_MAX_TOKENS=2000

# Telegram
TELEGRAM_BOT_TOKEN=123456789:ABCDEF...
TELEGRAM_CHAT_ID=987654321

# Obsidian
OBSIDIAN_VAULT_PATH=/home/serverino/Documents/Secondo_Cervello
OBSIDIAN_PERSONA_FILE=skill/bot-persona.md
OBSIDIAN_PADRONE_FILE=skill/bot-padrone.md
OBSIDIAN_MEMORY_FILE=idee/bot-memory.md

# Bot
LOG_LEVEL=INFO  # INFO | DEBUG
LOG_FILE=/home/serverino/bot/logs/app.log
DB_PATH=/home/serverino/bot/storage/bot.db

# Timeouts
DEEPSEEK_TIMEOUT_SEC=60
TELEGRAM_TIMEOUT_SEC=30

# Retry
DEEPSEEK_RETRY_COUNT=3
DEEPSEEK_RETRY_BACKOFF_SEC=30  # First retry, then halves
```

### 5.2 Config Object (runtime)
```
config = {
  deepseek: {
    api_key: str,
    model: str,
    temperature: float,
    max_tokens: int,
    timeout_sec: int,
    retry_count: int,
    retry_backoff: int
  },
  telegram: {
    bot_token: str,
    chat_id: int,
    timeout_sec: int,
    retry_count: int
  },
  obsidian: {
    vault_path: str,
    files: {
      persona: str,
      padrone: str,
      memory: str
    }
  },
  bot: {
    log_level: str,
    log_file: str,
    db_path: str,
    context_window_msgs: int = 10
  }
}
```

---

## 6. API CONTRACTS

### 6.1 DeepSeek API Request
```
POST https://api.deepseek.com/v1/chat/completions

Headers:
  Content-Type: application/json
  Authorization: Bearer {DEEPSEEK_API_KEY}

Body (JSON):
{
  "model": "deepseek-v4-flash",
  "temperature": 0.7,
  "max_tokens": 2000,
  "messages": [
    {
      "role": "system",
      "content": "[formatted system prompt from bot-persona + bot-padrone]"
    },
    {
      "role": "user",
      "content": "[user message or accumulated messages]"
    }
  ]
}
```

### 6.2 DeepSeek API Response
```json
{
  "id": "chatcmpl-...",
  "object": "chat.completion",
  "created": 1718475030,
  "model": "deepseek-chat",
  "choices": [
    {
      "index": 0,
      "message": {
        "role": "assistant",
        "content": "Response text here..."
      },
      "finish_reason": "stop"  // or "length", "error"
    }
  ],
  "usage": {
    "prompt_tokens": 156,
    "completion_tokens": 234,
    "total_tokens": 390
  }
}
```

### 6.3 Error Responses
```json
// Timeout / Network error
{ "error": { "message": "Timeout", "type": "timeout" } }

// Rate limit
{ "error": { "message": "Rate limited", "type": "rate_limit_error" }, "status_code": 429 }

// Auth error
{ "error": { "message": "Invalid API key", "type": "invalid_request_error" }, "status_code": 401 }

// Server error
{ "error": { "message": "Server error", "type": "server_error" }, "status_code": 500 }
```

---

## 7. PROMPT TEMPLATE

**Exact format che viene mandato a DeepSeek:**

```
[SYSTEM PROMPT]
You are: [full content of skill/bot-persona.md]

I am: [full content of skill/bot-padrone.md]

Context: [full content of idee/bot-memory.md if exists]

[CONVERSATION HISTORY]
Last messages from this session (up to 10):

User (2026-06-15 20:30): "First message"
Assistant: "Response..."

User (2026-06-15 20:31): "Second message"
Assistant: "Response..."

...

[CURRENT MESSAGE]
User message: {incoming_text}
```

**Note:**
- If conversation history doesn't exist (new session), skip "[CONVERSATION HISTORY]" section
- If bot-memory.md doesn't exist, skip "Context:" line
- Backlinks `[[...]]` sono inclusi letteralmente (no special parsing)
- YAML frontmatter (---) viene ignorato (solo body text)

---

## 8. RESPONSE FORMAT

**Bot invia a Telegram:**

```
{Response text — plain Markdown or formatted text}

<!-- tokens_in: 156 | tokens_out: 234 | time_ms: 2340 | model: deepseek-chat -->
```

**Esempio:**
```
Sì, Barcelona 2026 sarà interessante. Ecco perché:

1. **Antonelli vs Russell** — il duello mondiale si decide qui?
2. **Ferrari freni** — problema risolto o ancora buggy?
3. **Aggiornamenti** — chi arriva con novità significative?

Con Spa che torna alternato, Montmeló diventa cruciale.

<!-- tokens_in: 156 | tokens_out: 234 | time_ms: 2340 | model: deepseek-chat -->
```

---

## 9. ERROR HANDLING FLOWCHART

```
User sends message
  │
  ├─ Chat ID validation fail?
  │  └─ [SILENT] Log & ignore (unauthorized)
  │
  ├─ Message empty?
  │  └─ Reply: "Messaggio vuoto, riprova"
  │
  ├─ Queue + Start API call
  │  │
  │  ├─ Timeout (60s)?
  │  │  ├─ Retry 1 (30s timeout)
  │  │  ├─ Retry 2 (15s timeout)
  │  │  ├─ Retry 3 fails?
  │  │  │  └─ Reply: "⏱️ Timeout dopo 3 tentativi. Riprova dopo"
  │  │  │  └─ Log ERROR
  │  │  │
  │  │  └─ Any retry succeeds?
  │  │     └─ Proceed to response
  │  │
  │  ├─ Rate limit (429)?
  │  │  ├─ Queue message
  │  │  ├─ Wait 60s
  │  │  └─ Retry
  │  │
  │  ├─ Auth error (401)?
  │  │  └─ Reply: "❌ Errore autenticazione API. Contatta admin"
  │  │  └─ Log ERROR (senza esporre API key)
  │  │
  │  ├─ Server error (5xx)?
  │  │  ├─ Retry 3x (exponential backoff)
  │  │  └─ Fail: Reply "Server indisponibile, riprova dopo"
  │  │
  │  └─ Success?
  │     └─ Extract response + tokens
  │
  ├─ Store in SQLite
  │  ├─ INSERT logs
  │  ├─ UPDATE stats (date = today)
  │  └─ On lock → retry 5x (100ms backoff)
  │
  ├─ Send response to Telegram
  │  ├─ Send fail (network)?
  │  │  └─ Retry 3x
  │  │     └─ Final fail: Log error (msg already in DB)
  │  │
  │  └─ Sent successfully
  │
  └─ Check message queue
     ├─ Has buffered messages?
     │  └─ Add to next prompt → loop back to "Start API call"
     │
     └─ Queue empty?
        └─ Set state = IDLE
```

---

## 10. TELEGRAM COMMANDS

### `/start`
**Effect:** New session
```
- Clear conversation history (context window reset)
- Reload bot-persona.md + bot-padrone.md
- Optionally reload bot-memory.md if saved
- Reply: "🚀 Session started. [persona intro]"
```

### `/recap` (future, v1.1)
**Effect:** Show session summary, offer to save
```
[User can decide what to save to bot-memory.md]
```

---

## 11. VALIDATION CHECKLIST

**Before ogni API call:**
- [ ] DeepSeek API key non-empty
- [ ] Telegram chat ID matches env
- [ ] Obsidian vault path exists
- [ ] Bot-persona.md readable
- [ ] Obsidian files encoding = UTF-8

**After DeepSeek response:**
- [ ] Response has "choices" array non-empty
- [ ] Response has "usage" with token counts
- [ ] Tokens < DEEPSEEK_MAX_TOKENS

**Before send to Telegram:**
- [ ] Response text non-empty
- [ ] Response text <= 4096 chars (Telegram limit)
- [ ] Metadata comment well-formed

**Daily (or on startup):**
- [ ] SQLite file exists and writable
- [ ] Log file writable
- [ ] DB schema matches expected (check tables exist)

---

## 12. LOGGING LEVELS

| Level | When | Example |
|-------|------|---------|
| DEBUG | Granular events | "Loaded bot-persona.md (1234 chars)" |
| INFO | Key operations | "Message received from user", "API call started", "Response sent" |
| WARNING | Unusual but handled | "Obsidian file not found, using fallback", "API retry #2" |
| ERROR | Failure, needs attention | "DeepSeek API auth failed", "SQLite locked after 5 retries" |

**Log entry format:**
```
[TIMESTAMP] [LEVEL] [MODULE] - Message | context: {json with metrics}
```

**Example:**
```
2026-06-15T20:30:45Z INFO telegram_handler - Message received from user | context: {"chat_id": 987654321, "text_len": 45}
2026-06-15T20:30:46Z INFO obsidian_reader - Loaded 3 files | context: {"persona_chars": 1234, "padrone_chars": 567, "memory_chars": 0}
2026-06-15T20:30:47Z INFO deepseek_api - API call started | context: {"timeout_sec": 60, "estimated_tokens": 400}
2026-06-15T20:30:50Z INFO deepseek_api - Response received | context: {"tokens_in": 156, "tokens_out": 234, "time_ms": 2340}
2026-06-15T20:30:50Z INFO telegram_handler - Response sent | context: {"response_len": 890, "retries": 0}
```

---

## 13. DIRECTORY TREE (runtime)

```
/home/serverino/bot/
├── main.py
├── handlers/
│   ├── __init__.py
│   ├── telegram_handler.py
│   ├── deepseek_api.py
│   ├── obsidian_reader.py
│   └── storage.py
├── storage/
│   └── bot.db (created on first run)
├── logs/
│   └── app.log (created on first run)
├── config.py
├── requirements.txt
├── .env (NEVER commit)
├── .gitignore
├── systemd/
│   └── serverino-bot.service
└── README.md
```

---

## 14. TOKEN BUDGET CALC

**Given:** $5 budget

**DeepSeek pricing** (assumed similar to others):
- Input: ~$0.14 / 1M tokens
- Output: ~$0.28 / 1M tokens

**Monthly capacity:**
- 100 conversations/month average
- ~400 tokens input per conversation (persona + padrone + memory + message)
- ~300 tokens output per response
- Total/month: (100 × 400) + (100 × 300) = 70,000 tokens/month
- Cost: (400 × 0.14) + (300 × 0.28) ÷ 1000 = approx **$0.13/month**

**Conclusion:** $5 budget = ~38 months of usage (plenty) ✅

---

## 15. MONITORING & HEALTH

**Health check commands (manual):**
```bash
# Bot running?
sudo systemctl status serverino-bot

# Recent logs?
sudo journalctl -u serverino-bot -n 20

# DB integrity?
sqlite3 /home/serverino/bot/storage/bot.db "SELECT COUNT(*) FROM logs;"

# Obsidian files readable?
ls -lh /home/serverino/Documents/Secondo_Cervello/skill/bot-*.md
```

**Metrics to monitor:**
- Avg response time (should be <5 sec)
- Error rate (should be < 1%)
- Token usage (track weekly vs monthly budget)
- SQLite size (logs should auto-clean, stay under 100MB)

---

## 16. SECURITY NOTES

- ✅ `.env` never committed (in .gitignore)
- ✅ API keys not logged (sanitize logs before storage)
- ✅ Chat ID auth (only your Telegram ID allowed)
- ✅ SQLite readable only by `serverino` user (chmod 600)
- ✅ No passwords stored (only API keys in .env)
- ✅ Validate incoming messages (length, encoding)

---

**END OF SPECIFICATIONS**

[[progetti/Serverino/bot-architecture]] • [[skill/Bot Deployment Playbook]]


---

## 17. ADDENDUM — SCHEDULER & TASKS (2026-06-22)

> Estensione al livello 2.5. Vedi [[progetti/Serverino/DEFINIZIONE_ASSISTENTE]]. Aggiunge proattività su timer al flusso reattivo. Niente di quanto sopra cambia: questo è additivo.

### 17.1 Table: `tasks`
```sql
CREATE TABLE tasks (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  cron TEXT NOT NULL,              -- formato cron 5 campi, es. "0 8 * * *"
  descrizione TEXT NOT NULL,       -- cosa fa la task (human-readable)
  azione TEXT NOT NULL,            -- skill/comando da eseguire (es. "skill:daily_brief")
  payload TEXT,                    -- JSON opzionale con parametri della task
  stato TEXT DEFAULT 'proposta',   -- 'proposta' | 'attiva' | 'sospesa'
  ultima_esecuzione DATETIME,
  prossima_esecuzione DATETIME,    -- calcolata dal cron, usata per il check O(1)
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_tasks_stato ON tasks(stato);
CREATE INDEX idx_tasks_prossima ON tasks(prossima_esecuzione) WHERE stato = 'attiva';
```

**Ciclo di vita stato:**
```
proposta ──(conferma utente)──> attiva ──(comando /pausa)──> sospesa
   │                               │                            │
   └──(rifiuto utente)──> DELETE   └──(comando /stop)──> DELETE  └──(/riprendi)──> attiva
```

### 17.2 Scheduler loop (nuovo trigger, accanto al polling Telegram)
```
Ogni 60s (tick):
  now = current_time
  due = SELECT * FROM tasks
        WHERE stato = 'attiva' AND prossima_esecuzione <= now
  per ogni task in due:
    ├─ esegui task.azione (via skill)
    ├─ invia risultato a Telegram (chat_id padrone)
    ├─ UPDATE ultima_esecuzione = now
    └─ UPDATE prossima_esecuzione = next_cron(task.cron, now)
```

**Note:**
- Il tick è 60s → granularità minima 1 minuto (sufficiente, niente task al secondo).
- `next_cron()` usa una libreria cron (es. `croniter`) — deterministico, non LLM.
- Se l'esecuzione fallisce: log ERROR, NON aggiorna `ultima_esecuzione`, riprova al tick dopo (max 3 tentativi poi `sospesa` + notifica).

### 17.3 Handshake di conferma (creazione task nuova)
```
1. User (Telegram): "ogni giorno alle 8 dammi meteo + agenda"
2. Bot → LLM: estrae intent → { cron: "0 8 * * *", azione, descrizione }
3. Bot: INSERT tasks (stato='proposta')
4. Bot → Telegram: "📋 Nuova task:
                    'Meteo + agenda ogni giorno alle 08:00'
                    Confermo e attivo? [/conferma 12] [/annulla 12]"
5a. User: "/conferma 12" → UPDATE stato='attiva', calcola prossima_esecuzione
5b. User: "/annulla 12"  → DELETE FROM tasks WHERE id=12
```
**Regola dura:** una task `proposta` non viene MAI eseguita. Solo `attiva` entra nello scheduler loop.

### 17.4 Comandi Telegram aggiunti
| Comando | Effetto |
|---|---|
| `/tasks` | Lista task attive (id, cron, descrizione, prossima esecuzione) |
| `/conferma <id>` | Attiva una task in stato `proposta` |
| `/annulla <id>` | Elimina una task `proposta` |
| `/pausa <id>` | `attiva` → `sospesa` |
| `/riprendi <id>` | `sospesa` → `attiva` |
| `/stop <id>` | Elimina definitivamente una task |

### 17.5 Retention
- Le `tasks` NON vengono auto-pulite (sono configurazione, non log).
- Solo `logs` resta soggetto alla retention 30gg già definita (§1).


---

## 18. ADDENDUM — MEMORIA, FAILURE & TOKEN REALI (2026-06-22)

> Estensione MVP. Vedi [[progetti/Serverino/DEFINIZIONE_ASSISTENTE]]. Tre aggiunte: consolidamento memoria semi-automatico, notifica di skill fallita, token reali da DeepSeek.

### 18.1 Memoria a due livelli (sostituisce il `bot-memory.md` "log infinito")

Problema risolto: `bot-memory.md` accumulava l'intero dialogo → cresceva senza limite → riproponeva il problema del context troppo grande. Il `/recap` manuale non veniva mai usato.

**Due livelli distinti:**

| Livello | Contenuto | Dove | Scadenza |
|---|---|---|---|
| **Working memory** | Ultimi 10 messaggi della sessione | RAM / context window | Resettata a `/start` |
| **Long-term memory** | Solo fatti salienti (decisioni, preferenze, fatti sul padrone) | `idee/bot-memory.md` | Persistente tra sessioni |

**Formato long-term — un fatto per riga, non il dialogo:**
```markdown
# Bot Memory — Fatti salienti

- [2026-06-22] Chirone preferisce risposte dirette, niente lodi di apertura.
- [2026-06-22] Serverino target = livello 2.5 (reattivo + scheduler con conferma).
- [2026-06-20] Budget DeepSeek: free tier 5M token/30gg.
```
Niente trascrizione conversazione. Solo l'estratto compresso.

### 18.2 Consolidamento SEMI-automatico (con conferma)

Trigger: a `/start` di una nuova sessione **oppure** dopo N ore di inattività (default 6h).

```
1. Bot → LLM: analizza la sessione appena chiusa
   → estrae candidati fatti salienti (max 5)
2. Bot → Telegram:
   "💾 Dalla sessione salverei:
    1. [fatto A]
    2. [fatto B]
    3. [fatto C]
    Salvo? [/salva 1,3] [/salva tutti] [/scarta]"
3a. User: "/salva 1,3" → APPEND righe 1 e 3 a bot-memory.md
3b. User: "/salva tutti" → APPEND tutti
3c. User: "/scarta" → niente
```
**Regola:** il bot NON scrive mai in long-term memory senza conferma (stessa filosofia delle task). Coerente con [[progetti/Serverino/DEFINIZIONE_ASSISTENTE]] asse "Iniziativa".

### 18.3 Failure notification (skill che non parte)

Quando una task `attiva` fallisce l'esecuzione:
```
Tentativo 1 fallisce → log ERROR, retry al tick dopo
Tentativo 2 fallisce → log ERROR, retry al tick dopo
Tentativo 3 fallisce → UPDATE tasks SET stato='sospesa'
                     → Telegram: "⚠️ Task #12 'meteo+agenda' sospesa
                        dopo 3 errori: <messaggio errore>.
                        /riprendi 12 per riattivarla."
```
Vale anche per skill chiamate da **chat** che lanciano eccezione: il bot risponde con l'errore sanitizzato, non resta muto.

### 18.4 Token & saldo — fonti REALI, non stime

Due fonti distinte, entrambe autorevoli (rimuovere ovunque la parola "estimate"):

**A) Per-chiamata — dal campo `usage` della risposta DeepSeek** (già in §6.2):
```
response.usage.prompt_tokens     → tokens_in   (reale)
response.usage.completion_tokens → tokens_out  (reale)
```
Questi NON sono stimati: vengono da DeepSeek. La tabella `stats` (§1) li registra già — togliere il linguaggio "estimate" da §14.

**B) Saldo account — endpoint ufficiale DeepSeek:**
```
GET https://api.deepseek.com/user/balance
Headers: Authorization: Bearer {DEEPSEEK_API_KEY}, Accept: application/json

Response:
{
  "is_available": true,
  "balance_infos": [
    { "currency": "...", "total_balance": "...",
      "granted_balance": "...", "topped_up_balance": "..." }
  ]
}
```
- `granted_balance` = crediti gratis (consumati per primi)
- `topped_up_balance` = credito ricaricato
- `total_balance` = somma disponibile reale

Usato nel comando `/status` → mostra **credito reale residuo**, non spesa stimata.
Fonte: https://api-docs.deepseek.com/api/get-user-balance

### 18.5 Comando `/status` (entra nell'MVP)
```
🤖 Serverino — stato
• Uptime: <da systemd>
• Task attive: <COUNT stato='attiva'>
• Ultima task girata: <descrizione + timestamp>
• Token oggi: <tokens_in + tokens_out da stats WHERE date=today>
• Credito DeepSeek: <total_balance da /user/balance>  ← reale
• Errori 24h: <COUNT logs WHERE level='ERROR'>
```
