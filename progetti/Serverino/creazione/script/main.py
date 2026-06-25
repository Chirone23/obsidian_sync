"""Entry point Serverino — UN solo loop asyncio (§8).

Wiring: config → SQLite → DeepSeek → Scheduler → TelegramHandler → JobQueue.
Lo scheduler NON è un thread separato: gira come job ricorrente sulla stessa
JobQueue di python-telegram-bot (un loop, niente race su SQLite).

Lancio: `python main.py` da dentro script/ (layout a package handlers/ + skills/).
"""
from __future__ import annotations

import logging
from datetime import datetime, time

from telegram.ext import Application, ContextTypes

from config import load_config
from handlers import storage
from handlers.deepseek_api import DeepSeekClient
from handlers.telegram_handler import TelegramHandler
from skills import catalog
from skills.scheduler import Scheduler


def main() -> None:
    cfg = load_config()
    logging.basicConfig(
        level=cfg.log_level,
        format="%(asctime)s %(levelname)s %(name)s — %(message)s",
    )
    log = logging.getLogger("serverino")

    # ── Storage ──────────────────────────────────────────────────────────────
    db = storage.connect(cfg.db_path)
    storage.init_db(db)
    storage.reset_locks(db)  # azzera lucchetti orfani da eventuale crash (Decisione 1)
    storage.log(db, "INFO", "avvio bot")

    # Rigenera il menu skill dal codice (unica fonte di verità, no drift).
    catalog.write_menu(cfg.memory.skills())

    # ── DeepSeek ─────────────────────────────────────────────────────────────
    ds = DeepSeekClient(cfg.deepseek)

    # ── Shutdown pulito (registrato sul builder, non per assegnazione) ────────
    async def on_shutdown(_: Application) -> None:
        await ds.close()
        db.close()
        log.info("shutdown completato")

    # ── Telegram app (un loop) ───────────────────────────────────────────────
    app = (
        Application.builder()
        .token(cfg.telegram.bot_token)
        .post_shutdown(on_shutdown)
        .build()
    )

    # ── Scheduler + handler (DI reciproca via app.bot) ───────────────────────
    scheduler = Scheduler(cfg, db, ds, bot=app.bot)
    handler = TelegramHandler(cfg, db, ds, scheduler=scheduler)
    handler.register(app)

    # ── Trigger temporali sulla stessa JobQueue ──────────────────────────────
    app.job_queue.run_repeating(
        scheduler.tick, interval=cfg.scheduler_tick_sec, first=cfg.scheduler_tick_sec
    )

    async def retention(_: ContextTypes.DEFAULT_TYPE) -> None:
        """Pulizia log §1: solo il 1° del mese, retention 30 giorni."""
        if datetime.now().day == 1:
            n = storage.cleanup_logs(db)
            storage.log(db, "INFO", "retention log", {"eliminati": n})

    app.job_queue.run_daily(retention, time=time(hour=3, minute=0))

    async def promemoria_memoria(_: ContextTypes.DEFAULT_TYPE) -> None:
        """Promemoria giornaliero: ricorda di riordinare memory.md (se ha contenuto)."""
        testo = ""
        try:
            testo = cfg.memory.memory().read_text(encoding="utf-8").strip()
        except OSError:
            pass
        if testo:
            await app.bot.send_message(
                chat_id=cfg.telegram.chat_id,
                text="🧠 Promemoria: sistema `memory.md` (riordina i fatti del giorno).",
            )

    app.job_queue.run_daily(promemoria_memoria, time=time(hour=21, minute=0))

    log.info("NOA in ascolto (un loop asyncio, tick=%ss)", cfg.scheduler_tick_sec)
    app.run_polling()


if __name__ == "__main__":
    main()
