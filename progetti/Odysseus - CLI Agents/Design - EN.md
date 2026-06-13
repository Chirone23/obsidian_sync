---
tags: [project, odysseus, claude-code]
status: design
created: 2026-06-04
---

# Odysseus — CLI Agents (Claude Code) · Design EN

> Goal: add to [Odysseus](https://github.com/pewdiepie-archdaemon/odysseus) the ability to use **command-line agents** (primarily **Claude Code**, with all models) as a native execution engine, inspired by Open Design's *Execution & model* panel. To be contributed upstream as a **feature** via issue → PR.

## Odysseus context (verified)
- **FastAPI** backend: `app.py` + `routes/` (endpoints) + `src/` (logic) + `services/` + `core/` (auth, db, platform).
- The current **Agent** is **native Python** (`agent_loop.py` ~146KB, `tool_implementations.py` ~187KB). opencode is only inspiration/license, **not** a subprocess.
- Models/providers: configured in-app, OpenAI-compatible endpoints (vLLM/llama.cpp/Ollama/OpenRouter/OpenAI). `static/js/providers.js` = logos only.
- **Process execution already exists**: `routes/shell_routes.py` (PTY on POSIX, pipe/detached fallback on Windows via `core/platform_compat.py`), `StreamingResponse`, **admin-gated** (shell = RCE, already hardened).
- **Cookbook** = closest analog to the screenshot: detect hardware → install CLI → serve models, card UI with Install/Docs/installed.

## Claude Code auth (verified on machine)
- Login = **OAuth Pro subscription**, in `~/.claude/.credentials.json` (`accessToken` + `refreshToken`, `subscriptionType: pro`, scope `user:inference`).
- **No token copying.** Spawn the `claude` binary as the **same OS user**: the CLI reads and refreshes its own login.
- Real check: `claude -p "..." --model sonnet` → answers using the Pro account, **without** `ANTHROPIC_API_KEY`.
- `claude --model <x>` accepts **aliases** (`sonnet`/`opus`/`haiku`) **or full IDs** (`claude-opus-4-8`) → "all models" = curated dropdown **+ free-text field**.
- Caveat: in **Docker** the container can't see `~/.claude` → needs a volume mount, `claude login` inside, or BYOK.

## Chosen approach
**Option 2 — dedicated "CLI Agents" panel** (low risk, isolated, faithful to the screenshot). `agent_loop.py` untouched. Claude Code runs "pure" with its own tools/session; Odysseus provides UI + streaming.
v1 scope: **Claude Code only, done well**. Pluggable *driver* architecture for future engines (Codex, Gemini…).

### Two auth modes (for upstream portability)
- **Local CLI** → reuse the `~/.claude` login (native Windows/Linux/mac case). No secret handling.
- **BYOK** → `ANTHROPIC_API_KEY` passed as env to the process (Docker without mounted login).

## Components to create
- `routes/cli_agents_routes.py` — detect (`claude --version`), launch, stream, model list, auth status. **Admin-gated**, reuses `core/platform_compat`.
- Claude Code driver (command mapping: `claude -p <task> --model <m> --output-format stream-json`, parse stream → chat bubbles).
- Frontend module `static/js/cliAgents.js` + menu tile (Cookbook-style). On-style UI: existing CSS vars (`--red/--fg/--bg/--card/--border`), existing classes, **no emoji** (monochrome SVG), `Fira Code` font.
- UI choices still open: interactive terminal (PTY) vs clean chat (`-p`) vs both; dedicated workspace `data/cli_workspaces/<id>` vs user-chosen folder.

## Upstream constraints (CONTRIBUTING.md)
- **Large feature → open an issue first** describing the approach.
- **LLM-agent notice**: agent-fired PRs are **closed without review** → issue first, human-curated on-style PR.
- Strict visual style + **real-app screenshot** mandatory.
- Small PRs, one feature. Checks: `pytest`, `py_compile`, `node --check`.
- Primary test env = **Docker/Linux** (Windows "not actively tested").

## Path
1. Design doc (this) + issue text (EN).
2. User opens the issue on upstream.
3. If maintainer agrees → fork → branch → on-style implementation → screenshot → PR linking the issue.
4. Local personal use possible in parallel regardless of the PR.

## Open decisions
- [ ] Interaction: interactive terminal vs clean chat vs both
- [ ] Workspace: dedicated vs user folder
- [ ] Issue/PR language: EN (+ IT copy in vault)
- [ ] Open upstream issue before writing code
