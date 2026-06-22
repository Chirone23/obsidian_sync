# TuneMyMusic Clone

> Nota-indice del progetto. Collega: [[Index MOC]]

CLI Python che trasferisce playlist tra **Spotify** e **YouTube Music** (clone di [tunemymusic.com](https://www.tunemymusic.com)). Progetto di studio/portfolio.

## Scope (deciso)
- **Piattaforme:** Spotify ↔ YouTube Music
- **Forma:** script CLI Python (no UI web)
- **Scopo:** studio/portfolio → qualità codice conta

## Architettura (3 livelli)
- `directives/transfer_playlist.md` — SOP del trasferimento
- `execution/providers/` — connettori (`base.py`, `spotify.py`, `ytmusic.py`)
- `execution/matcher.py` — fuzzy matching engine (il cuore)
- `execution/transfer.py` — orchestratore CLI

## Il matching engine (cascata)
1. Match per **ISRC** → confidenza massima
2. Fallback **fuzzy** (rapidfuzz) su titolo+artista normalizzati + durata ±3s
3. Sotto soglia → report "non trovati"

## Stato avanzamento
- [x] Scaffold + modello dati comune (`base.py`)
- [ ] Provider Spotify (lettura)
- [ ] Matcher
- [ ] Provider YouTube Music (scrittura)
- [ ] Orchestratore CLI

## Setup richiesto (quando serve)
- App Spotify gratuita su developer.spotify.com (client_id/secret)
- Primo login YT Music via header browser (`ytmusicapi`)
