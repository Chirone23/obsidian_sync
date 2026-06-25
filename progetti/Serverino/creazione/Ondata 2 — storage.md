# Ondata 2 — Log di creazione: `handlers/storage.py`

> Diario tecnico della costruzione del bot Serverino. Una nota per file/step.
> Collegato a [[DEFINIZIONE_ASSISTENTE]] (§8 = fonte di verità), [[SPECS]], [[REALITY_CHECK]].

## Stato Ondata 2
1. ✅ **`handlers/storage.py`** — schema SQLite + CRUD
2. ✅ **`handlers/obsidian_reader.py`** — lettura contesto da filesystem + `/ricorda`
3. ✅ **`handlers/deepseek_api.py`** — client async, usage reale + `/user/balance`
4. ⏳ `handlers/telegram_handler.py`
5. ⏳ `skills/meteo.py`
6. ⏳ `skills/scheduler.py`
7. ⏳ `main.py`

---

## `storage.py` — decisioni prese

### Riconciliazione schema (SPECS vs §8)
- `logs` e `stats`: **verbatim** da SPECS §1.
- `tasks`: base SPECS §17.1 **+ colonna `in_esecuzione INTEGER DEFAULT 0`** (anti doppia-esecuzione, §8).

### Decisione 1 — lucchetto anti doppia-esecuzione → **opzione A (scelta dell'utente)**
- Solo `in_esecuzione` (flag 0/1). **NIENTE `lock_until`.**
- **Compagno obbligatorio:** `reset_locks()` da chiamare **all'avvio** → `UPDATE tasks SET in_esecuzione = 0`.
- **Perché regge:** un solo processo, `Restart=always`. Se il bot è crashato, niente stava davvero
  girando → al boot è corretto togliere ogni lucchetto orfano. Più semplice di `lock_until`,
  altrettanto sicuro *a patto del reset all'avvio*.
- ⚠️ **Promemoria per `main.py`:** chiamare `reset_locks(conn)` subito dopo `init_db`.

### Decisione 2 — cosa filtra `get_due_tasks` → **tre filtri in AND** (imposto da spec, non opzionale)
```sql
stato = 'attiva'  AND  prossima_esecuzione <= now  AND  in_esecuzione = 0
```
- `stato='attiva'`: le `proposta`/`sospesa` non girano MAI (regola dura §8/§17.3).
- `prossima_esecuzione <= now`: turno arrivato.
- `in_esecuzione = 0`: non già in corso (no `lock_until`, coerente con Decisione 1).

### Decisione 3 — leggi vs prendi → **separati (A)**
- `get_due_tasks` fa **solo la SELECT**; lo scheduler poi chiama `acquire_task_lock` task per task.
- Lecito perché un solo loop asyncio: niente race da gestire con SELECT+lock atomico.

### Altre scelte tecniche
- `connect()`: `PRAGMA journal_mode=WAL` + `busy_timeout=5000` + `foreign_keys=ON`.
  WAL riduce il lock contention chat ↔ scheduler tick su CPU 6W.
- `check_same_thread=False` + `row_factory=Row`.
- SQL raw con parametri `?` (anti-injection). Niente ORM. `sqlite3` stdlib (sync, query in μs).
- I secret **non passano mai** da storage.

### Funzioni esposte
`connect` · `init_db` · `reset_locks` · `log` · `cleanup_logs` ·
`bump_stats` · `stats_today` · `errors_last_24h` ·
`add_task` · `get_task` · `list_tasks` · `get_due_tasks` ·
`set_task_state` · `set_task_schedule` · `delete_task` ·
`acquire_task_lock` · `release_task_lock`

### Fuori scope (deliberato)
- Calcolo `prossima_esecuzione` → compito di `skills/scheduler.py` (parsing cron custom §8).
- `cleanup_logs()` esiste ma viene **invocato dallo scheduler** (1° del mese), non da storage.

---

---

## `obsidian_reader.py` — decisioni prese

### Decisione B — dove scrive `/ricorda` (mount `:ro` vs memoria scrivibile)
- Memoria long-term = `idee/bot-memory.md` **dentro il vault**, così è visibile in Obsidian e va in `obsidian_sync`.
- Il vault è montato `:ro` → serve un **bind rw del solo file memoria** (non tutto il vault).
- ⚠️ **Promemoria per `docker-compose.yml`:** aggiungere bind rw su `idee/bot-memory.md`
  (es. `- ./vault/idee/bot-memory.md:/vault/idee/bot-memory.md:rw`), tenendo il resto `:ro`.

### Scelte tecniche
- `strip_frontmatter()`: toglie il blocco YAML `--- … ---`; se assente/malformato lascia il testo intatto. Gestisce BOM.
- File mancante → stringa vuota (`FileNotFoundError` ingoiato). Il bot parte comunque.
- `read_memory_lines()`: un fatto per riga, salta vuote e bullet `- `.
- `append_memory()`: append-only, normalizza a-capo interni (un fatto = una riga), zero LLM (§8). Prefissa `- ` per resa Obsidian.
- Import da `config.VaultConfig` (già esposto: `persona()/padrone()/memory()`).

---

---

## `deepseek_api.py` — decisioni prese
- **Async** (`AsyncOpenAI`): non blocca il loop unico di ptb.
- **Nessun retry** (§8): `chat()` fa una sola chiamata, l'errore propaga → il chiamante notifica+logga+STOP.
- `ChatResult` espone usage REALE (`prompt_tokens`/`completion_tokens`) + `response_time_ms` (perf_counter) + `finish_reason` → alimenta `bump_stats`.
- `get_balance()`: GET `/user/balance` via httpx (endpoint proprietario, fuori dallo schema OpenAI). `raise_for_status` → errore propagato.
- Chiave mai loggata. `close()` per shutdown pulito.
- ⚠️ **Promemoria per `telegram_handler.py`:** compone `messages` (system = persona+padrone, poi storia ≤10 msg) e cattura le eccezioni di `chat()` per il flusso failure.

## Debiti / cose da non dimenticare
- [ ] `main.py`: `reset_locks(conn)` dopo `init_db` (vedi Decisione 1).
- [ ] `docker-compose.yml`: bind rw su `idee/bot-memory.md` (vedi Decisione B), resto vault `:ro`.
- [ ] `scheduler.py`: dopo esecuzione task → `set_task_schedule` + `release_task_lock`.
- [ ] Failure di una task = notifica + log + **STOP** (no retry-3, §8 prevale su SPECS §17.2).
