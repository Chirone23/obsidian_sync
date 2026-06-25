"""Handler Telegram — comandi MVP + chat. Lega storage, reader, deepseek.

Auth: SOLO il chat_id del padrone (da config). Ogni altro chat viene ignorato.
Dependency injection: lo scheduler è iniettato (duck-typed) per non importare
moduli che non esistono ancora — il wiring avviene in main.py.

Failure di una chat = notifica + log + STOP (§8): nessun retry.
"""
from __future__ import annotations

import json
import sqlite3
from collections import deque
from datetime import datetime

from telegram import Update
from telegram.ext import (
    Application,
    CommandHandler,
    ContextTypes,
    MessageHandler,
    filters,
)

from config import Config
from handlers.deepseek_api import DeepSeekClient
from handlers import obsidian_reader as reader
from handlers import storage

# Keyword che, in chat normale, fanno scattare la creazione di una task.
# Match deterministico (zero LLM finché non c'è match).
TASK_KEYWORDS = ("programma", "programmami", "pianifica", "ricordami")


class TelegramHandler:
    def __init__(self, cfg: Config, db: sqlite3.Connection,
                 ds: DeepSeekClient, scheduler=None):
        self._cfg = cfg
        self._db = db
        self._ds = ds
        self._scheduler = scheduler  # iniettato da main.py (ha .next_run(cron, now))
        # Working memory: ultimi N messaggi (mix user/assistant), in RAM (§8).
        self._history: deque[dict] = deque(maxlen=cfg.context_window_msgs)

    # ── Registrazione ───────────────────────────────────────────────────────

    def register(self, app: Application) -> None:
        app.add_handler(CommandHandler("start", self.start))
        app.add_handler(CommandHandler("status", self.status))
        app.add_handler(CommandHandler("ricorda", self.ricorda))
        app.add_handler(CommandHandler("salva", self.salva))
        app.add_handler(CommandHandler("scarta", self.scarta))
        app.add_handler(CommandHandler("task", self.task))
        app.add_handler(CommandHandler("tasks", self.tasks))
        app.add_handler(CommandHandler("conferma", self.conferma))
        app.add_handler(CommandHandler("annulla", self.annulla))
        app.add_handler(CommandHandler("pausa", self.pausa))
        app.add_handler(CommandHandler("riprendi", self.riprendi))
        app.add_handler(CommandHandler("stop", self.stop))
        app.add_handler(CommandHandler("automemoria", self.automemoria))
        app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, self.on_message))

    # ── Auth ──────────────────────────────────────────────────────────────────

    def _authorized(self, update: Update) -> bool:
        chat = update.effective_chat
        return chat is not None and chat.id == self._cfg.telegram.chat_id

    @staticmethod
    def _arg_id(context: ContextTypes.DEFAULT_TYPE) -> int | None:
        if not context.args:
            return None
        try:
            return int(context.args[0])
        except ValueError:
            return None

    # ── Chat ──────────────────────────────────────────────────────────────────

    def _build_messages(self, text: str) -> list[dict]:
        """System = contesto memoria (system+padrone+memory+MOC+link); poi
        storia; poi messaggio corrente. In forma OpenAI role-separated."""
        system = reader.read_context(self._cfg.memory)
        messages = [{"role": "system", "content": system}]
        messages.extend(self._history)
        messages.append({"role": "user", "content": text})
        return messages

    async def on_message(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        if not self._authorized(update):
            return
        text = update.message.text
        # Scanner keyword (deterministico): se la frase contiene una parola
        # chiave-task, instrada alla creazione task invece della chat.
        parole = set(text.lower().split())
        if parole & set(TASK_KEYWORDS):
            await self._propose_task(update, text)
            return
        try:
            result = await self._ds.chat(self._build_messages(text))
        except Exception as e:  # failure = notifica + log + STOP (§8)
            storage.log(self._db, "ERROR", "chat fallita", {"err": str(e)})
            storage.bump_stats(self._db, errors=1)
            await update.message.reply_text("⚠️ Errore nel rispondere. Ho loggato e mi fermo.")
            return
        footer = (f"\n\n<!-- tokens_in: {result.tokens_in} | tokens_out: {result.tokens_out} "
                  f"| time_ms: {result.response_time_ms} | model: {self._cfg.deepseek.model} -->")
        await update.message.reply_text(result.content + footer)
        self._history.append({"role": "user", "content": text})
        self._history.append({"role": "assistant", "content": result.content})
        storage.bump_stats(self._db, messages=1, tokens_in=result.tokens_in,
                           tokens_out=result.tokens_out,
                           response_time_ms=result.response_time_ms)
        storage.log(self._db, "INFO", "chat ok",
                    {"tokens_in": result.tokens_in, "tokens_out": result.tokens_out,
                     "time_ms": result.response_time_ms})

    # ── Comandi base ────────────────────────────────────────────────────────

    async def start(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        if not self._authorized(update):
            return
        await update.message.reply_text(
            "NOA attivo. Comandi: /status /ricorda (/salva /scarta) /task /tasks "
            "/conferma /annulla /pausa /riprendi /stop /automemoria"
        )

    async def status(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        if not self._authorized(update):
            return
        s = storage.stats_today(self._db)
        tokens = (s["tokens_in"] + s["tokens_out"]) if s else 0
        msgs = s["messages_count"] if s else 0
        errs = storage.errors_last_24h(self._db)
        attive = len(storage.list_tasks(self._db, "attiva"))
        try:
            bal = await self._ds.get_balance()
            saldo = f"{bal.total} {bal.currency}" + ("" if bal.is_available else " (non disponibile)")
        except Exception as e:
            storage.log(self._db, "WARNING", "balance non recuperato", {"err": str(e)})
            saldo = "n/d"
        await update.message.reply_text(
            f"📊 *Status*\n"
            f"• Messaggi oggi: {msgs}\n"
            f"• Token oggi: {tokens}\n"
            f"• Errori 24h: {errs}\n"
            f"• Task attive: {attive}\n"
            f"• Saldo: {saldo}",
            parse_mode="Markdown",
        )

    _RICORDA_PROMPT = (
        "Analizza la conversazione recente ed estrai i fatti che vale la pena "
        "memorizzare a lungo termine sul padrone o sul contesto. Regole: solo cose "
        "DETTE (niente tue inferenze), un fatto atomico per riga, raggruppati per "
        "topic, niente segreti/credenziali. Rispondi SOLO con un elenco markdown "
        "di righe '- <fatto>'. Se non c'è nulla da salvare, rispondi 'NIENTE'."
    )

    async def ricorda(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        """Capture (memory-rules §1): NOA analizza la conversazione e PROPONE uno
        schema; nulla viene scritto finché non confermi con /salva."""
        if not self._authorized(update):
            return
        storia = "\n".join(f"{m['role']}: {m['content']}" for m in self._history)
        if not storia:
            await update.message.reply_text("Non c'è ancora conversazione da cui estrarre fatti.")
            return
        try:
            result = await self._ds.chat([
                {"role": "system", "content": self._RICORDA_PROMPT},
                {"role": "user", "content": storia},
            ])
        except Exception as e:
            storage.log(self._db, "ERROR", "ricorda: analisi fallita", {"err": str(e)})
            await update.message.reply_text("⚠️ Errore nell'analisi. Mi fermo.")
            return
        schema = result.content.strip()
        if schema.upper().startswith("NIENTE") or not schema:
            await update.message.reply_text("Niente di rilevante da salvare.")
            return
        draft_id = storage.add_draft(self._db, "memory", json.dumps({"schema": schema}))
        await update.message.reply_text(
            f"🧠 Proposta di memoria:\n\n{schema}\n\n"
            f"Salvo? [/salva {draft_id}] [/scarta {draft_id}]"
        )

    async def salva(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        if not self._authorized(update):
            return
        did = self._arg_id(context)
        draft = storage.get_draft(self._db, did) if did is not None else None
        if not draft or draft["kind"] != "memory":
            await update.message.reply_text("Nessuna proposta di memoria con questo id.")
            return
        schema = json.loads(draft["payload"])["schema"]
        oggi = datetime.now().strftime("%Y-%m-%d")
        for riga in schema.splitlines():
            fatto = riga.strip().lstrip("-").strip()
            if fatto:
                reader.append_memory(self._cfg.memory, f"[{oggi}] {fatto}")
        storage.delete_draft(self._db, did)
        storage.log(self._db, "INFO", "memoria salvata", {"draft": did})
        await update.message.reply_text("🧠 Salvato in memoria.")

    async def scarta(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        if not self._authorized(update):
            return
        did = self._arg_id(context)
        if did is None or not storage.get_draft(self._db, did):
            await update.message.reply_text("Nessuna proposta con questo id.")
            return
        storage.delete_draft(self._db, did)
        await update.message.reply_text("🗑 Proposta scartata.")

    # ── Comandi task ──────────────────────────────────────────────────────────

    async def task(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        """Comando esplicito per creare una task. Stessa logica delle keyword."""
        if not self._authorized(update):
            return
        testo = " ".join(context.args).strip()
        if not testo:
            await update.message.reply_text(
                "Uso: /task <cosa e quando>\nEs: /task ogni giorno alle 8 il meteo di Roma"
            )
            return
        await self._propose_task(update, testo)

    async def _propose_task(self, update: Update, testo: str) -> None:
        """Estrae l'intent e propone la task. Se manca l'orario, chiede di
        specificarlo (follow-up one-shot: riscrivi il comando completo)."""
        if self._scheduler is None:
            await update.message.reply_text("Scheduler non disponibile.")
            return
        esito = await self._scheduler.propose_from_text(testo)
        if "errore" in esito:
            await update.message.reply_text(
                f"❌ {esito['errore']}\nSpecifica ogni quanto, es: 'ogni giorno alle 8' "
                f"oppure 'ogni 3 ore'."
            )
            return
        await update.message.reply_text(
            f"📋 Proposta task #{esito['id']}:\n"
            f"'{esito['descrizione']}' — {esito['cron']}\n"
            f"Confermo? [/conferma {esito['id']}] [/annulla {esito['id']}]"
        )

    async def automemoria(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        """Attiva/disattiva la manutenzione automatica della memoria (L1/L2)."""
        if not self._authorized(update):
            return
        arg = (context.args[0].lower() if context.args else "")
        if arg not in ("on", "off"):
            stato = storage.get_setting(self._db, "automemoria", "on")
            await update.message.reply_text(f"Manutenzione memoria: {stato}. Uso: /automemoria on|off")
            return
        storage.set_setting(self._db, "automemoria", arg)
        await update.message.reply_text(f"Manutenzione memoria: {arg}.")

    async def tasks(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        if not self._authorized(update):
            return
        rows = [r for r in storage.list_tasks(self._db)
                if r["stato"] in ("attiva", "proposta", "sospesa")]
        if not rows:
            await update.message.reply_text("Nessuna task.")
            return
        righe = [
            f"#{r['id']} [{r['stato']}] {r['descrizione']} — {r['cron']}"
            + (f" → {r['prossima_esecuzione']}" if r["prossima_esecuzione"] else "")
            for r in rows
        ]
        await update.message.reply_text("📋 Task:\n" + "\n".join(righe))

    async def conferma(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        if not self._authorized(update):
            return
        tid = self._arg_id(context)
        if tid is None:
            await update.message.reply_text("Uso: /conferma <id>")
            return
        task = storage.get_task(self._db, tid)
        if not task or task["stato"] != "proposta":
            await update.message.reply_text("Nessuna task 'proposta' con questo id.")
            return
        prossima = self._next_run(task["cron"])
        storage.set_task_state(self._db, tid, "attiva")
        storage.set_task_schedule(self._db, tid, prossima_esecuzione=prossima)
        await update.message.reply_text(f"✅ Task #{tid} attiva. Prossima: {prossima}")

    async def annulla(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        if not self._authorized(update):
            return
        tid = self._arg_id(context)
        if tid is None:
            await update.message.reply_text("Uso: /annulla <id>")
            return
        task = storage.get_task(self._db, tid)
        if not task or task["stato"] != "proposta":
            await update.message.reply_text("Nessuna task 'proposta' con questo id.")
            return
        storage.delete_task(self._db, tid)
        await update.message.reply_text(f"🗑 Task #{tid} annullata.")

    async def pausa(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        if not self._authorized(update):
            return
        tid = self._arg_id(context)
        if tid is None:
            await update.message.reply_text("Uso: /pausa <id>")
            return
        task = storage.get_task(self._db, tid)
        if not task or task["stato"] != "attiva":
            await update.message.reply_text("Nessuna task 'attiva' con questo id.")
            return
        storage.set_task_state(self._db, tid, "sospesa")
        await update.message.reply_text(f"⏸ Task #{tid} sospesa.")

    async def riprendi(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        if not self._authorized(update):
            return
        tid = self._arg_id(context)
        if tid is None:
            await update.message.reply_text("Uso: /riprendi <id>")
            return
        task = storage.get_task(self._db, tid)
        if not task or task["stato"] != "sospesa":
            await update.message.reply_text("Nessuna task 'sospesa' con questo id.")
            return
        prossima = self._next_run(task["cron"])
        storage.set_task_state(self._db, tid, "attiva")
        storage.set_task_schedule(self._db, tid, prossima_esecuzione=prossima)
        await update.message.reply_text(f"▶️ Task #{tid} ripresa. Prossima: {prossima}")

    async def stop(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        if not self._authorized(update):
            return
        tid = self._arg_id(context)
        if tid is None:
            await update.message.reply_text("Uso: /stop <id>")
            return
        if not storage.get_task(self._db, tid):
            await update.message.reply_text("Task inesistente.")
            return
        storage.delete_task(self._db, tid)
        await update.message.reply_text(f"🛑 Task #{tid} eliminata.")

    # ── Helper ────────────────────────────────────────────────────────────────

    def _next_run(self, cron: str) -> str | None:
        """Delega allo scheduler iniettato il calcolo del prossimo orario.
        Se non iniettato (es. test isolato) restituisce None."""
        if self._scheduler is None:
            return None
        return self._scheduler.next_run(cron)
