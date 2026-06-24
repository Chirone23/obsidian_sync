# Serverino — Definizione dell'Assistente

**Data:** 2026-06-22
**Status:** ✅ Definizione condivisa
**Scopo:** Fissare *cosa deve essere* l'assistente del serverino, prima e sopra l'implementazione. È il documento di riferimento contro cui valutare ogni scelta tecnica.

> Collega: [[progetti/Serverino/DECISIONE_ARCHITETTURA]] • [[progetti/Serverino/bot-architecture]] • [[progetti/Serverino/SPECS]] • [[progetti/Serverino/loop-engineering]] • [[moc/Index MOC]]

---

## 1. Il tre livelli — dove si colloca il serverino

| Livello | Cos'è | Memoria | Azione | Iniziativa |
|---|---|---|---|---|
| **1. Chatbot** | `input → testo` | ❌ | ❌ | ❌ |
| **2. Assistente** | esegue task su comando | ✅ | ✅ | ❌ |
| **2.5 Serverino** | **assistente + scheduler proattivo con conferma** | ✅ | ✅ | ⚠️ su timer, mai a sorpresa |
| **3. Agente** | goal-driven autonomo | ✅ | ✅ | ✅ libera |

Il serverino **non** è un agente full-autonomo e **non** è un semplice chatbot. È un assistente reattivo con un secondo trigger temporale e un freno di conferma.

---

## 2. Definizione in una frase

> Un servitore che dorme finché non lo chiami o non scatta un orario che gli hai dato — e quando scatta qualcosa di nuovo, ti chiede il permesso prima di renderlo abitudine.

---

## 3. I 5 assi del *mio* assistente

| Asse | Livello scelto | Implicazione tecnica |
|---|---|---|
| **Identità** | Reattivo + proattivo su timer | 2 trigger nel core: `on_message` (Telegram) **+** `on_schedule` (cron) |
| **Memoria** | Persistente (vault + SQLite) | Già speccato — `bot-memory.md` + tabella `logs` |
| **Azione** | Esegue task, non solo testo | `skills/` dir reale (oltre il chat) + tabella `tasks` |
| **Iniziativa** | Su trigger temporale, mai a sorpresa | Scheduler propone → **conferma obbligatoria** prima di azioni nuove |
| **Affidabilità** | Deterministico dove conta | Cron/esecuzione in codice; LLM solo per decidere *cosa* dire/fare |

Funzioni richieste (dalle 4 risposte): hub dati personali + assistente conversazionale + monitoraggio/notifiche + automazione/orchestrazione.

---

## 4. Il gap rispetto al blueprint attuale

Tutto il design esistente (SPECS, bot-architecture) descrive **solo** il flusso reattivo:

```
Telegram in → leggi vault → DeepSeek → Telegram out → IDLE
```

La state machine torna sempre a `IDLE`: nessuna iniziativa, nessuno scheduling. Per arrivare al livello 2.5 **mancano esattamente due cose** — il resto del design regge senza modifiche:

### 4.1 Tabella `tasks` (non presente nelle SPECS, che hanno solo `logs` e `stats`)
```sql
CREATE TABLE tasks (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  cron TEXT NOT NULL,            -- es. "0 8 * * *"
  descrizione TEXT NOT NULL,     -- cosa fa la task
  azione TEXT NOT NULL,          -- skill/comando da eseguire
  stato TEXT DEFAULT 'proposta', -- 'proposta' | 'attiva' | 'sospesa'
  ultima_esecuzione DATETIME,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### 4.2 Secondo trigger nel core loop (scheduler accanto al polling)
```
main.py
 ├─ on_message  (Telegram polling)   → flusso attuale
 └─ on_schedule (loop cron, ogni 60s) → legge tasks attive, esegue quelle dovute
```

### 4.3 Handshake di conferma (il freno)
```
Tu: "ogni giorno alle 8 dammi il meteo + agenda"
Bot: propone la task (cron + azione) → "Confermo e la attivo? [sì/no]"
Tu: "sì"
Bot: INSERT in tasks con stato='attiva'
```
Le task **nuove** nascono `proposta` e diventano `attiva` solo dopo il tuo ok. Le task già attive girano da sole senza richiederlo ogni volta.

---

## 5. Confini (cosa NON è)

- ❌ Non agisce mai su un'azione nuova senza conferma esplicita.
- ❌ Non inventa task da solo (no goal-seeking autonomo).
- ❌ Non è multi-utente, multi-canale, multi-provider (vedi [[progetti/Serverino/DECISIONE_ARCHITETTURA]]).
- ✅ Esegue da solo **solo** ciò che hai già approvato e schedulato.

---

## 6. Prossimo passo

Estendere — non riscrivere — il blueprint:
1. Aggiungere tabella `tasks` allo schema SQLite in [[progetti/Serverino/SPECS]].
2. Aggiungere il trigger `on_schedule` al core loop in [[progetti/Serverino/bot-architecture]].
3. Definire la skill `scheduler` (proponi → conferma → salva → esegui).

---

[[progetti/Serverino/README]] • [[progetti/Serverino/REALITY_CHECK]] • [[progetti/Serverino/hardware]]


---

## 7. ADDENDUM — Decisioni 2026-06-22

### 7.1 Monitoraggio = livello 2.7, NON 3.0 → rimandato a Phase 2
Il monitoraggio (trigger su *condizione*: "avvisami se X") è distinto dallo scheduler (trigger su *tempo*). Resta dentro confini dati dall'utente → **non** è l'agente autonomo del livello 3.0. È **2.7**, rimandato a Phase 2. Rimandarlo non apre la porta all'autonomia full: quando servirà, sarà un guardiano con soglia, non un agente che decide i propri goal.

### 7.2 Memoria a due livelli (aggiorna asse "Memoria")
- **Working memory**: ultimi 10 messaggi (continuità immediata, resettata a `/start`).
- **Long-term memory**: solo fatti salienti, estratti e compressi in `idee/bot-memory.md` (un fatto per riga). Sostituisce il vecchio "log infinito".
- **Consolidamento semi-automatico**: il bot propone i fatti da salvare → conferma utente (`/salva`). Mai scrittura autonoma in memoria. Coerente con l'asse "Iniziativa".
- Il context delle risposte usa la **conversazione**, non l'intera knowledge del vault.

### 7.3 Affidabilità — token reali
Asse "Affidabilità" rafforzato: niente stime. Token per-chiamata dal campo `usage` di DeepSeek; saldo account da `GET /user/balance`. Vedi [[progetti/Serverino/SPECS]] §18.4.

### 7.4 MVP confermato include
✅ scheduler+tasks con conferma · ✅ failure notification · ✅ memoria semi-automatica · ✅ token/saldo reali · ✅ `/status`
⏳ Phase 2: monitoraggio (2.7) · retrieval knowledge (quando il vault cresce) · media/voce


---

## 8. RICONCILIAZIONE v2 — opzione B (2026-06-22, FONTE DI VERITÀ)

> Questo §8 **prevale** su qualsiasi contraddizione negli altri file. Nasce dalla valutazione critica esterna del 22/06 (vedi cronologia). Scelta: **opzione B — MVP 2.5 fatto bene**, single loop asyncio. Dove SPECS/bot-architecture dicono altro, vale §8.

### 8.1 Decisioni canoniche (B)

| Tema | Verità v2 |
|---|---|
| **Concorrenza** | **UN solo loop asyncio**, NON due thread. Scheduler = `JobQueue` nativa di `python-telegram-bot` v20. Niente Thread A/B su SQLite condiviso. |
| **Anti doppia-esecuzione** | Colonna `in_esecuzione` (o `lock_until`) sulla task + `PRAGMA busy_timeout`. Una task in corso non viene ripresa dal tick successivo. |
| **Memoria** | Working = ultimi 10 msg (context). Long-term = `idee/bot-memory.md`, **un fatto per riga**, scrittura SOLO via comando manuale **`/ricorda <testo>`** (append-only, zero LLM). |
| **Consolidamento LLM** | ❌ TAGLIATO dall'MVP. Era rotto (candidati in RAM + `Restart=always` = fatti persi). → Phase 2. |
| **Failure handling** | Skill fallisce → **notifica + log**, STOP. Niente retry-3 (amplifica il throttle CPU 6W). Task che fallisce → `sospesa` + alert, una volta. |
| **Cron** | Solo `"ogni giorno HH:MM"` e `"ogni N ore"`. Niente cron 5-campi, niente dipendenza `croniter`. |
| **Sorgente contesto** | Filesystem: legge `.md` dal clone git locale del vault. **MCP Obsidian = MORTO** (non gira headless). |
| **Modello LLM** | `deepseek-v4-flash`. `deepseek-chat` = RITIRATO 24/07/2026. |
| **Token/saldo** | Per-chiamata: campo `usage` reale. Saldo: `GET /user/balance`. (invariato, OK) |
| **`/status`** | Nell'MVP. (invariato, OK) |
| **Monitoraggio** | Phase 2 (2.7). (invariato) |

### 8.2 PRIMA del codice — scegliere UNA skill eseguibile
Il 2.5 ha senso solo se lo scheduler ha qualcosa da eseguire. **Vincolo:** la prima skill NON deve richiedere OAuth (no Google Calendar nell'MVP). Candidata default: **meteo via API pubblica** (es. Open-Meteo, no key). Decisione skill = prerequisito allo scheduler, non successiva.

### 8.3 ERRATA — cosa correggere/cancellare negli altri file

| File · sezione | Contenuto MORTO | Azione |
|---|---|---|
| `bot-architecture` header + Tech Stack | "Knowledge: MCP Obsidian (read-only)" | Cancellare → filesystem git |
| `bot-architecture` `.env` (riga ~206/229) | `MCP_SERVER_URL=http://localhost:3000` | Cancellare (config di decisione revocata) |
| `bot-architecture` Message Flow step 7 | "APPEND to bot-memory.md ## timestamp User/Bot" (log infinito) | Sostituire con `/ricorda` append-only |
| `SPECS §3` STATE MACHINE | Macchina single-thread che torna a IDLE | Riscrivere come loop asyncio singolo (handler chat + JobQueue scheduler nello stesso loop) |
| `SPECS §4.3` bot-memory "Session Log" + `/recap` | Vecchio modello memoria | Sostituire con modello 2-livelli §18.1 + `/ricorda` |
| `SPECS §6.2 / §8` | `"model": "deepseek-chat"` | → `deepseek-v4-flash` |
| `SPECS §14` TOKEN BUDGET | Linguaggio "estimate" sui token | Token sono REALI (campo `usage`), togliere "estimate" |
| `SPECS §18.2` consolidamento LLM | Memoria semi-auto con conferma `/salva` | TAGLIATO → Phase 2, sostituito da `/ricorda` |
| `SPECS §18.3` retry-3-then-suspend | Retry automatico | Semplificare: notifica + log, niente retry |

### 8.4 Stato MVP v2 (definitivo)
✅ chat reattiva (asyncio) · ✅ scheduler via JobQueue (un loop) · ✅ tasks con conferma + `in_esecuzione` · ✅ `/ricorda` manuale · ✅ failure = notifica+log · ✅ token/saldo reali · ✅ `/status` · ✅ UNA skill no-OAuth (meteo)
⏳ Phase 2: monitoraggio 2.7 · consolidamento LLM · retrieval knowledge · cron avanzato · media/voce · Calendar/OAuth
