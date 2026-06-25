# Ondata 2 — Log di creazione: `handlers/storage.py`

> Diario tecnico della costruzione del bot Serverino. Una nota per file/step.
> Collegato a [[DEFINIZIONE_ASSISTENTE]] (§8 = fonte di verità), [[SPECS]], [[REALITY_CHECK]].

## Stato Ondata 2
1. ✅ **`handlers/storage.py`** — schema SQLite + CRUD
2. ✅ **`handlers/obsidian_reader.py`** — lettura contesto da filesystem + `/ricorda`
3. ✅ **`handlers/deepseek_api.py`** — client async, usage reale + `/user/balance`
4. ✅ **`handlers/telegram_handler.py`** — comandi MVP + chat, auth chat_id, DI scheduler
5. ✅ **`skills/meteo.py`** — Open-Meteo, no key
6. ✅ **`skills/scheduler.py`** — next_run custom, tick JobQueue, propose LLM
7. ✅ **`main.py`** — wiring loop unico + JobQueue + shutdown

**🎉 Ondata 2 completa.** Tutti i moduli passano `py_compile`.

> ⚠️ **LAYOUT A PACKAGE (ordinato):**
> ```
> script/
> ├── config.py  main.py
> ├── handlers/  __init__.py  storage.py  obsidian_reader.py  deepseek_api.py  telegram_handler.py
> └── skills/    __init__.py  meteo.py  scheduler.py
> ```
> Import: `config` resta top-level (è in `script/`, sul path); i sibling via package
> (`from handlers.storage import …`, `from skills.meteo import …`). Si lancia `python main.py` da `script/`.
> Bug risolto: prima gli import flat (`import storage`) non reggevano i moduli dentro sottocartelle.

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

---

## `telegram_handler.py` — decisioni prese
- **Classe `TelegramHandler`** con **dependency injection**: riceve `cfg/db/ds/scheduler`. Lo scheduler è duck-typed (`.next_run(cron)`) → niente import di moduli inesistenti.
- **Auth**: `_authorized()` confronta `effective_chat.id` con `cfg.telegram.chat_id`. Chat non autorizzati → ignorati in silenzio.
- **Working memory**: `deque(maxlen=context_window_msgs)` in RAM (§8). Persa al restart (accettabile MVP; la long-term sta in `bot-memory.md`).
- **Chat flow**: `_build_messages` (system=persona+padrone+memoria, poi storia, poi msg) → `ds.chat` → reply + footer token (SPECS §8) → `bump_stats`+`log`. Eccezione → log ERROR + bump errors + messaggio "mi fermo" (no retry, §8).
- **Comandi task**: `/tasks /pausa /riprendi /stop /annulla` = pure operazioni su `storage`. `/conferma` e `/riprendi` calcolano `prossima_esecuzione` via `_next_run` → scheduler.
- ⚠️ **Promemoria per `scheduler.py`:** deve esporre `next_run(cron) -> str|None` (ISO). La **creazione** di una task da linguaggio naturale (intent LLM → INSERT 'proposta') NON è qui: vive in scheduler/main.
- ⚠️ **Promemoria per `main.py`:** istanziare `TelegramHandler(cfg, db, ds, scheduler)` e chiamare `.register(app)`.

---

## `scheduler.py` — decisioni prese
- **Formato cron custom** (§8, no croniter): `daily HH:MM` · `every Nh`. `next_run(cron, now=None)` ritorna ISO `"%Y-%m-%d %H:%M:%S"` (combacia coi confronti SQLite di `get_due_tasks`), `None` se formato ignoto.
- **`tick()`** (callback JobQueue): per ogni task dovuta → `acquire_task_lock` → dispatch skill → invia → `set_task_schedule(ultima, prossima)` → `release_task_lock` (in `finally`).
- **Failure = log + notifica + STOP** (§8): la task fallita va in `sospesa`, niente retry (≠ SPECS §17.2 "max 3").
- **Dispatch skill**: registry `{"meteo": _run_meteo}`. `azione` ignota → errore.
- **Gap chiuso — creazione task**: `propose_from_text()` (LLM → JSON intent → INSERT 'proposta'). Cablata via nuovo comando **`/programma <testo>`** in `telegram_handler` (Decisione A: esplicito, no LLM per ogni messaggio).
- ⚠️ **Promemoria `main.py`:** istanziare `Scheduler(cfg, db, ds, bot=app.bot)`, passarlo a `TelegramHandler(..., scheduler=...)`, e registrare il tick: `app.job_queue.run_repeating(scheduler.tick, interval=cfg.scheduler_tick_sec)`.

## `main.py` — decisioni prese
- Wiring: `config → connect/init_db/reset_locks → DeepSeekClient → app(builder) → Scheduler(bot=app.bot) → TelegramHandler(scheduler) → register → job_queue`.
- **Un loop solo**: scheduler = `job_queue.run_repeating(tick, interval=scheduler_tick_sec)`, NON un thread.
- **Retention §1**: `run_daily(03:00)` che pulisce i log solo se è il 1° del mese.
- **Shutdown**: `post_shutdown` sul builder (non per assegnazione, vincolo ptb v21) → `ds.close()` + `db.close()`.

## Ondata 3 — deploy/hardening
- [x] `main.py`: `reset_locks(conn)` dopo `init_db` (Decisione 1). ✅
- [x] `docker-compose.yml`: bind rw su `idee/bot-memory.md` (Decisione B), resto vault `:ro`. ✅
      NB: il file deve **esistere sull'host** prima di `up` (altrimenti Docker crea una dir).
- [x] `Dockerfile`: `WORKDIR /app` + `COPY . .` + `CMD python -u main.py` → già corretto, nessuna modifica. ✅
- [x] **`.dockerignore`** aggiunto: tiene `.env`/`storage`/`logs`/`vault`/`__pycache__` fuori dall'immagine
      (`.gitignore` non protegge il build context Docker). ✅
- [ ] Test end-to-end: richiede secret reali (DEEPSEEK_API_KEY + TELEGRAM_BOT_TOKEN). Senza, verificabile solo l'avvio/fail pulito di `config`.
- [ ] Creare `vault/idee/bot-memory.md` vuoto sull'host prima del primo `up` (vedi Decisione B).
- [ ] `scheduler.py`: dopo esecuzione task → `set_task_schedule` + `release_task_lock`.
- [ ] Failure di una task = notifica + log + **STOP** (no retry-3, §8 prevale su SPECS §17.2).
