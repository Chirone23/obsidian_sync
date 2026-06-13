---
tags: [weft, valutazione, prompts]
created: 2026-06-07
---

# Prompts di Avvio — Weft Valutazione

*Copia il prompt del test che vuoi eseguire e incollalo in una nuova chat.*  
*Ogni prompt è autosufficiente: contiene tutto il contesto necessario.*

---

## T-001 — Compatibilità nodo Python

```
# Weft — Test T-001: Compatibilità nodo Python (gate critico)

## Contesto
Sto valutando Weft (https://github.com/WeaveMindAI/weft), un linguaggio di
orchestrazione per sistemi AI (core Rust, durable execution via Restate,
dashboard SvelteKit). Ho un sistema personale con script Python in execution/
che analizzano infrastrutture di progetto (codebase, dipendenze, architettura).
La domanda è: Weft può orchestrare quegli script senza riscriverli?

## Architettura attuale
3 livelli: Direttive (SOP .md) → Orchestrazione → Esecuzione (script Python
in execution/). Weft entrerebbe nel livello Orchestrazione, wrappando gli
script esistenti.

## Questo test
T-001 è un gate binario: se FAIL, tutti i test successivi saltano.

**Ipotesi:** il nodo code/exec/python di Weft esegue uno script Python
esistente senza modifiche al codice.

**Criterio PASS:** output identico a `python script_riferimento.py` diretto,
nessun errore runtime.
**Criterio FAIL:** errore runtime, crash nodo, output vuoto, import error
anche su stdlib.

**Tempo stimato:** 30 min

## Cosa devi fare
1. Mostrami i file disponibili in execution/ per scegliere script_riferimento.py
   (preferisco quello più semplice con almeno 1 libreria esterna e output JSON)
2. Guidami nell'installazione di Weft CLI + Restate
3. Creiamo insieme il primo workflow Weft con il nodo Python
4. Eseguiamo il test e confrontiamo l'output con l'esecuzione diretta Python
5. Aggiorna RISULTATI.md con esito T-001 e SESSION_HANDOFF.md con il log

## Configurazione modello (per i nodi LLM Weft nei test successivi)
model: claude-opus-4-8
thinking: {type: "adaptive"}
output_config: {effort: "high"}

## Documenti del progetto (vault Obsidian)
- progetti/Weft - Valutazione/Piano di Test.md — spec completa
- progetti/Weft - Valutazione/RISULTATI.md — aggiornare dopo il test
- progetti/Weft - Valutazione/SESSION_HANDOFF.md — aggiornare dopo la sessione
- progetti/Weft - Valutazione/INCIDENTS.md — se qualcosa si rompe
```

---

## T-002 — Librerie custom nel nodo Python

```
# Weft — Test T-002: Librerie custom nel nodo Python (gate critico)

## Contesto
Sto valutando Weft per orchestrare script Python di analisi infrastruttura.
T-001 è già stato eseguito con esito: [INCOLLA QUI: PASS / FAIL + note].

## Stato precedente
T-001 PASS — il nodo code/exec/python esegue script Python.
script_riferimento.py scelto: [INCOLLA QUI il nome del file].

## Questo test
T-002 è un gate binario: se FAIL, gli script di execution/ non sono
compatibili con Weft senza riscrittura.

**Ipotesi:** il nodo Python permette import di librerie non-stdlib installate
nell'ambiente locale (requests, pydantic, anthropic, ecc.).

**Criterio PASS:** import risolto, nessun ModuleNotFoundError, script eseguito
correttamente.
**Criterio FAIL:** sandbox blocca import esterni, ModuleNotFoundError su
qualsiasi libreria non-stdlib.

**Variante da testare se FAIL:** verificare se è possibile specificare un
requirements.txt nel progetto Weft (documentare il risultato).

**Librerie da testare (in ordine di priorità):**
1. La libreria non-stdlib usata in script_riferimento.py
2. anthropic (SDK usato dagli script di analisi)
3. pydantic (se presente in execution/)

**Tempo stimato:** 20 min

## Cosa devi fare
1. Aggiungi import delle librerie target allo script nel nodo Weft
2. Esegui il workflow — annota l'errore esatto se FAIL
3. Se FAIL: cerca nella doc Weft come specificare dipendenze (requirements.txt,
   pyproject.toml o configurazione del nodo)
4. Documenta il risultato per ogni libreria testata
5. Aggiorna RISULTATI.md e SESSION_HANDOFF.md

## Configurazione modello (nodi LLM Weft)
model: claude-opus-4-8
thinking: {type: "adaptive"}
output_config: {effort: "high"}

## Documenti del progetto
- progetti/Weft - Valutazione/Piano di Test.md — spec T-002 alla sezione 5
- progetti/Weft - Valutazione/RISULTATI.md — aggiornare con esito T-002
- progetti/Weft - Valutazione/INCIDENTS.md — se sandbox si comporta in modo
  inatteso
```

---

## T-003 — Developer Experience: Weft vs Python puro

```
# Weft — Test T-003: Developer Experience (DX)

## Contesto
Sto valutando Weft per orchestrare script Python di analisi infrastruttura.
T-001 e T-002 sono gate superati: Weft esegue gli script esistenti con
librerie custom.

## Stato precedente
T-001: PASS | T-002: PASS
script_riferimento.py: [INCOLLA QUI il nome]
Note emerse nei test precedenti: [INCOLLA QUI eventuali limitazioni trovate]

## Questo test
Confronto developer experience tra due implementazioni dello stesso workflow:

**Workflow da implementare:**
Analisi di un file/directory dell'infrastruttura → nodo LLM che interpreta
l'output → risultato strutturato in JSON.

**Versione A — Weft:**
- Nodo Python che esegue script_riferimento.py
- Nodo LLM Claude che interpreta l'output
- Output strutturato

**Versione B — Python puro:**
- Script Python che chiama script_riferimento.py (subprocess o import diretto)
- Chiamata Anthropic SDK per interpretare l'output
- Output strutturato

**Criteri di valutazione:**
- LOC infrastruttura (escludi logica di business — conta solo setup, routing,
  gestione errori, configurazione)
- Minuti per implementare da zero la prima versione funzionante
- Numero di errori incontrati prima del primo run verde

**Criterio PASS:** Versione A ha meno LOC infrastrutturali O tempo di
implementazione ≤ Versione B
**Criterio FAIL:** Versione A richiede più setup e più codice della Versione B

**Tempo stimato:** 90 min (45 per versione)

## Cosa devi fare
1. Implementa prima Versione B (Python puro — baseline nota)
2. Implementa Versione A (Weft) — annota ogni errore e minuto speso
3. Conta LOC separando logica di business da infrastruttura
4. Documenta la dashboard Weft: aggiunge valore visuale o è noise?
5. Aggiorna RISULTATI.md con metriche e SESSION_HANDOFF.md

## Configurazione modello (nodi LLM da usare nel workflow)
model: claude-opus-4-8
thinking: {type: "adaptive"}
output_config: {effort: "high"}

## Python SDK (Versione B)
import anthropic
client = anthropic.Anthropic()
response = client.messages.create(
    model="claude-opus-4-8",
    max_tokens=8000,
    thinking={"type": "adaptive"},
    output_config={"effort": "high"},
    messages=[{"role": "user", "content": "..."}]
)

## Documenti del progetto
- progetti/Weft - Valutazione/Piano di Test.md — spec T-003 alla sezione 5
- progetti/Weft - Valutazione/PROMPT_LOG.md — annota le iterazioni del
  prompt del nodo LLM Weft
- progetti/Weft - Valutazione/RISULTATI.md — aggiornare con metriche DX
```

---

## T-004 — Durable Execution

```
# Weft — Test T-004: Durable Execution (il claim più differenziante)

## Contesto
Sto valutando Weft per orchestrare script Python di analisi infrastruttura.
T-001 e T-002 sono gate superati. T-004 testa il claim principale di Weft:
un workflow riprende esattamente dove si era interrotto dopo un kill forzato.

## Stato precedente
T-001: PASS | T-002: PASS | T-003: [INCOLLA ESITO]
Note DX emerse: [INCOLLA eventuali note rilevanti]

## Prerequisito
Restate deve essere running. Verifica: `restate --version` e che il processo
sia attivo. Senza Restate questo test non ha senso — saltarlo e documentarlo
come "non eseguibile" se Restate non è disponibile.

## Questo test
**Workflow da costruire (3 step sequenziali):**
1. Step 1 — Nodo Python: esegue script_riferimento.py (~5 secondi)
2. Step 2 — Sleep simulato: pausa di 10 secondi (simula step lungo)
3. Step 3 — Nodo LLM: interpreta output dello Step 1

**Procedura:**
1. Avvia il workflow
2. Durante lo Step 2 (sleep), kill forzato del processo Weft (Ctrl+C o kill PID)
3. Restart del processo Weft
4. Verifica: il workflow riprende dallo Step 3 senza ripetere Step 1

**Criterio PASS:** ripresa corretta dallo step successivo, stato preservato,
Step 1 non viene rieseguito.
**Criterio FAIL:** workflow ricomincia da capo, stato perso, errore non
recuperabile.

**Tempo stimato:** 45 min

## Cosa documentare
- Come verificare dalla dashboard/log quale step è stato ripreso
- Se lo stato dell'output di Step 1 è accessibile dopo il restart
- Quanto tempo impiega il restart a riconnettersi a Restate

## Configurazione modello (nodo LLM Step 3)
model: claude-opus-4-8
thinking: {type: "adaptive"}
output_config: {effort: "xhigh"}

Nota: uso effort "xhigh" perché Step 3 è il nodo che produce l'output finale
di analisi — vale la qualità massima.

## Documenti del progetto
- progetti/Weft - Valutazione/Piano di Test.md — spec T-004 alla sezione 5
- progetti/Weft - Valutazione/RISULTATI.md — aggiornare con esito T-004
- progetti/Weft - Valutazione/INCIDENTS.md — documentare comportamento
  esatto del restart (anche se PASS)
```

---

## T-005 — Parallelismo automatico

```
# Weft — Test T-005: Parallelismo automatico su analisi multiple

## Contesto
Sto valutando Weft per orchestrare script Python di analisi infrastruttura.
T-001 e T-002 sono gate superati. T-005 testa se Weft esegue automaticamente
in parallelo nodi senza dipendenze, senza codice async esplicito.

## Stato precedente
T-001: PASS | T-002: PASS | T-003: [ESITO] | T-004: [ESITO]
Note sessioni precedenti: [INCOLLA eventuali note rilevanti]

## Questo test
**Workflow da costruire:**
- 3 nodi Python indipendenti (A, B, C) — ognuno analizza un componente
  diverso dell'infrastruttura, ciascuno con sleep(5) per rendere misurabile
  il timing
- 1 nodo aggregatore (LLM) che riceve output di A+B+C e produce un report
  unificato

**Procedura:**
1. Costruisci il workflow Weft con i 3 nodi senza dipendenze tra loro
2. Aggiungi timestamp start/end in ogni nodo Python
3. Esegui il workflow — misura il tempo totale
4. Verifica i timestamp: i 3 nodi hanno start time sovrapposti?

**Criterio PASS:** tempo totale ≤ max(tA, tB, tC) + 20% overhead. Zero codice
async scritto manualmente. Start time dei 3 nodi sovrapposti nei log.
**Criterio FAIL:** tempo totale ≈ tA+tB+tC (esecuzione sequenziale) oppure
Weft richiede configurazione esplicita del parallelismo.

**Metriche da raccogliere:**
- Timestamp start/end di ogni nodo (da log Weft o dashboard)
- Tempo totale workflow
- Righe di configurazione necessarie per ottenere il parallelismo

**Tempo stimato:** 40 min

## Nodo aggregatore — configurazione modello
model: claude-opus-4-8
thinking: {type: "adaptive"}
output_config: {effort: "xhigh"}

Nota: l'aggregatore deve sintetizzare 3 analisi separate in un report
coerente — effort "xhigh" per qualità massima sull'output finale.

## Python SDK (se confronti con versione Python puro)
import anthropic
client = anthropic.Anthropic()
response = client.messages.create(
    model="claude-opus-4-8",
    max_tokens=8000,
    thinking={"type": "adaptive"},
    output_config={"effort": "xhigh"},
    messages=[{"role": "user", "content": "Aggrega questi 3 report: ..."}]
)

## Cosa documentare
- La dashboard Weft mostra visualmente il parallelismo?
- Il parallelismo è automatico per topologia o richiede keyword/config?
- Confronto con asyncio Python: quanto codice in più richiede la versione
  Python per lo stesso parallelismo?

## Documenti del progetto
- progetti/Weft - Valutazione/Piano di Test.md — spec T-005 alla sezione 5
- progetti/Weft - Valutazione/RISULTATI.md — aggiornare con esito T-005 e
  metriche timing
- progetti/Weft - Valutazione/DECISIONI.md — aggiornare con verdetto finale
  su adozione Weft
```

---

## Note d'uso

- **Ordine obbligatorio:** T-001 → T-002 → (T-003, T-004, T-005 in qualsiasi ordine)
- **Gate:** se T-001 o T-002 FAIL, non eseguire i successivi
- **effort "high" vs "xhigh":** usa `high` per analisi intermedie, `xhigh` per
  l'output finale del workflow (nodo aggregatore o nodo che produce il report)
- **Dopo ogni test:** aggiorna `RISULTATI.md` + `SESSION_HANDOFF.md` prima di
  chiudere la chat
