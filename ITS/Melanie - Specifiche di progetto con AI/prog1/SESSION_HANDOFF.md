# SESSION_HANDOFF — SpecterAI

**Progetto:** SpecterAI — AI Contract Analyzer per Non-Avvocati (Italiano)
**Data progetto inizio:** 2026-04-28 (Lezione 1 brainstorming)
**Ultima sessione:** 2026-05-29 (allineamento spec↔codice, SPEC_ERRATA, config backend cli/sdk, riconciliazione incident)
**Prossima sessione:** Lezione 5 — Test plan §8 (T1-T12 + T13/T14/T15) + aggiornamento PROMPT_LOG/INCIDENTS con dati reali
**Spec corrente:** **v4** (`Specifica Tecnica v4 - SpecterAI.md`, 2026-05-29) — cambio architetturale (privacy-first §3.bis + backend cli/sdk) motivato in [[SPEC_ERRATA]]. La v3.1 (95/100, confermata prof) resta congelata e preservata come baseline.

---

## 🟢 SESSIONE 2026-05-29 — allineamento spec↔codice + config backend

**Contesto:** verifica dello stato reale del codice canonico (`prog1/specterai/specter-ai/`) contro la spec congelata, creazione SPEC_ERRATA, costruzione del config backend.

**Fatto:**
- **Creato [[SPEC_ERRATA]]** — narrativa evoluzione v1→v3 (da "prompt+LLM" a sistema indurito) + 9 voci errata (divergenze codice↔spec v3.1 congelata).
- **Promossa [[Specifica Tecnica v4 - SpecterAI]]** — 2 divergenze risultate **architetturali** (ERR-01 redazione PII pre-LLM, ERR-08 backend cli/sdk) → la v3.1 non descriveva più il sistema reale. v4 integra §3.bis privacy-first (GDPR Art. 5/25), backend configurabile, gate lingua post-LLM, T13/T14/T15. v3.1 congelata e preservata; SPEC_ERRATA è il driver documentato della v4.
- **Test di verifica:** 4/4 unit test privacy pass; redazione PII reale **zero-leak** (ERR-01 confermato); app FastAPI carica.
- **Eliminata cartella duplicata `prog1/27-05/`** (era byte-identica a `specterai/specter-ai/`; backup in `27-05.zip`).
- **ERR-08 risolto — backend LLM configurabile:** nuovo `config.py` (`LLM_BACKEND` cli/sdk, carica `.env`), `setup.py` (setup guidato), `.env.example`; `llm_client.py` dispatcha `_call_cli`/`_call_sdk` (SDK con `temperature=0`/`max_tokens=2048`); `main.py` con `config.validate()`. **cli** = dev €0 (solo macchina autenticata), **sdk** = deploy con API key.
- **ERR-09 deciso (opzione B):** gate lingua resta post-LLM, `langdetect` rinunciato, spec da allineare al codice. INC-005 chiuso.
- **Riconciliazione INC-007…011:** erano stati registrati come 🔴 Open (5 bug critici), ma la verifica sul codice ha dimostrato che sono **già risolti** nella sessione fix del 21/05 (commit `3b81fd1`…`999071f`); i numeri di riga citati non esistono nei file attuali. Riallineati a ✅ Risolto in forma conforme al template del corso (Fix applicato + Stato).
- **+INC-006** (over-redaction filtro privacy, Low, accettabile MVP).

**Aperti / da decidere:**
- ERR-05 (fuzzy 0.92 copre solo excerpt) → calibrare in T11.
- INC-006 (precision over-redaction) → eventuale context-keyword P.IVA post-MVP.
- Rettifiche testuali §3/§4/§6/§7 della spec via SPEC_ERRATA (ERR-08/ERR-09) — non si riapre la spec congelata.

---

---

## 🟢 STATO ATTUALE (post-sessione 2026-05-20)

### ✅ Completato dopo l'ultimo handoff (2026-05-04)

**Sessione 2026-05-07 — Spec v3 (13 fix)**
- Review interna Spec v2 + Meta-Review multi-agent (3 agenti OpenCode su modelli free OpenRouter)
- 4 query Perplexity di verifica fattuale (pricing Sonnet, Anthropic ToS, AI Act, provider alternativi)
- 13 fix integrati in Spec v3 — highlight: **AI Act riclassificato high-risk → limited-risk Art. 6(3)**, verifica anti-allucinazione `raw_excerpt` con fuzzy match, test plan T1-T12 eseguibile, scenari costo ricalcolati Sonnet (0,02→0,04 €), gate lingua IT/EN bloccante, Mistral Medium 3 come fallback post-MVP

**Sessione 2026-05-10 — Spec v3.1 (5 fix + 3 fix coerenza)**
- Letto feedback prof (95/100 + File3 stack tecnico) → 3 gap minori identificati
- 3 patch da feedback prof: **§7 strategia token dev zero-cost** (Claude Code CLI invece di Gemini/OpenRouter per evitare drift modello), **§2.bis Stretch Goals** separati (Playwright per PDF, self-consistency per confidence), **§12.bis Build Roadmap** moduli→lezioni→deliverable
- 2 fix da review Perplexity validation generale (9 punti, 7/9 confermati): retention Anthropic 30gg→7gg, DPA esplicito pre-deploy
- Audit indipendente Opus su spec v3.1 → 3 fix coerenza interna applicati: calibrazione soglia 0.92 da T11, caveat statistico kappa N=35, nota langdetect su contratti misti
- **PROMPT_LOG** e **INCIDENTS** aggiornati con storia v2→v3→v3.1 + 3 nuovi incident metodologici risolti (INC-000d sovraclass AI Act, INC-000e sottostima costi, INC-000f drift retention)

### 🟢 Nuovo feedback prof (sessione 2026-05-11)

Letti i 2 file in `valutazione 10-5/`:
- **`ChristianG_Valutazione_Aggiornata.md`** — 95/100 confermato. Verdetto prof: *"Non c'è nulla da aggiungere. Domani apri Cursor e parti da schemas.py."* Punti di forza citati: spec v3 (826 righe, 18 mod), build roadmap §12.bis, PROMPT_LOG come "racconto di come hai usato l'AI", INC-000c (metodologia), INC-000d (AI Act riclass), §11.bis (auto-correzione score).
- **`ChristianG_Promemoria_Lezione4.md`** — checklist pre-building + breakdown 5 fasi + struttura cartelle dettagliata (vedi sotto).

### ⏳ Checklist pre-building Lez. 4 (dal Promemoria prof)

Prima di aprire Cursor:
- [ ] **Testare prompt di sistema** su almeno 1 contratto reale — sessione su **Claude Code CLI** (sfrutta abbonamento, zero costo, no drift di modello)
- [ ] Verificare output JSON: 7 categorie presenti, `raw_excerpt` con citazioni reali, `risk_level` ∈ {low, medium, high}
- [ ] Avere pronti **3 contratti PDF** di test (servizi, NDA, fornitura)
- [ ] Creare **struttura cartelle** del progetto (vedi sotto)
- [ ] API key Anthropic funzionante in `.env`
- [ ] `PROMPT_LOG.md` e `INCIDENTS.md` già esistono ✅ (popolati con storia v1→v3.1)

### 📂 Struttura cartelle progetto (dal Promemoria prof)

```
specter-ai/
├── main.py                # FastAPI app, endpoint upload + analisi
├── pdf_processor.py       # PyMuPDF: estrazione testo da PDF
├── regex_layer.py         # Estrazione date, importi, scadenze
├── llm_client.py          # Chiamata Claude API + gestione retry
├── schemas.py             # Schema Pydantic per validazione JSON
├── templates/
│   ├── index.html         # Pagina upload
│   └── report.html        # Template Jinja2 per il report
├── prompts/
│   └── system_prompt.md   # Il prompt (già scritto nella spec v3 §6)
├── tests/
│   └── contratti/         # PDF di test
├── PROMPT_LOG.md
├── INCIDENTS.md
├── .env                   # ANTHROPIC_API_KEY
├── .gitignore
├── requirements.txt
└── README.md
```

### ⏳ Sequenza fasi building (dal Promemoria prof, 5 fasi)

| Fase | Lezione | Task | Dipendenze |
|---|---|---|---|
| **Fase 1 — Fondamenta** | Lez. 4 | `schemas.py` → `pdf_processor.py` → `regex_layer.py` | schemas è primo (tutto valida contro lui) |
| **Fase 2 — Core AI** | Lez. 4-5 | `prompts/system_prompt.md` → `llm_client.py` → integrazione regex+LLM | schemas + pdf + regex |
| **Fase 3 — Interfaccia** | Lez. 5 | `main.py` (endpoint FastAPI) + `templates/index.html` + `templates/report.html` | tutto Fase 1-2 |
| **Fase 4 — Test e docs** | Lez. 5-6 | Test 5 contratti + edge case + contratto EN + popolamento PROMPT_LOG/INCIDENTS con dati reali | sistema E2E funzionante |
| **Fase 5 — Rifinitura/demo** | Lez. 6-7 | Polish HTML + eventuali stretch goals (§2.bis spec) + selezione 2-3 contratti demo + presentazione 2 min | sistema testato |
| **Fase 6 — Presentazione di progetto totale** (ultimissimo step) | finale | Racconto end-to-end: pitch + evoluzione spec v1→v4 + architettura privacy-first + demo + processo/qualità (PROMPT_LOG/INCIDENTS/SPEC_ERRATA) + limiti dichiarati. Bozza: [[PRESENTAZIONE_SpecterAI (bozza)]] | tutto il progetto completato |

**Nota su numerazione lezioni:** la build roadmap §12.bis della spec (scritta prima del Promemoria) mappa Lez. 3/4/5/6; il calendario corso effettivo è Lez. 4/5/6/7 (slittamento +1). Non è un problema spec, solo allineamento al calendario.

**Cut-off operativo da audit Opus G5:** se a fine Fase 1 `regex_layer.py` non è testato, taglialo (estrazione minimale è già OK) e proteggi `llm_client.py` + `main.py` per la lezione successiva.

### ⏳ Sequenza building Lez. 3-6 (da `§12.bis Build Roadmap`)

| Lezione | Moduli | Deliverable |
|---|---|---|
| **Lez. 3** | `schemas.py` + `pdf_processor.py` + `regex_layer.py` | PDF→testo+metadati su 2 contratti |
| Lez. 4 | `llm_client.py` + `main.py` + `templates/` | Flusso E2E upload→report HTML |
| Lez. 5 | Test plan §8 eseguito (T1-T12) + `PROMPT_LOG`/`INCIDENTS` aggiornati | 12 test pass/fail loggati |
| Lez. 6 | Polish + eventuali stretch goals (§2.bis) + demo prep | Demo 2-3 contratti |

**Cut-off operativo (da audit Opus G5):** se a fine Lez. 3 `regex_layer.py` non è testato, taglialo (estrazione minimale già OK) e proteggi `llm_client.py` + `main.py` per Lez. 4.

### ❓ Domande aperte

| # | Domanda | Quando decidere |
|---|---|---|
| Q1 | Soglia fuzzy 0.92 va calibrata: quanti falsi negativi accettare? | Test T11 (Lez. 5) |
| Q2 | Kappa di Cohen o agreement % semplice come metrica risk_level? | Lez. 5, su N=35 punti dati reali |
| Q3 | Stretch goals da attivare (PDF download? confidence?) | Solo se Lez. 6 ha tempo |
| Q4 | OCR scansioni: reject definitivo o Tesseract opzionale post-MVP? | Mai nel MVP corso (fuori scope) |

### 💰 Budget API consegna

| Voce | Costo stimato |
|---|---|
| Dev iterazione prompt (Claude Code CLI) | €0 — abbonamento |
| Generazione codice (Cursor + Sonnet) | incluso |
| Test plan §8 runtime (12 test su Claude API) | ~€0,60 |
| Demo presentazione (3-5 contratti) | ~€0,40 |
| **Totale stimato** | **<€1,50** |

### 📁 File modificati nelle sessioni 2026-05-10 / 2026-05-11

**2026-05-10 (Spec v3.1 finalizzazione):**
- ✅ `Specifica Tecnica v3 - SpecterAI.md` — 5 patch nuove (§2.bis, §7, §12.bis + 2 fix Perplexity) + 3 fix coerenza Opus (changelog #16-18)
- ✅ `PROMPT_LOG.md` — header aggiornato, +3 righe timeline, +2 sezioni "Spec v3" e "Spec v3.1"
- ✅ `INCIDENTS.md` — 3 nuovi incident risolti (INC-000d/e/f) + date INC-001/002/003 allineate a Lez. 3-4
- ✅ Riorg cartelle: `gap spec-v2/`, `spec precedenti/`, `valutazione x (prima valutazione)/`, `valutazione 10-5/` (nuova)

**2026-05-11 (verifica nuovo feedback prof + 2 test runtime prompt v1-final):**
- ✅ Letti `valutazione 10-5/ChristianG_Valutazione_Aggiornata.md` e `ChristianG_Promemoria_Lezione4.md` → 95/100 confermato, nessuna patch spec richiesta
- ✅ `README_DOCUMENTAZIONE.md` — rimosso flag "DA LEGGERE", aggiornato esito + spec confermata + fase a Lez. 4
- ✅ `SESSION_HANDOFF.md` — checklist pre-building Lez. 4 + struttura cartelle + 5 fasi dal Promemoria prof
- ✅ Scaricati 8 PDF contratti reali da Perplexity free in `prog1/specterai/contratti/` (5/5 tipologie: servizi, NDA, fornitura, co.co.co., locazione) — vedi INC-000g
- ✅ **Test runtime #1** Sonnet via Claude.ai su Demanio appalto (PDF #1/8) → JSON conforme, autovalut. 4.75/5, 2 osservazioni: ellissi `[...]` in raw_excerpt + numeri calcolati (143€/g, 14.331€) in plain_language
- ✅ Verifica manuale numeri: 143.315,16 e 2.282.328,93 sono nel PDF; 143/14.331 sono calcoli di Sonnet (1‰ e 10%) → conferma rischio aritmetica spontanea
- ✅ **Test runtime #2** Sonnet via Claude.ai su co.co.co. ERSU Messina (PDF #2/8) **senza modifiche al prompt** → JSON conforme, autovalut. 5/5, **zero ellissi e zero calcoli**. Pattern Test #1 NON riprodotti → contesto-dipendenti (si attivano su contratti densi di percentuali + articoli lunghi)
- ✅ Note operativa scoperta in Test #2: **context bleed Claude.ai** — chat fresh obbligatoria per ogni run di test (primo run #2 ha riusato il contesto del PDF Demanio ignorando il nuovo upload). Da formalizzare INC-000h se si ripresenta
- ✅ **2 patch v2 prompt** definite e BLOCCO PRONTO DA INCOLLARE in `PROMPT_LOG.md` → da applicare a `prompts/system_prompt.md` quando si crea la struttura in Cursor (Fase 1 Lez. 4):
  1. no-ellissi nei raw_excerpt (singolo brano contiguo, mai `[...]`)
  2. no-calcoli nei plain_language (vietate operazioni aritmetiche su numeri citati; numeri ammessi solo se verbatim nel raw_excerpt corrispondente)
- ✅ **Test runtime #3** Sonnet v1-final su Consip Condizioni Generali Fornitura (PDF #3/8, denso, multi-articolo) → JSON conforme, autovalut. 5/5/5/4. Pattern v2 (ellissi + calcoli) NON riprodotti. **Scoperto pattern 3 nuovo:** cross-article extraction (5/6 fatti del plain_language presenti nel PDF ma in articoli diversi dal raw_excerpt) + 1/6 caso di **drift semantico su qualificatore modale** ("risoluzione automatica" vs "potrà risolvere") — più pericoloso dei calcoli per legal-AI
- ✅ Verifica fattuale Test #3 **delegata a Claude Haiku via Claude.ai** sul PDF allegato (sessione fresh, prompt verifica letterale): 5/6 PRESENTI + 1/6 SIMILE + 0/6 ASSENTI → zero allucinazioni, Ipotesi A (cross-article) confermata, Ipotesi B (invenzione) esclusa
- ✅ **Patch v2.1** definita come BLOCCO additivo in PROMPT_LOG: grounding stretto plain_language ↔ raw_excerpt (numeri, percentuali, riferimenti normativi, **qualificatori modali**) + raw_excerpt come lista multi-span per categorie cross-article. Richiede modifica minima schema Pydantic in Fase 1 Cursor (`raw_excerpt: str | list[str]`)
- ✅ Decisione: prompt resta source-of-truth nella spec v3.1 (text-level invariato); patch v2 + v2.1 vivranno in `prompts/system_prompt.md` in Cursor → spec **non** viene modificata ora. Gap fuzzy match 0.92 (protegge solo excerpt, non plain_language) da segnalare alla prof come edge-case post-MVP
- ✅ Tutti i commit pushati su `obsidian_sync`

### 🟢 Stato pre-Cursor (sintesi entrata Lez. 4)

| Asset | Stato |
|---|---|
| 8 PDF contratti reali (5/5 tipologie) | ✅ pronti in `prog1/specterai/contratti/` |
| Prompt v1-final validato cross-contratto | ✅ **5/8 PDF** (mini-suite completata: Demanio appalto + ERSU co.co.co. + Consip Cond. Gen. + NDA Politecnico Milano + Locazione INPS) |
| Patch v2 prompt (ellissi + calcoli) | ✅ definite, blocco pronto da incollare in PROMPT_LOG (post Test #2) |
| Patch v2.1 prompt (grounding stretto + qualificatori modali) | ✅ definita post Test #3 + verifica Haiku, blocco additivo in PROMPT_LOG |
| Patch v2.2 prompt (anti-speculazione + no inferenza giurisprudenziale + clausola positiva "if absent → say so") | ✅ definita post Test #4 e rafforzata post Test #5 (pattern 5b asserzione non-qualificata), blocco additivo in PROMPT_LOG |
| Sintesi cumulativa 5/5 test in PROMPT_LOG | ✅ tabella 8 pattern catalogati + decisioni finali pre-Cursor (patch + schema Pydantic + few-shot + 2 nuovi test T13/T14) |
| **`prompts/system_prompt.md` consolidato** | ✅ creato 2026-05-20 — v1-final + patch v2/v2.1/v2.2 + few-shot |
| **Convalida patch v2/v2.1/v2.2 su 3/3 PDF rimanenti** | ✅ PASS 2026-05-20 — Test #6/7/8. 0 pattern nuovi. |
| **MVP Fase 1-3 completo** | ✅ `schemas.py`, `pdf_processor.py`, `regex_layer.py`, `privacy_filter.py` |
| **MVP Fase 4 completo** | ✅ `llm_client.py` (fix INC-004 stdin), `main.py`, `templates/index.html`, `templates/report.html` |
| **INC-004 WinError 206** | ✅ RESOLVED 2026-05-20 — stdin fix in `llm_client.py`, commit `d2dc4ba` |
| **Test E2E: 7/8 PDF** | ✅ 5 OK precedenti + Consip OK (268s, 7/7 cat) + Capitolato Demanio OK (163s, 5/7 cat attesi) — Contratto firmato.pdf rifiutato correttamente (scansionato, no OCR) |
| Test T13 (grep speculazione=0) + T14 (grounding plain↔raw_excerpt) in test plan §8 | ⏳ da eseguire in Lez. 5 |
| Spec v3.1 | ✅ confermata 95/100, nessuna patch richiesta dalla prof |
| Server uvicorn `main:app` | ⏳ da testare avvio manuale (`uvicorn main:app --reload`) |

---

## 🔍 Code Review Critica (post-MVP, sessione 2026-05-21)

**File:** `CODE_REVIEW_SPECTERAI_20260521.md` (333 righe)

**Scope:** Review di qualità production-grade su 6 moduli Python (schemas.py, pdf_processor.py, regex_layer.py, privacy_filter.py, llm_client.py, main.py).

**Risultati:**
- ✅ **Giudizio:** MVP pre-beta — architettura solida, 3 bug critici in produzione
- ✅ **5 pregi:** validator Pydantic (schemas), separazione privacy layer, rifiuto PDF vuoti, retry logic, HTTPstatus mapping
- ⚠️ **6 problemi critici:** PIVA false positive (no Luhn), CF pattern debole, spaCy offset bug, no subprocess timeout (DoS), JSON parsing fragile, event loop bloccato
- ✅ **10 problemi minori:** phone regex laxa, caricamento spaCy sync, modello hardcoded, retry senza backoff, MAX_CHARS silenzioso, ecc.

**Top 3 fix (ROI massimo):**
1. `timeout=300` + `await asyncio.to_thread(analyze, ...)` → 5 min, elimina DoS
2. PIVA Luhn + CF context-aware → 30 min, elimina false positive massicia
3. JSON parsing robusto (`JSONDecoder.raw_decode`) → 20 min, elimina silent failure

**Mapping spec vs codice:**
- 5/6 bug erano già documentati in spec (Privacy Filter Integration.md, Specifica Tecnica v3) ma non implementati
- 3 bug nuovi non nella spec: offset spaCy, event loop block, JSON parsing
- Conclusione: **spec è più matura del codice**, normale per MVP da corso (priorità end-to-end over robustezza)

**Confidence:** 4/5 (bug critici certi, caveat su testing spaCy runtime)

---

## Riepilogo Storico Completo

### Lezione 1 — Case Study & Setup (2026-04-28)

**Compito:** Generare 5 idee di progetto AI con validazione 5-dimensionale.

**Processo:**
1. Brainstorming 7 idee iniziali
2. Scartate 5 per vari motivi (crowded market, complexity, data constraints)
3. **Selezionata:** SpecterAI — AI Contract Analyzer for Non-Lawyers

**Output:** 
- ✅ Brainstorming - Validazione Idea.md (5 scartate + 1 scelta con motivazione)
- ✅ Validazione 5D confermata (Technical: M, Economic: H, Complexity: M, Risk: M-H, Sustainability: H)

**Deliverable:** Brainstorming note nel vault + idea validation documented

---

### Lezione 2 — Specifica Tecnica & Prompt Engineering (2026-04-29 → 2026-05-03)

**Compito:** Market research, stress-test idea, write full technical specification with prompt design.

**Fasi:**

#### 2.1 — Market Research (2026-04-29)
- Query Perplexity: "Italian contract analysis market, non-lawyer SMB"
- Ricerca competitor: Harvey, Legora, Spellbook, Docusign
- Market size: ~2.7M freelancer/self-employed in Italy
- Assumption: 10-20 contratti/anno (no public data)
- **Risultato:** ✅ Idea viable, medium risk su competitor enterprise-scale

**Deliverable:** Contract Analyzer - Validazione Idea.md

#### 2.2 — Specifica Tecnica v1 (2026-04-30)
- 11 sezioni complete: problem statement, MVP scope, stack, architecture, 7 risk categories
- Stack definito: Python 3.12, FastAPI, PyMuPDF, Claude API, Pydantic, Jinja2
- 7 categorie rischio: payment_terms, auto_renewal, penalties, liability_limitation, termination, governing_law, intellectual_property
- Edge cases formalizzati (PDF size, encoding, language, density)
- Rischi identificati con mitigations (PDF parsing, hallucinations, AI Act, scope creep)
- Pre-build checklist completata

**Deliverable:** Specifica Tecnica v1 - SpecterAI.md (11 sezioni, 470 righe)

#### 2.3 — PC Verification ARROW (2026-05-01)
- Windows 11 Pro, 16GB RAM, AMD Ryzen 5 PRO 5650U
- Python 3.12.10 installato
- Dipendenze installate: fastapi, uvicorn, pymupdf, anthropic, jinja2, python-multipart, python-dotenv, pydantic ✅ 8/8
- Cursor IDE installato ✅
- Git 2.53.0 confermato ✅
- ANTHROPIC_API_KEY: confermato (configurazione pending)

**Deliverable:** Verifica PC - ARROW.md (Status: QUASI PRONTO)

#### 2.4 — Competitive Analysis Mikeoss (2026-05-02)
- User query: "Mikeoss" competitor discovered
- Ricerca Perplexity su Mikeoss vs SpecterAI positioning
- **Risultato:** Medium Risk (Mikeoss = lawyer-centric, firm-scale, self-hosted; SpecterAI = SMB-first, Italian-first, plain-language)
- Conclusione: Non cannibalize — target diversi

**Deliverable:** Mikeoss competitive analysis (used in Specifica v2 update)

#### 2.5 — Specifica Tecnica v2 (2026-05-03 → 2026-05-04)
- **5 major improvements aggiunte:**
  1. Competitive Positioning section (vs Mikeoss, Harvey, Legora)
  2. Full prompt text (C.I.A.R.E. structure: Ruolo → Task → Formato → Vincoli → DO NOT)
  3. Few-shot examples (2x per categoria: presente + assente)
  4. Multi-model routing (task-specific LLM selection)
  5. Green AI / AI Sustainability section (token optimization, deterministic routing)

- **Prompt final parameters:**
  - Model: claude-sonnet-4-6
  - Temperature: 0 (determinism)
  - Max tokens: 2048
  - System prompt: full text (600 token)
  - Few-shot: 14 examples total (2x7 categories)

- **Green AI optimizations:**
  - Text truncation a 40K chars (save ~30% token input)
  - No LLM for input validation/language detection
  - Stateless design (no history)
  - Temperature=0 (no probabilistic overhead)

**Deliverable:** Specifica Tecnica v2 - SpecterAI.md (12 sezioni, 500+ righe, production-ready)

---

### Lezione 3 — Building e Deployment (2026-05-04 → ongoing)

**Compito:** Implementare MVP in Cursor seguendo Specifica v2. Documentare processo.

#### 3.0 — Documentation Framework Setup (2026-05-04)
- Creati 3 file di documentazione:
  - ✅ **PROMPT_LOG.md** — Iterazioni prompt, v0 (brainstorming) → v1 (specifica v1) → v1-final (specifica v2)
  - ✅ **INCIDENTS.md** — Registro errori: INC-000a/b/c (resolved), INC-001/2/3 (pending testing)
  - ✅ **SESSION_HANDOFF.md** — Stato progetto tra sessioni (questo file)

**Deliverable:** 3 documentation files + git commit

#### 3.1 — MVP Fase 1: Skeleton Project (2026-05-05, TBD)
- [ ] Setup Cursor project structure
- [ ] Git init + first commit
- [ ] requirements.txt with all deps
- [ ] .env.example for API key

#### 3.2 — MVP Fase 2: PDF Input Validation (2026-05-05, TBD)
- [ ] Validazione file size (≤10MB)
- [ ] Validazione MIME type (application/pdf)
- [ ] Test apertura PDF con PyMuPDF
- [ ] Unit tests: valid PDF, corrupted, oversized
- [ ] Monitor INC-001 (PyMuPDF edge cases)

#### 3.3 — MVP Fase 3: Text Extraction + Regex Layer (2026-05-06, TBD)
- [ ] PyMuPDF text extraction
- [ ] Regex for dates, amounts, scadenze
- [ ] UTF-8 validation (monitor INC-003)
- [ ] Language detection heuristic
- [ ] Unit tests: 5 test cases

#### 3.4 — MVP Fase 4: Claude API Integration (2026-05-07, TBD)
- [ ] System prompt injection (from Specifica v2)
- [ ] Few-shot examples loading
- [ ] JSON schema validation (Pydantic)
- [ ] Error handling: timeout, malformed JSON
- [ ] Retry logic with backoff (monitor INC-002)
- [ ] Integration test: 1 real contract

#### 3.5 — MVP Fase 5: End-to-End Testing (2026-05-08, TBD)
- [ ] Load 5+ real contracts (diverse types)
- [ ] Latency measurement
- [ ] Accuracy spot-check vs manual
- [ ] Document results
- [ ] Resolve any INC-001/2/3 findings

#### 3.6 — MVP Fase 6: Final Documentation (2026-05-09, TBD)
- [ ] README.md with setup instructions
- [ ] API documentation (request/response)
- [ ] Known limitations
- [ ] Update PROMPT_LOG.md with v2 testing results
- [ ] Update INCIDENTS.md with resolved incidents
- [ ] Final git commit + push

---

## Task Completati (Lezioni 1-2)

| # | Task | Data | File/Deliverable | Status |
|---|---|---|---|---|
| 1 | Brainstorming 7 idee | 2026-04-28 | Brainstorming - Validazione Idea.md | ✅ |
| 2 | 5D validation on SpecterAI | 2026-04-28 | (same file) | ✅ |
| 3 | Market research (Perplexity) | 2026-04-29 | Contract Analyzer - Validazione Idea.md | ✅ |
| 4 | Specifica Tecnica v1 (11 sezioni) | 2026-04-30 | Specifica Tecnica v1 - SpecterAI.md | ✅ |
| 5 | PC verification ARROW | 2026-05-01 | Verifica PC - ARROW.md | ✅ |
| 6 | Competitive analysis Mikeoss | 2026-05-02 | (used in Specifica v2) | ✅ |
| 7 | Specifica Tecnica v2 (+5 improvements) | 2026-05-03-04 | Specifica Tecnica v2 - SpecterAI.md | ✅ |
| 8 | Documentation framework (PROMPT_LOG, INCIDENTS, SESSION_HANDOFF) | 2026-05-04 | 3 files in prog1/ | ✅ |

**Total progress:** 100% specification phase complete, 0% building phase

---

## Task In Progress (Lezione 3)

| # | Task | Fase | Prerequisiti | Status |
|---|---|---|---|---|
| 9 | MVP Skeleton project | Fase 1 | None | ⏳ PENDING |
| 10 | PDF input validation layer | Fase 2 | #9 | ⏳ PENDING |
| 11 | Text extraction + regex | Fase 3 | #10 | ⏳ PENDING |
| 12 | Claude API integration | Fase 4 | #11 | ⏳ PENDING |
| 13 | End-to-end testing | Fase 5 | #12 | ⏳ PENDING |
| 14 | Final documentation | Fase 6 | #13 | ⏳ PENDING |

**Total progress:** 0% building phase

---

## Documento Architettura Finale (Specifica v2)

### 7 Categorie Rischio (Locked)

1. **payment_terms** — Termini di pagamento, clock, ritardi, interessi
2. **auto_renewal** — Rinnovo automatico, exit clauses, notice periods
3. **penalties** — Penali per inadempimento, breach consequences
4. **liability_limitation** — Cap responsabilità, esclusioni danni
5. **termination** — Condizioni recesso, early exit, wind-down
6. **governing_law** — Legge applicabile, giurisdizione, dispute resolution
7. **intellectual_property** — IP ownership, usage rights, derivative works

### Stack Confermato (Locked)

```
Linguaggio:          Python 3.12+
Framework web:       FastAPI (async, lightweight)
PDF parsing:         PyMuPDF (fitz) — production-ready
Template:            Jinja2 (built-in FastAPI)
Regex:               stdlib re module
Validazione schema:  Pydantic v2
LLM provider:        Anthropic Claude API (sonnet-4-6)
Code editor:         Cursor (con AI integrata)
VCS:                 Git + GitHub
Deploy MVP:          localhost (uvicorn)

Dependencies (8 required, all installed):
  - fastapi
  - uvicorn
  - pymupdf
  - anthropic
  - pydantic
  - jinja2
  - python-multipart
  - python-dotenv
```

### Parametri Claude (Locked)

```python
model="claude-sonnet-4-6"
temperature=0              # Determinism for technical task
max_tokens=2048           # Sufficient for 7 categories + explanations
system_prompt=[full text] # 600 token from Specifica v2
top_p=1.0                 # Default
```

### Prompt System Structure (Locked — from Specifica v2)

```
RUOLO: Specialized contract analyst for non-lawyers

TASK: Extract 7 risk categories from contract text

OUTPUT FORMAT: Valid JSON only, exact schema matching

CONSTRAINTS: If category absent → "present": false; 
            raw_excerpt must be verbatim quote; risk_level ∈ {low, medium, high}

DO NOT: Invent clauses; use legal jargon; advise sign/don't sign; 
        output text outside JSON
```

### Few-shot Examples (Locked — 14 total, 2x7 categories)

Ogni categoria ha 2 esempi:
1. Clausola PRESENTE (high risk) — example from real contract language
2. Clausola ASSENTE — example of what absence means

Tutti gli esempi sono nel file Specifica v2 §6.

---

## Blocchi / Domande in Sospeso

| # | Domanda | Componente | Impacto | Stato |
|---|---|---|---|---|
| Q1 | OCR per PDF scansionati (Tesseract vs Vision API vs reject)? | INC-001 (PyMuPDF) | High | Decide during testing |
| Q2 | Rate limit strategy (queue vs batch vs sequential)? | INC-002 (Claude API) | Medium | Decide during testing |
| Q3 | JSON encoding (ensure_ascii=False sufficient)? | INC-003 (UTF-8) | Medium | Decide during testing |
| Q4 | Multi-model routing: implement in MVP or post-v1? | Architecture | Low | Design decision: v2 only |
| Q5 | Database storage: MVP stateless or add persistence? | Scope | Medium | Design decision: stateless MVP |

---

## File da Sincronizzare (Git)

Tutti i file .md in prog1/ devono essere committati dopo ogni sessione:

```bash
git add -A && git commit -m "Update: [tipo] [descrizione]" && git push
```

**File tracked:**
- ✅ Specifica Tecnica v1 - SpecterAI.md
- ✅ Specifica Tecnica v2 - SpecterAI.md
- ✅ Brainstorming - Validazione Idea.md
- ✅ Contract Analyzer - Validazione Idea.md
- ✅ Verifica PC - ARROW.md
- ✅ PROMPT_LOG.md
- ✅ INCIDENTS.md
- ✅ SESSION_HANDOFF.md

**Not tracked:**
- .env (never commit, contains API key)
- credentials.json, token.json (OAuth files)
- __pycache__/, venv/ (Python artifacts)

---

## Metriche di Successo MVP

| Metrica | Target | Status |
|---|---|---|
| Latency (single contract) | <15 sec | TBD (pending Fase 4-5) |
| Accuracy (red flags detected) | >80% | TBD (pending Fase 5) |
| Error rate (malformed PDFs) | <5% | TBD (pending Fase 5) |
| JSON output validation | 100% valid schema | TBD (pending Fase 4) |
| Category coverage | 7/7 always present | TBD (pending Fase 5) |

---

## Git History (Commits on this Branch)

```
c635c2b Update: Specifica v2 - Aggiunta sezione Competitive Positioning (Mikeoss analysis)
3b85e85 Update: Verifica PC - ARROW - Dipendenze installate, API key confermata, Cursor verificato
ea70998 Add: Verifica PC - ARROW environment check
ed84304 Add: Specifica Tecnica v2 SpecterAI - prompt completo, few-shot, Green AI, multi-model routing
c6faff1 Add: Specifica Tecnica v1 SpecterAI - Contract Analyzer for Non-Lawyers
```

Next commit: "Add: Documentazione SpecterAI (PROMPT_LOG, INCIDENTS, SESSION_HANDOFF)"

---

## Prossima Sessione: Checklist di Avvio

Quando inizi la prossima sessione (Lezione 3 — Building):

1. **Leggi questo file** — capisci lo stato attuale e cosa resta da fare
2. **Apri Cursor** — setup skeleton project (Fase 1)
3. **Leggi Specifica v2** — familiarizzati con prompt system, 7 categorie, stack
4. **Leggi PROMPT_LOG.md** — capisci evolution del prompt
5. **Leggi INCIDENTS.md** — capisci potential blockers (INC-001/2/3)
6. **Implemen ta Fase 1-2** — PDF input validation layer
7. **Log progress** — aggiorna PROMPT_LOG, INCIDENTS, SESSION_HANDOFF after each phase
8. **Commit** — `git add -A && git commit && git push` ogni sessione

---

## Note per Supervisione Umana

Ricorda: **Agents read, humans write.** Questo file deve essere aggiornato manualmente dopo ogni sessione. 

Non è template vuoto — è **documento vivo** che traccia il progetto dal giorno 1 alla presentazione finale.

Ogni sessione di building produce:
- **Codice:** file Python in src/
- **Testing:** unit tests
- **Documentation:** aggiornamenti a PROMPT_LOG, INCIDENTS, SESSION_HANDOFF
- **Git:** commits

Supervisione critica durante Fase 4-5 (Claude integration): monitora INC-001/2/3, aggiorna INCIDENTS quando si manifestano.

---

---

## Fix Applicati — Sessione fix_specter-AI (2026-05-21)

Fonte: `CODE_REVIEW_SPECTERAI_20260521.md` (review Opus, 333 righe).
Tutti i fix sono stati implementati, testati e pushati in questa sessione.

### Commit prodotti

| Commit | Descrizione | File |
|---|---|---|
| `3b81fd1` | Batch 1 quick-win: timeout, async, env var, model_copy, disclaimer | llm_client.py, main.py, schemas.py, report.html |
| `b61baef` | Luhn PIVA + mod-97 IBAN validation (spec compliance) | privacy_filter.py |
| `ec862f8` | Regex PII context-aware (CF, phone, importi) | privacy_filter.py, regex_layer.py |
| `1c86e81` | LLM client JSON parsing strutturato + backoff esponenziale | llm_client.py |
| `dc4275a` | Hardening UX: truncation warning, upload size pre-check, spaCy fallback | pdf_processor.py, main.py, privacy_filter.py, report.html |
| `999071f` | spaCy ordering bug fix — Option A (spaCy before regex) + test unitari | privacy_filter.py, tests/test_privacy_filter.py |

### Dettaglio fix per file

**`llm_client.py`**
- `timeout=300` su `subprocess.run` (anti-DoS)
- `_MODEL` da env var `CLAUDE_MODEL` con fallback `claude-sonnet-4-6`
- `_restore_excerpts` usa `model_copy(update=...)` invece di mutazione in-place
- `_parse_response()`: `json.JSONDecoder().raw_decode()` dal primo `{` — ignora testo extra pre/post JSON
- Loop 3 tentativi con `time.sleep(2**(attempt-1))` [1s, 2s]:
  - `TimeoutExpired`/`RuntimeError` → retry stesso messaggio
  - `JSONDecodeError`/`ValidationError` → 1 solo retry con prompt restrittivo
  - `ValueError` altri → propaga immediatamente
- `analyze()` → `await asyncio.to_thread(analyze, ...)` in main.py (event loop non bloccato)

**`privacy_filter.py`**
- `validate_piva_luhn()` — Luhn IT ufficiale (posizioni dispari/pari, mod-10)
- `validate_iban_it()` — rearrange + lettere→numeri + mod-97
- `redact()`: PIVA e IBAN redatti solo se checksum valido
- CF regex context-aware: matcha solo con keyword `codice fiscale/CF/C.F.` + case-sensitive uppercase sul CF
- Phone: prefisso `+39`/`0039` obbligatorio (elimina falsi positivi su protocolli e numeri civici)
- **spaCy ordering bug fix**: spaCy gira sul testo ORIGINALE (Passo 1), regex sul post-NER (Passo 2). Eliminazione della corruzione `[PER[CF_1]_2]` — impossibile per costruzione
- spaCy wrapped in `try/except (OSError, ImportError)` → `_SPACY_AVAILABLE = False` se non disponibile, solo regex attiva

**`schemas.py`**
- Campo `disclaimer` rimosso dal modello Pydantic (era hardcoded in un campo che l'LLM avrebbe dovuto ripetere)

**`pdf_processor.py`**
- `extract_text()` restituisce `(text, truncated: bool)` invece di solo `str`

**`main.py`**
- Pre-check `Content-Length` header prima di leggere tutto il body
- Lettura chunked con limite (413 invece di 422 se supera MAX_SIZE)
- `truncation_warning` passato al template se PDF troncato a 40K chars

**`templates/report.html`**
- Banner giallo se PDF troncato ("Contratto lungo: analizzate le prime 40.000 parole.")
- Disclaimer hardcoded nel template (rimosso da Pydantic)

**`regex_layer.py`**
- Commento `loose validation, downstream-safe` sui pattern importi (alimentano solo metadati LLM, non redazione PII)

**`tests/test_privacy_filter.py`** *(nuovo)*
- 4 test unitari per il bug spaCy ordering:
  1. `test_no_nested_placeholders` — no `[PER[CF_1]_2]`
  2. `test_spacy_receives_original_text` — verifica che `_nlp` riceva testo senza placeholder
  3. `test_cf_and_piva_redacted_regex_only` — regex-only senza spaCy
  4. `test_restore_roundtrip` — redact + restore = originale

### Stato codice post-fix

| Bug critico (review) | Stato |
|---|---|
| No subprocess timeout (DoS) | ✅ FIXED — timeout=300 |
| Event loop bloccato | ✅ FIXED — asyncio.to_thread |
| PIVA false positive (no Luhn) | ✅ FIXED — validate_piva_luhn + validate_iban_it |
| CF pattern debole | ✅ FIXED — context-aware + uppercase-only |
| spaCy offset bug (placeholder corrotti) | ✅ FIXED — spaCy first, regex second |
| JSON parsing fragile (regex greedy) | ✅ FIXED — raw_decode |
| Retry senza backoff | ✅ FIXED — backoff 1s/2s |
| MAX_CHARS silenzioso | ✅ FIXED — truncated flag + warning UI |
| Upload legge tutto prima del check | ✅ FIXED — chunked read con limite |
| spaCy crasha senza graceful fallback | ✅ FIXED — _SPACY_AVAILABLE flag |
| Modello hardcoded | ✅ FIXED — CLAUDE_MODEL env var |
| model_copy vs mutazione in-place | ✅ FIXED |
| disclaimer in campo Pydantic | ✅ FIXED — spostato in template |

---

**Fine del handoff — pronto per Lezione 3 MVP building.**
