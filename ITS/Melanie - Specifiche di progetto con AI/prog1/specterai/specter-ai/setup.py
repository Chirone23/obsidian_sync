"""Setup guidato di SpecterAI.

Esegui:  python setup.py

Configura in modo interattivo:
  1. il backend LLM (Claude Code CLI oppure API Anthropic via SDK);
  2. le credenziali necessarie (login CLI o ANTHROPIC_API_KEY);
  3. il motore privacy (spaCy + modello italiano it_core_news_sm).

Scrive le scelte in `.env` (gitignored). Chiede sempre conferma prima di
installare qualcosa.
"""

import getpass
import shutil
import subprocess
import sys
from pathlib import Path

ENV_PATH = Path(__file__).parent / ".env"
SPACY_MODEL = "it_core_news_sm"


# ──────────────────────────────────────────────────────────────────────────
# Utility
# ──────────────────────────────────────────────────────────────────────────

def ask(prompt: str, default: str | None = None) -> str:
    suffix = f" [{default}]" if default else ""
    answer = input(f"{prompt}{suffix}: ").strip()
    return answer or (default or "")


def confirm(prompt: str) -> bool:
    return ask(f"{prompt} (s/n)", "n").lower() in ("s", "si", "sì", "y", "yes")


def read_env() -> dict[str, str]:
    env: dict[str, str] = {}
    if ENV_PATH.exists():
        for line in ENV_PATH.read_text(encoding="utf-8").splitlines():
            line = line.strip()
            if line and not line.startswith("#") and "=" in line:
                k, v = line.split("=", 1)
                env[k.strip()] = v.strip()
    return env


def write_env(updates: dict[str, str]) -> None:
    """Upsert delle chiavi senza perdere quelle esistenti."""
    env = read_env()
    env.update(updates)
    lines = [
        "# Config SpecterAI — generato da setup.py. NON committare (è in .gitignore).",
        f"LLM_BACKEND={env.get('LLM_BACKEND', 'cli')}",
        f"CLAUDE_MODEL={env.get('CLAUDE_MODEL', 'claude-sonnet-4-6')}",
        f"ANTHROPIC_API_KEY={env.get('ANTHROPIC_API_KEY', '')}",
    ]
    ENV_PATH.write_text("\n".join(lines) + "\n", encoding="utf-8")
    print(f"  ✓ scritto {ENV_PATH.name}")


def run(cmd: list[str]) -> int:
    print(f"  → {' '.join(cmd)}")
    return subprocess.run(cmd).returncode


# ──────────────────────────────────────────────────────────────────────────
# Step 1 — backend CLI
# ──────────────────────────────────────────────────────────────────────────

def setup_cli() -> None:
    print("\n[Backend CLI — Claude Code]")
    print("  Costo marginale €0 (usa il tuo abbonamento), ma funziona SOLO su una")
    print("  macchina dove `claude` è installato e autenticato col tuo account.")

    if not confirm("Controllo se Claude Code è installato?"):
        print("  · salto il controllo. Assicurati che `claude` sia nel PATH.")
        return

    if shutil.which("claude"):
        ver = subprocess.run(["claude", "--version"], capture_output=True, text=True)
        print(f"  ✓ Claude Code trovato: {ver.stdout.strip() or 'ok'}")
    else:
        print("  ✗ `claude` non trovato nel PATH.")
        print("    Installazione consigliata (richiede Node.js):")
        print("      npm install -g @anthropic-ai/claude-code")
        if shutil.which("npm") and confirm("Provo a installarlo ora con npm?"):
            if run(["npm", "install", "-g", "@anthropic-ai/claude-code"]) != 0:
                print("  ✗ installazione fallita — installa manualmente e rilancia setup.")
                return
        else:
            print("  · installa manualmente, poi rilancia `python setup.py`.")
            return

    # Login: NON automatizzabile (OAuth nel browser) → guida l'utente.
    print("\n  Verifica login: apri un terminale e lancia `claude` una volta.")
    print("  Se chiede di autenticarti, completa il login nel browser.")
    print("  (Se stai già usando Claude Code, sei già autenticato.)")


# ──────────────────────────────────────────────────────────────────────────
# Step 1 — backend SDK
# ──────────────────────────────────────────────────────────────────────────

def setup_sdk() -> None:
    print("\n[Backend SDK — API Anthropic]")
    print("  Deployabile ovunque (chiunque con una API key). Costo a consumo")
    print("  (~0,04 €/analisi su Sonnet). Usa le Commercial Terms (no-training).")

    key = getpass.getpass("  Incolla la ANTHROPIC_API_KEY (input nascosto): ").strip()
    if not key:
        print("  ✗ nessuna chiave inserita — riavvia setup quando ce l'hai.")
        return
    write_env({"ANTHROPIC_API_KEY": key})

    try:
        import anthropic
    except ImportError:
        print("  ✗ SDK 'anthropic' non installato.")
        if confirm("Lo installo ora (pip install anthropic)?"):
            run([sys.executable, "-m", "pip", "install", "anthropic"])
        return

    if confirm("Faccio una chiamata di test per validare la chiave?"):
        try:
            client = anthropic.Anthropic(api_key=key)
            client.messages.create(
                model="claude-sonnet-4-6", max_tokens=8,
                messages=[{"role": "user", "content": "ping"}],
            )
            print("  ✓ chiave valida, API raggiungibile.")
        except Exception as e:  # noqa: BLE001 — feedback utente, non logica
            print(f"  ✗ chiamata fallita: {e}")


# ──────────────────────────────────────────────────────────────────────────
# Step 2 — motore privacy (spaCy)
# ──────────────────────────────────────────────────────────────────────────

def setup_privacy_engine() -> None:
    print("\n[Motore privacy — spaCy NER italiano]")
    print(f"  Serve il modello `{SPACY_MODEL}` per redarre nomi/luoghi (PII) prima")
    print("  di inviare il contratto al modello.")

    try:
        import spacy
    except ImportError:
        print("  ✗ spaCy non installato.")
        if confirm("Lo installo ora (pip install spacy)?"):
            if run([sys.executable, "-m", "pip", "install", "spacy"]) != 0:
                return
        else:
            return

    try:
        import spacy
        spacy.load(SPACY_MODEL)
        print(f"  ✓ modello `{SPACY_MODEL}` già installato e caricabile.")
    except OSError:
        print(f"  ✗ modello `{SPACY_MODEL}` non presente.")
        print("    Nota: usiamo `_sm` (leggero), non `_lg` — il lg va in timeout su Windows.")
        if confirm("Lo scarico ora (python -m spacy download it_core_news_sm)?"):
            run([sys.executable, "-m", "spacy", "download", SPACY_MODEL])
        else:
            print("  · senza modello, il filtro gira in modalità solo-regex (no NER nomi/luoghi).")


# ──────────────────────────────────────────────────────────────────────────
# Main
# ──────────────────────────────────────────────────────────────────────────

def main() -> None:
    print("=" * 64)
    print(" SpecterAI — setup guidato")
    print("=" * 64)

    print("\nScegli il backend LLM:")
    print("  1) cli  — Claude Code (gratis, solo questa macchina)  [default]")
    print("  2) sdk  — API Anthropic (deployabile, richiede API key)")
    choice = ask("Opzione 1 o 2", "1")
    backend = "sdk" if choice in ("2", "sdk") else "cli"

    write_env({"LLM_BACKEND": backend})

    if backend == "cli":
        setup_cli()
    else:
        setup_sdk()

    setup_privacy_engine()

    print("\n" + "=" * 64)
    print(f" Setup completato. Backend: {backend}")
    print(" Avvio app:  python -m uvicorn main:app --reload")
    print("=" * 64)


if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\nInterrotto.")
