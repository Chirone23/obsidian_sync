"""Lettura del contesto dal FILESYSTEM (MCP Obsidian è morto headless, §8).

Legge persona/padrone/memory dal clone git locale del vault, ignora il
frontmatter YAML. Gestisce `/ricorda` in append-only (zero LLM, §8).
File mancante = stringa vuota, non crash: il bot deve partire comunque.
"""
from __future__ import annotations

from pathlib import Path

from config import VaultConfig


def strip_frontmatter(text: str) -> str:
    """Toglie il blocco YAML iniziale delimitato da '---' … '---'.
    Se non c'è (o è malformato) restituisce il testo intatto."""
    if not text.startswith("---"):
        return text.lstrip("﻿").lstrip()
    lines = text.splitlines()
    # lines[0] è '---'; cerca il '---' di chiusura.
    for i in range(1, len(lines)):
        if lines[i].strip() == "---":
            return "\n".join(lines[i + 1:]).lstrip()
    return text  # delimitatore di chiusura mancante → non tocco nulla


def _read(path: Path) -> str:
    try:
        return path.read_text(encoding="utf-8")
    except FileNotFoundError:
        return ""


def read_persona(vault: VaultConfig) -> str:
    return strip_frontmatter(_read(vault.persona()))


def read_padrone(vault: VaultConfig) -> str:
    return strip_frontmatter(_read(vault.padrone()))


def read_memory_lines(vault: VaultConfig) -> list[str]:
    """Fatti long-term, un fatto per riga. Salta righe vuote e bullet markdown
    ('- ') per restituire il contenuto nudo da iniettare nel contesto."""
    raw = strip_frontmatter(_read(vault.memory()))
    fatti = []
    for riga in raw.splitlines():
        riga = riga.strip().lstrip("-").strip()
        if riga:
            fatti.append(riga)
    return fatti


def append_memory(vault: VaultConfig, text: str) -> None:
    """`/ricorda <testo>`: append-only, una riga, zero LLM (§8).
    Normalizza gli a-capo interni così un fatto resta su una riga sola.
    Richiede che il file memoria sia su mount scrivibile (Decisione B)."""
    fatto = " ".join(text.split()).strip()
    if not fatto:
        return
    path = vault.memory()
    path.parent.mkdir(parents=True, exist_ok=True)
    newline = "" if (not path.exists() or path.read_text(encoding="utf-8").endswith("\n")) else "\n"
    with path.open("a", encoding="utf-8") as f:
        f.write(f"{newline}- {fatto}\n")
