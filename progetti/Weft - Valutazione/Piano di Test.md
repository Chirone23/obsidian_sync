---
tags: [progetto, weft, valutazione, test-plan]
status: pronto-per-esecuzione
created: 2026-06-07
---

# Weft — Piano di Valutazione · Analisi Infrastrutture di Progetto

**Oggetto:** Valutare se Weft è uno strumento adatto a orchestrare l'AI che analizza infrastrutture di progetto (codebase, dipendenze, architettura)  
**Data:** 2026-06-07  
**Contesto:** [[Architettura 3 livelli]] — gli script di analisi esistono già in `execution/` (Python); la domanda è se Weft li può orchestrare meglio  
**Stato:** ⏳ Prereq §3 corretti (2026-06-07, verifica repo+docs). Subject scelti (§4.1). Prossimo: self-host locale WSL2+Docker → T-001

---

## 0. Sintesi

Weft è un linguaggio di programmazione per sistemi AI (core Rust, dashboard SvelteKit, durable execution via Restate). È in beta pubblica da aprile 2026 con 4 contributor attivi. Promette: parallelismo automatico dei nodi, esecuzione durabile, human-in-the-loop nativo, type safety compile-time. Il nodo `code/:exec/python` è presente nel catalog — ma le sue limitazioni reali (sandbox, import, dipendenze) non sono documentate.

**Domanda da rispondere con questi test:**  
Gli script Python esistenti in `execution/` possono girare dentro Weft senza riscrittura? E se sì, Weft aggiunge valore reale rispetto a chiamarli direttamente?

---

## 1. Obiettivo della Valutazione

Il sistema deve permettere di:

- Caricare uno script Python di analisi infrastruttura esistente in un nodo Weft
- Collegarlo a un nodo LLM per interpretarne l'output
- Ottenere un risultato strutturato senza riscrivere la logica Python

**Criterio generale di successo:** almeno T-001 e T-002 PASS → Weft è integrabile con il sistema attuale. In caso contrario: rivalutare dopo 2-3 mesi.

---

## 2. Perimetro del Test

**Dentro scope:**
- Compatibilità del nodo `code/exec/python` con script Python esistenti
- Possibilità di importare librerie custom (non solo stdlib)
- Confronto developer experience: Weft vs Python puro per un workflow infra-analysis
- Durable execution: il workflow sopravvive a un kill forzato?

**Fuori scope:**
- Performance raw (latenza LLM domina, confronto ms irrilevante)
- Scalabilità produzione (progetto beta)
- Migrazione completa del sistema a Weft
- Test di sicurezza o penetration testing

---

## 3. Prerequisiti

> ⚠️ **Corretto 2026-06-07** dopo verifica repo + docs ufficiali. Weft **non** è un CLI standalone: non esistono `weft --version` né `restate --version` da installare a mano. Il modello reale è *clona il monorepo → `./dev.sh`* (avvia Postgres, Restate, servizi). Rust/Restate/pnpm vengono auto-installati. `./dev.sh` è bash + `brew install bash` ⇒ Mac/Linux-first; su **Windows serve WSL2 + Docker Desktop**.

| Prerequisito | Verifica | Note |
|---|---|---|
| Docker Desktop attivo | `docker ps` | Serve per PostgreSQL (avviato da `./dev.sh`) |
| Node.js | `node --version` | Per la dashboard SvelteKit |
| WSL2 (solo Windows) | `wsl -l -v` | `./dev.sh` è bash; su Windows gira dentro WSL2 |
| Bash 4+ | `bash --version` | Richiesto dallo script di avvio |
| Repo + `.env` | `git clone …/weft && cp .env.example .env` | Inserire API key: OpenRouter, Tavily, ecc. |
| Servizi avviati | `./dev.sh server` + `./dev.sh dashboard` | Dashboard su http://localhost:5173 |
| Script di test pronto | vedere §4 / §4.1 | Subject già scelti |

---

## 4. Script di Riferimento per i Test

Scegliere lo **script Python più semplice** presente in `execution/` che:
- Accetta un path o una stringa come input
- Restituisce un output strutturato (dict, JSON, lista)
- Usa almeno una libreria esterna (non stdlib pura)

Questo script diventa il soggetto di T-001 e T-002. Chiamarlo **`script_riferimento.py`** nei test.

### 4.1 Subject scelti (deciso 2026-06-07)

**Scoperta:** il nodo Python di Weft è **`ExecPython`** e accetta codice **inline** in un campo *triple-backtick* — **non** referenzia un file `.py`. Quindi "senza modifiche" significa: il corpo della logica si incolla invariato, non si fa `import` di un file.

Dato che T-001 è un gate binario, si isolano le variabili usando **due** subject invece di uno:

| Test | Subject | Perché |
|---|---|---|
| T-001 | `execution/parse_noleggio_preventivi.py` → funzione `parse(txt)` | Stdlib pura (`re`, `json`), deterministica, zero I/O/rete → un FAIL significa "nodo rotto", non "ambiente mancante". Output `dict` confrontabile byte-a-byte. |
| T-002 | `execution/extract_noleggio_pdfs.py` (`import pdfplumber`) | Unico script di `execution/` con libreria esterna reale → probe diretto su import non-stdlib. |

> Nota cloud vs locale: il T-002 *fedele* va eseguito in **self-host locale**, dove conta se le librerie installate nel mio ambiente sono raggiungibili. La sandbox del cloud (app.weavemind.ai) testerebbe solo cosa permette il loro ambiente, non il mio.

---

## 5. Suite di Test

### T-001 — Compatibilità nodo Python (CRITICO)

**Ipotesi:** Il nodo `code/exec/python` di Weft può eseguire `script_riferimento.py` senza modifiche.

| Campo | Valore |
|---|---|
| **Input** | `script_riferimento.py` copiato nel nodo Weft, input hardcoded con dato reale |
| **Output atteso** | Stesso output che produce lo script eseguito direttamente con `python script_riferimento.py` |
| **Criterio PASS** | Output identico (o equivalente) senza errori di runtime |
| **Criterio FAIL** | Errore di esecuzione, output vuoto, crash del nodo |
| **Tempo stimato** | 30 min |
| **Blocca altri test?** | Sì — se FAIL, T-002 è irrilevante |

**Cosa annotare:**
- Il nodo richiede di riscrivere lo script o accetta il file com'è?
- Ci sono errori di importazione anche per stdlib (os, sys, json)?

---

### T-002 — Librerie custom nel nodo Python (CRITICO)

**Ipotesi:** Il nodo Python permette di usare librerie installate nell'ambiente locale (non solo stdlib).

| Campo | Valore |
|---|---|
| **Input** | Script che importa almeno una libreria non-stdlib usata in `execution/` (es. `requests`, `pydantic`, `anthropic`) |
| **Output atteso** | Import risolto, script eseguito senza `ModuleNotFoundError` |
| **Criterio PASS** | Import funziona, nessun errore di modulo mancante |
| **Criterio FAIL** | `ModuleNotFoundError` o sandbox che blocca import esterni |
| **Tempo stimato** | 20 min (incluso test con 2-3 librerie diverse) |
| **Blocca altri test?** | Sì — se FAIL, Weft non è compatibile con gli script esistenti senza riscrittura |

**Variante da testare se FAIL:** verificare se è possibile specificare un `requirements.txt` nel nodo o nel progetto Weft.

---

### T-003 — Developer Experience: Weft vs Python puro

**Ipotesi:** Weft riduce il codice di infrastruttura necessario per un workflow analisi → LLM → output.

| Campo | Valore |
|---|---|
| **Input** | Stesso workflow implementato in 2 versioni: (A) Weft con nodo Python + nodo LLM, (B) script Python con chiamata diretta Anthropic SDK |
| **Output atteso** | Entrambe le versioni producono lo stesso output |
| **Criterio PASS** | Versione A ha meno righe di codice infrastrutturale (esclusa logica di business) oppure tempo di implementazione ≤ versione B |
| **Criterio FAIL** | Versione A richiede più setup e più codice della versione B per lo stesso risultato |
| **Metrica** | LOC infrastruttura (escludi logica business) + minuti per implementare da zero |
| **Tempo stimato** | 90 min (45 per versione) |

**Cosa annotare:**
- Quanto tempo per far girare il primo workflow funzionante in Weft?
- Quali errori si incontrano (compilazione, configurazione, nodi)?
- La dashboard visuale aggiunge valore o è noise?

---

### T-004 — Durable Execution (il claim più differenziante)

**Ipotesi:** Un workflow Weft riprende esattamente dal punto di interruzione dopo un kill forzato del processo.

| Campo | Valore |
|---|---|
| **Input** | Workflow con 3 step sequenziali: (1) analisi Python ~5s, (2) sleep 10s simulato, (3) output LLM. Kill del processo durante lo step 2. |
| **Output atteso** | Dopo restart, il workflow riprende dallo step 3 senza ripetere lo step 1 |
| **Criterio PASS** | Ripresa corretta dallo step successivo all'interruzione, stato preservato |
| **Criterio FAIL** | Workflow ricomincia da capo, stato perso, errore non recuperabile |
| **Tempo stimato** | 45 min |
| **Prerequisito** | Restate running (senza Restate questo test non ha senso) |

**Nota:** questo è il test che differenzia Weft da "un semplice script Python con asyncio". Se PASS, è il vero argomento per adottarlo su workflow lunghi.

---

### T-005 — Parallelismo automatico su analisi multiple

**Ipotesi:** Weft esegue automaticamente in parallelo nodi senza dipendenze, senza che il developer scriva codice async esplicito.

| Campo | Valore |
|---|---|
| **Input** | Workflow con 3 nodi Python indipendenti (analisi di 3 componenti diversi dell'infrastruttura), poi nodo aggregatore |
| **Output atteso** | I 3 nodi girano in parallelo — tempo totale ≈ tempo del nodo più lento, non somma dei 3 |
| **Criterio PASS** | Tempo esecuzione ≤ max(t1, t2, t3) + 20% overhead. Zero codice async scritto manualmente. |
| **Criterio FAIL** | I nodi girano in sequenza (tempo ≈ t1+t2+t3) o richiedono configurazione esplicita del parallelismo |
| **Metrica** | Timestamp start/end di ogni nodo — verificare dalla dashboard o dai log |
| **Tempo stimato** | 40 min |

---

## 6. Matrice Rischi

| Rischio | Probabilità | Impatto | Mitigazione |
|---|---|---|---|
| T-001 FAIL: sandbox troppo restrittiva | Media | Alto | Testare subito — se FAIL, stop, rivalutare tra 3 mesi |
| T-002 FAIL: no custom libraries | Media | Alto | Cercare workaround `requirements.txt` nel progetto Weft |
| Restate non si installa correttamente | Bassa | Medio | T-004 salta — gli altri test restano validi |
| Breaking change Weft tra ora e test | Bassa | Medio | Fissare versione specifica in `Cargo.lock` / package.json |
| Documentazione insufficiente per setup | Alta | Basso | Discord Weft come supporto; community attiva |

---

## 7. Verdetto Condizionale

| Scenario | Conclusione | Azione |
|---|---|---|
| T-001 + T-002 PASS | Weft può wrappare gli script esistenti | Procedere con T-003, T-004, T-005 per valutare se vale la migrazione parziale |
| T-001 PASS + T-002 FAIL | Weft funziona solo con stdlib Python | Non compatibile con `execution/` attuale senza riscrittura. Rivalutare tra 3 mesi. |
| T-001 FAIL | Nodo Python non funziona come atteso | Weft non è valutabile per questo use case oggi. Stop. |
| T-001+T-002 PASS + T-004 PASS | Durable execution reale | Weft ha senso per workflow lunghi (analisi multi-step con step umani) |
| T-001+T-002 PASS + T-004 FAIL | Solo orchestratore senza durabilità | Non aggiunge valore rispetto a Python + asyncio |
| Tutti PASS | Weft è genuinamente utile | Pianificare migrazione progressiva: prima i workflow multi-step, non i single-shot |

---

## 8. Cosa Documentare Durante i Test

Per ogni test eseguito, annotare in [[INCIDENTS]] se qualcosa si rompe, e nel log di questa valutazione:

- Versione Weft usata
- Comando esatto usato per lanciare il workflow
- Output grezzo (anche se errato)
- Tempo effettivo (non stimato)
- Deviazioni dal criterio pass/fail + motivazione

---

## 9. Decisioni Aperte

- [x] Script subject → **split**: `parse_noleggio_preventivi.py` (T-001) + `extract_noleggio_pdfs.py` (T-002). Vedi §4.1. _Deciso 2026-06-07._
- [x] Install → **self-host locale (WSL2 + Docker)**, dopo aver corretto i prereq §3. Cloud (app.weavemind.ai) esiste ma la sua sandbox non rappresenta l'env locale → non fedele alla domanda "gira i miei script con le mie librerie". _Deciso 2026-06-07._
- [ ] T-004 e T-005 restano opzionali se T-001/T-002 falliscono — priorità confermata

---

## Connessioni

- [[Architettura 3 livelli]] — sistema su cui Weft verrebbe integrato
- [[Progettistica AI MOC]] — metodo di specifica usato per questo documento
- [[progetti/Odysseus - CLI Agents/Design - IT]] — altro progetto tecnico con format simile
