# Octogent — Dashboard Orchestrazione Claude Code

**Repo:** [hesamsheikh/octogent](https://github.com/hesamsheikh/octogent)
**Stack:** Node.js 22+, TypeScript, pnpm monorepo, node-pty, WebSocket, Vite + React

---

## Cos'è

Dashboard locale per gestire visivamente sessioni Claude Code parallele. Risolve il problema di avere 10+ terminali aperti contemporaneamente.

### Concetti chiave

| Concetto | Cosa fa |
|----------|---------|
| **Tentacle** | Cartella `.octogent/tentacles/<id>/` con `CONTEXT.md` + `todo.md` — contesto operativo persistente |
| **Terminal** | Istanza PTY (tty) gestita dall'API — può essere Claude Code o qualsiasi shell |
| **Deck** | UI web che mostra stato agenti, transcript, files, todo |
| **Channel** | Coda in-memory per messaggi brevi tra terminali |
| **Worktree** | Isolamento git per worker paralleli su branch `octogent/<id>` |
| **Swarm** | Parent coordinator che spawna worker dai todo, poi fa merge |

### Architettura

```
Browser ──HTTP / WS──▶ API locale (:8787+) ──PTY──▶ terminali Claude Code
                            │
                      ┌─────┴──────┐
                      ▼            ▼
              .octogent/      ~/.octogent/
           tentacles/       projects/<id>/state/
           CONTEXT.md          terminal records
           todo.md             transcripts
```

---

## Setup

```bash
# Prerequisiti
node -v        # >= 22
pnpm -v        # installa con: npm install -g pnpm

# Clone
git clone https://github.com/hesamsheikh/octogent.git ~/octogent
cd ~/octogent

# Installa dipendenze (su Windows approva build scripts)
pnpm install
pnpm rebuild node-pty esbuild

# Avvio (su Windows usa --parallel, non lo script dev.mjs che fallisce con EINVAL)
pnpm -r --parallel --filter @octogent/api --filter @octogent/web dev
# API → http://127.0.0.1:8787
# UI  → http://localhost:5173
```

## Comandi

```bash
octogent init [name]                          # inizializza .octogent/
octogent tentacle create <name> --desc "..."   # crea tentacle
octogent tentacle list                         # lista tentacles
octogent terminal create --tentacle-id <id>... # spawna terminale
octogent terminal list                         # lista terminali
octogent terminal stop <id>                    # ferma terminale
octogent channel send <id> "msg"               # messaggio tra agenti
```

### API principali (per script)

| Endpoint | Metodo | Cosa fa |
|----------|--------|---------|
| `/api/deck/tentacles` | GET | Lista tentacles |
| `/api/deck/tentacles` | POST | Crea tentacle |
| `/api/deck/tentacles/:id/todo` | POST | Aggiunge todo |
| `/api/deck/tentacles/:id/todo/toggle` | PATCH | Toggle todo done/undone |
| `/api/terminals` | POST | Crea terminale |
| `/api/terminal-snapshots` | GET | Lista terminali |
| `/api/setup` | GET | Stato setup workspace |
| `/api/setup/steps/:id` | POST | Inizializza setup step |

---

## Integrazione col Sistema 3-Layer

| Componente | Ruolo |
|---|---|
| `directives/*.md` | Fonte di verità → finisce in tentacle `CONTEXT.md` |
| `execution/*.py` | Eseguiti dentro terminali Octogent (PTY) |
| Tentacle `.octogent/` | Contesto operativo volatile + coda task |
| Obsidian `knowledge/` | Archiviazione permanente sessioni completate |

### Flusso

1. **Direttiva** → crei/aggiorni file in `directives/`
2. **Tentacle** → `octogent tentacle create <nome>` o via Deck UI
3. **Todo** → aggiungi task in `todo.md` (o via API)
4. **Worker** → spawna terminali dai todo (swarm) o manualmente
5. **Review** → coordinator raccoglie risultati, fa merge worktree
6. **Salva** → risultati importanti in Obsidian

---

## Worktree Mode (isolamento git)

```bash
octogent terminal create --tentacle-id <id> -w worktree -p "prompt"
```

- Crea branch `octogent/<terminal-id>` in `.octogent/worktrees/`
- Worker committa lì
- Parent fa merge e test
- Permette lavoro parallelo sullo stesso repo senza conflitti

---

## Setup tramite script

```bash
# Lo script di bootstrap (in ~/execution/) verifica prerequisiti e avvia:
python ~/execution/setup_octogent.py
```

---

## Limiti

- Max 32 PTY live (default; `OCTOGENT_MAX_TERMINAL_SESSIONS`)
- Messaggi canale in-memory — persi a ogni restart API
- PTY non sopravvivono a restart (terminali diventano `stale`)
- Worktree mode richiede merge manuale
- **Windows**: lo script `scripts/dev.mjs` fallisce con `EINVAL` — usare `pnpm -r --parallel`

---

## Collegamenti

- [[Skill MOC]] — hub delle skill operative
- `directives/octogent.md` — SOP dettagliata
- `execution/setup_octogent.py` — script bootstrap
