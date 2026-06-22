# Serverino VMware Setup — Guida Operativa

**Obiettivo:** Creare VM Ubuntu Server 26.04 LTS su VMware, installare Nanobot, testare bot Telegram.

**Host:** HP EliteBook 835 G8 (16GB RAM, 6-core CPU, Windows 11 Pro)  
**VM:** serverino-dev (4 vCPU, 6GB RAM, 40GB disk, Ubuntu 26.04 LTS)

---

## FASE 1: Crea VM su VMware

### Step 1.1 — Apri VMware Workstation Pro

- Start menu → VMware Workstation Pro
- Oppure da CLI (PowerShell admin):
  ```powershell
  & "C:\Program Files (x86)\VMware\VMware Workstation\vmware.exe"
  ```

### Step 1.2 — Nuova VM

```
File → New Virtual Machine
↓
Typical (non Custom)
↓
Installer disc image file (ISO)
  → Seleziona: ubuntu-24.04-live-server-amd64.iso
  ↓
  Se non ce l'hai, scaricala:
  https://releases.ubuntu.com/24.04/ubuntu-24.04-live-server-amd64.iso
↓
Easy Install Information:
  Full Name: serverino
  User name: serverino
  Password: [scegli password sicura]
↓
Virtual Machine Name: serverino-dev
Location: C:\VMs\serverino-dev (oppure default)
↓
Disk Size: 40 GB
  ☑ Store virtual disk as single file
↓
Customize Hardware (IMPORTANTE):
  Processors: 4
  Memory: 6 GB
  Network: Bridged (importante per Telegram polling realistico)
  ☑ USB Controller
  [Remove Printer, Sound, etc. se vuoi minimale]
↓
Finish
```

### Step 1.3 — Verifica Specs in VMware

VM appena creata nella libreria:
```
Right-click serverino-dev
↓
Settings
↓
Hardware:
  ✓ CPUs: 4
  ✓ RAM: 6 GB
  ✓ Disk: 40 GB
  ✓ Network Adapter: Bridged
  ✓ Sound Card: (Removable se non serve)
↓
OK
```

---

## FASE 2: Boot VM e Installa Ubuntu Server

### Step 2.1 — Power on VM

```
Right-click serverino-dev
↓
Power On
```

VM aprirà una finestra e avvierà boot da ISO.

### Step 2.2 — Ubuntu Server Installation

La schermata di install è interattiva. Segui così:

```
Language: English (oppure Italiano, non importa)
↓
Keyboard layout: (default va bene)
↓
Network: DHCP (Bridged fornisce IP automaticamente)
  Aspetta che rilevi IP (es. 192.168.1.X)
↓
Configure proxy: (skip, premere Enter)
↓
Mirror: (default ok)
↓
Guided - Use entire disk
  Storage configuration: LVM (default)
  Conferma: YES, erase disk
↓
File system setup:
  ✓ ext4 (standard)
  ↓
  Continue
↓
Profile setup:
  Full name: serverino
  Server name: serverino-dev
  Username: serverino
  Password: (scegli e ricorda!)
  ☑ Install OpenSSH server (IMPORTANTE!)
↓
Featured Server Snaps: (skip tutti, non servono)
↓
Installation Summary: Review
  → Conferma: Done
↓
System installs... aspetta 5-10 minuti
↓
Installation complete
  ☑ Reboot now
```

VM riavvia e mostra login prompt:

```
serverino-dev login: _
```

### Step 2.3 — Login prima volta

```
Login: serverino
Password: [quella scelta sopra]

serverino@serverino-dev:~$
```

✓ Ubuntu Server installato. Pronto per SSH.

---

## FASE 3: Configura VM da Host (SSH)

### Step 3.1 — Trova IP della VM

Dentro VM:

```bash
ip addr show
```

Output simile a:

```
...
2: eth0: <BROADCAST,MULTICAST,UP,LOWER_UP>
    inet 192.168.1.123/24 brd 192.168.1.255
...
```

**Ricorda:** `192.168.1.123` (il tuo IP sarà diverso)

### Step 3.2 — SSH dalla host (EliteBook)

PowerShell (sulla host, non nella VM):

```powershell
ssh serverino@192.168.1.123
# Rimpiazza 192.168.1.123 con l'IP trovato sopra

# Primo login chiede:
# The authenticity of host '192.168.1.123' can't be established...
# Type 'yes' and press Enter

# Poi chiede password (quella scelta durante install)
```

✓ Sei dentro la VM via SSH.

```
serverino@serverino-dev:~$
```

---

## FASE 4: Setup Python + Nanobot nella VM

### Step 4.1 — Update system

```bash
sudo apt update && sudo apt upgrade -y
```

Aspetta che finisca (3-5 minuti).

### Step 4.2 — Installa Python 3.11+

```bash
sudo apt install -y python3.11 python3-pip python3-venv git curl
```

Verifica:

```bash
python3 --version
# Output: Python 3.11.x (oppure 3.12+)

pip --version
# Output: pip X.X from /usr/lib/python3.X/dist-packages
```

### Step 4.3 — Crea workspace

```bash
mkdir -p ~/nanobot-workspace
cd ~/nanobot-workspace
pwd
# Output: /home/serverino/nanobot-workspace
```

### Step 4.4 — Virtual environment

```bash
python3 -m venv venv
source venv/bin/activate

# Prompt cambia a:
# (venv) serverino@serverino-dev:~/nanobot-workspace$
```

### Step 4.5 — Installa Nanobot da PyPI

```bash
pip install --upgrade pip
pip install nanobot-ai
```

Aspetta 2-3 minuti (download e install).

Verifica:

```bash
nanobot --version
# Output: nanobot X.X.X
```

✓ Nanobot installato.

---

## FASE 5: Configura Nanobot (Wizard)

### Step 5.1 — Avvia wizard

Sempre dentro venv:

```bash
nanobot onboard --wizard
```

Rispondere alle domande così:

```
Welcome to Nanobot Onboard!

[Provider Setup]
What's your primary LLM provider? 
→ Scegli: deepseek
  API Key: sk_your_deepseek_api_key_here
  (Sostituisci con la tua chiave da https://platform.deepseek.com)

[Channel Setup]
What's your primary chat channel?
→ Scegli: telegram
  Bot Token: 123456789:ABCdefGHIjklMNOpqrst-UVWxyz
  (Sostituisci con il token da @BotFather)
  
  Chat ID: 987654321
  (Sostituisci con il tuo Telegram user ID)
  
  Puoi trovarla: manda /start al bot, controlla i log, troverai chat_id

[Config Save]
Configuration saved to: ~/.nanobot/config.json
```

✓ Config salvato.

### Step 5.2 — Verifica config

```bash
cat ~/.nanobot/config.json | head -30
```

Dovresti vedere:

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
      "botToken": "123456789:ABC...",
      "chatId": 987654321
    }
  },
  ...
}
```

---

## FASE 6: Test Nanobot

### Step 6.1 — Avvia bot

```bash
nanobot run
```

Aspetta che dica:

```
[INFO] Nanobot initialized
[INFO] Providers: deepseek
[INFO] Channels: telegram
[INFO] Polling for messages...
```

**NON chiudere questo terminale** — il bot è in polling.

### Step 6.2 — Test da Telegram

Apri Telegram sul tuo telefono/desktop:

```
Apri chat con il bot (creato via @BotFather)
↓
Scrivi: "Ciao bot"
↓
Aspetta 2-5 secondi
↓
Bot dovrebbe rispondere con qualcosa da DeepSeek
```

Nel terminale SSH dovrebbe compare:

```
[INFO] Message received from chat_id: 987654321
[INFO] Calling deepseek-chat...
[DEBUG] Prompt sent (156 tokens)
[DEBUG] Response received (234 tokens)
[INFO] Response sent to telegram
```

✓ **Bot funziona!**

### Step 6.3 — Stop bot

```
Premi Ctrl+C nel terminale
```

---

## FASE 7: Rendi Bot Persistente (Systemd)

### Step 7.1 — Crea systemd service

```bash
sudo tee /etc/systemd/system/nanobot-dev.service > /dev/null << 'EOF'
[Unit]
Description=Serverino Bot Development
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
EOF
```

### Step 7.2 — Abilita e avvia service

```bash
sudo systemctl daemon-reload
sudo systemctl enable nanobot-dev
sudo systemctl start nanobot-dev
```

Verifica status:

```bash
sudo systemctl status nanobot-dev
# Output: active (running) [green]
```

### Step 7.3 — Monitora logs

```bash
sudo journalctl -u nanobot-dev -f
```

Dovresti vedere:

```
[INFO] Nanobot initialized
[INFO] Polling for messages...
```

**Premi Ctrl+C per uscire dai logs** (il service continua).

---

## FASE 8: Snapshot VMware (Importante!)

Salva lo stato della VM prima di fare esperimenti.

### Step 8.1 — Pausa VM

PowerShell (host):

```powershell
# Trova la VM in VMware
# Right-click serverino-dev
# → Power Off
```

### Step 8.2 — Take Snapshot

```
Right-click serverino-dev
↓
Snapshot
↓
Take Snapshot
↓
Name: "nanobot-working-base"
Description: "Nanobot installato, bot funziona, pronto per custom skills"
↓
Take Snapshot
```

✓ Snapshot creato. Se qualcosa si rompe in futuro, puoi revertire qui.

### Step 8.3 — Riaccendi VM

```
Right-click serverino-dev
↓
Power On
```

---

## FASE 9: Verifica Finale

SSH nella VM:

```powershell
ssh serverino@192.168.1.123
```

Dentro VM:

```bash
# Bot status
sudo systemctl status nanobot-dev
# Expected: active (running)

# Logs
sudo journalctl -u nanobot-dev -n 10
# Expected: vedi ultimi 10 log, niente errori

# Test API DeepSeek
curl -H "Authorization: Bearer sk_your_key" \
  https://api.deepseek.com/v1/models
# Expected: lista modelli (non errore auth)

# Disk space
df -h
# Expected: /dev/sda1 40G (vedi quanto usato)

# RAM usage
free -h
# Expected: total 6G, used ~1-2G
```

---

## Prossimi Step

1. ✅ VM installata e funzionante
2. ✅ Nanobot polling Telegram
3. ⏳ **Creare custom skill: `git_context_reader`**
   - Legge repo da Git URL
   - Estrae `.md` come contesto
   - Integra nel prompt
4. ⏳ Test multi-repo (scope multipli)
5. ⏳ Quando stabile → Deploy su Serverino fisico (AMD A9-9420e)

---

## Troubleshooting

| Problema | Soluzione |
|----------|-----------|
| **SSH connection refused** | VM è spenta? Controlla IP (ip addr show in VM) |
| **Bot non risponde** | Check: `sudo journalctl -u nanobot-dev` per errori |
| **"API key invalid"** | Verifica chiave DeepSeek in `~/.nanobot/config.json` |
| **"Out of disk"** | `df -h` per controllare, clean logs: `sudo journalctl --vacuum=100M` |
| **Bot non riceve messaggi Telegram** | Verifica chat_id, bot token, che sia il giusto bot |
| **Systemd service won't start** | `sudo systemctl status nanobot-dev` per errore esatto |

---

## Quick Commands Reference

```bash
# VM control (da host PowerShell)
vmrun list  # Elenca VM running
vmrun pause "C:\VMs\serverino-dev\serverino-dev.vmx"
vmrun reset "C:\VMs\serverino-dev\serverino-dev.vmx"

# SSH (da host PowerShell)
ssh serverino@192.168.1.123  # Rimpiazza IP
ssh serverino@192.168.1.123 "df -h"  # Comandi remoti

# Dentro VM (SSH session)
sudo systemctl status nanobot-dev  # Bot status
sudo journalctl -u nanobot-dev -f  # Live logs
sudo systemctl restart nanobot-dev  # Restart bot
source ~/nanobot-workspace/venv/bin/activate  # Attiva venv
nanobot run  # Test manuale
```

---

**Creato:** 2026-06-15  
**Status:** Pronto per il deploy
