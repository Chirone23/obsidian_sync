# Serverino Bot — Nanobot Setup

**Status:** Base framework selection confirmed  
**Framework:** Nanobot v0.2.1+  
**Installation method:** PyPI (stable, production-ready)

---

## Links

| Resource | URL |
|----------|-----|
| **Home** | https://nanobot.wiki/home |
| **Docs** | https://nanobot.wiki/docs/0.2.1/getting-started/nanobot-overview |
| **GitHub** | https://github.com/HKUDS/nanobot |
| **PyPI** | https://pypi.org/project/nanobot-ai/ |

---

## Installation

### Install from PyPI (Recommended for production)

```bash
python -m pip install nanobot-ai
```

**Why PyPI?**
- ✅ Stable day-to-day experience
- ✅ Tested releases (not bleeding-edge)
- ✅ Easy updates (`pip install --upgrade nanobot-ai`)
- ✅ Production-ready for 24/7 Serverino deployment
- ❌ Not newest features (use source if needed)

### Alternatives (not recommended for MVP)

**From source (newest features, experiments):**
```bash
git clone https://github.com/HKUDS/nanobot.git
cd nanobot
python -m pip install -e .
```

**With uv (modern package manager):**
```bash
uv tool install nanobot-ai
```

---

## What Nanobot Provides (out of box)

✅ **Chat channels:** Telegram, Discord, Slack, Feishu, Email, WeChat, etc.  
✅ **LLM providers:** OpenAI, Claude, DeepSeek, Gemini, Kimi, etc.  
✅ **Memory system:** Persistent conversation context, goals  
✅ **Tools/Skills:** Built-in tools + custom skill framework  
✅ **MCP support:** Model Context Protocol integration  
✅ **WebUI:** Bundled browser interface  
✅ **Automation:** Scheduled tasks, cron, webhooks  
✅ **Deployment:** systemd-ready, Docker support  

---

## What We Build Custom

❌ ~~Bot framework~~ (Nanobot provides this)  
❌ ~~Telegram integration~~ (Nanobot provides this)  
❌ ~~LLM API wrapper~~ (Nanobot provides this)  
❌ ~~Memory system~~ (Nanobot provides this)  

✅ **Custom skill:** Read Git repos as context  
✅ **Configuration:** Set provider (DeepSeek), channel (Telegram), model  
✅ **Optional MCP integration:** If needed for Obsidian access  

---

## Nanobot Architecture (High-level)

```
User (Telegram)
    ↓
Channel Plugin (Nanobot's Telegram handler)
    ↓
Agent Core Loop
    ├─ Parse message
    ├─ Run skills (built-in + custom)
    ├─ Build prompt + context
    ├─ Call LLM (DeepSeek via Nanobot provider)
    ├─ Memory system (persist conversation)
    └─ Send response via channel
    ↓
User (Telegram response)
```

---

## Configuration Structure

Nanobot uses `~/.nanobot/config.json`:

```json
{
  "providers": {
    "deepseek": {
      "apiKey": "sk_...",
      "model": "deepseek-chat"
    }
  },
  "channels": {
    "telegram": {
      "botToken": "123456789:ABCDEF...",
      "chatId": 987654321
    }
  },
  "skills": {
    "git_context_reader": {
      "enabled": true,
      "repoUrls": ["https://github.com/Chirone23/obsidian_sync.git"],
      "pullInterval": 420  // 7 minutes
    }
  },
  "memory": {
    "type": "file",
    "path": "~/.nanobot/workspace/memory"
  }
}
```

---

## Deployment on Serverino (Ubuntu Server 24.04)

### 1. SSH & Python setup
```bash
ssh user@serverino.local
python3 --version  # Should be 3.11+
```

### 2. Create virtual environment
```bash
cd ~/nanobot-workspace
python3 -m venv venv
source venv/bin/activate
```

### 3. Install Nanobot
```bash
pip install --upgrade pip
pip install nanobot-ai
nanobot --version  # Verify install
```

### 4. Initialize config
```bash
nanobot onboard --wizard
# Follow wizard to set:
# - Provider: DeepSeek
# - API key: sk_...
# - Channel: Telegram
# - Bot token: 123456789:ABCDEF...
# - Chat ID: 987654321
```

### 5. Create custom skill (git_context_reader)
**Location:** `~/.nanobot/workspace/skills/git_context_reader.py`

(To be defined in next phase)

### 6. Systemd service
**File:** `/etc/systemd/system/nanobot-serverino.service`

```ini
[Unit]
Description=Serverino Bot — Nanobot + DeepSeek + Git context
After=network-online.target
Wants=network-online.target

[Service]
Type=simple
User=serverino
WorkingDirectory=/home/serverino/nanobot-workspace
Environment="PATH=/home/serverino/nanobot-workspace/venv/bin"
ExecStart=/home/serverino/nanobot-workspace/venv/bin/nanobot run
Restart=always
RestartSec=10
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
```

### 7. Enable & start
```bash
sudo systemctl daemon-reload
sudo systemctl enable nanobot-serverino
sudo systemctl start nanobot-serverino
sudo systemctl status nanobot-serverino

# Monitor logs
sudo journalctl -u nanobot-serverino -f
```

---

## Feature Set (MVP vs Future)

### MVP (Day 1)
- ✅ Telegram polling
- ✅ Text-only chat with DeepSeek
- ✅ Single user (chat_id auth)
- ✅ Built-in memory
- ✅ `/start` command (new session)

### Phase 2 (Roadmap)
- ⏳ Custom skill: Git repo context reader
- ⏳ Multi-repo support (scope multipli)
- ⏳ MCP integration (Obsidian access via MCP)
- ⏳ Custom commands (`/recap`, `/status`, etc.)

### Phase 3 (Future)
- ⏳ File attachments
- ⏳ Image uploads/analysis
- ⏳ Voice input (Telegram voice messages)

---

## Key Differences from Hand-Built Bot

| Aspect | Hand-built (SPECS.md) | Nanobot |
|--------|----------------------|---------|
| **Code to write** | ~1000+ lines | ~200 lines (skill only) |
| **Deployment** | Custom systemd + venv | Nanobot's built-in |
| **Memory** | SQLite custom | Nanobot's token-based |
| **Error handling** | Manual retry logic | Built-in resilience |
| **LLM providers** | Only DeepSeek | 20+ providers |
| **Channels** | Only Telegram | 10+ channels |
| **Maintenance** | High (bug fixes, updates) | Low (upstream handles) |

---

## Next Steps

1. ✅ Confirm PyPI installation method
2. ⏳ Write custom skill: `git_context_reader`
   - Fetch Git repos on interval
   - Extract .md files as context
   - Integrate with Nanobot skill system
3. ⏳ Define config template for Serverino
4. ⏳ Deploy on Ubuntu Server 24.04
5. ⏳ Test Telegram → DeepSeek → Response flow

---

## Resources

- **Nanobot docs:** https://nanobot.wiki/docs/latest/
- **Custom skill development:** https://nanobot.wiki/docs/latest/developers/skill-development
- **Configuration reference:** https://nanobot.wiki/docs/latest/configuration
- **Troubleshooting:** https://github.com/HKUDS/nanobot/issues

---

[[progetti/Serverino/hardware]] • [[progetti/Serverino/bot-architecture]] • [[skill/Bot Deployment Playbook]]
