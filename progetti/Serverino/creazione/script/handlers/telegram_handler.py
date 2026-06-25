"""Handler Telegram — comandi MVP + chat. Lega storage, reader, deepseek.

Auth: SOLO il chat_id del padrone (da config). Ogni altro chat viene ignorato.
Dependency injection: lo scheduler è iniettato (duck-typed) per non importare
moduli che non esistono ancora — il wiring avviene in main.py.

Failure di una chat = notifica + log + STOP (§8): nessun retry.
"""
from __future__ import annotations

import sqlite3
from collections import deque

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
        app.add_handler(CommandHandler("programma", self.programma))
        app.add_handler(CommandHandler("tasks", self.tasks))
        app.add_handler(CommandHandler("conferma", self.conferma))
        app.add_handler(CommandHandler("annulla", self.annulla))
        app.add_handler(CommandHandler("pausa", self.pausa))
        app.add_handler(CommandHandler("riprendi", self.riprendi))
        app.add_handler(CommandHandler("stop", self.stop))
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
        """System = persona + padrone + memoria; poi storia; poi msg corrente.
        Equivalente al template SPECS §7 in forma OpenAI role-separated."""
        persona = reader.read_persona(self._cfg.vault)
        padrone = reader.read_padrone(self._cfg.vault)
        fatti = reader.read_memory_lines(self._cfg.vault)
        sys_parts = []
        if persona:
            sys_parts.append(f"You are:\n{persona}")
        if padrone:
            sys_parts.append(f"I am:\n{padrone}")
        if fatti:
            sys_parts.append("Context (memoria long-term):\n" + "\n".join(f"- {f}" for f in fatti))
        messages = [{"role": "system", "content": "\n\n".join(sys_parts)}]
        messages.extend(self._history)
        messages.append({"role": "user", "content": text})
        return messages

    async def on_message(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        if not self._authorized(update):
            return
        text = update.message.text
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
            "Serverino attivo. Comandi: /status /ricorda /programma /tasks "
            "/conferma /annulla /pausa /riprendi /stop"
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

    async def ricorda(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        if not self._authorized(update):
            return
        testo = " ".join(context.args).strip()
        if not testo:
            await update.message.reply_text("Uso: /ricorda <fatto da memorizzare>")
            return
        reader.append_memory(self._cfg.vault, testo)
        storage.log(self._db, "INFO", "memoria aggiunta", {"len": len(testo)})
        await update.message.reply_text("🧠 Memorizzato.")

    # ── Comandi task ──────────────────────────────────────────────────────────

    async def programma(self, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
        if not self._authorized(update):
            return
        testo = " ".join(context.args).strip()
        if not testo:
            await update.message.reply_text(
                "Uso: /programma <cosa e quando>\nEs: /programma ogni giorno alle 8 il meteo di Roma"
            )
            return
        if self._scheduler is None:
            await update.message.reply_text("Scheduler non disponibile.")
            return
        esito = await self._scheduler.propose_from_text(testo)
        if "errore" in esito:
            await update.message.reply_text(f"❌ {esito['errore']}")
            return
        await update.message.reply_text(
            f"📋 Proposta task #{esito['id']}:\n"
            f"'{esito['descrizione']}' — {esito['cron']}\n"
            f"Confermo? [/conferma {esito['id']}] [/annulla {esito['id']}]"
        )

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
