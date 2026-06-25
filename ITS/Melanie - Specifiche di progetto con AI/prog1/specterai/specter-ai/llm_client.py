import json
import os
import re
import subprocess
import tempfile
import time
from pathlib import Path

# cwd "pulita" per il CLI: evita il caricamento di CLAUDE.md/skill del vault a ogni call.
_CLEAN_CWD = Path(tempfile.gettempdir()) / "specterai_clean_cwd"
_CLEAN_CWD.mkdir(exist_ok=True)

from pydantic import ValidationError

import config
from privacy_filter import redact, restore
from schemas import ContractAnalysis


_SYSTEM_PROMPT_PATH = Path(__file__).parent / "prompts" / "system_prompt.md"


def _load_system_prompt() -> str:
    raw = _SYSTEM_PROMPT_PATH.read_text(encoding="utf-8")
    match = re.search(r"## SYSTEM PROMPT\s*```(.*?)```", raw, re.DOTALL)
    if not match:
        raise ValueError("System prompt non trovato in prompts/system_prompt.md")
    return match.group(1).strip()


def _call_cli(system_prompt: str, user_message: str) -> str:
    """Backend Claude Code CLI (costo €0, solo su macchina autenticata)."""
    # Disattiva l'extended thinking: senza, l'analisi di un contratto generava ~1700
    # token di ragionamento nascosto → ~163s. Con thinking off scende a ~13s (12x),
    # JSON valido al primo colpo. L'estrazione clausole è meccanica, non serve thinking.
    env = {**os.environ, "MAX_THINKING_TOKENS": "0"}
    result = subprocess.run(
        ["claude", "-p",
         "--system-prompt", system_prompt,
         "--model", config.MODEL,
         "--strict-mcp-config",  # ignora i server MCP utente/progetto: niente spawn a ogni call
         "--output-format", "json"],
        input=user_message,
        capture_output=True,
        text=True,
        encoding="utf-8",
        timeout=300,
        cwd=str(_CLEAN_CWD),  # cwd vuota: niente CLAUDE.md/skill del vault da caricare
        env=env,
    )
    if result.returncode != 0:
        raise RuntimeError(f"Claude CLI error: {result.stderr[:300]}")

    data = json.loads(result.stdout)
    if data.get("is_error"):
        raise RuntimeError(f"Claude API error: {data}")
    return data["result"]


# Client SDK persistente: istanziato UNA volta (al boot via warmup(), o lazy alla
# prima chiamata) e riusato per ogni analisi. È il "processo caldo in attesa":
# nessun avvio da ripagare per contratto, ogni analisi è una chiamata indipendente.
_sdk_client = None


def _get_sdk_client():
    """Ritorna il client SDK singleton, creandolo se serve."""
    global _sdk_client
    if _sdk_client is None:
        try:
            import anthropic
        except ImportError as e:
            raise RuntimeError("SDK 'anthropic' non installato (pip install anthropic)") from e
        _sdk_client = anthropic.Anthropic(api_key=config.ANTHROPIC_API_KEY or None)
    return _sdk_client


def warmup() -> None:
    """Inizializza il client SDK all'avvio del server (warm). No-op sul backend CLI."""
    if config.LLM_BACKEND == "sdk":
        _get_sdk_client()


def _call_sdk(system_prompt: str, user_message: str) -> str:
    """Backend SDK Anthropic (deployabile ovunque, richiede ANTHROPIC_API_KEY).

    Applica i parametri di determinismo della spec §6 (temperature=0, max_tokens)
    che il path CLI non espone — vedi SPEC_ERRATA ERR-08.

    Usa il client persistente (warm) + prompt caching sul system prompt: dal secondo
    contratto entro ~5 min il prefisso di sistema non viene rielaborato.
    """
    import anthropic  # per anthropic.APIError; l'import vero è già avvenuto in _get_sdk_client

    client = _get_sdk_client()
    try:
        response = client.messages.create(
            model=config.MODEL,
            max_tokens=config.MAX_TOKENS,
            temperature=config.TEMPERATURE,
            system=[{
                "type": "text",
                "text": system_prompt,
                "cache_control": {"type": "ephemeral"},  # prompt caching del prefisso di sistema
            }],
            messages=[{"role": "user", "content": user_message}],
        )
    except anthropic.APIError as e:
        # Rete/timeout/rate-limit/5xx → trattati come errore ritentabile dal loop.
        raise RuntimeError(f"Anthropic SDK error: {e}") from e

    return "".join(block.text for block in response.content if block.type == "text")


def _call_claude(system_prompt: str, user_message: str) -> str:
    """Dispatch sul backend configurato (config.LLM_BACKEND)."""
    if config.LLM_BACKEND == "sdk":
        return _call_sdk(system_prompt, user_message)
    return _call_cli(system_prompt, user_message)


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
