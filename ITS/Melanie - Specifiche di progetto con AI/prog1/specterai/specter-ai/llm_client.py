import json
import os
import re
import subprocess
import time
from pathlib import Path

from pydantic import ValidationError

from privacy_filter import redact, restore
from schemas import ContractAnalysis


_SYSTEM_PROMPT_PATH = Path(__file__).parent / "prompts" / "system_prompt.md"
_MODEL = os.getenv("CLAUDE_MODEL", "claude-sonnet-4-6")


def _load_system_prompt() -> str:
    raw = _SYSTEM_PROMPT_PATH.read_text(encoding="utf-8")
    match = re.search(r"## SYSTEM PROMPT\s*```(.*?)```", raw, re.DOTALL)
    if not match:
        raise ValueError("System prompt non trovato in prompts/system_prompt.md")
    return match.group(1).strip()


def _call_claude(system_prompt: str, user_message: str) -> str:
    result = subprocess.run(
        ["claude", "-p",
         "--system-prompt", system_prompt,
         "--model", _MODEL,
         "--output-format", "json"],
        input=user_message,
        capture_output=True,
        text=True,
        encoding="utf-8",
        timeout=300,
    )
    if result.returncode != 0:
        raise RuntimeError(f"Claude CLI error: {result.stderr[:300]}")

    data = json.loads(result.stdout)
    if data.get("is_error"):
        raise RuntimeError(f"Claude API error: {data}")
    return data["result"]


def _parse_response(raw_response: str) -> ContractAnalysis:
    """Trova il primo oggetto JSON valido nella risposta con raw_decode."""
    idx = raw_response.find('{')
    if idx == -1:
        raise json.JSONDecodeError("Nessun JSON trovato nella risposta", raw_response, 0)
    obj, _ = json.JSONDecoder().raw_decode(raw_response, idx)
    return ContractAnalysis.model_validate(obj)


def _restore_excerpts(analysis: ContractAnalysis, mapping: dict) -> ContractAnalysis:
    restored_categories = {
        key: cat.model_copy(update={"raw_excerpt": [restore(span, mapping) for span in cat.raw_excerpt]})
        for key, cat in analysis.categories.items()
    }
    return analysis.model_copy(update={"categories": restored_categories})


def analyze(contract_text: str, metadata: dict) -> ContractAnalysis:
    text_redacted, mapping = redact(contract_text)
    system_prompt = _load_system_prompt()

    base_message = (
        f"Analizza il seguente testo contrattuale e restituisci SOLO il JSON richiesto.\n\n"
        f"METADATI STRUTTURATI (date/importi estratti pre-analisi):\n{json.dumps(metadata, ensure_ascii=False)}\n\n"
        f"TESTO CONTRATTO:\n{text_redacted}"
    )
    restrictive_suffix = (
        "\n\nRisposta precedente non valida. "
        "Restituisci ESCLUSIVAMENTE il JSON, nessun testo aggiuntivo."
    )

    message = base_message
    last_exc: Exception | None = None

    for attempt in range(3):
        if attempt > 0:
            time.sleep(2 ** (attempt - 1))  # attempt 1 → 1s, attempt 2 → 2s
        try:
            raw_response = _call_claude(system_prompt, message)
            analysis = _parse_response(raw_response)
            break
        except (subprocess.TimeoutExpired, RuntimeError) as e:
            # Errori di rete/timeout: ritenta con lo stesso messaggio
            last_exc = e
        except (json.JSONDecodeError, ValidationError) as e:
            # Risposta malformata: un solo retry con prompt restrittivo
            last_exc = e
            if message is base_message:
                message = base_message + restrictive_suffix
            else:
                raise ValueError("Analisi non riuscita dopo retry") from e
        except ValueError:
            raise
    else:
        raise RuntimeError("Servizio non disponibile dopo retry") from last_exc

    if analysis.language_detected not in ("italian", "english"):
        raise ValueError(f"language_detected non valido: {analysis.language_detected}")

    return _restore_excerpts(analysis, mapping)
