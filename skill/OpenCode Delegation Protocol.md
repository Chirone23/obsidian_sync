# OpenCode Delegation Protocol

> Direttiva per delegare task ciuccia-token a `opencode` con modelli free di OpenRouter,
> mantenendo nelle mani di Claude Code (sessione principale) le decisioni che richiedono
> contesto del vault.

**Contesto d'uso:** quando un task ha alto volume di lettura/elaborazione ma output piccolo e ben definito, o quando si possono pre-comporre artefatti riutilizzabili (es. query NotebookLM) senza bisogno della memoria di sessione.

---

## Strumenti

- `execution/opencode_delegate.py` — wrapper con fallback su top-N modelli free + auto-refresh settimanale
- `execution/update_free_models.py` — aggiorna `free_models.json` da OpenRouter API
- `execution/free_models.json` — cache modelli free ordinati per context size

**Uso base:**
```bash
python execution/opencode_delegate.py "<prompt completo e autosufficiente>"
python execution/opencode_delegate.py --top 5 "..."
python execution/opencode_delegate.py --model openrouter/z-ai/glm-4.5-air:free "..."
```

---

## Quando delegare (DELEGA)

- Lettura/scansione di molti file con output piccolo (es. "conta le note per cartella")
- Riassunti lunghi di un singolo documento ben isolato
- Refactor ripetitivi e meccanici
- **Composizione di artefatti strutturati a partire da un template** (es. le 4 query NotebookLM)
- Estrazione di dati da testo grezzo (regex/parsing semantico)
- Task batch ripetitivi (es. processa N file applicando la stessa trasformazione)
- **Vault triage** — identificare la cartella target per un topic generico (evita letture massive di file)

## Quando NON delegare (RESTA IN SESSIONE)

- Decisioni di architettura del vault o del codice
- Scrittura di nuove note nei MOC (richiede contesto + giudizio)
- Modifiche che richiedono coerenza con il resto della sessione corrente
- Workflow che richiedono autenticazione/sessione browser (skill `notebooklm`, `perplexity`)
- Task che richiedono di consultare la memoria utente o feedback storico

---

## Pattern Ibrido — NotebookLM Query Composer

Caso d'uso modello: usare OpenCode per *comporre* le 4 query del playbook NotebookLM, mantenendo in sessione la selezione del MOC e l'integrazione finale.

### Flusso

1. **Claude Code (sessione)**
   - Identifica la fonte e il MOC rilevante nel vault
   - Legge via MCP il MOC + 1-2 note collegate (contesto leggero)
   - Estrae: titolo fonte, numero esatto di elementi, tipologie (PDF/slide/immagini), 1 paragrafo di contesto del MOC

2. **Delega a OpenCode**
   - Input: titolo + N fonti + tipologie + paragrafo contesto MOC + riferimento a `[[NotebookLM Query Playbook]]`
   - Output richiesto: JSON con 4 query pronte da incollare:
     ```json
     {
       "discovery": "...",
       "deep_dive": "...",
       "esempi": "...",
       "contraddizioni": "..."
     }
     ```
   - Vincoli non negoziabili da forzare nel prompt:
     - Numero esatto di fonti specificato in OGNI query
     - Estrazione esplicita testo da immagini/slide menzionata
     - Formato strutturato (no prosa)
     - Personalizzazione Deep Dive sul MOC fornito

3. **Claude Code (sessione)**
   - Valida che le 4 query rispettino i vincoli (regole NON negoziabili)
   - Esegue via skill `notebooklm` round per round
   - Integra l'output nel MOC con backlink (regola "no isolotti")

### Template prompt per OpenCode

```
Sei un compositore di query per NotebookLM. Segui rigorosamente il playbook:
[incolla qui le sezioni "Principi Operativi" e "Sequenza Base" da NotebookLM Query Playbook.md]

CONTESTO FONTE:
- Titolo: {titolo}
- Numero fonti: {N} (di cui {tipologie})
- MOC target: {nome_moc}
- Paragrafo contesto MOC: {1 paragrafo}

OUTPUT: solo JSON valido, senza preamboli, con chiavi "discovery", "deep_dive", "esempi", "contraddizioni".
Ogni query deve:
1) menzionare esplicitamente i {N} elementi
2) chiedere estrazione testo da slide/immagini
3) richiedere formato strutturato
4) per "deep_dive": personalizzare sul contesto MOC fornito
```

---

## Pattern Ibrido — Vault Triage

Caso d'uso modello: trovare la cartella giusta nel vault senza leggere file a caso quando il topic è generico.

### Flusso

1. **Claude Code (sessione)**
   - Riceve topic generico dall'utente
   - NON legge file: delega subito il triage a OpenCode

2. **Delega a OpenCode**
   - Input: struttura cartelle vault + topic
   - Output richiesto: solo il path della cartella più rilevante (es. `moc/`)
   - Costo: ~0 token di sessione, modello free

3. **Claude Code (sessione)**
   - Fa Glob/Grep mirato solo nella cartella restituita
   - Procede con il task originale

### Template prompt per OpenCode

```
Vault Obsidian struttura:
- moc/       → Mappe di Contenuto (centro del sistema)
- ITS/       → Progetti ITS
- idee/      → Idee grezze
- knowladge/ → Conoscenza generale
- skill/     → Guide, playbook operativi
- daily/     → Note giornaliere

Topic: {topic}

Rispondi con il solo path della cartella più rilevante. Nessun altro testo.
```

---

## Caveat Operativi (Windows)

- **Encoding:** prima di lanciare la skill `notebooklm` (e altri script che stampano emoji/UTF-8) impostare `PYTHONIOENCODING=utf-8`, altrimenti Python crasha con `UnicodeEncodeError` su `cp1252`. Esempio:
  ```bash
  PYTHONIOENCODING=utf-8 python scripts/run.py auth_manager.py status
  ```
- **Modelli lenti deprioritizzati:** `google/gemma-4-26b-a4b-it:free` e `google/gemma-4-31b-it:free` vanno in timeout sistematico → spinti in fondo alla lista in `update_free_models.py` (variabile `DEPRIORITIZE_IDS`). Se nuovi modelli mostrano lo stesso pattern, aggiungerli al set.
- **Modelli affidabili confermati nei test:** `nvidia/nemotron-3-super-120b-a12b:free` (262k ctx), `z-ai/glm-4.5-air:free` (131k ctx).

---

## Regole di sicurezza

1. **Mai delegare task con segreti** (.env, token, credenziali) — OpenCode usa provider esterni
2. **Mai delegare scrittura nel vault** — solo lettura/elaborazione, l'integrazione la fa Claude Code
3. **Validare sempre l'output strutturato** prima di usarlo (JSON malformato = fallback manuale)
4. **Su rate limit / errori a catena**: il wrapper prova top-5 modelli, se tutti falliscono → torna in sessione e fai tu il task

---

## Manutenzione

- Lista modelli auto-refresh ogni 7 giorni (controllo lazy a ogni invocazione)
- Refresh manuale: `python execution/update_free_models.py`
- Promemoria calendario: revisione setup il **2026-05-19** (decidere se replicare su PC fisso)

---

## Stato

- **Creato:** 2026-05-05
- **Test iniziale:** OK (Nemotron 3 Super 120B ha risposto correttamente dopo fallback su 2 Gemma lenti)
- **Adozione:** in valutazione per 2 settimane prima di replicare altrove
