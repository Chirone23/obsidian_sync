# CLAUDE.md — Secondo Cervello

Vault Obsidian di Chirone. **Percorso:** `C:\Users\Chirone\Documents\Secondo_Cervello`
**Sync:** Git auto → `github.com/Chirone23/obsidian_sync` (ogni 10 min). **MCP:** `obsidian-mcp` attivo.

## Struttura

| Cartella | Contenuto |
|---|---|
| `moc/` | Mappe di Contenuto — il centro del sistema |
| `ITS/` | Progetti ITS |
| `idee/` | Idee grezze |
| `knowladge/` | Conoscenza generale |
| `skill/` | Guide, playbook operativi |
| `daily/` | Note giornaliere |

## Regole operative

- **Leggi prima di scrivere.** Cerca nel vault via MCP se esiste già nota correlata, prima di crearne una nuova.
- **Niente isolotti.** Ogni nota nuova va collegata via `[[backlink]]` ai MOC esistenti.
- **C.I.A.R.E.** per generare contenuto: Contesto → Intento → Audience → Regole → Esempi.
- **Triage:** task critici/business → fai domande prima. Task creativi/personali → procedi.

## Sync Git — obbligatorio per ogni .md

Dopo OGNI modifica a un `.md`:
```bash
git add -A && git commit -m "Update: ..." && git push
```
Niente .md uncommitted a fine sessione.

## Workflow specializzati (leggi nel vault quando servono)

- **Knowledge Compiler** (articolo/PDF/doc nuovo) → cerca MOC correlato, aggiorna con insight + backlink.
  Per formati non leggibili nativamente (.docx/.pptx/.xlsx/audio/YouTube): converti prima con `markitdown <file> -o .tmp/out.md`, poi leggi l'output. PDF e immagini si leggono direttamente.
- **NotebookLM** → vedi [[skill/NotebookLM Query Playbook]] (4 query: Discovery → Deep Dive → Esempi → Contraddizioni).
- **Delega OpenCode** (task ciuccia-token) → vedi [[skill/OpenCode Delegation Protocol]]. Pattern ibrido: io seleziono contesto, OpenCode compone, io integro.

## NON fare

- Note isolate senza backlink ai MOC.
- Sovrascrivere direttive esistenti senza chiedere.
