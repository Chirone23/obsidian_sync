# Bot Deployment Playbook — Serverino

**Audience:** Primo deployment di Serverino Bot  
**Time:** ~2 ore (setup + test)  
**Prerequisiti:** Ubuntu Server 24.04 already installed, SSH access

---

## Phase 1: System Preparation (20 min)

### 1.1 SSH into Serverino
```bash
ssh user@serverino.local
# oppure IP if hostname non funziona
ssh user@192.168.1.X
```

### 1.2 Update system & install Python
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y python3.11 python3-pip python3-venv git
python3 --version  # Verify 3.11+
```

### 1.3 Create bot user (optional, security)
```bash
sudo useradd -m -s /bin/bash serverino
sudo usermod -aG sudo serverino  # If needs sudo access
su - serverino  # Switch to bot user
```

### 1.4 Setup directories
```bash
# As serverino user
mkdir -p ~/bot ~/obs  # ~/obs for Obsidian vault later
cd ~/bot
pwd  # Should show /home/serverino/bot
```

---

## Phase 2: Code & Environment Setup (30 min)

### 2.1 Clone/setup repository
**Option A: From GitHub**
```bash
cd ~/bot
git clone https://github.com/yourusername/serverino-bot.git .
git checkout main
```

**Option B: Manual (if no repo yet)**
```bash
# Create skeleton manually, we'll write code next
touch main.py requirements.txt config.py .env .gitignore
```

### 2.2 Create Python virtual environment
```bash
cd ~/bot
python3 -m venv venv
source venv/bin/activate
# Prompt should show (venv)
```

### 2.3 Install dependencies
```bash
pip install --upgrade pip
# Create requirements.txt first (see below)
pip install -r requirements.txt
```

**`requirements.txt` (copy this):**
```
python-telegram-bot==20.7
requests==2.31.0
python-dotenv==1.0.0
SQLAlchemy==2.0.23
pydantic==2.5.0
```

### 2.4 Create `.env` file (SECRETS)
```bash
cat > .env << 'EOF'
# DeepSeek
DEEPSEEK_API_KEY=sk_your_actual_key_here
DEEPSEEK_MODEL=deepseek-chat

# Telegram
TELEGRAM_BOT_TOKEN=123456789:ABCdefGHIjklMNOpqrst-UVWxyz
TELEGRAM_CHAT_ID=987654321

# Obsidian MCP
OBSIDIAN_VAULT_PATH=/home/serverino/Secondo_Cervello
MCP_SERVER_URL=http://localhost:3000

# Bot settings
LOG_LEVEL=INFO
DEEPSEEK_TEMPERATURE=0.7
DEEPSEEK_MAX_TOKENS=2000
EOF

chmod 600 .env
cat .env  # Verify contents (should show your keys)
```

**How to get values:**
- `DEEPSEEK_API_KEY`: Platform DeepSeek → API keys → copy
- `TELEGRAM_BOT_TOKEN`: [@BotFather](https://t.me/botfather) on Telegram → `/newbot` → copy token
- `TELEGRAM_CHAT_ID`: Manda `/start` al tuo bot → estrai ID dai logs o da [questa API](https://api.telegram.org/bot123456789:ABC/getUpdates)
- `OBSIDIAN_VAULT_PATH`: Percorso al tuo vault (es. `/home/serverino/Documents/Secondo_Cervello`)

### 2.5 Create `.gitignore`
```bash
cat > .gitignore << 'EOF'
.env
.env.local
venv/
__pycache__/
*.pyc
.DS_Store
*.db
logs/
EOF
```

---

## Phase 3: Code Skeleton & Testing (30 min)

### 3.1 Create `config.py`
```python
import os
from dotenv import load_dotenv

load_dotenv()

# DeepSeek
DEEPSEEK_API_KEY = os.getenv("DEEPSEEK_API_KEY")
DEEPSEEK_MODEL = os.getenv("DEEPSEEK_MODEL", "deepseek-chat")
DEEPSEEK_TEMPERATURE = float(os.getenv("DEEPSEEK_TEMPERATURE", "0.7"))
DEEPSEEK_MAX_TOKENS = int(os.getenv("DEEPSEEK_MAX_TOKENS", "2000"))

# Telegram
TELEGRAM_BOT_TOKEN = os.getenv("TELEGRAM_BOT_TOKEN")
TELEGRAM_CHAT_ID = int(os.getenv("TELEGRAM_CHAT_ID", "0"))

# Obsidian
OBSIDIAN_VAULT_PATH = os.getenv("OBSIDIAN_VAULT_PATH")
MCP_SERVER_URL = os.getenv("MCP_SERVER_URL", "http://localhost:3000")

# Bot
LOG_LEVEL = os.getenv("LOG_LEVEL", "INFO")

print(f"[CONFIG] Bot initialized: {TELEGRAM_BOT_TOKEN[:10]}...")
```

### 3.2 Create `main.py` (MVP skeleton)
```python
#!/usr/bin/env python3
import logging
from telegram import Update
from telegram.ext import Application, CommandHandler, MessageHandler, filters, ContextTypes
from config import TELEGRAM_BOT_TOKEN

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Handle /start command"""
    await update.message.reply_text("🤖 Serverino Bot online! Send me a message.")

async def handle_message(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Handle incoming messages"""
    user_message = update.message.text
    logger.info(f"📨 Received: {user_message}")
    
    # TODO: Call DeepSeek API
    response = f"Echo: {user_message}"  # Placeholder
    
    await update.message.reply_text(response)

def main():
    logger.info("🚀 Starting Serverino Bot...")
    
    app = Application.builder().token(TELEGRAM_BOT_TOKEN).build()
    
    app.add_handler(CommandHandler("start", start))
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, handle_message))
    
    logger.info("⏳ Polling for messages...")
    app.run_polling()

if __name__ == "__main__":
    main()
```

### 3.3 Test basic connectivity
```bash
# Verify imports work
python3 -c "from config import *; from telegram import *; print('✓ Imports OK')"

# Start bot (will run until Ctrl+C)
python3 main.py
```

**Expected output:**
```
INFO:__main__:🚀 Starting Serverino Bot...
INFO:__main__:⏳ Polling for messages...
```

### 3.4 Test from Telegram
1. Open Telegram
2. Search for your bot (created via @BotFather)
3. Send `/start`
4. Send "test message"
5. Should reply "Echo: test message"
6. Check logs on Serverino for `📨 Received: test message`

**If not working:**
```bash
# Check .env loaded correctly
grep TELEGRAM_BOT_TOKEN .env

# Verify bot token is valid (should start with numbers:AB...)
echo $TELEGRAM_BOT_TOKEN

# Check network connectivity
curl -I https://api.telegram.org
```

---

## Phase 4: Systemd Service Setup (20 min)

### 4.1 Create systemd service file
```bash
sudo tee /etc/systemd/system/serverino-bot.service > /dev/null << 'EOF'
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
EOF

sudo systemctl daemon-reload
sudo systemctl enable serverino-bot
```

### 4.2 Start service
```bash
sudo systemctl start serverino-bot
sudo systemctl status serverino-bot

# Should show: Active (running)
```

### 4.3 Monitor logs
```bash
# Live logs (Ctrl+C to exit)
sudo journalctl -u serverino-bot -f

# Last 50 lines
sudo journalctl -u serverino-bot -n 50

# Today's logs
sudo journalctl -u serverino-bot --since today
```

### 4.4 Test restart
```bash
# Stop service
sudo systemctl stop serverino-bot
sleep 2
sudo systemctl status serverino-bot  # Should show inactive

# Restart
sudo systemctl restart serverino-bot
sudo systemctl status serverino-bot  # Should show active

# Send Telegram message — should work (auto-restarted)
```

---

## Phase 5: Integration with Obsidian (30 min)

### 5.1 Prepare Obsidian notes
SSH into Serverino and create bot-related notes in your vault:

```bash
cd ~/Secondo_Cervello/skill
cat > bot-persona.md << 'EOF'
# Bot Persona

You are a helpful AI assistant specialized in:
- Answering questions
- Providing code examples
- Technical guidance

## Behavior
- Respond concisely (max 2000 chars)
- Be professional but friendly
- Admit when you don't know
EOF

cat > bot-memory.md << 'EOF'
# Bot Memory — Conversation Log

## Session started: 2026-06-15 20:00 UTC
Waiting for first message...
EOF

git add bot-persona.md bot-memory.md
git commit -m "Bot: initial persona and memory notes"
git push
```

### 5.2 Verify MCP Obsidian connectivity
From Serverino:
```bash
# Test reading a note
python3 << 'PYEOF'
import os
vault_path = "/home/serverino/Secondo_Cervello"
persona_file = f"{vault_path}/skill/bot-persona.md"

if os.path.exists(persona_file):
    with open(persona_file, "r") as f:
        print(f.read()[:200])
    print("\n✓ Obsidian note readable")
else:
    print("✗ File not found")
PYEOF
```

---

## Phase 6: Production Checklist

Before considering bot "live":

- [ ] `.env` file created with actual API keys
- [ ] DeepSeek API key validated (test call works)
- [ ] Telegram bot token validated (polling active)
- [ ] Systemd service starts correctly
- [ ] Logs visible via `journalctl`
- [ ] Telegram messages → bot → response (echo test passed)
- [ ] Obsidian vault readable from bot
- [ ] Swap enabled on SSD (for 4GB RAM safety)
- [ ] No secrets in git history

```bash
# Verify no secrets leaked
git log --grep="API_KEY\|TOKEN" --oneline
git grep "sk_\|123:" -- '*.py' '*.md'  # Should be empty
```

---

## Troubleshooting

| Problem | Diagnosis | Fix |
|---------|-----------|-----|
| Bot not responding | Check logs: `journalctl -u serverino-bot -f` | Likely API key issue or token invalid |
| `ModuleNotFoundError: telegram` | Venv not activated | `source venv/bin/activate` |
| `DEEPSEEK_API_KEY not found` | `.env` not in right location | Verify: `cat .env` from ~/bot directory |
| `Connection refused` to DeepSeek | Network issue or API key wrong | `curl https://api.deepseek.com` + check key |
| Systemd service won't start | Permission issue | Check logs: `sudo systemctl status serverino-bot` |
| High RAM usage (>1GB) | Memory leak or large response | Monitor: `top -p $(pgrep -f main.py)` |

---

## Next Steps

1. ✅ Complete Phase 1-6 above
2. ⏳ Integrate DeepSeek API calls (replace echo placeholder)
3. ⏳ Integrate MCP Obsidian context reading
4. ⏳ Setup conversation logging to SQLite
5. ⏳ Monitor for 48h, iterate

---

**Estimated total time:** ~2 hours (first deploy)  
**Maintenance:** ~5 min/week (logs, monitoring)

[[progetti/Serverino/bot-architecture]] • [[moc/Index MOC]]
