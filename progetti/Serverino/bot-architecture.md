# Serverino Bot — Architecture MVP
> ⚠️ **PARZIALMENTE SUPERATO (2026-06-22).** Dove questo documento contraddice [[progetti/Serverino/DEFINIZIONE_ASSISTENTE]] §8 (Riconciliazione v2 / opzione B), **vale §8**. In particolare: header e Tech Stack ("MCP Obsidian" → filesystem git), `.env` (`MCP_SERVER_URL` morto), Message Flow step 7 (log infinito → `/ricorda`), addendum doppio trigger (2 thread → UN loop asyncio con `JobQueue`). Vedi tabella ERRATA in DEFINIZIONE §8.3.


**Status:** Design Phase  
**Version:** 1.0 MVP (text-only chat)  
**Provider:** DeepSeek API  
**Knowledge:** MCP Obsidian (read-only)

---

## Overview

**Cosa fa:** Bot Telegram 24/7 che riceve messaggi, legge il tuo vault Obsidian per contesto, chiama DeepSeek API, risponde.

**Architettura minimalista:**
```
Telegram User
    ↓
Polling loop (Python)
    ↓
Message handler
    ├─ Read MCP Obsidian (persona, memory, context)
    ├─ Build prompt con contesto
    └─ Call DeepSeek API
         ↓
    Log conversazione (SQLite)
    ↓
Response → Telegram
```

**Perché MVP chat-only:** Focus su stabilità, dopo aggiungiamo media (file/immagini/vocali) quando DeepSeek capabilities verificate.

---

## Tech Stack

| Layer | Choice | Why |
|-------|--------|-----|
| **OS** | Ubuntu Server 24.04 LTS | Stabile, LTS 5 anni, kernel 6.8 |
| **Runtime** | Python 3.11+ | Leggero (100MB), ecosystem ricco |
| **Bot framework** | python-telegram-bot | Maturo, polling (no webhook complesso) |
| **LLM** | DeepSeek API | Tuo choice, cloud-based |
| **Knowledge** | MCP Obsidian (read-only) | Vault integrato, zero friction |
| **Storage** | SQLite | Niente server, conversation logs |
| **Process mgr** | systemd | Auto-restart, logging nativo |

**Memory footprint:**
- Python: ~100 MB
- Dependencies: ~50 MB
- SQLite: ~10 MB
- **Free:** ~3.8 GB (ok per buffer + swap)

---

## Directory Structure

```
/home/serverino/bot/
├── main.py                    # Entry point
├── handlers/
│   ├── __init__.py
│   ├── telegram.py           # Polling, message routing
│   ├── deepseek.py          # DeepSeek API wrapper
│   └── obsidian_mcp.py      # MCP client (read Obsidian)
├── storage/
│   ├── __init__.py
│   └── db.py               # SQLite abstraction
├── config.py                # Settings, env vars
├── requirements.txt         # Dependencies
├── .env                     # Secrets (API keys, tokens) — .gitignore
├── systemd/
│   └── serverino-bot.service # Auto-start unit
└── README.md
```

---

## Message Flow (Detailed)

```
1. Telegram Polling
   └─ bot.polling() — long-poll for new messages
      
2. Message Handler (telegram.py)
   ├─ Receive update from Telegram
   ├─ Extract text, chat_id, user_id
   ├─ Validate (auth check if needed)
   └─ → obsidian_mcp.py
   
3. Obsidian Context (obsidian_mcp.py)
   ├─ Read skill/bot-persona.md
   │  └─ "You are a [role], respond like [style]"
   ├─ Read idee/bot-memory.md
   │  └─ "Recent conversations, user preferences"
   ├─ Optional: skill/bot-commands.md
   │  └─ "Available commands, help text"
   └─ Build context_block (markdown)
   
4. Prompt Builder
   ├─ system = context_block (from Obsidian)
   ├─ user_message = incoming text
   └─ Build final prompt
   
5. DeepSeek API Call (deepseek.py)
   ├─ POST /v1/chat/completions
   ├─ Payload: model, messages, temperature, max_tokens
   ├─ Retry logic (3x with backoff)
   └─ Response: text
   
6. Storage (db.py)
   ├─ INSERT conversation log
   │  └─ timestamp, user_id, prompt, response, tokens
   └─ SQLite on disk
   
7. Obsidian Update (async)
   ├─ APPEND to idee/bot-memory.md
   │  └─ "## [timestamp] — User: {msg}, Bot: {response}"
   └─ Sync to vault via git (optional)
   
8. Telegram Response
   └─ send_message(chat_id, response_text)
```

---

## Obsidian Integration — Filesystem Direct

**Architecture Change:** ~~MCP Obsidian~~ → Filesystem read (no GUI required)

**Why:** MCP Obsidian requires the Obsidian app running with REST plugin. On Ubuntu Server headless (no GUI), this is impossible. Instead, we read `.md` files directly from the local vault, synced via git.

**Vault Sync Strategy:**
```
Local Obsidian vault (Windows)
    ↓ git auto-sync every 10 min
GitHub (obsidian_sync repo)
    ↓ git pull every 7 min (cron in bot)
Ubuntu VM local path: /home/serverino/obsidian_vault/
    ↓ bot reads .md files
```

**Files che il bot legge ogni volta:**

### `skill/bot-persona.md`
```markdown
# Bot Persona

You are a helpful AI assistant with these characteristics:
- [your personality traits]
- [response style]
- [domain expertise]
- [constraints/rules]

## Behavior rules
- Respond in [language]
- Max response length: [N] chars
- Tone: [formal/casual/technical]
```

### `idee/bot-memory.md`
```markdown
# Bot Memory — Episodic Log

## 2026-06-15 20:30 UTC
**User:** "What's the capital of France?"
**Bot:** "Paris is the capital of France..."
**Tokens:** 42 in, 89 out

## 2026-06-15 20:31 UTC
**User:** "Tell me about its history"
**Bot:** "Paris has a rich history spanning..."
**Tokens:** 156 in, 234 out
```

### `skill/bot-commands.md` (optional, for future)
```markdown
# Available Commands

- `/status` — Bot health check
- `/memory` — Show recent conversations
- `/reset` — Clear context (new conversation)
- `/help` — Show this
```

**Why this design?**
- Edit Obsidian → bot changes behavior instantly
- No code deploy needed
- Conversational history in your vault
- Git-synced (backup, versioning)

---

## Configuration & Environment

**`.env` file** (NEVER commit):
```bash
# DeepSeek API
DEEPSEEK_API_KEY=sk_test_...
DEEPSEEK_MODEL=deepseek-v4-flash

# Telegram
TELEGRAM_BOT_TOKEN=123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11
TELEGRAM_CHAT_ID=987654321  # Your Telegram user ID (auth)

# MCP Obsidian
OBSIDIAN_VAULT_PATH=/home/serverino/obsidian_vault
MCP_SERVER_URL=http://localhost:3000  # If local MCP server

# Bot settings
LOG_LEVEL=INFO
DEEPSEEK_TEMPERATURE=0.7
DEEPSEEK_MAX_TOKENS=2000
POLLING_TIMEOUT=30
```

**`config.py` (skeleton)**:
```python
import os
from dotenv import load_dotenv

load_dotenv()

DEEPSEEK_API_KEY = os.getenv("DEEPSEEK_API_KEY")
DEEPSEEK_MODEL = os.getenv("DEEPSEEK_MODEL", "deepseek-v4-flash")

TELEGRAM_BOT_TOKEN = os.getenv("TELEGRAM_BOT_TOKEN")
TELEGRAM_CHAT_ID = int(os.getenv("TELEGRAM_CHAT_ID"))

OBSIDIAN_VAULT_PATH = os.getenv("OBSIDIAN_VAULT_PATH")
MCP_SERVER_URL = os.getenv("MCP_SERVER_URL", "http://localhost:3000")

DEEPSEEK_TEMPERATURE = float(os.getenv("DEEPSEEK_TEMPERATURE", "0.7"))
DEEPSEEK_MAX_TOKENS = int(os.getenv("DEEPSEEK_MAX_TOKENS", "2000"))
```

---

## Dependencies

**`requirements.txt`**:
```
python-telegram-bot==20.7
requests==2.31.0
deepseek==0.1.0  # DeepSeek SDK (TBD — verificare versione)
python-dotenv==1.0.0
SQLAlchemy==2.0.23  # ORM for SQLite
```

**Install:**
```bash
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
```

---

## Deployment on Ubuntu Server 24.04

### 1. System Setup
```bash
# SSH into Serverino
ssh user@serverino.local

# Create bot user (optional, security best practice)
sudo useradd -m -s /bin/bash serverino

# Create project directory
sudo mkdir -p /home/serverino/bot
sudo chown serverino:serverino /home/serverino/bot
cd /home/serverino/bot
```

### 2. Clone/Setup Code
```bash
# Git clone your repo (or manual upload)
git clone https://github.com/yourusername/serverino-bot.git .

# Virtual environment
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
```

### 3. Environment & Secrets
```bash
# Create .env (NEVER commit this)
cat > .env << EOF
DEEPSEEK_API_KEY=sk_...
TELEGRAM_BOT_TOKEN=...
TELEGRAM_CHAT_ID=...
OBSIDIAN_VAULT_PATH=/home/serverino/Documents/Secondo_Cervello
MCP_SERVER_URL=http://localhost:3000
EOF

# Restrict permissions
chmod 600 .env
```

### 4. Test Locally
```bash
# Verify imports, env loading
python3 -c "from config import *; print('OK')"

# Test Telegram connection
python3 main.py
# Should say "Bot is running..." and wait for messages
```

### 5. Systemd Service
**Create `/etc/systemd/system/serverino-bot.service`:**
```ini
[Unit]
Description=Serverino Bot — Telegram + DeepSeek
After=network-online.target
Wants=network-online.target

[Service]
Type=simple
User=serverino
WorkingDirectory=/home/serverino/bot
Environment="PATH=/home/serverino/bot/venv/bin"
ExecStart=/home/serverino/bot/venv/bin/python3 main.py
Restart=always
RestartSec=10
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
```

**Enable & start:**
```bash
sudo systemctl daemon-reload
sudo systemctl enable serverino-bot
sudo systemctl start serverino-bot

# Check status
sudo systemctl status serverino-bot
sudo journalctl -u serverino-bot -f  # Live logs
```

---

## MVP Scope & Constraints

### ✅ Included
- Text-only chat (Telegram)
- Single authenticated user (chat_id check)
- DeepSeek API calls with retry logic
- MCP Obsidian integration (read persona, memory, context)
- SQLite conversation logging
- Rate limiting (max N requests/minute to DeepSeek)
- Graceful error handling (timeouts, API errors)

### ❌ Out of scope (Phase 2)
- File uploads (PDF, txt)
- Image analysis (requires DeepSeek vision support — TBD)
- Voice/audio (requires external STT + DeepSeek audio support — TBD)
- Multi-user support
- Web dashboard
- Database replication

---

## Phase 2 Roadmap

| Feature | Effort | Dependency |
|---------|--------|-----------|
| `/commands` system | 🟢 Low | Config rewrite |
| Media preview (file/img) | 🟡 Medium | DeepSeek vision API |
| Voice input → text | 🟡 Medium | STT service (Whisper?) |
| Conversation context in DB | 🟡 Medium | Conversation threading |
| Multi-user support | 🔴 High | Auth layer, isolation |
| Web admin panel | 🔴 High | FastAPI, frontend |

---

## Error Handling & Edge Cases

| Scenario | Behavior |
|----------|----------|
| DeepSeek API timeout | Retry 3x with exponential backoff, then send "Service busy" |
| Telegram connection lost | Restart polling loop (systemd Restart=always) |
| Obsidian file not found | Log warning, use fallback persona |
| SQLite locked | Queue in-memory, retry on next cycle |
| Auth check fails (wrong chat_id) | Ignore message, log attempt |
| Rate limit hit | Queue message, send after delay |

---

## Testing Strategy

### Unit Tests (sqlite, deepseek, obsidian modules)
```bash
pytest tests/test_deepseek.py -v
pytest tests/test_obsidian_mcp.py -v
pytest tests/test_storage.py -v
```

### Integration Test (full flow, no real API)
```bash
# Mock DeepSeek API responses
python3 tests/integration_test.py
```

### Manual Test (real Telegram bot)
```bash
# Start bot locally
python3 main.py

# Send test message from Telegram
# Verify response, check logs
sudo journalctl -u serverino-bot -f
```

---

## Cost Estimation

**DeepSeek API:**
- ~$0.14 per 1M input tokens
- ~$0.28 per 1M output tokens
- **Estimate:** 100 messages/day × 200 tokens avg = ~$0.003/day ≈ $0.09/month (very cheap)

**Telegram Bot:** Free (just API usage)

**Serverino hardware:** Already bought (one-time)

**Total monthly:** ~$0.10 (negligible)

---

## Security Checklist

- [ ] `.env` in `.gitignore` (NO secrets in repo)
- [ ] Chat ID auth (only you can trigger bot)
- [ ] HTTPS for all external API calls (DeepSeek enforced)
- [ ] Rate limiting enabled (prevent cost runaway)
- [ ] SQLite file readable only by `serverino` user
- [ ] systemd service runs as non-root user
- [ ] No logging of sensitive data (API keys, tokens)
- [ ] MCP Obsidian credentials in `.env`, not hardcoded

---

## Next Steps

- [ ] Verify DeepSeek API Python SDK & pricing
- [ ] Setup MCP Obsidian client (confirm it works on Linux)
- [ ] Write skeleton `main.py` + handlers
- [ ] Test DeepSeek API call
- [ ] Test Telegram polling
- [ ] Deploy on Ubuntu Server 24.04
- [ ] Monitor for 48h, iterate

---

[[moc/Index MOC]] • [[progetti/Serverino/hardware]] • [[skill/Bot Deployment Playbook]]


---

## ADDENDUM — Doppio trigger: reattivo + proattivo (2026-06-22)

> Estensione al livello 2.5. Vedi [[progetti/Serverino/DEFINIZIONE_ASSISTENTE]] e [[progetti/Serverino/SPECS]] §17. Il design sopra resta valido: qui si aggiunge un secondo trigger al core loop.

### Core loop con due trigger

Il flusso originale gira sempre su input Telegram. Si aggiunge uno scheduler che parte da solo:

```
main.py (orchestratore)
 ├─ Thread A — on_message   (Telegram long-polling)
 │     └─> message handler  → context → DeepSeek → response   [flusso originale]
 │
 └─ Thread B — on_schedule  (tick ogni 60s)
       └─> scheduler.check_due()
             ├─ SELECT tasks WHERE stato='attiva' AND prossima_esecuzione <= now
             ├─ per ogni task dovuta: esegui skill → invia a Telegram
             └─ aggiorna ultima/prossima_esecuzione
```

I due thread condividono: SQLite (`tasks`, `logs`), il client Telegram (per inviare), il provider DeepSeek (se la skill ne ha bisogno). Accesso DB serializzato (SQLite lock + retry già previsto).

### Struttura file aggiornata (core "Serverino")
```
serverino/
├── main.py              # avvia Thread A + Thread B
├── core/
│   ├── channel.py       # Telegram (polling + send)
│   ├── provider.py      # call_llm() → DeepSeek
│   ├── context.py       # legge .md dal vault
│   ├── memory.py        # SQLite: logs + last-N
│   └── scheduler.py     # NUOVO: tick loop, check_due(), next_cron()
├── skills/
│   ├── __init__.py
│   ├── daily_brief.py   # esempio: meteo + agenda
│   └── ...              # nuova capability = nuovo file
├── config.py
└── systemd/serverino.service
```

### Mappatura sui 3 principi Nanobot (coerenza col design)
- **Core sottile** — `scheduler.py` chiama `skills/`, non sa cosa fanno.
- **Capability via skill** — ogni azione schedulabile è una skill, zero `if` nel core.
- **Config-driven** — le task vivono in SQLite (runtime), non hardcoded.

### Skill: contratto minimo
Ogni skill espone una funzione uniforme, così scheduler e chat la chiamano allo stesso modo:
```python
def run(payload: dict, ctx: Context) -> str:
    """Esegue la capability, ritorna testo da inviare a Telegram."""
```
- Chiamata da **chat**: l'LLM decide quale skill e con che payload.
- Chiamata da **scheduler**: la task ha già `azione` + `payload` salvati.

### Confine di sicurezza
Lo scheduler esegue **solo** task in stato `attiva`. Una task nasce `proposta` e diventa `attiva` solo dopo `/conferma` dell'utente (handshake in [[progetti/Serverino/SPECS]] §17.3). Niente azione proattiva senza approvazione pregressa.
