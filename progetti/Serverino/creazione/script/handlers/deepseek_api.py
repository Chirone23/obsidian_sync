"""Client DeepSeek — OpenAI-compatible, async (un solo loop asyncio, §8).

- chat(): completion con usage REALE (prompt/completion tokens) + tempo di risposta.
- get_balance(): saldo REALE via GET /user/balance (httpx, fuori dallo schema OpenAI).
- NESSUN retry (§8 prevale su SPECS §6.4/§18.3): un fallimento propaga,
  il chiamante notifica + logga + STOP. Il retry su CPU 6W amplifica il throttle.

model = "deepseek-v4-flash" (deepseek-chat è RITIRATO dal 24/07/2026).
"""
from __future__ import annotations

import time
from dataclasses import dataclass

import httpx
from openai import AsyncOpenAI

from config import DeepSeekConfig


@dataclass(frozen=True)
class ChatResult:
    content: str
    tokens_in: int
    tokens_out: int
    response_time_ms: int
    finish_reason: str


@dataclass(frozen=True)
class Balance:
    currency: str
    total: str          # stringa: l'API restituisce importi come "10.50"
    is_available: bool


class DeepSeekClient:
    """Wrapper sottile sopra AsyncOpenAI + httpx. Una istanza per il bot,
    riusa le connessioni. La chiave NON viene mai loggata."""

    def __init__(self, cfg: DeepSeekConfig):
        self._cfg = cfg
        self._client = AsyncOpenAI(
            api_key=cfg.api_key,
            base_url=cfg.base_url,
            timeout=cfg.timeout_sec,
        )

    async def chat(self, messages: list[dict]) -> ChatResult:
        """Una sola chiamata, nessun retry. `messages` è già nel formato OpenAI
        [{role, content}, ...] composto dal telegram_handler (system + storia)."""
        start = time.perf_counter()
        resp = await self._client.chat.completions.create(
            model=self._cfg.model,
            messages=messages,
            temperature=self._cfg.temperature,
            max_tokens=self._cfg.max_tokens,
        )
        elapsed_ms = int((time.perf_counter() - start) * 1000)
        usage = resp.usage
        return ChatResult(
            content=resp.choices[0].message.content or "",
            tokens_in=usage.prompt_tokens if usage else 0,
            tokens_out=usage.completion_tokens if usage else 0,
            response_time_ms=elapsed_ms,
            finish_reason=resp.choices[0].finish_reason or "stop",
        )

    async def get_balance(self) -> Balance:
        """GET /user/balance — endpoint proprietario DeepSeek, fuori dallo schema
        chat. Restituisce il primo conto valuta disponibile."""
        url = self._cfg.base_url.rstrip("/") + "/user/balance"
        async with httpx.AsyncClient(timeout=self._cfg.timeout_sec) as http:
            r = await http.get(url, headers={"Authorization": f"Bearer {self._cfg.api_key}"})
            r.raise_for_status()
            data = r.json()
        infos = data.get("balance_infos") or [{}]
        first = infos[0]
        return Balance(
            currency=first.get("currency", "?"),
            total=first.get("total_balance", "?"),
            is_available=bool(data.get("is_available", False)),
        )

    async def close(self) -> None:
        await self._client.close()
