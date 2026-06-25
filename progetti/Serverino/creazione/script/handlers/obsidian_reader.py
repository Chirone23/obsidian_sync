"""Lettura memoria dal FILESYSTEM (MCP morto headless, §8).

La memoria è una CARTELLA (/bot-memory) indicizzata da un MOC:
- default sempre caricati: system.md (identità bot) + padrone.md (info utente)
  + memory.md (dump grezzo di /ricorda, finché non viene riordinato).
- memory-moc.md: il suo testo (sintesi) entra nel contesto e i suoi [[link]]
  vengono seguiti, caricando gli altri file della cartella (dedup).

`/ricorda` fa append in memory.md (append-only, zero LLM, §8).
File mancante = stringa vuota: il bot parte comunque.
"""
from __future__ import annotations

import re
from pathlib import Path

from config import MemoryConfig

_LINK = re.compile(r"\[\[([^\]]+)\]\]")


def strip_frontmatter(text: str) -> str:
    """Toglie il blocco YAML iniziale '---' … '---'. Se assente/malformato
    restituisce il testo intatto. Gestisce il BOM."""
    text = text.lstrip("﻿")
    if not text.startswith("---"):
        return text.lstrip()
    lines = text.splitlines()
    for i in range(1, len(lines)):
        if lines[i].strip() == "---":
            return "\n".join(lines[i + 1:]).lstrip()
    return text


def _read(path: Path) -> str:
    try:
        return strip_frontmatter(path.read_text(encoding="utf-8"))
    except FileNotFoundError:
        return ""


def parse_links(text: str) -> list[str]:
    """Estrae i nomi dai [[link]] del MOC. Normalizza alias ([[a|b]] → a) e
    ancore ([[a#sez]] → a). Preserva l'ordine, niente duplicati."""
    nomi: list[str] = []
    for raw in _LINK.findall(text):
        nome = raw.split("|")[0].split("#")[0].strip()
        if nome and nome not in nomi:
            nomi.append(nome)
    return nomi


def read_context(mem: MemoryConfig) -> str:
    """Costruisce il blocco di contesto per il system prompt:
    default (system/padrone/memory) + MOC + file linkati dal MOC."""
    parts: list[str] = []
    visti: set[str] = set()

    def aggiungi(path: Path, etichetta: str) -> None:
        chiave = path.name.lower()
        if chiave in visti:
            return
        visti.add(chiave)
        testo = _read(path)
        if testo:
            parts.append(f"{etichetta}\n{testo}")

    # Default sempre presenti.
    aggiungi(mem.system(), "You are:")
    aggiungi(mem.padrone(), "I am:")
    aggiungi(mem.memory(), "Memoria recente (da /ricorda, non ancora riordinata):")

    # MOC: testo + traversal dei [[link]].
    moc_testo = _read(mem.moc())
    if moc_testo:
        parts.append(f"Indice memoria (MOC):\n{moc_testo}")
        for nome in parse_links(moc_testo):
            aggiungi(mem.file(nome), f"[{nome}]")

    return "\n\n".join(parts)


def append_memory(mem: MemoryConfig, text: str) -> None:
    """`/ricorda <testo>`: append-only, una riga, zero LLM (§8).
    Scrive in memory.md (mount rw → sync Obsidian)."""
    fatto = " ".join(text.split()).strip()
    if not fatto:
        return
    path = mem.memory()
    path.parent.mkdir(parents=True, exist_ok=True)
    newline = "" if (not path.exists() or path.read_text(encoding="utf-8").endswith("\n")) else "\n"
    with path.open("a", encoding="utf-8") as f:
        f.write(f"{newline}- {fatto}\n")
