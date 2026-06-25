"""Accesso a SQLite — deterministico, zero LLM, zero logica di business.

Solo: connessione configurata, schema, CRUD. Le query usano parametri `?`
(anti-injection). I secret non passano MAI da qui.

Riconciliazione SPECS vs DEFINIZIONE_ASSISTENTE §8:
- `logs` e `stats`: verbatim da SPECS §1.
- `tasks`: da SPECS §17.1 + colonna `in_esecuzione` (anti doppia-esecuzione, §8).
  NIENTE `lock_until`: siamo un solo loop con Restart=always, quindi al boot
  `reset_locks()` azzera ogni lucchetto orfano lasciato da un crash.
"""
from __future__ import annotations

import json
import sqlite3
from pathlib import Path

SCHEMA = """
CREATE TABLE IF NOT EXISTS logs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  level TEXT NOT NULL,
  message TEXT NOT NULL,
  context TEXT
);
CREATE INDEX IF NOT EXISTS idx_logs_timestamp ON logs(timestamp);
CREATE INDEX IF NOT EXISTS idx_logs_level_timestamp ON logs(level, timestamp DESC);

CREATE TABLE IF NOT EXISTS stats (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  date DATE UNIQUE NOT NULL,
  messages_count INTEGER DEFAULT 0,
  tokens_in INTEGER DEFAULT 0,
  tokens_out INTEGER DEFAULT 0,
  errors_count INTEGER DEFAULT 0,
  avg_response_time_ms REAL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_stats_date ON stats(date DESC);

CREATE TABLE IF NOT EXISTS tasks (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  cron TEXT NOT NULL,
  descrizione TEXT NOT NULL,
  azione TEXT NOT NULL,
  payload TEXT,
  stato TEXT DEFAULT 'proposta',
  in_esecuzione INTEGER DEFAULT 0,
  ultima_esecuzione DATETIME,
  prossima_esecuzione DATETIME,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_tasks_stato ON tasks(stato);
CREATE INDEX IF NOT EXISTS idx_tasks_prossima
  ON tasks(prossima_esecuzione) WHERE stato = 'attiva';

-- Impostazioni chiave/valore persistite (es. automemoria on/off). Mai RAM.
CREATE TABLE IF NOT EXISTS settings (
  key TEXT PRIMARY KEY,
  value TEXT NOT NULL
);

-- Suggerimenti di manutenzione memoria L2 in attesa di /conferma. Mai RAM
-- (è il buco §8: candidati in RAM + Restart=always = persi).
CREATE TABLE IF NOT EXISTS memory_suggestions (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  tipo TEXT NOT NULL,            -- 'merge' | 'supersede' | 'shorten' | 'delete' | 'rewrite' | 'promote'
  motivazione TEXT NOT NULL,     -- perché, con citazione del testo coinvolto
  citazione TEXT NOT NULL,       -- riga/e di memory.md interessate
  proposta TEXT,                 -- testo risultante proposto (NULL per delete)
  stato TEXT DEFAULT 'in_attesa',-- 'in_attesa' | 'inviato' | 'confermato' | 'rifiutato'
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_sugg_stato ON memory_suggestions(stato);

-- Bozze in attesa di conferma (proposta /ricorda, draft task da /task). Mai RAM.
CREATE TABLE IF NOT EXISTS drafts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  kind TEXT NOT NULL,            -- 'memory' | 'task'
  payload TEXT NOT NULL,         -- JSON: per memory = schema fatti; per task = intent
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_drafts_kind ON drafts(kind);
"""


# ── Connessione ────────────────────────────────────────────────────────────

def connect(db_path: Path) -> sqlite3.Connection:
    """Apre la connessione e applica i PRAGMA. WAL riduce il lock contention
    tra chat e scheduler tick; busy_timeout copre i picchi su CPU 6W."""
    db_path = Path(db_path)
    db_path.parent.mkdir(parents=True, exist_ok=True)
    conn = sqlite3.connect(db_path, check_same_thread=False)
    conn.row_factory = sqlite3.Row
    conn.execute("PRAGMA journal_mode=WAL")
    conn.execute("PRAGMA busy_timeout=5000")
    conn.execute("PRAGMA foreign_keys=ON")
    return conn


def init_db(conn: sqlite3.Connection) -> None:
    conn.executescript(SCHEMA)
    conn.commit()


def reset_locks(conn: sqlite3.Connection) -> None:
    """Da chiamare all'avvio: azzera i lucchetti orfani lasciati da un crash.
    Lecito perché c'è un solo processo — se eravamo morti, niente girava."""
    conn.execute("UPDATE tasks SET in_esecuzione = 0")
    conn.commit()


# ── logs ───────────────────────────────────────────────────────────────────

def log(conn: sqlite3.Connection, level: str, message: str,
        context: dict | None = None) -> None:
    conn.execute(
        "INSERT INTO logs (level, message, context) VALUES (?, ?, ?)",
        (level, message, json.dumps(context, ensure_ascii=False) if context else None),
    )
    conn.commit()


def cleanup_logs(conn: sqlite3.Connection, days: int = 30) -> int:
    """Retention §1. Chiamata dallo scheduler il 1° del mese, non da qui."""
    cur = conn.execute(
        "DELETE FROM logs WHERE timestamp < DATETIME('now', ?)", (f"-{days} days",)
    )
    conn.commit()
    return cur.rowcount


# ── stats ──────────────────────────────────────────────────────────────────

def bump_stats(conn: sqlite3.Connection, *, messages: int = 0, tokens_in: int = 0,
               tokens_out: int = 0, errors: int = 0,
               response_time_ms: float | None = None) -> None:
    """UPSERT incrementale sulla riga di oggi. avg_response_time_ms è una media
    mobile pesata sul numero di messaggi del giorno."""
    conn.execute(
        "INSERT INTO stats (date) VALUES (DATE('now')) "
        "ON CONFLICT(date) DO NOTHING"
    )
    conn.execute(
        "UPDATE stats SET "
        "  messages_count = messages_count + ?, "
        "  tokens_in = tokens_in + ?, "
        "  tokens_out = tokens_out + ?, "
        "  errors_count = errors_count + ?, "
        "  avg_response_time_ms = CASE "
        "    WHEN ? IS NULL THEN avg_response_time_ms "
        "    WHEN messages_count = 0 THEN ? "
        "    ELSE (avg_response_time_ms * messages_count + ?) / (messages_count + 1) "
        "  END "
        "WHERE date = DATE('now')",
        (messages, tokens_in, tokens_out, errors,
         response_time_ms, response_time_ms, response_time_ms),
    )
    conn.commit()


def stats_today(conn: sqlite3.Connection) -> sqlite3.Row | None:
    return conn.execute(
        "SELECT * FROM stats WHERE date = DATE('now')"
    ).fetchone()


def errors_last_24h(conn: sqlite3.Connection) -> int:
    return conn.execute(
        "SELECT COUNT(*) FROM logs "
        "WHERE level = 'ERROR' AND timestamp >= DATETIME('now', '-1 day')"
    ).fetchone()[0]


# ── tasks ──────────────────────────────────────────────────────────────────

def add_task(conn: sqlite3.Connection, cron: str, descrizione: str, azione: str,
             payload: dict | None = None, stato: str = "proposta") -> int:
    cur = conn.execute(
        "INSERT INTO tasks (cron, descrizione, azione, payload, stato) "
        "VALUES (?, ?, ?, ?, ?)",
        (cron, descrizione, azione,
         json.dumps(payload, ensure_ascii=False) if payload else None, stato),
    )
    conn.commit()
    return cur.lastrowid


def get_task(conn: sqlite3.Connection, task_id: int) -> sqlite3.Row | None:
    return conn.execute("SELECT * FROM tasks WHERE id = ?", (task_id,)).fetchone()


def list_tasks(conn: sqlite3.Connection, stato: str | None = None) -> list[sqlite3.Row]:
    if stato:
        return conn.execute(
            "SELECT * FROM tasks WHERE stato = ? ORDER BY id", (stato,)
        ).fetchall()
    return conn.execute("SELECT * FROM tasks ORDER BY id").fetchall()


def get_due_tasks(conn: sqlite3.Connection, now: str) -> list[sqlite3.Row]:
    """Task da eseguire ORA. Tre filtri in AND (Decisione 2):
    attiva (mai proposta/sospesa) · turno arrivato · non già in esecuzione.
    `now` è una stringa ISO confrontabile con prossima_esecuzione."""
    return conn.execute(
        "SELECT * FROM tasks "
        "WHERE stato = 'attiva' "
        "  AND prossima_esecuzione <= ? "
        "  AND in_esecuzione = 0 "
        "ORDER BY prossima_esecuzione",
        (now,),
    ).fetchall()


def set_task_state(conn: sqlite3.Connection, task_id: int, stato: str) -> None:
    conn.execute("UPDATE tasks SET stato = ? WHERE id = ?", (stato, task_id))
    conn.commit()


def set_task_schedule(conn: sqlite3.Connection, task_id: int, *,
                      ultima_esecuzione: str | None = None,
                      prossima_esecuzione: str | None = None) -> None:
    """Aggiorna i timestamp dopo un'esecuzione. Il calcolo del prossimo orario
    è compito dello scheduler (parsing cron custom §8), non di storage."""
    conn.execute(
        "UPDATE tasks SET "
        "  ultima_esecuzione = COALESCE(?, ultima_esecuzione), "
        "  prossima_esecuzione = COALESCE(?, prossima_esecuzione) "
        "WHERE id = ?",
        (ultima_esecuzione, prossima_esecuzione, task_id),
    )
    conn.commit()


def delete_task(conn: sqlite3.Connection, task_id: int) -> None:
    conn.execute("DELETE FROM tasks WHERE id = ?", (task_id,))
    conn.commit()


def acquire_task_lock(conn: sqlite3.Connection, task_id: int) -> bool:
    """Mette il post-it (in_esecuzione=1). UPDATE condizionale: se qualcun altro
    l'ha già preso, rowcount=0 → False. Atomico grazie a busy_timeout."""
    cur = conn.execute(
        "UPDATE tasks SET in_esecuzione = 1 "
        "WHERE id = ? AND in_esecuzione = 0",
        (task_id,),
    )
    conn.commit()
    return cur.rowcount == 1


def release_task_lock(conn: sqlite3.Connection, task_id: int) -> None:
    conn.execute("UPDATE tasks SET in_esecuzione = 0 WHERE id = ?", (task_id,))
    conn.commit()


# ── settings (chiave/valore) ──────────────────────────────────────────────

def get_setting(conn: sqlite3.Connection, key: str, default: str | None = None) -> str | None:
    row = conn.execute("SELECT value FROM settings WHERE key = ?", (key,)).fetchone()
    return row["value"] if row else default


def set_setting(conn: sqlite3.Connection, key: str, value: str) -> None:
    conn.execute(
        "INSERT INTO settings (key, value) VALUES (?, ?) "
        "ON CONFLICT(key) DO UPDATE SET value = excluded.value",
        (key, value),
    )
    conn.commit()


# ── memory_suggestions (manutenzione L2) ──────────────────────────────────

def add_suggestion(conn: sqlite3.Connection, tipo: str, motivazione: str,
                   citazione: str, proposta: str | None = None) -> int:
    cur = conn.execute(
        "INSERT INTO memory_suggestions (tipo, motivazione, citazione, proposta) "
        "VALUES (?, ?, ?, ?)",
        (tipo, motivazione, citazione, proposta),
    )
    conn.commit()
    return cur.lastrowid


def list_suggestions(conn: sqlite3.Connection, stato: str = "in_attesa") -> list[sqlite3.Row]:
    return conn.execute(
        "SELECT * FROM memory_suggestions WHERE stato = ? ORDER BY id", (stato,)
    ).fetchall()


def set_suggestion_state(conn: sqlite3.Connection, sugg_id: int, stato: str) -> None:
    conn.execute(
        "UPDATE memory_suggestions SET stato = ? WHERE id = ?", (stato, sugg_id)
    )
    conn.commit()


# ── drafts (proposte in attesa di conferma) ───────────────────────────────

def add_draft(conn: sqlite3.Connection, kind: str, payload: str) -> int:
    cur = conn.execute(
        "INSERT INTO drafts (kind, payload) VALUES (?, ?)", (kind, payload)
    )
    conn.commit()
    return cur.lastrowid


def get_draft(conn: sqlite3.Connection, draft_id: int) -> sqlite3.Row | None:
    return conn.execute("SELECT * FROM drafts WHERE id = ?", (draft_id,)).fetchone()


def delete_draft(conn: sqlite3.Connection, draft_id: int) -> None:
    conn.execute("DELETE FROM drafts WHERE id = ?", (draft_id,))
    conn.commit()
