# AVVIO — primo boot di NOA

> Guida operativa per far girare il bot la prima volta. `script/` = codice,
> `bot-memory/` = contesto (montato in `/bot-memory`).

## 1. Prerequisiti (una volta sola)
- Docker Desktop attivo.
- Un **bot Telegram**: parla con @BotFather → `/newbot` → ottieni il **token**.
- Il tuo **chat_id**: scrivi al bot, poi apri
  `https://api.telegram.org/bot<TOKEN>/getUpdates` e leggi `chat.id`.
- Una **API key DeepSeek** (platform.deepseek.com → API keys).

## 2. Configura i secret
Dentro `script/`, copia il template e compilalo:
```bash
cd script
cp .env.example .env
```
Riempi in `.env` SOLO questi (gli altri hanno default buoni):
```
DEEPSEEK_API_KEY=sk-...
TELEGRAM_BOT_TOKEN=123456789:ABC...
TELEGRAM_CHAT_ID=<il tuo chat.id numerico>
```
`.env` è in `.gitignore` e `.dockerignore` → non finisce né su git né nell'immagine.

## 3. Avvio
```bash
docker compose up --build
```
- Monta `../bot-memory` → `/bot-memory` (rw): NOA legge system/padrone/memory/skills
  e `/ricorda` scrive in `memory.md`.
- Persiste DB e log in `script/storage/` e `script/logs/`.
- `skills-menu.md` viene rigenerato dal codice a ogni avvio.

## 4. Smoke test (in ordine)
1. `/start` → risponde con la lista comandi → **auth + polling OK**.
2. Un messaggio qualsiasi → risponde → **DeepSeek + contesto OK** (controlla il footer token).
3. `/status` → mostra messaggi/token/errori/saldo → **stats + balance OK**.
4. `meteo a Roma` (chat) o `/task ogni giorno alle 8 il meteo di Roma` →
   propone task → `/conferma <id>` → **scheduler OK**.
5. Chatta un po', poi `/ricorda` → propone schema → `/salva <id>` →
   controlla che `bot-memory/memory.md` si aggiorni → **capture OK**.

## 5. Se qualcosa si rompe
- Il bot non parte e logga "Variabile d'ambiente obbligatoria mancante" → manca un secret in `.env` (fail-fast voluto).
- Nessuna risposta ai messaggi → `TELEGRAM_CHAT_ID` sbagliato (auth silenziosa scarta i non-autorizzati).
- `/status` saldo `n/d` → endpoint `/user/balance` non raggiunto (non blocca il resto).
- `/ricorda` dice "non c'è conversazione" → la working memory è vuota (parla prima).

## 6. Limiti noti (by design, non bug)
- Manutenzione memoria **L1/L2 non ancora attiva** (tabella pronta, motore TODO).
- Follow-up orario task = **one-shot**: se ometti "ogni…", NOA chiede di riscrivere completo.
- Working memory = ultimi 10 messaggi in RAM, persa al restart (la long-term è `memory.md`).
- `compila ≠ funziona`: questo è il primo giro reale, aspettati aggiustamenti.
