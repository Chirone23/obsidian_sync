"""
Aggiorna la lista dei modelli free su OpenRouter compatibili con opencode.
Salva in execution/free_models.json ordinati per context size (desc).

Uso: python update_free_models.py
"""
import json
import subprocess
import time
import urllib.request
from pathlib import Path

ROOT = Path(__file__).parent
OUT = ROOT / "free_models.json"
OPENROUTER_API = "https://openrouter.ai/api/v1/models"

# Filtri: vogliamo modelli text-in/text-out con context >= 32k
MIN_CONTEXT = 32_000
EXCLUDE_KEYWORDS = ("vision", "image", "flux", "audio", "tts", "embedding")

# Modelli noti come lenti/inaffidabili (timeout sistematici).
# Vengono comunque scaricati ma rilegati in fondo alla lista.
DEPRIORITIZE_IDS = {
    "google/gemma-4-26b-a4b-it:free",
    "google/gemma-4-31b-it:free",
}


def fetch_openrouter_free():
    req = urllib.request.Request(OPENROUTER_API, headers={"User-Agent": "opencode-delegate/1.0"})
    with urllib.request.urlopen(req, timeout=30) as r:
        data = json.load(r)
    out = []
    for m in data.get("data", []):
        pricing = m.get("pricing", {})
        if pricing.get("prompt") not in ("0", 0, "0.0"):
            continue
        if pricing.get("completion") not in ("0", 0, "0.0"):
            continue
        ctx = m.get("context_length") or 0
        if ctx < MIN_CONTEXT:
            continue
        mid = m.get("id", "")
        if any(kw in mid.lower() for kw in EXCLUDE_KEYWORDS):
            continue
        if not mid.endswith(":free"):
            continue
        out.append({"id": mid, "context": ctx, "name": m.get("name", mid)})
    # Ordina per affidabilita' (deprioritizzati in fondo) e poi per context desc
    out.sort(key=lambda x: (x["id"] in DEPRIORITIZE_IDS, -x["context"]))
    return out


def get_opencode_available():
    """Modelli effettivamente registrati in opencode."""
    try:
        res = subprocess.run(
            ["opencode", "models"], capture_output=True, text=True, timeout=30
        )
        return set(line.strip() for line in res.stdout.splitlines() if line.strip())
    except Exception:
        return set()


def main():
    print("Fetching OpenRouter free models...")
    free = fetch_openrouter_free()
    print(f"  Trovati {len(free)} modelli free su OpenRouter")

    available = get_opencode_available()
    print(f"  Opencode espone {len(available)} modelli totali")

    ranked = []
    for m in free:
        oc_id = f"openrouter/{m['id']}"
        if available and oc_id not in available:
            continue
        ranked.append({"opencode_id": oc_id, **m})

    payload = {
        "updated_at": int(time.time()),
        "updated_iso": time.strftime("%Y-%m-%d %H:%M:%S"),
        "count": len(ranked),
        "models": ranked,
    }
    OUT.write_text(json.dumps(payload, indent=2, ensure_ascii=False), encoding="utf-8")
    print(f"OK -> {OUT} ({len(ranked)} modelli)")
    for m in ranked[:10]:
        print(f"  - {m['opencode_id']}  ({m['context']:,} ctx)")


if __name__ == "__main__":
    main()
