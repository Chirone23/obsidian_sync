import json
import re
import subprocess
from pathlib import Path

from privacy_filter import redact, restore
from schemas import ContractAnalysis


_SYSTEM_PROMPT_PATH = Path(__file__).parent / "prompts" / "system_prompt.md"
_MODEL = "claude-sonnet-4-6"


def _load_system_prompt() -> str:
    raw = _SYSTEM_PROMPT_PATH.read_text(encoding="utf-8")
    # Estrae solo il blocco dentro i triple backtick dopo "## SYSTEM PROMPT"
    match = re.search(r"## SYSTEM PROMPT\s*```(.*?)```", raw, re.DOTALL)
    if not match:
        raise ValueError("System prompt non trovato in prompts/system_prompt.md")
    return match.group(1).strip()


def _call_claude(system_prompt: str, user_message: str) -> str:
    result = subprocess.run(
        ["claude", "-p", user_message,
         "--system-prompt", system_prompt,
         "--model", _MODEL,
         "--output-format", "json"],
        capture_output=True,
        text=True,
        encoding="utf-8",
    )
    if result.returncode != 0:
        raise RuntimeError(f"Claude CLI error: {result.stderr[:300]}")

    data = json.loads(result.stdout)
    if data.get("is_error"):
        raise RuntimeError(f"Claude API error: {data}")
    return data["result"]


def _restore_excerpts(analysis: ContractAnalysis, mapping: dict) -> ContractAnalysis:
    for cat in analysis.categories.values():
        cat.raw_excerpt = [restore(span, mapping) for span in cat.raw_excerpt]
    return analysis


def analyze(contract_text: str, metadata: dict) -> ContractAnalysis:
    text_redacted, mapping = redact(contract_text)

    system_prompt = _load_system_prompt()

    user_message = (
        f"Analizza il seguente testo contrattuale e restituisci SOLO il JSON richiesto.\n\n"
        f"METADATI STRUTTURATI (date/importi estratti pre-analisi):\n{json.dumps(metadata, ensure_ascii=False)}\n\n"
        f"TESTO CONTRATTO:\n{text_redacted}"
    )

    raw_response = _call_claude(system_prompt, user_message)

    # Estrae il JSON dalla risposta (in caso contenga testo extra)
    json_match = re.search(r'\{.*\}', raw_response, re.DOTALL)
    if not json_match:
        raise ValueError("Nessun JSON trovato nella risposta")
    json_str = json_match.group(0)

    try:
        analysis = ContractAnalysis.model_validate_json(json_str)
    except Exception as e:
        # Retry con prompt più restrittivo
        retry_message = user_message + "\n\nRisposta precedente non valida. Restituisci ESCLUSIVAMENTE il JSON, nessun testo aggiuntivo."
        raw_response = _call_claude(system_prompt, retry_message)
        json_match = re.search(r'\{.*\}', raw_response, re.DOTALL)
        if not json_match:
            raise ValueError("Analisi non riuscita dopo retry") from e
        analysis = ContractAnalysis.model_validate_json(json_match.group(0))

    if analysis.language_detected not in ("italian", "english"):
        raise ValueError(f"language_detected non valido: {analysis.language_detected}")

    return _restore_excerpts(analysis, mapping)
