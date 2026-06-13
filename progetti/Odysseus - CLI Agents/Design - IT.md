---
tags: [progetto, odysseus, claude-code]
status: design
created: 2026-06-04
---

# Odysseus — CLI Agents (Claude Code) · Design IT

> Obiettivo: aggiungere a [Odysseus](https://github.com/pewdiepie-archdaemon/odysseus) la possibilità di usare **agent a riga di comando** (in primis **Claude Code**, con tutti i modelli) come motore nativo, ispirandosi al pannello *Execution & model* di Open Design. Da restituire all'upstream come **feature** via issue → PR.

## Contesto Odysseus (cosa ho verificato)
- Backend **FastAPI**: `app.py` + `routes/` (endpoint) + `src/` (logica) + `services/` + `core/` (auth, db, platform).
- L'**Agent** attuale è **Python nativo** (`agent_loop.py` ~146KB, `tool_implementations.py` ~187KB). opencode è solo ispirazione/licenza, **non** un sottoprocesso.
- Modelli/provider: configurati in‑app, endpoint OpenAI‑compatibili (vLLM/llama.cpp/Ollama/OpenRouter/OpenAI). `static/js/providers.js` = solo loghi.
- **Esecuzione processi già pronta**: `routes/shell_routes.py` (PTY su POSIX, fallback pipe/detached su Windows via `core/platform_compat.py`), streaming `StreamingResponse`, **admin‑gating** (shell = RCE, già blindato).
- **Cookbook** = analogo più vicino alla foto: detect hardware → install CLI → serve modelli, UI a card con Install/Docs/installed.

## Auth Claude Code (verificato sulla macchina)
- Login = **OAuth abbonamento Pro**, in `~/.claude/.credentials.json` (`accessToken` + `refreshToken`, `subscriptionType: pro`, scope `user:inference`).
- **Non si copiano token.** Si lancia il binario `claude` come **stesso utente OS**: la CLI legge e rinnova il login da sola.
- Verifica reale: `claude -p "..." --model sonnet` → risponde usando l'account Pro, **senza** `ANTHROPIC_API_KEY`.
- `claude --model <x>` accetta **alias** (`sonnet`/`opus`/`haiku`) **o ID completo** (`claude-opus-4-8`) → "tutti i modelli" = dropdown curato **+ campo libero**.
- Caveat: in **Docker** il container non vede `~/.claude` → serve mount della cartella o `claude login` nel container, oppure BYOK.

## Approccio scelto
**Opzione 2 — Pannello "CLI Agents" dedicato** (basso rischio, isolato, fedele alla foto). Non si tocca `agent_loop.py`. Claude Code gira "puro" con i suoi tool/sessione; Odysseus fa da UI + streaming.
Scope v1: **solo Claude Code, fatto bene**. Architettura a *driver* pluggable per estensioni future (Codex, Gemini…).

### Due modalità auth (per portabilità upstream)
- **Local CLI** → riusa il login `~/.claude` (caso nativo Windows/Linux/mac dell'utente). Niente gestione segreti.
- **BYOK** → `ANTHROPIC_API_KEY` passata come env al processo (caso Docker senza login montato).

## Componenti da creare
- `routes/cli_agents_routes.py` — detect (`claude --version`), launch, stream, lista modelli, stato auth. **Admin‑gated**, riusa `core/platform_compat`.
- Driver Claude Code (mapping comando: `claude -p <task> --model <m> --output-format stream-json`, parse stream → bolle chat).
- Modulo frontend `static/js/cliAgents.js` + tile nel menu (stile Cookbook). UI in stile: variabili CSS esistenti (`--red/--fg/--bg/--card/--border`), classi esistenti, **niente emoji** (SVG monocromatici), font `Fira Code`.
- Scelte UI ancora da decidere: terminale interattivo (PTY) vs chat pulita (`-p`) vs entrambe; workspace dedicata `data/cli_workspaces/<id>` vs cartella scelta dall'utente.

## Vincoli upstream (CONTRIBUTING.md)
- **Large feature → aprire prima una issue** descrivendo l'approccio.
- **Avviso agent LLM**: PR sparate da agent vengono **chiuse senza review** → issue prima, PR curata in stile umano.
- Stile visivo rigido + **screenshot dell'app reale** obbligatorio.
- PR piccole, una feature. Test: `pytest`, `py_compile`, `node --check`.
- Ambiente test primario = **Docker/Linux** (Windows "not actively tested").

## Percorso
1. Design doc (questo) + testo issue (EN).
2. Utente apre issue sull'upstream.
3. Se ok maintainer → fork → branch → implementazione in stile → screenshot → PR che linka la issue.
4. Uso personale locale possibile in parallelo a prescindere dalla PR.

## Stato / decisioni aperte
- [ ] Interazione: terminale interattivo vs chat pulita vs entrambe
- [ ] Workspace: dedicata vs cartella utente
- [ ] Lingua issue/PR: EN (+ copia IT nel vault)
- [ ] Aprire issue upstream prima di scrivere codice
