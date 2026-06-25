"""Scheduler — cuore proattivo. proponi → conferma → esegui (§8).

- next_run(cron): parsing CUSTOM, solo due formati (niente croniter):
    "daily HH:MM"  → ogni giorno a quell'ora
    "every Nh"     → ogni N ore
- tick(): callback JobQueue (ptb), esegue le task dovute con lock
  anti-doppia-esecuzione. Failure = log + notifica + STOP (sospende la task,
  niente retry — §8 prevale su SPECS §17.2).
- propose_from_text(): estrae l'intent via LLM e INSERT come 'proposta'.

Timestamp in "%Y-%m-%d %H:%M:%S" per combaciare coi confronti SQLite.
"""
from __future__ import annotations

import json
import re
from datetime import datetime, timedelta

from telegram.ext import ContextTypes

from config import Config
from handlers import storage
from handlers.deepseek_api import DeepSeekClient
from skills import meteo

TS_FMT = "%Y-%m-%d %H:%M:%S"

_DAILY = re.compile(r"^daily\s+(\d{1,2}):(\d{2})$")
_EVERY = re.compile(r"^every\s+(\d{1,3})h$")

# Skill disponibili (MVP: solo meteo, no-OAuth).
_INTENT_PROMPT = (
    "Estrai una task pianificata dal testo dell'utente. Rispondi SOLO con JSON, "
    "nessun altro testo. Schema:\n"
    '{"cron": "daily HH:MM" oppure "every Nh", '
    '"azione": "meteo", '
    '"payload": {"citta": "<nome città se serve>"}, '
    '"descrizione": "<frase breve>"}\n'
    "Azioni ammesse: solo \"meteo\". Se non riesci a estrarre una task valida, "
    'rispondi {"errore": "motivo"}.'
)


class Scheduler:
    def __init__(self, cfg: Config, db, ds: DeepSeekClient, bot=None):
        self._cfg = cfg
        self._db = db
        self._ds = ds
        self._bot = bot  # telegram.Bot, iniettato da main.py per inviare
        self._skills = {"meteo": self._run_meteo}

    # ── Parsing cron custom ───────────────────────────────────────────────────

    def next_run(self, cron: str, now: datetime | None = None) -> str | None:
        """Prossima esecuzione come stringa ISO, o None se il formato è ignoto."""
        now = now or datetime.now()
        cron = (cron or "").strip().lower()

        m = _DAILY.match(cron)
        if m:
            h, mi = int(m.group(1)), int(m.group(2))
            if not (0 <= h <= 23 and 0 <= mi <= 59):
                return None
            target = now.replace(hour=h, minute=mi, second=0, microsecond=0)
            if target <= now:
                target += timedelta(days=1)
            return target.strftime(TS_FMT)

        m = _EVERY.match(cron)
        if m:
            n = int(m.group(1))
            if n <= 0:
                return None
            return (now + timedelta(hours=n)).strftime(TS_FMT)

        return None

    # ── Tick (JobQueue) ───────────────────────────────────────────────────────

    async def tick(self, context: ContextTypes.DEFAULT_TYPE) -> None:
        now_str = datetime.now().strftime(TS_FMT)
        for task in storage.get_due_tasks(self._db, now_str):
            tid = task["id"]
            if not storage.acquire_task_lock(self._db, tid):
                continue  # già in esecuzione (lock)
            try:
                out = await self._dispatch(task)
                await self._send(out)
                prossima = self.next_run(task["cron"])
                storage.set_task_schedule(self._db, tid,
                                          ultima_esecuzione=now_str,
                                          prossima_esecuzione=prossima)
                storage.log(self._db, "INFO", "task eseguita", {"id": tid})
            except Exception as e:  # failure = log + notifica + STOP (§8)
                storage.log(self._db, "ERROR", "task fallita", {"id": tid, "err": str(e)})
                storage.set_task_state(self._db, tid, "sospesa")
                await self._send(f"⚠️ Task #{tid} fallita: {e}. Sospesa, niente retry.")
            finally:
                storage.release_task_lock(self._db, tid)

    async def _dispatch(self, task) -> str:
        fn = self._skills.get(task["azione"])
        if fn is None:
            raise ValueError(f"skill sconosciuta: {task['azione']}")
        payload = json.loads(task["payload"]) if task["payload"] else {}
        return await fn(payload)

    async def _run_meteo(self, payload: dict) -> str:
        return await meteo.get_meteo(payload.get("citta", ""))

    async def _send(self, text: str) -> None:
        if self._bot is None:
            return
        await self._bot.send_message(chat_id=self._cfg.telegram.chat_id, text=text)

    # ── Proposta da linguaggio naturale ───────────────────────────────────────

    async def propose_from_text(self, text: str) -> dict:
        """Estrae l'intent via LLM e crea una task 'proposta'. Ritorna un dict
        con esito: {"id", "descrizione", "cron"} oppure {"errore": ...}."""
        result = await self._ds.chat([
            {"role": "system", "content": _INTENT_PROMPT},
            {"role": "user", "content": text},
        ])
        try:
            data = json.loads(self._extract_json(result.content))
        except (ValueError, json.JSONDecodeError):
            return {"errore": "non sono riuscito a interpretare la richiesta"}
        if "errore" in data:
            return {"errore": data["errore"]}

        cron = str(data.get("cron", "")).strip()
        azione = str(data.get("azione", "")).strip()
        if self.next_run(cron) is None:
            return {"errore": f"orario non valido: '{cron}'"}
        if azione not in self._skills:
            return {"errore": f"azione non supportata: '{azione}'"}

        tid = storage.add_task(
            self._db, cron=cron,
            descrizione=str(data.get("descrizione", azione)),
            azione=azione, payload=data.get("payload") or {},
            stato="proposta",
        )
        return {"id": tid, "descrizione": data.get("descrizione", azione), "cron": cron}

    @staticmethod
    def _extract_json(text: str) -> str:
        """Isola il primo blocco {...} dalla risposta LLM (tollera testo attorno)."""
        start, end = text.find("{"), text.rfind("}")
        if start == -1 or end == -1 or end < start:
            raise ValueError("nessun JSON nella risposta")
        return text[start:end + 1]
