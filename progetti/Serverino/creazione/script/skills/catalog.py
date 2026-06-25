"""Catalogo skill — UNICA fonte di verità per il menu mostrato a NOA.

`skills-menu.md` viene rigenerato da qui all'avvio (main.py), così il menu non
si disallinea mai dal codice. Per aggiungere una skill: una voce qui + il
dispatch in scheduler._skills.
"""
from __future__ import annotations

from pathlib import Path

SKILLS = [
    {
        "name": "meteo",
        "trigger": "es. 'meteo a Roma'",
        "desc": "Meteo attuale + min/max di oggi per una città (Open-Meteo, nessuna key).",
    },
]


def render_menu() -> str:
    righe = ["# Skill disponibili", ""]
    for s in SKILLS:
        righe.append(f"- **{s['name']}** — {s['desc']} ({s['trigger']})")
    righe.append("")
    righe.append("Per pianificare una skill nel tempo: comando /task o keyword "
                 "(programma, programmami, pianifica, ricordami).")
    return "\n".join(righe) + "\n"


def write_menu(path: Path) -> None:
    """Scrive il menu nella cartella memoria (mount rw). Best-effort: se il
    percorso non è scrivibile non blocca l'avvio."""
    try:
        path.parent.mkdir(parents=True, exist_ok=True)
        path.write_text(render_menu(), encoding="utf-8")
    except OSError:
        pass
