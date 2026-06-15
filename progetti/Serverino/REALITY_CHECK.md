# Serverino Bot — Reality Check

**Data:** 2026-06-15
**Modalità:** critical review distruttivo (reality check)
**Verdict:** ⚖️ **PIVOT** — caso d'uso sano, esecuzione over-engineered

---

## ⚠️ La doc mente sull'hardware

`hardware.md` dice **TDP ~15W, Carrizo 7ª gen**. Ground truth ([cpu-monkey](https://www.cpu-monkey.com/en/cpu-amd_a9_9420e), [technical.city](https://technical.city/en/cpu/A9-9420e-SoC)):

| Campo | Doc | Realtà |
|---|---|---|
| Architettura | Carrizo 7ª gen | **Stoney Ridge, 28nm, Q2 2017** |
| TDP | ~15W | **6W** |
| Core/Thread | 2/2 ✅ | 2/2 ✅ |
| Clock | n/d | 1.8–2.7 GHz |
| RAM | DDR4 4GB | DDR4-**2133** (lenta) |

TDP sopravvalutato di **2.5×**. Un 6W passivo del 2017 è più vicino a un Raspberry Pi che a un "server edge".

---

## Tabella Rischi

| ID | Categoria | Show-Stopper? | Gravità | Costo se rompe | Mitigazione |
|---|---|---|---|---|---|
| R1 | **API model retirement** | 🔴 **SÌ** | 5 | Bot muore il 24/07/2026 | `deepseek-chat` **ritirato il 24 luglio 2026** ([fonte](https://deepseek.ai/pricing)). SPECS.md lo hardcoda ovunque. Migrare a `deepseek-v4-flash`. |
| R2 | **Dipendenza Nanobot** | 🟠 Parziale | 4 | Settimane di stallo | Nanobot nato feb 2026 (8k star in 4gg — [HKUDS/nanobot](https://github.com/HKUDS/nanobot)). 4 mesi di vita, API instabili, no LTS, internals sconosciuti. |
| R3 | **RAM 4GB** | 🟠 Parziale | 4 | Crash sotto carico | Ubuntu idle ~1GB + Python + Nanobot + MCP (Node) + git clone → swap costante. Il "3.8GB free" è fantasia. |
| R4 | **Doc schizofrenica** | 🟡 | 3 | Rework | Due architetture incompatibili: hand-built (python-telegram-bot + SQLite) vs Nanobot (memoria built-in). Sceglierne UNA. |
| R5 | **SSD wear (git pull)** | 🟢 No | 1 | Trascurabile | Pull ogni 7 min = quasi sempre no-op. Non riempie il disco. Solo latenza sync max 7 min. |
| R6 | **`deepseek` SDK fantasma** | 🟡 | 2 | Build fallisce | `requirements.txt` ha `deepseek==0.1.0` (TBD). DeepSeek **non ha SDK ufficiale** — è OpenAI-compatible. |
| R7 | **MCP Obsidian su headless** | 🟠 | 3 | Feature core non va | `obsidian-mcp` richiede **app Obsidian aperta** con plugin REST. Su Ubuntu Server headless non c'è GUI → leggere i `.md` da filesystem/git. |
| R8 | **SPOF rete** | 🟡 | 2 | Bot offline | WiFi 1x1, no Ethernet. Connessione persa = loop `Restart=always`, nessun failover. |
| R9 | **Budget** | 🟢 No | 1 | Nessuno | ~$0.13/mese corretto ($0.14/$0.28 per 1M token confermati). Unico numero giusto nella doc. |
| R10 | **Secret in log** | 🟡 | 3 | Leak credenziali | `.env` chmod 600 ok, ma `journalctl` di Nanobot può loggare config al boot. Chi sanitizza i log di un framework non controllato? |

---

## 💀 3 Edge Case Catastrofici

**1. 24 luglio 2026 — il bot smette di rispondere.**
`deepseek-chat` ritirato → 401/404 → `Restart=always` in loop → journalctl si riempie. Bastava cambiare una stringa. **Probabilità: 100%, è una data sul calendario.**

**2. Git clone arbitrario da Telegram = disk bomb / RCE.**
README: *"analyze this repo: <url>"* → `git clone` arbitrario. Un repo da 5GB → 3-4 cloni e disco pieno → SQLite/swap falliscono. Un `.md` da 500MB → OOM kill su 4GB. Nessuna validazione dimensione/whitelist.

**3. Concorrenza messaggi su 2 core / 6W.**
10 messaggi mentre BUSY su chiamata da 60s → ogni ciclo ricarica file + ricostruisce prompt su CPU che throttola → coda che cresce più veloce dello smaltimento → timeout a cascata.

---

## ✅ Ground Truth Fact-Check

| Assunzione | Verdetto |
|---|---|
| A9-9420e ~15W Carrizo | ❌ FALSO — 6W Stoney Ridge 2017 |
| "3.8GB free" | ❌ FALSO — Ubuntu idle ~1GB |
| Nanobot "production-ready" | ⚠️ IPOTESI — 4 mesi di vita |
| `deepseek-chat` stabile | ❌ FALSO — ritirato 24/07/2026 |
| Pricing → ~$0.13/mese | ✅ VERO |
| `deepseek==0.1.0` SDK | ❌ FALSO — non esiste, usa client OpenAI |
| MCP Obsidian su headless | ❌ NON VERIFICATO — richiede GUI aperta |
| Free tier | ➕ DeepSeek dà 5M token gratis/30gg |

---

## ⚖️ Verdict: PIVOT

Caso d'uso (chat personale single-user che legge il vault) **sano e fattibile**. Budget reale. Marcio = **over-engineering** + assunzioni non verificate.

**Buttare:**
- ❌ **Nanobot** — overkill per single-user text-only. Un wrapper Python ~80 righe fa tutto.
- ❌ Doppia architettura — scegline una.
- ❌ MCP Obsidian sul server — leggi i `.md` da git filesystem.

**Costruire (MVP ~100 righe):**
```
python-telegram-bot (polling)
  → leggi N file .md dal clone git locale (no MCP)
  → client OpenAI puntato a api.deepseek.com, model="deepseek-v4-flash"
  → SQLite per log (lo schema in SPECS.md va benissimo)
  → systemd Restart=always
```

**3 azioni immediate:**
1. Sostituire ogni `deepseek-chat` → `deepseek-v4-flash`. **Oggi.**
2. Whitelist/limite dimensione sui git clone temporanei (o tagliare la feature dall'MVP).
3. Correggere `hardware.md`: 6W, Stoney Ridge, 2017.

**Backup plan:** hardware già comprato ($0 incrementale). Rischi 2-3 settimane su internals Nanobot + crash garantito del 24/07. Con wrapper custom: ~1 weekend, zero sorprese.

---

## 🪜 Scala di Impatto sul Progetto

Dal fattore che condiziona TUTTO il resto, fino a quelli marginali. Affronta dall'alto verso il basso: ogni gradino sopra rende inutile lavorare su quelli sotto.

| # | Fattore | Impatto | Perché sta qui |
|---|---|---|---|
| 1️⃣ | **Scelta architettura: Nanobot vs wrapper custom** | 🔴 DECISIVO | Decide ogni altra cosa: codice, deploy, manutenzione, debug, RAM. Finché non decidi questo, tutto il resto è aria. Raccomandazione: wrapper custom. |
| 2️⃣ | **Model retirement (`deepseek-chat` → `v4-flash`)** | 🔴 DECISIVO | Senza un modello vivo non c'è bot. Data fissa 24/07/2026. Fix banale ma obbligatorio prima di tutto. |
| 3️⃣ | **RAM 4GB + CPU 6W (vincolo fisico)** | 🟠 ALTO | Tetto invalicabile. Determina cosa puoi farci girare. Più pesante l'architettura (vedi #1), più questo morde. Spinge verso wrapper leggero + niente Docker/MCP-Node. |
| 4️⃣ | **Sorgente contesto: git filesystem vs MCP Obsidian** | 🟠 ALTO | Feature core. MCP non gira headless → se sbagli qui, il bot non legge il vault. Scelta: leggere `.md` da clone git. |
| 5️⃣ | **Scope feature: git clone arbitrario da messaggio** | 🟡 MEDIO | È la feature più rischiosa (disk bomb/OOM) e meno utile dell'MVP. Tagliarla semplifica sicurezza, storage e concorrenza in un colpo. |
| 6️⃣ | **Sicurezza secret + log** | 🟡 MEDIO | Conta solo dopo che il bot esiste e gira. Gestibile con `.env` chmod 600 + sanitize log. |
| 7️⃣ | **Affidabilità rete (WiFi 1x1, no failover)** | 🟢 BASSO | Fastidio operativo, non blocca. `Restart=always` copre l'80%. |
| 8️⃣ | **SSD wear da git pull** | 🟢 TRASCURABILE | Pull ogni 7 min ≈ no-op. Non lo tocchi nemmeno. |
| 9️⃣ | **Budget API** | 🟢 NON-PROBLEMA | ~$0.13/mese. Mai stato un rischio. |

**Regola di lettura:** se sei bloccato, lavora sempre sul gradino più alto non ancora risolto. Ottimizzare il #6 mentre il #1 è indeciso = sprecare token e tempo.

---

## Fonti

- [HKUDS/nanobot](https://github.com/HKUDS/nanobot)
- [DeepSeek pricing](https://deepseek.ai/pricing)
- [A9-9420e — cpu-monkey](https://www.cpu-monkey.com/en/cpu-amd_a9_9420e)
- [A9-9420e — technical.city](https://technical.city/en/cpu/A9-9420e-SoC)

---

[[progetti/Serverino/README]] • [[progetti/Serverino/hardware]] • [[progetti/Serverino/SPECS]] • [[progetti/Serverino/bot-architecture]] • [[progetti/Serverino/NANOBOT_SETUP]]
