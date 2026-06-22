# Serverino — Definizione dell'Assistente

**Data:** 2026-06-22
**Status:** ✅ Definizione condivisa
**Scopo:** Fissare *cosa deve essere* l'assistente del serverino, prima e sopra l'implementazione. È il documento di riferimento contro cui valutare ogni scelta tecnica.

> Collega: [[progetti/Serverino/DECISIONE_ARCHITETTURA]] • [[progetti/Serverino/bot-architecture]] • [[progetti/Serverino/SPECS]] • [[moc/Index MOC]]

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
