# Claude Code Setup MOC

Mappa completa di tutto ciò che è installato e configurato nell'ambiente Claude Code personale.
Ultimo aggiornamento: 2026-05-17

---

## Configurazione Base

| File | Scopo |
|------|-------|
| `~/.claude/CLAUDE.md` | Istruzioni globali — [[../CLAUDE.md\|Architettura a 3 livelli]] |
| `~/.claude/settings.json` | MCP servers, permessi, configurazione globale |
| `~/.claude/settings.local.json` | Override locali |
| `~/.claude/commands/` | Slash commands custom |
| `~/.claude/Skills/` | Skill globali |

---

## Architettura Operativa

Il sistema segue un'[[../CLAUDE.md|architettura a 3 livelli]]:

- **Livello 1 — Direttive** (`directives/`): SOP in Markdown, obiettivi e istruzioni
- **Livello 2 — Orchestrazione**: Claude stesso — routing, decisioni, gestione errori
- **Livello 3 — Esecuzione** (`execution/`): Script Python deterministici

---

## MCP Servers

<<<<<<< HEAD
### Diretti (settings.json) — 2
=======
### Diretti (settings.json) — 4
>>>>>>> origin/main
| Server | Scopo |
|--------|-------|
| `n8n-docs` | Documentazione n8n |
| `n8n-mcp` | Controllo diretto workflow n8n |
<<<<<<< HEAD
=======
| `perplexity` | Ricerca web con Perplexity AI |
| `perplexity-playwright` | Perplexity + automazione browser |
>>>>>>> origin/main

### Cloud (connessioni Claude.ai) — 3
| Server | Scopo |
|--------|-------|
| Google Calendar | Gestione calendario |
| Google Drive | Accesso file Drive |
| Notion | Lettura/scrittura Notion |

---

## Skill Globali — 2

| Skill | Trigger | Scopo |
|-------|---------|-------|
| `notebooklm` | `/notebooklm` | Query Google NotebookLM con citazioni |
| `open-design` | `/open-design` | Genera artefatti HTML/CSS production-ready (landing, dashboard, slide, mockup) |

---

## Slash Commands Custom — 7

Tutti orientati a n8n:

| Comando | Scopo |
|---------|-------|
| `n8n-code-javascript` | Scrivere JS in nodi Code n8n |
| `n8n-code-python` | Scrivere Python in nodi Code n8n |
| `n8n-expression-syntax` | Validare e correggere espressioni n8n |
| `n8n-mcp-tools-expert` | Guida all'uso degli strumenti n8n-mcp |
| `n8n-node-configuration` | Configurazione nodi n8n |
| `n8n-validation-expert` | Interpretare errori di validazione n8n |
| `n8n-workflow-patterns` | Pattern architetturali per workflow n8n |

---

## Plugin Installati — 37

### Integrazioni Servizi Esterni
| Plugin | Scopo |
|--------|-------|
| `github` | Gestione repo, PR, issue GitHub |
| `gitlab` | GitLab DevOps — MR, CI/CD, pipeline |
| `linear` | Issue tracking Linear |
| `asana` | Project management Asana |
| `firebase` | Backend Firebase (Firestore, Auth, Functions) |
| `telegram` | Canale messaggi Telegram per Claude Code |
| `discord` | Canale messaggi Discord per Claude Code |
| `playwright` | Automazione browser e testing E2E (Microsoft) |
| `terraform` | Infrastructure as Code con Terraform |
| `serena` | Analisi semantica del codice via LSP |
| `context7` | Documentazione aggiornata da repo sorgente |
| `greptile` | Code review AI su GitHub/GitLab |
| `laravel-boost` | Toolkit Laravel (Artisan, Eloquent, routing) |
| `imessage` | Bridge iMessage per Claude Code (macOS) |
| `fakechat` | Chat locale stile iMessage per test |

### Workflow di Sviluppo
| Plugin | Scopo |
|--------|-------|
| `code-review` | Review automatizzata PR con agenti specializzati |
| `code-simplifier` | Semplifica e raffina il codice |
| `code-modernization` | Modernizza codebase legacy (COBOL, Java legacy, monoliti) |
| `feature-dev` | Workflow completo sviluppo feature |
| `pr-review-toolkit` | Agenti specializzati per review PR |
| `commit-commands` | Comandi git rapidi (commit, push, PR) |
| `ralph-loop` | Loop continui Claude-su-Claude (tecnica Ralph Wiggum) |

### Tool Claude Code
| Plugin | Scopo |
|--------|-------|
| `claude-code-setup` | Analizza codebase e raccomanda automazioni |
| `claude-md-management` | Mantiene e migliora i file CLAUDE.md |
| `plugin-dev` | Toolkit per creare plugin, agenti, comandi, hook |
| `skill-creator` | Crea/migliora skill, misura performance con eval |
| `agent-sdk-dev` | Sviluppo con Claude Agent SDK |
| `hookify` | Crea hook da pattern di conversazione |
| `security-guidance` | Hook di avviso sicurezza su file editing |

### Output e Learning
| Plugin | Scopo |
|--------|-------|
| `explanatory-output-style` | Aggiunge insight educativi sulle scelte implementative |
| `learning-output-style` | Modalità interattiva di apprendimento |

### Specializzati
| Plugin | Scopo |
|--------|-------|
| `math-olympiad` | Risolve matematica da competizione (IMO, Putnam) con verifica avversariale |
| `mcp-server-dev` | Guida design e build di MCP server |
| `playground` | Genera playground HTML interattivi self-contained |
| `frontend-design` | Skill UI/UX implementation |

### Maker / Fun
| Plugin | Scopo |
|--------|-------|
| `cwc-makers` | Onboarding M5Stack Cardputer per Code-with-Claude |
| `example-plugin` | Plugin esempio con tutte le opzioni di estensione |

---

## Agent Types Personalizzati — 24

Agenti specializzati disponibili via `Agent tool`:

| Agente | Plugin di origine |
|--------|------------------|
| `code-reviewer` | pr-review-toolkit / code-review |
| `code-architect` | feature-dev |
| `code-explorer` | feature-dev |
| `feature-dev` | feature-dev |
| `legacy-analyst` | code-modernization |
| `security-auditor` | code-modernization |
| `test-engineer` | code-modernization |
| `business-rules-extractor` | code-modernization |
| `architecture-critic` | code-modernization |
| `pr-test-analyzer` | pr-review-toolkit |
| `silent-failure-hunter` | pr-review-toolkit |
| `type-design-analyzer` | pr-review-toolkit |
| `comment-analyzer` | pr-review-toolkit |
| `code-simplifier` | code-simplifier |
| `skill-reviewer` | plugin-dev |
| `agent-creator` | plugin-dev |
| `plugin-validator` | plugin-dev |
| `skill-creator` | skill-creator |
| `analyzer` (skill eval) | skill-creator |
| `comparator` (skill eval) | skill-creator |
| `grader` (skill eval) | skill-creator |
| `hookify` | hookify |
| `conversation-analyzer` | hookify |
| `agent-sdk-verifier` | agent-sdk-dev |

---

## Sistema di Memoria — 7 file

Memoria persistente in `~/.claude/projects/C--Users-Chirone/memory/`:

| File | Contenuto |
|------|-----------|
| `user_profile.md` | Chi è l'utente, cosa usa, come lavora |
| `feedback_prompt_structure.md` | Framework 3 blocchi per prompt AI |
| `feedback_bracketed_headers.md` | Chiudere heading Markdown con `#` nei prompt |
| `project_architecture.md` | Architettura 3 livelli — Direttive/Orchestrazione/Esecuzione |
| `project_obsidian.md` | Struttura vault e come salvare conoscenza |
| `project_open_design.md` | Repo Open Design + skill `/open-design` |

---

## Link Correlati

- [[../CLAUDE.md|CLAUDE.md globale]] — istruzioni operative complete
- [[Skill MOC]] — skill e strumenti del vault
- [[Agenti IA Design Patterns MOC]] — design pattern per sistemi agentici

<<<<<<< HEAD
=======

>>>>>>> origin/main
---

## Guida alla Replica su Nuovo Computer

### 1. Installa Claude Code
```powershell
npm install -g @anthropic-ai/claude-code
claude login
```

### 2. CLAUDE.md globale
Copia il contenuto di `~/.claude/CLAUDE.md` (architettura 3 livelli) sul nuovo PC nello stesso percorso.

### 3. Marketplace Plugin Ufficiale
Tutti i 37 plugin provengono da **un unico marketplace**: `anthropics/claude-plugins-official`.

```
/plugin marketplace add anthropics/claude-plugins-official
```

Poi installa i singoli plugin con `/plugin install <nome>` (oppure via UI `/plugin`).

### 4. Lista completa plugin da installare

**Da `external_plugins/` (integrazioni terze parti — 15):**
```
asana, context7, discord, fakechat, firebase, github, gitlab,
greptile, imessage, laravel-boost, linear, playwright, serena,
telegram, terraform
```

**Da `plugins/` (ufficiali Anthropic — 22 attivi):**
```
agent-sdk-dev, claude-code-setup, claude-md-management,
code-modernization, code-review, code-simplifier, commit-commands,
cwc-makers, example-plugin, explanatory-output-style, feature-dev,
frontend-design, hookify, learning-output-style, math-olympiad,
mcp-server-dev, playground, plugin-dev, pr-review-toolkit,
ralph-loop, security-guidance, skill-creator
```

> Nota: nel marketplace ci sono anche LSP plugin (clangd, csharp, gopls, jdtls, kotlin, lua, php, pyright, ruby, rust-analyzer, swift, typescript) e `session-report` non attivi nella mia config.

### 5. MCP Servers
Nel `settings.json` aggiungere la sezione `mcpServers` con:

| Server | Comando installazione |
|--------|----------------------|
| `n8n-mcp` | `npx -y @czlonkowski/n8n-mcp` |
| `n8n-docs` | Knowledge MCP per docs n8n |
<<<<<<< HEAD
=======
| `perplexity` | API key Perplexity necessaria |
| `perplexity-playwright` | Perplexity + automazione browser |
>>>>>>> origin/main

MCP Cloud (Calendar/Drive/Notion) sono configurati da claude.ai → Settings → Connectors.

### 6. Skill `notebooklm`

Repo: clona in `~/.claude/Skills/notebooklm/`
```powershell
git clone <repo notebooklm> C:\Users\Chirone\.claude\Skills\notebooklm
cd C:\Users\Chirone\.claude\Skills\notebooklm
python -m venv .venv
.\.venv\Scripts\pip install -r requirements.txt
```
Richiede autenticazione Google (vedi `AUTHENTICATION.md` nella skill).

### 7. Skill `open-design`

Struttura split — la skill orchestratrice è leggera, le risorse pesanti stanno nel repo clonato:

**A) Repo clonato esternamente** (148 design system, 111 template):
```powershell
git clone https://github.com/nexu-io/open-design C:\Users\Chirone\tools\open-design
```

**B) Skill installata in:** `~/.claude/Skills/open-design/`
Contiene:
- `SKILL.md` — orchestratore del flow Discovery → Direction → Generate → Critique → Export
- `craft/` — 11 file di craft rules (anti-slop, typography, color, a11y, animation, state-coverage, laws-of-ux, rtl, form-validation)
- `skills/` — 14 skill specializzate (frontend-design, web-design-guidelines, ui-ux-pro-max, design-md, design-brief, design-review, design-consultation, slides, pptx, web-artifacts-builder, artifacts-builder, brand-guidelines, copywriting, marketing-psychology)

**Risorse esterne (lette dal repo clonato, NON copiate):**
- Design systems: `C:\Users\Chirone\tools\open-design\design-systems\<brand>\DESIGN.md` (148 brand)
- Design templates: `C:\Users\Chirone\tools\open-design\design-templates\<name>\` (111 template)
- Docs protocollo: `C:\Users\Chirone\tools\open-design\docs\` (skills-protocol.md, modes.md, critique-theater.md)

**Flow obbligatorio del comando `/open-design <prompt>`:**
1. **Discovery** — 5 domande in singolo turno (Forma, Audience, Tone, Brand, Scala) — **mai generare al primo prompt**
2. **Direction** — propone direzione visiva + brand da `design-systems/`
3. **Generate** — produce artefatti HTML/CSS
4. **Critique** — applica craft rules + auto-review
5. **Export** — output production-ready

### 8. Slash Commands Custom (n8n)
Copia in `~/.claude/commands/` i 7 file `.md`. Provengono dal repo `czlonkowski/n8n-skills` (dist v1.2.0).

```powershell
# Download e estrazione
curl -L https://raw.githubusercontent.com/czlonkowski/n8n-skills/main/dist/n8n-mcp-skills-v1.2.0.zip -o n8n-skills.zip
# Estrai e copia i .md in ~/.claude/commands/
```

### 9. Memoria persistente
File da copiare in `~/.claude/projects/C--Users-Chirone/memory/`:
- `MEMORY.md` (indice)
- `user_profile.md`, `feedback_prompt_structure.md`, `feedback_bracketed_headers.md`
- `project_architecture.md`, `project_obsidian.md`, `project_open_design.md`

### 10. Variabili d'ambiente (settings.json → `env`)
- `N8N_API_URL` — URL Cloudflare tunnel o locale del tuo n8n
- `N8N_API_KEY` — JWT API key di n8n
- `OBSIDIAN_API_KEY` — API key del plugin REST API di Obsidian

---

## Checklist Replica Rapida

- [ ] Installato Claude Code (`npm i -g @anthropic-ai/claude-code`)
- [ ] Login (`claude login`)
- [ ] Copiato `CLAUDE.md` globale
- [ ] Aggiunto marketplace `anthropics/claude-plugins-official`
- [ ] Installati 37 plugin (15 external + 22 official)
<<<<<<< HEAD
- [ ] Configurati 2 MCP server (`n8n-mcp`, `n8n-docs`)
=======
- [ ] Configurati 4 MCP server (`n8n-mcp`, `n8n-docs`, `perplexity`, `perplexity-playwright`)
>>>>>>> origin/main
- [ ] Connessi 3 MCP cloud (Google Calendar/Drive, Notion) via claude.ai
- [ ] Clonato `notebooklm` in `~/.claude/Skills/notebooklm/` + venv
- [ ] Clonato repo `nexu-io/open-design` in `~/tools/open-design/`
- [ ] Installata skill `open-design` in `~/.claude/Skills/open-design/`
- [ ] Copiati 7 slash commands n8n in `~/.claude/commands/`
- [ ] Copiata cartella `memory/` con 7 file
- [ ] Settate env vars `N8N_API_URL`, `N8N_API_KEY`, `OBSIDIAN_API_KEY`
- [ ] Configurato Obsidian REST API plugin nel vault
