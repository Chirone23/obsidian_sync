# CHANGELOG BUILD — Sessioni 20-05 e 21-05 · SpecterAI

**Progetto:** SpecterAI — AI Contract Analyzer per Non-Avvocati
**Periodo documentato:** 2026-05-20 (building MVP) + 2026-05-21 (code review + hardening)
**Spec di riferimento all'epoca:** v3.1 (congelata) · ora superata da [[Specifica Tecnica v4 - SpecterAI]]
**Fonti:** git log, [[DISCUSSION_FILE_1]] (18 round building), [[CODE_REVIEW_SPECTERAI_20260521]], [[SESSION_HANDOFF]], [[PROMPT_LOG]]

> Questo file consolida in un unico posto le modifiche delle due sessioni in cui SpecterAI è passato **da spec a software funzionante e poi indurito**. Prima erano sparse tra commit, discussion file e log.

---

## Parte 1 — Sessione 2026-05-20: building dell'MVP

Da specifica congelata (v3.1) a MVP end-to-end funzionante in 18 round di building con Claude Code CLI.

### 1.0 Setup pre-build

| Commit | Modifica |
|---|---|
| `e2ff7d1` | `requirements.txt` + `.gitignore` + `.cursorrules` — setup iniziale |
| `96ae9c4` → `5515342` → `f7190b7` | Regole Cursor (Pydantic v2 + Python 3.12) → migrate in `.cursor/rules/*.mdc` → **rimosse**: abbandonato Cursor, build con **Claude Code CLI** |

**Decisione chiave:** scelto Claude Code CLI come ambiente di build (zero costo, stesso modello del runtime, no drift). Questo ha determinato anche l'integrazione runtime via CLI subprocess — poi riconosciuta come divergenza architetturale e resa configurabile in v4 (vedi [[SPEC_ERRATA]] ERR-08).

### 1.1 Consolidamento system prompt

| Commit | Modifica |
|---|---|
| `a07abe9` | `prompts/system_prompt.md` consolidato — v1-final + **patch v2/v2.1/v2.2** integrate (8 pattern di drift chiusi) |
| `e35aded` | Test #6/#7/#8 **PASS** — convalida patch su 3/3 PDF rimanenti, 0 nuovi pattern |
| `b55c9b3` | +3 segnali S-01/S-02/S-03 da monitorare post-convalida |

Le patch (da mini-suite test #1-#8 su 8 contratti reali):
- **v2** — no ellissi `[...]` negli excerpt; no calcoli aritmetici nei plain_language
- **v2.1** — grounding stretto plain_language↔raw_excerpt (numeri, %, riferimenti normativi, **qualificatori modali**); excerpt multi-span per clausole cross-article
- **v2.2** — anti-speculazione, no inferenza giurisprudenziale, clausola positiva "if absent → say so"

### 1.2 Pivot del motore privacy: OPF → Hybrid

| Commit | Modifica |
|---|---|
| `229292e` | `Privacy Filter Integration` — design iniziale (OpenAI Privacy Filter) + link in MOC |
| `f96473e` | Verifica Perplexity P1+P2+P3 → privacy filter **promosso a requisito GDPR** (Art. 5/25), non feature |
| `cb014bd` | **PIVOT OPF→Hybrid** (regex IT + spaCy) dopo smoke test |

**Perché il pivot:** OPF (1.5B MoE) testato su 2 PDF reali → ~55-83s di sola inferenza su 3KB → ~10 min per contratto medio, inaccettabile per UX MVP. Soluzione: regex deterministico per PII strutturate (CF, P.IVA-Luhn, IBAN-mod97, email, tel) + spaCy `it_core_news_sm` per NER nomi/luoghi → latency <1s. Dettaglio in [[Privacy Filter Integration]].

### 1.3 Moduli del programma (ordine di costruzione)

| Commit | File | Cosa fa |
|---|---|---|
| `40eb0f8` | `schemas.py` | Pydantic v2 — `CategoryResult` + `ContractAnalysis`, validatori ellissi/span/7-categorie, `raw_excerpt: str \| list[str]` |
| `19c15fc` | `pdf_processor.py` | PyMuPDF 1.27 — estrazione testo + troncamento 40k + gestione PDF corrotti/scansionati |
| `e903c97` | `regex_layer.py` | re stdlib — estrazione date/importi/scadenze (metadati strutturati pre-filtro) |
| `1e6a762` | `privacy_filter.py` | Hybrid regex IT + spaCy `sm` — `redact()`/`restore()` testati su CF/PIVA/IBAN/email/tel/NER |
| `28c1f81` | `llm_client.py` | Claude Code CLI + fix `schemas.py` str→list[str] normalizer — E2E testato su co.co.co. ERSU |
| `61a2de5` | `main.py` + `templates/index.html` + `templates/report.html` | MVP completo (upload → report HTML) |

### 1.4 Incident e test E2E

| Commit | Modifica |
|---|---|
| `d2dc4ba` | **INC-004** — WinError 206 su Windows (testi >40k char come argv) → fix: `user_message` da argv a **stdin** |
| `5780dac` | INCIDENTS/SESSION_HANDOFF/PROMPT_LOG aggiornati — INC-004 risolto, **test E2E 7/8 PDF OK** |
| `69da64f` | `DISCUSSION_FILE_1.md` — 18 round building documentati |
| `3084c31` | `BREAKDOWN.md` — piano task 5 fasi, score 7/8 PDF |

**Esito 20-05:** MVP Fase 1-4 completo. E2E su 7/8 PDF (Consip 40k/268s 7/7 cat; Capitolato Demanio 40k/163s 5/7 cat attesi; "Contratto firmato.pdf" rifiutato correttamente — scansionato, no OCR).

---

## Parte 2 — Sessione 2026-05-21: code review + production hardening

Code review critica ([[CODE_REVIEW_SPECTERAI_20260521]], 333 righe) su 6 moduli → 3 bug critici + minori, tutti corretti in 6 batch.

| Commit | Batch | File toccati |
|---|---|---|
| `177f152` | Code review + mapping spec↔implementazione | `CODE_REVIEW_SPECTERAI_20260521.md` |
| `3b81fd1` | Batch 1 quick-win: timeout, async, env var, model_copy, disclaimer | llm_client, main, schemas, report.html |
| `b61baef` | Luhn PIVA + mod-97 IBAN (spec compliance) | privacy_filter |
| `ec862f8` | Regex PII context-aware (CF, phone, importi) | privacy_filter, regex_layer |
| `1c86e81` | LLM client JSON parsing strutturato + backoff esponenziale | llm_client |
| `dc4275a` | Hardening UX: truncation warning, upload size pre-check, spaCy fallback | pdf_processor, main, privacy_filter, report.html |
| `999071f` | spaCy ordering bug fix (spaCy before regex) + test unitari | privacy_filter, tests/ |
| `26ea53b` | SESSION_HANDOFF — sintesi 6 batch, 13 bug risolti | — |

### Bug corretti (mappati agli incident)

| Incident | Bug | Fix | Commit |
|---|---|---|---|
| **INC-009** (Critical) | Ordering spaCy: offset pre-regex applicati post-regex → `[PER[CF_1]_2]` | spaCy Passo 1 (testo originale) **prima**, regex Passo 2 **dopo** | `999071f` |
| **INC-007** (High) | CF regex `IGNORECASE` globale → falsi positivi su clausole legali | context-keyword + CF uppercase-only | `ec862f8` |
| **INC-008** (High) | PIVA `\d{11}` senza checksum → matcha importi/protocolli | checksum Luhn IT + guard | `b61baef` |
| **INC-010** (High) | `subprocess.run` senza `timeout=` → hang/DoS | `timeout=300` | `3b81fd1` |
| **INC-011** (High) | `async def` con subprocess sincrono → blocca event loop | `await asyncio.to_thread(...)` | `3b81fd1` |

Più 5 minori risolti: backoff esponenziale, `CLAUDE_MODEL` da env, MAX_CHARS con warning UI, phone regex con prefisso +39 obbligatorio, `disclaimer` spostato da Pydantic al template.

> Tutti questi incident sono **✅ Risolti** e verificati il 2026-05-29 sul codice canonico (4/4 unit test, redazione PII zero-leak). Dettaglio conforme al template in [[INCIDENTS]] INC-007…INC-011.

---

## Riepilogo: stato del sistema dopo le due sessioni

| Aspetto | Stato a fine 21-05 |
|---|---|
| MVP Fase 1-4 (6 moduli + templates) | ✅ completo |
| Pipeline privacy (redact/restore Hybrid) | ✅ funzionante |
| Test E2E | ✅ 7/8 PDF (1 scansionato rifiutato by design) |
| Bug critici code review (INC-007…011) | ✅ tutti risolti |
| Spec di riferimento | v3.1 (poi → v4 il 29-05) |

**Cosa è venuto dopo (29-05):** allineamento spec↔codice, [[SPEC_ERRATA]], backend configurabile cli/sdk, promozione a [[Specifica Tecnica v4 - SpecterAI]]. Vedi [[SESSION_HANDOFF]] sessione 2026-05-29.

---

## Connessioni
- [[Specifica Tecnica v4 - SpecterAI]] · [[Specifica Tecnica v3 - SpecterAI]]
- [[SPEC_ERRATA]] · [[Privacy Filter Integration]] · [[CODE_REVIEW_SPECTERAI_20260521]]
- [[DISCUSSION_FILE_1]] · [[INCIDENTS]] · [[PROMPT_LOG]] · [[SESSION_HANDOFF]]
- [[Progettistica AI MOC]]
