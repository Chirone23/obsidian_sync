# CHANGELOG BUILD — Sessione 24/25-06 · SpecterAI

**Progetto:** SpecterAI — AI Contract Analyzer per Non-Avvocati
**Periodo documentato:** 2026-06-24 / 25 (ottimizzazione latenza CLI + detector PDF corrotti + scaffold SDK warm)
**Spec di riferimento:** [[Specifica Tecnica v4 - SpecterAI]]
**Fonti:** sessione Claude Code, log uvicorn, test curl cronometrati, diagnostica `diag.py`
**Collegati:** [[INCIDENTS]] INC-013 (latenza thinking), INC-001 (mitigato), INC-006 (nuova occorrenza) · [[CHANGELOG_BUILD_04-06]] · [[docs/OCR_fallback_decisione]]

> Sessione focalizzata sui **tempi di analisi** (erano ~1-4 min, inaccettabili per contratti pesanti) e sulla **robustezza dell'estrazione PDF**. Risultato principale: latenza tagliata **12×** restando su backend `cli` a €0. Backup pre-modifiche: `specter-ai_backup_pre-SDK_20260624_1659.zip`.

---

## 1 — Diagnosi latenza (la scoperta che ha ribaltato le ipotesi)

Le ipotesi iniziali (cold-start CLI, retry, server MCP, contesto vault, lingua del prompt) erano **tutte sbagliate**. Misure isolate:

| Misura | Tempo | Conclusione |
|---|---|---|
| Chiamata CLI banale ("ciao") | **6,3s** | Il cold-start del CLI è ~6s, NON il problema |
| Una sola analisi contratto, thinking ON | **162,9s** | JSON valido al 1° colpo → niente retry |
| Una sola analisi contratto, **thinking OFF** | **13,3s** | **12× più veloce**, output valido |

**Root cause:** l'**extended thinking** del CLI generava ~1700 token di ragionamento nascosto per ogni analisi. L'estrazione clausole è meccanica e non ne ha bisogno. Vedi [[INCIDENTS]] INC-013.

---

## 2 — Modifiche al codice

| File | Modifica | Perché |
|---|---|---|
| `llm_client.py` | `env = {**os.environ, "MAX_THINKING_TOKENS": "0"}` passato al subprocess CLI | **Fix latenza 12×** (vedi §1) |
| `llm_client.py` | flag `--strict-mcp-config` nella chiamata CLI | Igiene: non caricare i server MCP utente/progetto |
| `llm_client.py` | `cwd=_CLEAN_CWD` (dir temp vuota) + import `tempfile` | Igiene: non caricare CLAUDE.md/skill del vault |
| `llm_client.py` | client SDK singleton (`_sdk_client`, `_get_sdk_client()`, `warmup()`) | Scaffold "client caldo" per backend `sdk` (dormiente) |
| `llm_client.py` | `_call_sdk` usa il singleton + prompt caching (`cache_control: ephemeral`) sul system prompt | Riuso del prefisso di sistema sul path `sdk` |
| `main.py` | `import llm_client` + `llm_client.warmup()` al boot | Istanzia il client SDK all'avvio (no-op su `cli`) |
| `pdf_processor.py` | detector testo corrotto: `_garbled_ratio()`, `GARBLED_RATIO_THRESHOLD=0.01` | Rifiuta i PDF con font ToUnicode rotta invece di citare mojibake (vedi §4) |
| `.env` | creato (era assente); `LLM_BACKEND=cli`, `CLAUDE_MODEL` | Config persistente |

> ⚠️ **Vincolo di progetto confermato:** SpecterAI resta su **Claude Code CLI via terminale (€0)**. Il backend `sdk` (API a pagamento) è scaffoldato come fallback ma **non attivato**. Vedi memoria `project-specterai-cli-only`.

---

## 3 — Modello: Haiku vs Sonnet (stesso Consip)

| | Haiku 4.5 | Sonnet 4.6 |
|---|---|---|
| Tempo | 23,8s | 44,4s |
| Penali | Medio | **Alto** (coglie "fatto salvo il maggior danno") |
| Citazioni | corrette, brevi | più complete e contestualizzate |

**Decisione:** **Sonnet per demo/consegna** (giudizio sul rischio migliore), **Haiku per iterare veloce**. `.env` attualmente su Sonnet.

---

## 4 — Detector testo corrotto (nuova feature)

Alcuni PDF (es. Consip) estraggono mojibake (`Đoŵplessivo` = "complessivo") per via di font subset con **ToUnicode rotta** (`Calibri-Identity-H`). Nessun estrattore testo lo risolve (fitz, markitdown → peggio con `cid:`); solo l'OCR, in roadmap.

`pdf_processor.extract_text` ora misura la densità di caratteri Latin Extended-A/B e **rifiuta onestamente** il PDF se supera l'1% (coerente con l'anti-allucinazione: niente testo non verificabile).

**Calibrazione su PDF reali:** Consip 4,42% · NDA Polimi / co.co.co. / locazione INPS 0,00% → soglia 1%.

Dettaglio + scelta OCR (Tesseract vs baidu/Unlimited-OCR) in [[docs/OCR_fallback_decisione]].

---

## 5 — Verifica E2E (backend cli + thinking OFF)

| Test | Modello | Esito |
|---|---|---|
| NDA Polimi (corto) | Haiku | **13s**, 200 OK, citazioni verbatim |
| Capitolato Demanio (1 MB, troncato) | Haiku | **55s**, 200 OK, rischi completi |
| Consip (corrotto) | — | **HTTP 422 in 0,7s** (rifiutato dal detector, nessuna call LLM sprecata) |
| co.co.co. Sapienza (pulito) | Sonnet | **33s**, 200 OK, qualità da demo |

**Prima → dopo:** NDA 166s → 13s. Obiettivo "ridurre i minuti" raggiunto su CLI a €0.

---

## 6 — Bug emerso (da risolvere)

Sul co.co.co.: il report mostra **prosa e citazione del Foro contraddittorie** — prosa "segnaposto non leggibile", citazione "il Foro competente è quello di Roma". Causa probabile: il privacy_filter oscura "Roma" (città→PII) nel testo verso l'LLM, ma il restore la ripristina nella citazione. Nuova occorrenza di **over-redaction** → [[INCIDENTS]] INC-006.

---

## Connessioni

- [[INCIDENTS]] — INC-013 (latenza thinking, nuovo), INC-001 (mitigato dal detector), INC-006 (over-redaction, nuova occorrenza)
- [[docs/OCR_fallback_decisione]] — perché il detector e quale OCR per il fallback
- [[Specifica Tecnica v4 - SpecterAI]] · [[CHANGELOG_BUILD_04-06]] (sessione precedente)
- [[PRESENTAZIONE_SpecterAI (bozza)]] — materiale per §6 (qualità) e §7 (limiti)
