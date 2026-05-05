"""
Wrapper per delegare task a opencode usando modelli FREE di OpenRouter.

Funzionalita':
- Auto-refresh della lista modelli ogni 7 giorni (chiama update_free_models.py)
- Tenta in ordine i top-N modelli; passa al successivo se errore (rate limit, model not found, ecc.)
- Stampa output del modello su stdout
- Exit code != 0 se tutti i fallback falliscono

Uso:
    python opencode_delegate.py "il prompt da eseguire"
    python opencode_delegate.py --top 5 "..."
    python opencode_delegate.py --model openrouter/z-ai/glm-4.5-air:free "..."
"""
import argparse
import json
import subprocess
import sys
import time
from pathlib import Path

ROOT = Path(__file__).parent
MODELS_FILE = ROOT / "free_models.json"
UPDATER = ROOT / "update_free_models.py"
MAX_AGE_SECONDS = 7 * 24 * 3600  # 7 giorni

# Pattern di errore che indicano che dobbiamo cambiare modello
TRANSIENT_ERRORS = (
    "rate limit",
    "too large",
    "tpm",
    "key limit",
    "model not found",
    "providermodelnotfound",
    "503",
    "502",
    "upstream",
    "timeout",
    "unavailable",
)


def ensure_fresh_models():
    needs_update = False
    if not MODELS_FILE.exists():
        needs_update = True
    else:
        try:
            data = json.loads(MODELS_FILE.read_text(encoding="utf-8"))
            age = time.time() - data.get("updated_at", 0)
            if age > MAX_AGE_SECONDS:
                needs_update = True
        except Exception:
            needs_update = True

    if needs_update:
        print("[delegate] Lista modelli scaduta o assente, aggiorno...", file=sys.stderr)
        subprocess.run([sys.executable, str(UPDATER)], check=False)

    return json.loads(MODELS_FILE.read_text(encoding="utf-8"))


def run_opencode(model_id: str, prompt: str, timeout: int = 180):
    cmd = [
        "opencode",
        "run",
        "--dangerously-skip-permissions",
        "-m",
        model_id,
        prompt,
    ]
    try:
        res = subprocess.run(
            cmd, capture_output=True, text=True, timeout=timeout, encoding="utf-8", errors="replace"
        )
    except subprocess.TimeoutExpired:
        return None, "timeout", ""

    out = (res.stdout or "")
    err = (res.stderr or "")
    combined = (out + "\n" + err).lower()

    if res.returncode == 0 and not any(p in combined for p in ("error:", "errore:")):
        return out, None, err

    for pat in TRANSIENT_ERRORS:
        if pat in combined:
            return None, pat, err

    return None, f"exit_{res.returncode}", err


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("prompt", help="Il prompt da inviare al modello")
    ap.add_argument("--top", type=int, default=5, help="Quanti modelli provare (default 5)")
    ap.add_argument("--model", help="Forza un modello specifico (salta fallback)")
    ap.add_argument("--timeout", type=int, default=180)
    ap.add_argument("--no-refresh", action="store_true", help="Non aggiornare la lista")
    args = ap.parse_args()

    if args.no_refresh and MODELS_FILE.exists():
        data = json.loads(MODELS_FILE.read_text(encoding="utf-8"))
    else:
        data = ensure_fresh_models()

    if args.model:
        candidates = [args.model]
    else:
        candidates = [m["opencode_id"] for m in data.get("models", [])[: args.top]]

    if not candidates:
        print("[delegate] Nessun modello disponibile.", file=sys.stderr)
        sys.exit(2)

    for i, model in enumerate(candidates, 1):
        print(f"[delegate] ({i}/{len(candidates)}) Provo {model}...", file=sys.stderr)
        out, error, raw_err = run_opencode(model, args.prompt, timeout=args.timeout)
        if error is None:
            print(out)
            print(f"[delegate] OK con {model}", file=sys.stderr)
            sys.exit(0)
        print(f"[delegate] FAIL ({error}) -> fallback", file=sys.stderr)

    print("[delegate] Tutti i fallback falliti.", file=sys.stderr)
    sys.exit(1)


if __name__ == "__main__":
    main()
