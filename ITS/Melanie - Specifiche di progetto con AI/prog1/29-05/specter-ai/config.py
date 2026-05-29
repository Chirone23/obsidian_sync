"""Configurazione centralizzata di SpecterAI.

Carica le variabili da `.env` (se presente) e le espone al resto dell'app.
Scelta backend LLM:
  - "cli": Claude Code CLI (costo marginale €0 via abbonamento, ma funziona solo
           su una macchina dove `claude` è installato e autenticato — tipicamente
           solo lo sviluppatore).
  - "sdk": API Anthropic via SDK (richiede ANTHROPIC_API_KEY; deployabile ovunque,
           usa le Commercial Terms su cui si basa il ragionamento GDPR della spec §7).
"""

import os
from pathlib import Path

try:
    from dotenv import load_dotenv
    load_dotenv(Path(__file__).parent / ".env")
except ImportError:
    # python-dotenv assente: si usano le variabili d'ambiente di sistema.
    pass


VALID_BACKENDS = ("cli", "sdk")

LLM_BACKEND = os.getenv("LLM_BACKEND", "cli").strip().lower()
MODEL = os.getenv("CLAUDE_MODEL", "claude-sonnet-4-6").strip()
ANTHROPIC_API_KEY = os.getenv("ANTHROPIC_API_KEY", "").strip()

# Parametri modello (spec §6) — applicati al path SDK.
MAX_TOKENS = 2048
TEMPERATURE = 0


def validate() -> None:
    """Solleva ValueError se la configurazione è incoerente. Chiamata all'avvio."""
    if LLM_BACKEND not in VALID_BACKENDS:
        raise ValueError(
            f"LLM_BACKEND non valido: '{LLM_BACKEND}'. Usa uno tra {VALID_BACKENDS}. "
            f"Esegui `python setup.py` per configurare."
        )
    if LLM_BACKEND == "sdk" and not ANTHROPIC_API_KEY:
        raise ValueError(
            "LLM_BACKEND=sdk ma ANTHROPIC_API_KEY è vuota. "
            "Esegui `python setup.py` o imposta la chiave in `.env`."
        )
