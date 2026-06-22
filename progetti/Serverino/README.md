# Serverino Bot

🤖 Personal AI agent on Serverino. Reads Git repos as context, responds via Telegram using DeepSeek API.

**Status:** MVP Development  
**Framework:** Nanobot v0.2.1+  
**LLM:** DeepSeek (cloud API)  
**Hardware:** AMD A9-9420e, 4GB RAM, 128GB SSD (Ubuntu Server 24.04)

---

## Quick Links

| Resource | Purpose | Link |
|----------|---------|------|
| **Nanobot Home** | Framework overview | https://nanobot.wiki/home |
| **Nanobot Docs** | Installation & config | https://nanobot.wiki/docs/latest |
| **Nanobot GitHub** | Source code & issues | https://github.com/HKUDS/nanobot |
| **Nanobot PyPI** | Install command | https://pypi.org/project/nanobot-ai/ |
| **DeepSeek API** | LLM provider | https://platform.deepseek.com |
| **Telegram Bot API** | Chat channel | https://core.telegram.org/bots |
| **GitHub CLI** | Git operations | https://cli.github.com/ |

---

## What It Does

```
User (Telegram)
    ↓
Bot reads Git repos
    ├─ Repo principale (obsidian_sync) — always available
    └─ Repo temporanea (from message) — one-time read, then forget
    ↓
Combine context from repos + user message
    ↓
Call DeepSeek API
    ↓
Respond via Telegram
```

**Features (MVP):**
- ✅ Text-only chat via Telegram
- ✅ Reads `.md` files from ONE configured Git repo (principal)
- ✅ Auto-pull main repo every 7 minutes
- ✅ Built-in memory (via `.md` files in vault)
- ✅ Auto-restart on failure (systemd)

**Explicitly OUT OF SCOPE (MVP):**
- ❌ Dynamic git clone from user message (disk bomb risk, storage validation complex)
- ❌ Multi-repo "scope multipli" (Phase 2)

**Future (Phase 2+):**
- ⏳ Multi-repo support (with whitelist + size limits)
- ⏳ File/image attachments
- ⏳ Voice input
- ⏳ Custom commands (`/status`, `/recap`, etc.)

---

## Installation

### Prerequisites
- Python 3.11+
- Ubuntu Server 24.04 (or any Linux)
- Git installed
- DeepSeek API key
- Telegram bot token

### 1. Install Nanobot
```bash
python -m pip install nanobot-ai
```

### 2. Initialize
```bash
nanobot onboard --wizard
# Configure:
# - Provider: DeepSeek (api_key: sk_...)
# - Channel: Telegram (bot_token: ..., chat_id: ...)
# - Model: deepseek-v4-flash
```

### 3. Clone this repo (when available)
```bash
git clone https://github.com/yourusername/serverino-bot.git
cd serverino-bot
pip install -r requirements.txt
```

### 4. Configure main repo
Edit `~/.nanobot/config.json` or `config.json.example`:

```json
{
  "skills": {
    "git_context_reader": {
      "enabled": true,
      "mainRepo": "https://github.com/Chirone23/obsidian_sync.git",
      "pullInterval": 420
    }
  }
}
```

### 5. Deploy (systemd)
```bash
sudo cp systemd/nanobot-serverino.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable nanobot-serverino
sudo systemctl start nanobot-serverino
```

---

## Directory Structure

```
serverino-bot/
├── README.md (this file)
├── config.json.example
├── requirements.txt
├── skills/
│   └── git_context_reader.py (custom skill)
├── systemd/
│   └── nanobot-serverino.service
└── .gitignore
```

---

## Configuration

### Minimal Config (`~/.nanobot/config.json`)

```json
{
  "providers": {
    "deepseek": {
      "apiKey": "sk_your_key_here",
      "model": "deepseek-v4-flash",
      "temperature": 0.7,
      "maxTokens": 2000
    }
  },
  "channels": {
    "telegram": {
      "botToken": "123456789:ABCdefGHIjklMNOpqrst-UVWxyz",
      "chatId": 987654321
    }
  },
  "skills": {
    "git_context_reader": {
      "enabled": true,
      "mainRepo": "https://github.com/Chirone23/obsidian_sync.git",
      "pullInterval": 420,
      "localPath": "/tmp/serverino-repos"
    }
  }
}
```

### Where to find files

| Config | Location | Edit with |
|--------|----------|-----------|
| Nanobot config | `~/.nanobot/config.json` | Text editor |
| This repo config | `./config.json.example` | Copy + customize |
| Skill code | `./skills/git_context_reader.py` | Python IDE |
| Systemd service | `/etc/systemd/system/nanobot-serverino.service` | `sudo` + editor |
| Bot logs | `journalctl -u nanobot-serverino -f` | (auto, read-only) |
| Nanobot workspace | `~/.nanobot/workspace/` | (auto, don't edit) |

---

## Usage

### Start Bot
```bash
nanobot run
```

Or (if deployed via systemd):
```bash
sudo systemctl start nanobot-serverino
sudo journalctl -u nanobot-serverino -f  # Watch logs
```

### Send Message in Telegram
```
User: "Hi, analyze this repo: https://github.com/user/project.git"

Bot:
1. Clones/pulls https://github.com/user/project.git
2. Reads .md files from project
3. Combines context (obsidian_sync + project)
4. Calls DeepSeek
5. Responds
6. Forgets project (temp repo)
```

### Check Status
```bash
sudo systemctl status nanobot-serverino
nanobot --version
```

---

## Troubleshooting

### "Bot not responding"
```bash
# Check logs
sudo journalctl -u nanobot-serverino -f

# Check config
cat ~/.nanobot/config.json | grep -i deepseek

# Test DeepSeek API key
curl -H "Authorization: Bearer sk_your_key" https://api.deepseek.com/v1/models
```

### "Repository not found / git clone fails"
```bash
# Check main repo URL
grep "mainRepo" ~/.nanobot/config.json

# Test git access
git clone https://github.com/Chirone23/obsidian_sync.git /tmp/test
rm -rf /tmp/test
```

### "Telegram message not received"
```bash
# Verify chat ID and bot token
grep -A2 "telegram" ~/.nanobot/config.json

# Check if bot is actually polling
sudo journalctl -u nanobot-serverino | grep "polling\|telegram"
```

### "Out of memory / high CPU"
```bash
# Monitor resource usage
top -p $(pgrep -f nanobot)

# Check if repo is too large
du -sh ~/.nanobot/workspace/

# Consider smaller main repo or increase swap
free -h
```

---

## FAQ

**Q: How do I find where X is configured?**  
A: Check the table above ("Where to find files"). If still unsure, check these links:
- Nanobot config: https://nanobot.wiki/docs/latest/configuration
- Skill development: https://nanobot.wiki/docs/latest/developers/skill-development
- Systemd: `man systemd.service`

**Q: Can I use a private repo?**  
A: Yes, configure GitHub SSH or token in git config. Nanobot will inherit git's auth.

**Q: What if the main repo is huge (1GB+)?**  
A: It'll slow down pulls and eat disk space. Consider:
- Smaller repo
- Shallow clone (`--depth 1`)
- Selective folder sync

**Q: Can I have multiple main repos?**  
A: Not in MVP. Use temp repos (one per message) instead. See "Usage" section.

**Q: How do I update Nanobot?**  
A: 
```bash
pip install --upgrade nanobot-ai
sudo systemctl restart nanobot-serverino
```

**Q: Where are bot logs stored?**  
A: In journalctl (systemd). View with:
```bash
sudo journalctl -u nanobot-serverino -n 100  # Last 100 lines
sudo journalctl -u nanobot-serverino --since today
```

**Q: Can I run multiple bots on the same server?**  
A: Yes, create separate Nanobot workspaces:
```bash
NANOBOT_WORKSPACE=/home/serverino/.nanobot-bot2 nanobot run
```

---

## Development

### Edit Custom Skill
```bash
nano skills/git_context_reader.py
# Make changes, restart bot
sudo systemctl restart nanobot-serverino
```

### View Skill Logs
```bash
sudo journalctl -u nanobot-serverino -f | grep git_context_reader
```

### Test Config
```bash
nanobot config validate
```

---

## Deployment Checklist

- [ ] Python 3.11+ installed
- [ ] Nanobot installed from PyPI
- [ ] DeepSeek API key valid
- [ ] Telegram bot token valid
- [ ] Main repo URL accessible
- [ ] `~/.nanobot/config.json` configured
- [ ] Custom skill installed in `~/.nanobot/workspace/skills/`
- [ ] Systemd service file copied and enabled
- [ ] Bot starts without errors: `sudo systemctl start nanobot-serverino`
- [ ] Telegram message received and responded to
- [ ] Logs look healthy: `sudo journalctl -u nanobot-serverino -n 20`

---

## Resources

**Nanobot:**
- Docs: https://nanobot.wiki/docs/latest
- GitHub: https://github.com/HKUDS/nanobot
- Issues: https://github.com/HKUDS/nanobot/issues

**DeepSeek:**
- API Docs: https://platform.deepseek.com/docs
- Pricing: https://platform.deepseek.com/pricing

**Telegram:**
- Bot API: https://core.telegram.org/bots/api
- BotFather: https://t.me/botfather

**Git/Linux:**
- Git Docs: https://git-scm.com/doc
- Systemd: `man systemd.service`
- Ubuntu Server: https://ubuntu.com/server

---

## License

MIT (or your choice)

---

## Contact / Support

For issues with:
- **Nanobot framework:** https://github.com/HKUDS/nanobot/issues
- **This bot:** (your repo issues)
- **DeepSeek API:** https://platform.deepseek.com/support

---

Last updated: 2026-06-15
