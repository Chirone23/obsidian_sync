# CLAUDE.md — Secondo Cervello

> Questo file guida Claude Code all'interno del vault Obsidian di Chirone.
> Obiettivo: trasformare il vault in un secondo cervello attivo, non un archivio passivo.

---

## Il Vault

**Percorso:** `C:\Users\Chirone\Documents\Secondo_Cervello`
**Sync:** Git automatico → `https://github.com/Chirone23/obsidian_sync` (ogni 10 min)
**MCP attivo:** `obsidian-mcp` — puoi leggere, cercare e scrivere note direttamente

### Struttura cartelle

| Cartella | Contenuto |
|----------|-----------|
| `moc/` | Mappe di Contenuto (Index MOC, topic MOC) — il centro del sistema |
| `ITS/` | Progetti ITS (es. Bibliò) |
| `idee/` | Idee grezze e brainstorming |
| `knowladge/` | Conoscenza generale e architettura |
| `skill/` | Guide, PDF, skill operative |
| `daily/` | Note giornaliere |

---

## L'Architettura a 3 Livelli

**Livello 1 — Direttiva (Cosa fare)**
- SOP scritte in Markdown in `moc/` e `skill/`
- Definiscono obiettivi, input, output e casi limite

**Livello 2 — Orchestrazione (Tu, Claude)**
- Leggi le note rilevanti via MCP prima di agire
- Routing intelligente: cerca prima, poi scrivi o aggiorna
- Gestisci errori, chiedi chiarimenti, aggiorna le note con ciò che impari

**Livello 3 — Esecuzione (Deterministico)**
- Script Python in `execution/` se presenti
- Preferisci script testabili al lavoro manuale ripetitivo

---

## Principi Operativi

**1. Leggi prima di scrivere**
Prima di creare una nuova nota, cerca nel vault con MCP se esiste già qualcosa di correlato.

**2. Aggiorna i MOC, non creare isolotti**
Ogni nuova conoscenza va collegata via `[[backlink]]` ai MOC esistenti. La conoscenza deve compoundare, non accumularsi.

**3. Framework C.I.A.R.E. per i prompt**
Quando generi contenuto, struttura sempre: Contesto → Intento → Audience → Regole → Esempi.

**4. Protocollo di Triage**
- Task critici/business → fai domande preventive prima di agire
- Task creativi/personali → procedi, chiedi solo chiarimenti di stile

**5. Auto-correggiti**
Se qualcosa si rompe: correggi → testa → aggiorna la nota/direttiva con ciò che hai imparato.

---

## Workflow Knowledge Compiler (Setup 04)

Quando l'utente porta un articolo, PDF o fonte nuova:

1. Cerca nel vault le note correlate via MCP
2. Estrai i punti chiave della fonte
3. Aggiorna il MOC più rilevante con i nuovi insight
4. Crea `[[backlink]]` alle note esistenti
5. Non creare una nota isolata — tutto deve essere connesso

**Esempio:** _"Leggi questo articolo e aggiorna il MOC più rilevante con nuovi insight."_

---

## Workflow NotebookLM (estrazione fonti)

Quando l'utente porta un link NotebookLM da integrare nel vault:

1. Usa la skill `notebooklm` per autenticarti e interrogare
2. Segui il playbook in [[skill/NotebookLM Query Playbook]] — sequenza di 4 query (Discovery → Deep Dive → Esempi → Contraddizioni)
3. Regole non negoziabili:
   - Specifica SEMPRE il numero esatto di fonti nella prima query
   - Forza l'estrazione del testo da immagini/slide (altrimenti vengono ignorate)
   - Mai fermarsi a una sola query — minimo 2 round
4. Integra l'output nel MOC più rilevante, non creare note isolate

---

## Workflow Delega a OpenCode (token-saving)

Per task ciuccia-token (lettura massiva, riassunti lunghi, composizione di artefatti strutturati ripetitivi) delega a `execution/opencode_delegate.py` invece di consumare token in sessione.

**Direttiva completa:** [[skill/OpenCode Delegation Protocol]]

**Regole sintetiche:**
- Delega: lettura batch, riassunti isolati, composizione query NotebookLM (pattern ibrido)
- NON delegare: decisioni MOC, scrittura nel vault, workflow con autenticazione (notebooklm, perplexity)
- Pattern NotebookLM: io seleziono MOC + leggo contesto → OpenCode compone le 4 query → io eseguo skill e integro

**Uso:** `python execution/opencode_delegate.py "<prompt autosufficiente>"` (auto-fallback top-5 modelli free, refresh settimanale)

---

## Sincronizzazione Git — Regola Obbligatoria

**Per OGNI modifica ai file .md:**
1. Modifica il file `.md` (via Write/Edit/Patch)
2. **Immediatamente dopo:** Fai `git add -A && git commit -m "..."` + `git push`
3. Non lasciare mai file .md uncommitted in locale

**Comando standard:**
```bash
git add -A && git commit -m "Update: [tipo modifica] [file interessato]" && git push
```

**Eccezioni:** Solo per modifiche incomplete o in fase di debug (ma comunque pushare entro sessione)

---

## Cosa NON fare

- Non creare note isolate senza backlink ai MOC
- Non sovrascrivere direttive esistenti senza chiedere
- ~~Non committare su git manualmente~~ **→ ORA DEVI farlo per ogni .md**
- Non generare contenuto nel vault senza che l'utente abbia fornito la fonte — "Agents read, humans write"

---

## Visione

L'IA non sostituisce il pensiero di Chirone — lo potenzia.
Il vault deve contenere il pensiero autentico. Claude legge per contesto, connette per utilità, scrive solo su richiesta esplicita.

Il secondo cervello è vivo quando ogni sessione parte da dove l'ultima si è fermata.
