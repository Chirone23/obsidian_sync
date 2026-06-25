"""Configurazione runtime — carica .env in un oggetto immutabile.

Fail-fast: se manca un secret obbligatorio, il bot non parte (meglio che
crashare a metà di una chiamata). Le chiavi NON vengono mai loggate.
"""
from __future__ import annotations

import os
from dataclasses import dataclass
from pathlib import Path

from dotenv import load_dotenv

load_dotenv()


def _require(key: str) -> str:
    val = os.getenv(key, "").strip()
    if not val:
        raise RuntimeError(f"Variabile d'ambiente obbligatoria mancante: {key}")
    return val


@dataclass(frozen=True)
class DeepSeekConfig:
    api_key: str
    base_url: str
    model: str
    temperature: float
    max_tokens: int
    timeout_sec: int


@dataclass(frozen=True)
class TelegramConfig:
    bot_token: str
    chat_id: int


@dataclass(frozen=True)
class VaultConfig:
    path: Path
    persona_file: str
    padrone_file: str
    memory_file: str

    def persona(self) -> Path:
        return self.path / self.persona_file

    def padrone(self) -> Path:
        return self.path / self.padrone_file

    def memory(self) -> Path:
        return self.path / self.memory_file


@dataclass(frozen=True)
class Config:
    deepseek: DeepSeekConfig
    telegram: TelegramConfig
    vault: VaultConfig
    db_path: Path
    log_level: str
    context_window_msgs: int
    scheduler_tick_sec: int


def load_config() -> Config:
    return Config(
        deepseek=DeepSeekConfig(
            api_key=_require("DEEPSEEK_API_KEY"),
            base_url=os.getenv("DEEPSEEK_BASE_URL", "https://api.deepseek.com"),
            model=os.getenv("DEEPSEEK_MODEL", "deepseek-v4-flash"),
            temperature=float(os.getenv("DEEPSEEK_TEMPERATURE", "0.7")),
            max_tokens=int(os.getenv("DEEPSEEK_MAX_TOKENS", "2000")),
            timeout_sec=int(os.getenv("DEEPSEEK_TIMEOUT_SEC", "60")),
        ),
        telegram=TelegramConfig(
            bot_token=_require("TELEGRAM_BOT_TOKEN"),
            chat_id=int(_require("TELEGRAM_CHAT_ID")),
        ),
        vault=VaultConfig(
            path=Path(os.getenv("VAULT_PATH", "/vault")),
            persona_file=os.getenv("PERSONA_FILE", "skill/bot-persona.md"),
            padrone_file=os.getenv("PADRONE_FILE", "skill/bot-padrone.md"),
            memory_file=os.getenv("MEMORY_FILE", "idee/bot-memory.md"),
        ),
        db_path=Path(os.getenv("DB_PATH", "/app/storage/bot.db")),
        log_level=os.getenv("LOG_LEVEL", "INFO").upper(),
        context_window_msgs=int(os.getenv("CONTEXT_WINDOW_MSGS", "10")),
        scheduler_tick_sec=int(os.getenv("SCHEDULER_TICK_SEC", "60")),
    )
