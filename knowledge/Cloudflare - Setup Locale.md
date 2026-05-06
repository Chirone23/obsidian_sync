# Cloudflare – Cos'è e Come Installarlo in Locale

## Cos'è Cloudflare

Cloudflare è una piattaforma di rete globale che offre una serie di servizi:

- **CDN (Content Delivery Network):** distribuisce il tuo sito/app su server sparsi nel mondo, riducendo la latenza
- **Protezione DDoS:** filtra il traffico malevolo prima che raggiunga il tuo server
- **DNS veloce:** gestisce i record DNS con uno dei resolver più rapidi al mondo (1.1.1.1)
- **SSL/TLS automatico:** certificati HTTPS gratuiti e gestiti
- **Cloudflare Tunnel:** espone un servizio locale su internet **senza aprire porte** sul router

---

## Cloudflare in Locale: il Concetto

Quando si parla di "usare Cloudflare in locale", si intende quasi sempre **Cloudflare Tunnel** (ex Argo Tunnel), tramite il tool `cloudflared`.

**Come funziona:**
1. Installi `cloudflared` sulla tua macchina locale
2. Ti autentichi con il tuo account Cloudflare
3. `cloudflared` crea un tunnel cifrato tra la tua macchina e la rete Cloudflare
4. Cloudflare assegna un dominio pubblico che punta al tuo servizio locale (es. `localhost:3000`)
5. Chiunque visita quel dominio viene instradato attraverso il tunnel direttamente al tuo PC

**Vantaggi:**
- Non devi aprire porte sul router (niente port forwarding)
- Traffico cifrato end-to-end
- Dominio HTTPS gratuito anche per test locali
- Funziona dietro NAT, VPN, firewall aziendali

---

## Prerequisiti

- Account Cloudflare gratuito → [cloudflare.com](https://cloudflare.com)
- Un dominio registrato e gestito da Cloudflare (o usare un sottodominio `trycloudflare.com` gratuitamente senza login)
- Un servizio in ascolto in locale (es. un server Node.js su `localhost:3000`)

---

## Installazione di `cloudflared`

### Windows

**Opzione 1 – Winget (consigliato):**
```powershell
winget install --id Cloudflare.cloudflared
```

**Opzione 2 – Download manuale:**
1. Vai su https://github.com/cloudflare/cloudflared/releases
2. Scarica `cloudflared-windows-amd64.exe`
3. Rinominalo in `cloudflared.exe` e mettilo in una cartella nel PATH (es. `C:\tools\`)

Verifica installazione:
```powershell
cloudflared --version
```

---

### macOS

**Con Homebrew:**
```bash
brew install cloudflare/cloudflare/cloudflared
```

Verifica:
```bash
cloudflared --version
```

---

### Linux (Debian/Ubuntu)

```bash
# Scarica il pacchetto
wget https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb

# Installa
sudo dpkg -i cloudflared-linux-amd64.deb

# Verifica
cloudflared --version
```

Per altre distribuzioni usa il binario direttamente:
```bash
wget https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64
chmod +x cloudflared-linux-amd64
sudo mv cloudflared-linux-amd64 /usr/local/bin/cloudflared
```

---

## Utilizzo Rapido (senza account – dominio temporaneo)

Perfetto per test veloci. Non serve login:

```bash
cloudflared tunnel --url http://localhost:3000
```

Cloudflare genera un URL pubblico temporaneo tipo:
```
https://random-name.trycloudflare.com
```

Questo URL funziona finché tieni attivo il processo. È usa e getta.

---

## Utilizzo con Account Cloudflare (tunnel permanente)

### Step 1 – Login

```bash
cloudflared tunnel login
```

Si apre il browser → autorizza Cloudflare → scarica automaticamente il certificato in `~/.cloudflared/cert.pem`

### Step 2 – Crea il tunnel

```bash
cloudflared tunnel create nome-tunnel
```

Questo genera un ID univoco per il tunnel e un file di credenziali JSON in `~/.cloudflared/`

### Step 3 – Crea il file di configurazione

Crea `~/.cloudflared/config.yml`:

```yaml
tunnel: <ID-DEL-TUNNEL>
credentials-file: /home/utente/.cloudflared/<ID-DEL-TUNNEL>.json

ingress:
  - hostname: miosito.miodominio.com
    service: http://localhost:3000
  - service: http_status:404
```

Sostituisci:
- `<ID-DEL-TUNNEL>` con l'ID ottenuto al passo 2
- `miosito.miodominio.com` con il tuo sottodominio
- `localhost:3000` con la porta del tuo servizio

### Step 4 – Aggiungi il record DNS

```bash
cloudflared tunnel route dns nome-tunnel miosito.miodominio.com
```

Questo crea automaticamente il record CNAME nel pannello DNS di Cloudflare.

### Step 5 – Avvia il tunnel

```bash
cloudflared tunnel run nome-tunnel
```

Il tuo servizio locale è ora raggiungibile pubblicamente su `https://miosito.miodominio.com`

---

## Avvio Automatico come Servizio di Sistema

### Windows

```powershell
cloudflared service install
```

### Linux / macOS

```bash
sudo cloudflared service install
sudo systemctl enable cloudflared
sudo systemctl start cloudflared
```

---

## Riepilogo Comandi Utili

| Comando | Descrizione |
|---|---|
| `cloudflared tunnel --url http://localhost:PORT` | Tunnel rapido temporaneo |
| `cloudflared tunnel login` | Autenticazione account |
| `cloudflared tunnel create <nome>` | Crea tunnel permanente |
| `cloudflared tunnel list` | Lista tunnel esistenti |
| `cloudflared tunnel run <nome>` | Avvia tunnel |
| `cloudflared tunnel delete <nome>` | Elimina tunnel |
| `cloudflared tunnel route dns <nome> <dominio>` | Collega dominio al tunnel |

---

## Troubleshooting Comune

**Il tunnel non si connette:**
- Verifica che il servizio locale sia in ascolto sulla porta corretta
- Controlla che `cert.pem` esista in `~/.cloudflared/`

**Errore 502/503 dal browser:**
- Il processo locale non risponde → verifica che `localhost:PORT` funzioni prima di avviare il tunnel

**Dominio non raggiungibile:**
- Controlla che il record DNS CNAME sia corretto nel pannello Cloudflare
- Attendi qualche minuto per la propagazione DNS

---

*Tags: #cloudflare #tunnel #networking #devops #localhost*
