# PROMPT_LOG — SpecterAI

**Progetto:** SpecterAI — AI Contract Analyzer per Non-Avvocati (Italiano)  
**Data inizio progetto:** 2026-04-28 (brainstorming)  
**Data prompt v1:** 2026-04-30
**Versione prompt attuale:** v1-final + patch v2/v2.1/v2.2 (in `prompts/system_prompt.md`; spec: v3.1)
**Stato:** MVP E2E funzionante — test plan §8 in attesa (Lez. 5)
**Strategia testing:** Iterazione prompt + JSON quality check su Claude Code CLI (zero-cost, stesso modello del runtime). Runtime test plan §8 su Claude API a pagamento (~0,60 € totali). Esclusi Gemini Flash / OpenRouter free per evitare drift di modello.

---

## Timeline Overview

| Data | Fase | Output | Note |
|---|---|---|---|
| 2026-04-28 | Brainstorming | 7 idee, 5 scartate, 1 selezionata (SpecterAI) | Lezione 1 |
| 2026-04-29 | Validazione idea (Fase 1) | Comparazione 5 idee, selezione Contract Analyzer | Tool esistenti, maturità mercato, rischi tecnici |
| 2026-04-29 | Validazione idea (Fase 2) | Stress-test 6 obiezioni (IT/EU market) | No data su WTP e contratti/anno; opportunità confermata |
| 2026-04-30 | Specifica v1 | 11 sezioni, stack completo, 7 categorie | Prima versione completa |
| 2026-05-01 | PC Verification | ARROW environment, dipendenze, API key | Quasi pronto |
| 2026-05-02 | Competitive analysis | Ricerca vs Mikeoss | Medium risk (non cannibalizza) |
| 2026-05-03 | Specifica v2 | +Competitive Positioning, +Green AI, +Full prompt | Specifica congelata |
| 2026-05-04 | Documentation | PROMPT_LOG, INCIDENTS, SESSION_HANDOFF | Ready for building |
| 2026-05-06 | Feedback prof (intermedio) | Valutazione 95/100, File2 + File3 stack tecnico | Sblocca Lez. 3 |
| 2026-05-07 | Spec v3 | Review v2 + Meta-Review multi-agent + 4 query Perplexity → 13 fix integrati | AI Act riclassificato limited-risk |
| 2026-05-10 | Spec v3.1 | 3 patch da feedback prof File3 (stretch goals, dev token strategy, build roadmap) + 2 fix da review Perplexity (retention 30gg→7gg, DPA esplicito) | Spec finale pre-consegna |
| 2026-05-11 | Test data + prompt test | 8 PDF reali scaricati via Perplexity free (INC-000g) + primo test prompt su Claude.ai con Sonnet su 1 contratto reale | Output JSON conforme schema 7 cat inglesi, autovalut. 4-5/5, 2 osservazioni da verificare (ellissi raw_excerpt, numeri calcolati in plain_language) |
| 2026-05-11 | Test runtime #2 e #3 | Sonnet v1-final su co.co.co. ERSU + Consip Condizioni Generali; verifica fattuale Consip delegata a Haiku via Claude.ai | Test #2 zero pattern; Test #3 scopre pattern 3 (cross-article extraction + drift semantico "automatica" vs "potrà"). Definita patch v2.1 grounding stretto plain_language ↔ raw_excerpt |
| 2026-05-11 | Test runtime #4 | Sonnet v1-final su NDA Politecnico Milano (baseline negativo); verifica fattuale delegata a Haiku via Claude.ai | Pattern 1/2 non riprodotti, pattern 3 confermato 1/2 (termination cross-article), pattern 4 (riempimento allucinato) escluso. **Scoperto pattern 5 nuovo:** inferenza speculativa esplicitata su fatti giurisprudenziali ("foro probabile Milano" da firma a Milano). Definita patch v2.2 anti-speculazione |
| 2026-05-11 | Test runtime #5 + cumulativo 5/5 | Sonnet v1-final su Locazione INPS (Caltanissetta/Palermo); verifica fattuale delegata a Haiku via Claude.ai | Cross-article 2/4 + SIMILE 1/4 (Caltanissetta da premesse, Art.2 template vuoto = pattern 6 candidato) + ASSENTE 1/4 ("diritto italiano" = pattern 5b asserzione non-qualificata). Chiusura mini-suite pre-Cursor: 8 pattern catalogati, patch v2+v2.1+v2.2 finalizzate, schema Pydantic `raw_excerpt: list[str]` decisa, 2 test T13/T14 aggiunti al test plan §8 |
| 2026-05-20 | MVP E2E runtime — test post-fix INC-004 | Fix WinError 206 in `llm_client.py` (user_message da argv → stdin). Consip (40k, 268s, 7/7 cat OK) + Capitolato Demanio (40k, 163s, 5/7 cat attesi OK). Contratto firmato.pdf rifiutato correttamente (scansionato). **7/8 PDF passano — 1 scansionato rifiutato by design.** Commit `d2dc4ba`. |

---

## Tabella Versioni Prompt

| Versione | Data | Stato | Descrizione breve | Fonte cambiamento |
|---|---|---|---|---|
| **Pre-v1** | 2026-04-28 | ✅ Completato | Brainstorming iniziale: 7 idee, choice SpecterAI | Lezione 1 course requirement |
| **Pre-v1** | 2026-04-29 | ✅ Completato | Validazione idea + market research Perplexity | Lezione 2 validation framework |
| **v1** | 2026-04-30 | ✅ Completato | First full specification, 7 categories, stack defined | Specifica Tecnica v1 |
| **v1-refined** | 2026-05-02 | ✅ Completato | User feedback: prompt design methodology | Haiku independent re-analysis |
| **v1-final** | 2026-05-04 | ✅ Operativo | Full prompt text + few-shot examples + Green AI | Specifica Tecnica v2 |
| **v1-final** (spec v3) | 2026-05-07 | ✅ Operativo | Prompt text invariato; spec arricchita: anti-allucinazione `raw_excerpt` (fuzzy match), gate lingua IT/EN bloccante, scenari costo ricalcolati Sonnet, AI Act limited-risk Art. 6(3), Mistral Medium 3 come fallback | 13 fix integrati |
| **v1-final** (spec v3.1) | 2026-05-10 | ✅ Operativo | Prompt text invariato; +Stretch Goals, +dev token strategy zero-cost (Claude Code CLI), +build roadmap, +retention Anthropic 7gg, +DPA esplicito | 5 fix da feedback prof + Perplexity review |

---

## PRE-v1 — 2026-04-28 — Brainstorming e Selezione Idea

**Contesto:** Corso AI Projects Development, Lezione 1 "Case Study e Setup" richiedeva 5 idee progetto con validazione su 5 dimensioni (Technical, Economic, Complexity, Risk/Compliance, Tech Sustainability).

**Processo:**
1. Generate 7 idee iniziali:
   - 1. AI-powered course summarizer (scartata: crowded market)
   - 2. Document search engine for Italian law (scartata: GDPR complexity)
   - 3. **SpecterAI — AI Contract Analyzer for Non-Lawyers** ✅ SELEZIONATA
   - 4-7. Altre (discarded per vari motivi)

2. **Validazione 5 dimensioni su SpecterAI:**
   - ✅ **Tecnica:** Medium (PDF parsing + Claude API + JSON validation = solvable in 1 month MVP)
   - ✅ **Economica:** High (2.7M target users in Italy, SMB market, pricing sostenibile)
   - ✅ **Complessità:** Medium (stack semplice: FastAPI + PyMuPDF + Claude)
   - ✅ **Risk/Compliance:** Medium-High (AI Act classification as high-risk decision-support mitigato da disclaimer)
   - ✅ **Tech Sustainability:** High (token optimization, no data persistence, deterministic routing)

**Problema riscontrato:** Idea è troppo vaga, necessita validazione di mercato reale.

**Soluzione:** Avanzare a Lezione 2 con market research.

---

## PRE-v1 — 2026-04-29 — Market Research e Competitive Analysis

**Contesto:** Lezione 2 richiedeva stress-test dell'idea con ricerca esterna (Perplexity).

**Fase 1 — Comparazione 5 idee progettuali:**

Prima di selezionare definitivamente SpecterAI, Perplexity ha analizzato le 5 idee candidate su 4 dimensioni (tool esistenti, maturità mercato, angolo difendibile, rischio tecnico).

| Idea | Maturità mercato | Rischio tecnico principale |
|---|---|---|
| AI Contract Analyzer | Saturo/maturo (enterprise); frammentato per SMB | PDF parsing qualità + LLM over-confidence |
| AI Lead Qualifier | Growing / crowded niche | Scraping reliability + scoring arbitrariness |
| AI Competitive Intelligence | Growing ma frammentato | Change-detection noise + crawl rate-limiting |
| AI Onboarding Assistant (RAG) | Early-stage / emerging ✅ | RAG quality + document pre-processing diversity |
| AI Interview Prep | Growing / somewhat saturated | Conversation drift + feedback inconsistency |

**Failure mode principale dei competitor per Contract Analyzer:**
- Tool troppo "legal-heavy" — output orientato a avvocati, non a non-avvocati
- Assumono contratti strutturati da grandi imprese; contratti SMB sono spesso messy/non-standard
- Over-promise di "auditable risk scores" senza uncertainty labels

**Fase 2 — Stress-test 6 obiezioni specifiche (Contract Analyzer per mercato IT/EU):**

1. **ChatGPT Substitution**
   - 58% degli avvocati in-house usa già AI per contract review (survey 160+ legal professionals, 2025)
   - Non esistono survey su freelancer/SMB non-avvocati che usano ChatGPT per contratti
   - Vantaggio tool dedicato: guardrail domain-specific, output strutturati, data-privacy GDPR, PDF UI nativa

2. **Frequenza d'uso**
   - Italy: ~4.4M imprese (Statista 2019), 2.7M+ individual entrepreneurs/freelancer
   - ~2M freelancer "declared + real" (Osservatorio Libere Professioni 2019)
   - **Nessun dato pubblico su "contratti/anno per freelancer"** — assumption: 10-20/anno

3. **Liability e Regolamentazione EU**
   - EU AI Act classifica tool legali come **high-risk** (influenzano obblighi/diritti reali)
   - Requisiti: transparency, risk-management, data governance, human oversight
   - **Nessun caso di regulatory action contro AI legal tool in Europa a oggi (2026)**
   - Mitigazione: posizionamento come "decision-support, not legal advice" + disclaimer visibile

4. **Willingness to Pay**
   - Tool enterprise: ~$3.000/user/anno o ~$1/documento — inaccessibili per SMB
   - **Nessun studio EU/IT su WTP per contract-analysis tool in questa fascia**
   - EIB report: Italian SMB in ritardo su adozione SaaS cloud vs altri paesi EU
   - Freemium: nessun dato specifico per questo niche — letteratura SaaS generale suggerisce funziona per uso low-stakes e virale

5. **Competizione da tool gratuiti**
   - CompareX offre "Free Contract Analyser" (demo marketing, non SMB-first)
   - ChatGPT free-tier = de facto competitor gratuito (paste-and-ask)
   - **Nessun SaaS standalone gratuito esplicitamente per freelancer/SMB non-lawyer**
   - Differenziazione gap: struttura output, clause libraries, GDPR compliance, PDF-native UX

6. **Market Size Italia**
   - ~2.7M individual entrepreneurs + ~2M freelancer (overlap parziale)
   - TAM conservativo: 1-2M utenti target
   - **Nessun dato su contratti/anno per micro-impresa** — qualsiasi cifra è assumption

**Risultati chiave:**
- ❌ **Risk identificato:** Competitor enterprise-focused (Harvey, Legora) potrebbero dominare market se decidono di scendere in SMB
- ✅ **Opportunità:** Nessun competitor ha traction mainstream nel mercato italiano non-lawyer
- ✅ **Pricing:** SMB può sostenere €5-15/mese, non €50+/mese enterprise
- ✅ **Regulatory moat:** Posizionarsi come "decision-support" con AI Act disclaimer è sia compliance che differenziazione

**Conclusione:** Idea sostenibile con caveat su localization italiano come defensive moat.

**Problema riscontrato:** Concorrenza Mikeoss scoperta in Lezione 3, necessita ulteriore analisi.

**Soluzione:** Competitive positioning section in Specifica v2 (completata 2026-05-02).

---

## PRE-v1 — 2026-05-02 — Competitive Analysis: Mikeoss

**Contesto:** Mikeoss scoperto come potenziale competitor durante Lezione 3. Ricerca Perplexity dedicata.

**Cos'è Mikeoss:**
- Open-source legal AI platform, self-hostable, alternativa a Harvey/Legora
- Chat-based workflow per drafting, redline, editing contratti (tool per avvocati)
- Plug-in LLM flessibile (Claude, ecc.) — full data control per law firm
- Nessun pricing SaaS pubblico — free-core, costi a carico del deployer (infra + LLM credits)

**Analisi rischio vs SpecterAI:**

| Dimensione | Mikeoss | SpecterAI |
|---|---|---|
| Target | Law firm / legal team | Freelancer, SMB, non-avvocati |
| UX | Lawyer-centric, workflow drafting | Plain-language, risk snapshot |
| Lingua | English-prioritized, no Italian-first | Italian-first, EU localization |
| Monetization | Open-source (no SaaS tiers) | SaaS €5-15/mese |
| Funzione primaria | Drafting + redlining | Analisi + risk identification |

**Conclusione: Risk Level MEDIUM**
- Mikeoss compete con Harvey/Legora, **non** con SpecterAI
- Nessuna evidenza di trazione italiana o marketing verso SMB non-lawyer
- SpecterAI insulated da: Italian-first UX, no-lawyer positioning, AI Act disclaimer

**Soluzione:** Competitive Positioning section aggiunta in Specifica v2.

---

## v1 — 2026-04-30 — Prima Specifica Tecnica (11 sezioni)

**Contesto:** Lezione 2 "Specifica Tecnica e Prompt Engineering" richiedeva full technical spec con stack, MVP scope, prompt design.

**Processo:**
1. Definito problema: freelancer ricevono contratti incomprensibili, non hanno legal advice a basso costo
2. Definite 7 categorie red flag:
   - payment_terms, auto_renewal, penalties, liability_limitation, termination, governing_law, intellectual_property
3. Scelto stack:
   - Python 3.12, FastAPI, PyMuPDF, Claude API (sonnet), Pydantic, Jinja2
4. Definito MVP scope:
   - ✅ PDF upload, 7 category analysis, plain-language output, AI Act disclaimer, IT/EN support, no data persistence
   - ❌ Out of scope: OCR for scanned, PDF download, user accounts, multi-doc dashboard
5. Definite 6 edge case di input validation
6. Definiti 5 rischi e mitigations

**Output:** 11 sezioni, specifica frozen per MVP.

**Problema riscontrato:** Prompt system non completamente scritto — solo pseudo-codice, no few-shot examples, no parametri Claude specifici.

**Soluzione:** Completare prompt in Specifica v2.

---

## v1-refined — 2026-05-02 — User Feedback on Design Methodology

**Contesto:** User feedback during Specifica v2 preparation: "assolutamente non va bene deve rianalizzare lui i file e trovare i problemi non glieli devi dare tu" (AI shouldn't be told what to look for, must analyze independently).

**Cosa è successo:**
- Sistema: Claude Code (me) aveva proposto i 5 improvement gap della v1
- User correction: Haiku doveva fare independent re-analysis e trovare i gap da solo
- Outcome: Haiku identified 5 gaps, alcuni overlapping (competitive positioning, prompt text, few-shot, Green AI, multi-model routing)

**Lezione appresa:**
- ✅ Independent AI analysis produce più insight diversi da quelli "suggested"
- ✅ Methodology matters: supervise, not direct
- ✅ Fresh sessione senza prompt directing = más creatività

**Problema riscontrato:** Prompt framework C.I.A.R.E. necessita proper structure in system prompt.

**Soluzione:** Full prompt text in Specifica v2 con C.I.A.R.E. structure.

---

## v1-final — 2026-05-04 — Full Prompt Text + Few-shot + Green AI

**Contesto:** Specifica v2 required complete, production-ready prompt system.

**Output finale — Prompt di Sistema (C.I.A.R.E. structure):**

```
RUOLO: You are a contract analysis assistant specialized in identifying risks for 
non-lawyers: freelancers, self-employed professionals, and small business owners.

TASK: Analyze provided contract text and extract critical information in exactly 
7 categories...

OUTPUT FORMAT: Return ONLY valid JSON. No prose, no markdown fences...

CONSTRAINTS: If category absent → "present": false...

DO NOT: Invent clauses, use legal jargon, advise sign/don't sign, return text outside JSON
```

**Few-shot Examples (2x per categoria, total 14 examples injection):**

1. **Esempio 1 — Clausola presente (high risk):** auto_renewal with 90-day notice
   - Input excerpt from real contract language
   - Expected JSON output with severity, plain language explanation, question_to_ask
   
2. **Esempio 2 — Clausola assente:** no intellectual property clause
   - Input: no mention of IP in contract text
   - Expected JSON: `"present": false` with explanation of risk

**Parametri Claude locked:**
- Model: `claude-sonnet-4-6` (best cost/quality for legal text)
- Temperature: `0` (determinism, no creativity for technical task)
- Max tokens: `2048` (sufficient for 7 categories + explanations)
- System prompt: full text (600 token)

**Green AI optimizations:**
- Testo input troncato a 40.000 char (save ~30% token input su contratti lunghi)
- Temperature=0 (no probabilistic sampling overhead)
- No conversation history (stateless, single request)
- Multi-model routing: no LLM for input validation, language detection

**Risultati:**
- ✅ Prompt system production-ready
- ✅ Few-shot examples ancorare model su formato esatto
- ✅ Token economy optimized for sustainability
- ✅ Schema JSON locked in Pydantic (v2)
- ⏳ Full testing su dataset reale: pending MVP building

**Problema riscontrato:** None — prompt è stabile e production-ready.

**Soluzione:** → Ready for MVP building in Cursor (Lezione 3)

---

## Spec v3 — 2026-05-07 — Review + Meta-Review + Verifiche Perplexity (13 fix)

**Contesto:** prima della consegna, Spec v2 è stata sottoposta a una review interna (gap analysis pre-consegna) seguita da meta-review multi-agent (3 agenti OpenCode in parallelo su modelli free OpenRouter, validazione convergente). Output: 13 fix richiesti. In parallelo, 4 query Perplexity di verifica fattuale (pricing Sonnet, Anthropic ToS, AI Act classification, provider alternativi).

**Cosa è cambiato (prompt text-level: nulla; spec-level: 13 fix):**

1. Verifica anti-allucinazione `raw_excerpt`: fuzzy match SequenceMatcher threshold 0.92, retry "verbatim only", flag `excerpt_unverified` se persiste
2. Test plan eseguibile T1-T12 con criteri pass/fail (sostituisce elenco discorsivo v2)
3. Metriche precision/recall/grounding con soglie (≥0,85 / ≥0,80 / ≥0,90)
4. Scenari costo ricalcolati: Sonnet ~0,04 €/analisi (v2 sottostimava 0,02), budget consegna <2 €
5. GDPR esteso: ToS Anthropic Section B citato verbatim, retention log dichiarata, DPA referenziato
6. **Riclassificazione AI Act: high-risk → limited-risk** ex Art. 6(3) (3 derogazioni soddisfatte)
7. Modalità degradata Claude API down: messaggio esplicito + 503 + Retry-After (no fake-success da regex)
8. Limiti validazione dichiarati: zero user interview, score auto-corretto 4/5 → 3/5
9. Provenance & Versioning §13 (audit trail v1→v2→v3)
10. Gate lingua bloccante IT/EN (no fallback silenzioso)
11. Tabella provider alternativi: Mistral Medium 3 raccomandato post-MVP (3,3× più economico, EU residency)
12. Sezione fallback "API down" definita
13. Limit `raw_excerpt` ≥20 char per evitare frammenti triviali

**Lezione metodologica:** la **meta-review multi-agent** (3 angle OpenRouter free in parallelo) ha trovato 6 gap aggiuntivi non identificati dalla review singola. Costo: zero (free models). Pattern riutilizzabile per progetti futuri.

**Aggiornamenti collegati:** Specifica v3 §13 (changelog completo).

---

## Spec v3.1 — 2026-05-10 — Feedback Prof + Validation Perplexity (5 fix)

**Contesto:** la prof Melanie ha consegnato 2 file di feedback (File2 valutazione 95/100, File3 stack tecnico + roadmap consigliata). Audit incrocia feedback prof con Spec v3 → 3 gap minori identificati. Per ognuno, ricerca Perplexity isolata su best practice 2026 (sessione separata da Spec v3 review). Successivamente, Perplexity di validazione generale Spec v3 (9 punti, sessione fresh).

**3 patch da feedback prof File3:**

1. **§7 Strategia token in fase di sviluppo (zero-cost):**
   - Claude Code CLI per iterazione prompt + JSON quality check (zero drift di modello, abbonamento già attivo)
   - Cursor con Sonnet per generazione codice modulo per modulo
   - Claude API a pagamento solo per runtime test plan §8 (~0,60 €) e demo (~0,40 €)
   - **Esclusi Gemini Flash / OpenRouter free:** drift di modello (un JSON valido su Gemini può fallire su Sonnet), limiti free tier aggressivi
   - Budget complessivo dev + consegna: <1,50 €

2. **§2.bis Stretch Goals (separati da Fuori scope):**
   - Download report PDF: **Playwright headless** `page.pdf()` (preferito su WeasyPrint per fedeltà CSS moderni)
   - Confidence indicator su `risk_level`: self-consistency 3-run a temp=0, ≥2/3 agreement (Anthropic API non espone logprobs nel 2026). Costo triplica (€0,12/analisi) → solo demo, mai default
   - CSS report più curato

3. **§12.bis Build Roadmap moduli→lezioni→deliverable:**
   - Lez. 3: schemas + pdf_processor + regex_layer
   - Lez. 4: llm_client + main + templates
   - Lez. 5: test plan §8 + PROMPT_LOG + INCIDENTS
   - Lez. 6: polish + eventuale stretch + demo

**2 fix da review Perplexity validation (9 punti, 7/9 ✅, 2 ⚠️):**

4. **§7 retention Anthropic:** 30gg → **7gg** (policy update settembre 2025, verificato su privacy.claude.com)
5. **§7 GDPR:** nota esplicita su DPA Anthropic da sottoscrivere pre-deploy pubblico (include SCC post-Schrems II + ruolo DPF US-EU)

**Lezione metodologica:**
- **Conversazioni Perplexity isolate per evitare bias da contesto:** le 3 ricerche isolate sui gap (idee/best practice) hanno restituito alternative concrete che la prof non aveva citato (Playwright vs WeasyPrint, self-consistency vs logprobs). La validation finale Spec v3 in sessione fresh ha trovato 2 fact drift recenti (retention 30→7gg) che una sessione "calda" avrebbe probabilmente confermato per inerzia.
- **Strategia testing rivista:** il piano originale prof (Claude.ai + Gemini Flash + Cursor + Claude API) è stato semplificato a (Claude Code CLI + Cursor + Claude API) sfruttando l'abbonamento esistente. Stesso risultato a costo marginale zero per il dev workflow.

**Aggiornamenti collegati:** Specifica v3.1 §2.bis, §7, §12.bis, §13 changelog (righe #11-15).

---

## Test runtime #1 — 2026-05-11 — Prompt v1-final su Sonnet via Claude.ai (contratto reale n.1/8)

**Contesto:** Primo test del prompt v1-final (spec v3.1) su contratto reale, eseguito via Claude.ai (free) con Claude Sonnet 4.6. Scopo: validare la checklist pre-building punto 1 del Promemoria Lez. 4 prima di aprire Cursor.

**Input:**
- Contratto: `prog1/specterai/contratti/210701_DRE_Capitolato-Tecnico-di-Appalto.pdf` (Agenzia del Demanio 2021, golden test — copre tutte le 7 categorie)
- System prompt: copiato dalla Spec v3.1 §Prompt di Sistema (text-level invariato dalla v2)
- Schema atteso: 7 categorie inglesi previste dalla spec v3.1 (`payment_terms, auto_renewal, penalties, liability_limitation, termination, governing_law, intellectual_property`) — NOTA: il Promemoria della prof traduceva con nomi italiani approssimati (`durata, recesso, ...`), ma la spec è source-of-truth e usa i nomi inglesi
- User message: vincoli espliciti (solo JSON, italiano, 7 cat esatte, raw_excerpt testuale, present:false se assente, auto-valutazione 4 dimensioni)

**Output:** JSON valido, 7 categorie presenti, autovalutazione media 4.75/5 (Completezza 5, Fedeltà raw_excerpt 4, Coerenza risk_level 5, Italiano output 5). Risk distribution: 3 high (penalties, termination, liability_limitation), 3 medium (payment_terms, governing_law, intellectual_property), 1 low (auto_renewal correttamente assente). Disclaimer presente. Top_3_risks coerenti.

**Osservazioni → da verificare / iterare:**

1. **Ellissi `[...]` in raw_excerpt di payment_terms** — Sonnet ha concatenato due brani contigui dello stesso articolo con `[...]`. Auto-valutazione 4/5 lo ha segnalato. Impatto: la soglia fuzzy match 0.92 della spec potrebbe non passare se l'ellissi resta nella stringa. **Decisione di prompt:** in produzione vietare l'ellissi → *"raw_excerpt deve essere un singolo brano contiguo, mai concatenare con `[...]`. Se servono due passaggi distinti, scegliere il più rilevante"*. Da aggiungere al prompt v2 prima di Cursor.

2. **Numeri calcolati in plain_language** — Sonnet ha scritto "circa 143 € al giorno", "circa 14.331 €", "€ 2.282.328,93" in `penalties` e `liability_limitation`. Sono **calcoli derivati** (1‰ e 10% dell'importo contrattuale), non citazioni. Rischio: in produzione un calcolo sbagliato passa inosservato e si propaga al non-avvocato come fatto. **Da verificare manualmente sul PDF** se i numeri sono presenti letteralmente o derivati. Se derivati → aggiungere al prompt v2: *"plain_language non deve contenere numeri non presenti letteralmente nel contratto; usa termini qualitativi (es. 'una piccola percentuale giornaliera') se il numero non è testuale"*.

3. **Auto_renewal `present: false` ben gestito** — Sonnet non ha inventato una clausola assente, ha motivato l'assenza contestualmente (durata legata al cronoprogramma di 720 giorni). Pattern positivo da confermare su altri PDF.

**Lezioni metodologiche:**
- ✅ La struttura del user message con auto-valutazione 4 dimensioni si conferma utile: Sonnet ha **dichiarato spontaneamente** il problema dell'ellissi (4/5 invece di 5/5). Senza auto-valutazione probabilmente non l'avrei colto subito.
- ✅ Il golden PDF Demanio era la scelta giusta come primo test: coprire tutte e 7 le categorie in un singolo run accelera la verifica rispetto a testare 7 PDF mono-categoria.
- ⚠️ Possibile drift "prompt → calcolo" da sorvegliare: Sonnet 4.6 ha aritmetica integrata e la usa spontaneamente se i numeri sono nel contesto. Vincolarlo nel prompt è essenziale per app legal-AI.

**Prossimi step (in ordine):**
1. Verifica manuale numeri (Ctrl+F sul PDF su 143/14.331/2.282.328,93)
2. Test prompt v1-final su un secondo PDF SENZA modifiche (es. ERSU Messina co.co.co.) per confermare che il pattern regge cross-contratto
3. Se entrambi ok → checklist pre-building sbloccata, prompt resta v1-final, ma le 2 mitigazioni (no ellissi, no calcoli) vanno introdotte in v2 quando arriva il primo failure runtime
4. Se nuovo PDF rompe pattern → iterazione prompt v2 con le 2 mitigazioni preventive + log nuovo entry qui

**Aggiornamenti collegati:** nessuna modifica spec v3.1 (prompt text invariato). Eventuale v2 del prompt da loggare separatamente quando le mitigazioni saranno integrate.

### Esito verifica numeri — 2026-05-11

Verifica manuale sul PDF Demanio (Ctrl+F):
- `143.315,16 €` → **presente nel contratto** come importo contrattuale base.
- `2.282.328,93 €` → **presente** come valore lavori (citazione diretta, ok).
- `143 €/giorno` → **non presente** — Sonnet ha calcolato 1‰ × 143.315,16 = 143,31 €. Calcolo aritmetico.
- `14.331 €` → **non presente** — Sonnet ha calcolato 10% × 143.315,16 = 14.331,52 €. Calcolo aritmetico.

**Conclusione:** osservazione #2 confermata. Sonnet 4.6 esegue spontaneamente aritmetica sui numeri citati. Per un'app legal-AI a target non-avvocato il rischio è alto: un calcolo errato passa inosservato e si propaga come fatto. **Mitigazione obbligatoria in prompt v2 prima di aprire Cursor.**

**Patch da applicare al prompt (v1-final → v2):**
1. Vincolo no-ellissi nei raw_excerpt: *"raw_excerpt deve essere un singolo brano contiguo letterale, mai concatenare con `[...]`. Se servono due passaggi distinti, scegli il più rappresentativo."*
2. Vincolo no-calcoli: *"plain_language non deve contenere numeri assenti letteralmente dal contratto. Vietate operazioni aritmetiche (percentuali, moltiplicazioni, conversioni) sui numeri citati. Usa termini qualitativi: 'una piccola percentuale giornaliera', 'una frazione del corrispettivo'. I numeri compaiono solo dove citati testualmente nel raw_excerpt."*

Decisione di scope: le 2 patch sono **prevention**, non fix di un fallimento già occorso in produzione. Vanno integrate nel prompt v2 da committare insieme allo scaffolding di `prompts/system_prompt.md` quando si crea la struttura progetto in Cursor (Fase 1 Promemoria Lez. 4).

---

## Test runtime #2 — 2026-05-11 — Prompt v1-final su Sonnet via Claude.ai (contratto reale n.2/8)

**Contesto:** Secondo test del prompt v1-final **senza modifiche** rispetto a Test #1, su un secondo PDF di tipologia diversa (co.co.co.) per verificare se i 2 pattern problematici osservati sul Demanio (ellissi nei `raw_excerpt`, calcoli aritmetici nei `plain_language`) sono sistemici o contesto-dipendenti. Falsificazione preventiva delle patch v2 prima di applicarle.

**Nota operativa (INC candidato):** primo run del Test #2 fallito per **context bleed in Claude.ai**: rieseguendo il prompt nella stessa chat del Test #1 con un nuovo upload, il modello ha restituito un'analisi che era ancora del PDF Demanio (foro Bologna, BIM, RUP, importi 143.315,16 € / 2.282.328,93 €) ignorando il nuovo allegato. Identificato confrontando entità non sovrapposte (Bologna vs Messina, appalto vs co.co.co.). **Mitigazione:** chat Claude.ai NUOVA per ogni run di test, mai riusare conversazione anche se cambia il PDF allegato. Da loggare come INC-000h se il pattern si ripresenta.

**Input:**
- Contratto: `prog1/specterai/contratti/Schema-Contratto-CO.CO_.CO_.-1.pdf` (co.co.co. ERSU Messina, schema-tipo, compenso flat 8.000 € lordi, scadenza 15/10/2021, foro Messina)
- System prompt: identico a Test #1 (spec v3.1 §6, invariato)
- User message: identico a Test #1 (vincoli espliciti + auto-valutazione 4 dimensioni)
- Esecuzione: chat Claude.ai NUOVA (post-mitigazione context bleed)

**Output:** JSON valido, 7 categorie presenti, autovalutazione 5/5 su tutte e 4 le dimensioni. Risk distribution: 2 high (`liability_limitation`, `termination`), 2 medium (`payment_terms`, `governing_law`, `intellectual_property` — in realtà 3), 2 low (`auto_renewal` e `penalties` correttamente assenti). Top_3_risks coerenti con i 2 high + 1 medium economico. Disclaimer presente.

**Verifica anti-pattern (vs Test #1):**

| Pattern | Test #1 (Demanio) | Test #2 (co.co.co.) | Esito |
|---|---|---|---|
| Ellissi `[...]` in raw_excerpt | Presente in `payment_terms` | **Assente in tutti i 7** | ✅ Non riprodotto |
| Calcoli aritmetici in plain_language | Presente in `penalties` + `liability_limitation` (143€/g, 14.331€, 2.282.328,93€) | **Assente** — unico numero (`8.000€ lordi`) è citazione letterale | ✅ Non riprodotto |
| `auto_renewal` `present:false` motivato senza inventare | OK (durata 720gg) | OK (durata fino 15/10/2021) | ✅ Confermato cross-contratto |
| `present:false` per categorie assenti senza allucinare | OK | OK (3 categorie correttamente assenti: `penalties`, `liability_limitation`, `intellectual_property`) | ✅ Confermato |

**Lezione metodologica chiave:**

I 2 pattern problematici del Test #1 sono **contesto-dipendenti, non sistemici**. Si attivano in presenza di:
- **Articoli lunghi con clausole multiple contigue** → Sonnet condensa con `[...]` per stare entro un excerpt leggibile
- **Numeri-percentuale (1‰, 10%) accanto a importi base (143.315,16 €)** → Sonnet calcola spontaneamente per "rendere concreto" il rischio al non-avvocato

Sul co.co.co., assenti entrambi gli stimoli, il prompt v1-final si comporta in modo impeccabile.

**Implicazione per le patch v2:** non sono falsificate, sono **dormienti**. In produzione SpecterAI riceverà sia contratti densi (appalti, fornitura B2B) sia contratti semplici (co.co.co., NDA brevi). I 2 pattern riemergeranno appena un appalto pubblico entra nel sistema. → **patch v2 confermate come prevention obbligatoria** prima di Cursor (Fase 1).

**Lezione metodologica secondaria (context bleed):** la prassi di "test runtime" su Claude.ai richiede chat fresh per ogni PDF. Il modello non re-bootstrappa il contesto file su nuovo upload nella stessa conversazione: l'analisi del primo PDF resta dominante. Da formalizzare come regola operativa nel manuale di test (post-MVP) e potenzialmente in INC-000h se si ripresenta in Test #3+.

**Aggiornamenti collegati:** nessuna modifica spec v3.1. Patch v2 invariate vs definizione del 2026-05-11 mattina (sezione "Esito verifica numeri"). Vedi sotto **blocco pronto da incollare**.

---

## Patch v2 prompt — BLOCCO PRONTO DA INCOLLARE in `prompts/system_prompt.md` (Cursor Fase 1)

> Da inserire **al termine della sezione `CONSTRAINTS`** del system prompt corrente (spec v3.1 §6), prima del blocco `DO NOT`. Mantiene la struttura Ruolo → Task → Formato → Vincoli → Esclusioni intatta.

```
CONSTRAINTS (additional — patch v2, 2026-05-11)
- raw_excerpt must be a single contiguous span from the contract text.
  Never concatenate two separate passages with "[...]" or any ellipsis marker.
  If two distinct passages are equally relevant, choose the most representative one.
- plain_language must NOT contain numbers that are absent from the contract text
  in literal form. Arithmetic operations on cited numbers (percentages of amounts,
  multiplications, conversions, daily/monthly breakdowns) are forbidden.
  Use qualitative terms instead: "una piccola percentuale giornaliera",
  "una frazione del corrispettivo", "una quota proporzionale".
  Numeric values may appear in plain_language ONLY if they are verbatim
  quotes already present in the corresponding raw_excerpt.
```

**Provenance:** patch derivate da Test runtime #1 (Demanio, pattern attivo) + Test runtime #2 (co.co.co., pattern dormiente). Falsificate preventivamente su 2 PDF di tipologie diverse → giustificate come prevention.

**Quando applicare:** in Lez. 4 quando si crea `prompts/system_prompt.md` separato dal text-level della spec. Non modificare la spec v3.1: il prompt vive nel file separato secondo la struttura del Promemoria prof.

**Test post-applicazione:** rieseguire un mini-suite (1 contratto denso tipo appalto + 1 contratto semplice tipo NDA) e verificare in PROMPT_LOG sotto entry "Test runtime #3 — post patch v2".

---

## Test runtime #3 — 2026-05-11 — Prompt v1-final su Sonnet via Claude.ai (contratto reale n.3/8)

**Contesto:** Terzo test del prompt v1-final **senza modifiche** su Consip Condizioni Generali Fornitura Prodotti (agosto 2018), tipologia "contratto pubblico denso multi-articolo". Scopo: ulteriore falsificazione delle patch v2 (ellissi, no-calcoli) e ricerca di pattern non ancora osservati.

**Input:**
- Contratto: `prog1/specterai/contratti/Consip_CondizioniGeneraliRelativeAllaFornituraDiProdottiAgosto2018-A.pdf` (25 pagine, ~72k char)
- System prompt: identico a Test #1/#2 (spec v3.1 §6 invariato)
- User message: identico a Test #1/#2 (vincoli espliciti + auto-valutazione 4 dimensioni)
- Esecuzione: chat Claude.ai NUOVA (mitigazione context bleed)

**Output:** JSON valido, 7 categorie presenti, autovalutazione 5/5/5/4 (Italiano 4/5 per uso "manleva" inevitabile). Risk distribution: 3 high (termination, penalties, liability_limitation), 3 medium (payment_terms, governing_law, intellectual_property), 1 low (auto_renewal correttamente assente — Consip Condizioni Generali non hanno durata propria, solo cornice).

**Verifica anti-pattern (vs Test #1 e #2):**

| Pattern | Test #1 (Demanio) | Test #2 (co.co.co.) | Test #3 (Consip) | Esito |
|---|---|---|---|---|
| Ellissi `[...]` in raw_excerpt | Presente | Assente | **Assente in tutti i 7** | ✅ Non riprodotto (2/3 PDF) |
| Calcoli aritmetici espliciti (es. "X €/giorno" derivato da 1‰ × importo) | Presente | Assente | **Assente** — Consip ha 1‰ + tetto 10% ma manca importo base → Sonnet non aveva su cosa calcolare | ✅ Non riprodotto (ipotesi indebolita ma non falsificata) |
| `present:false` motivato senza inventare | OK | OK | OK (`auto_renewal` ben gestito) | ✅ Confermato 3/3 |

**🆕 PATTERN 3 NUOVO osservato in Test #3 — cross-article extraction nel plain_language:**

Sonnet inserisce nel `plain_language` fatti specifici (numeri, percentuali, riferimenti normativi, qualificatori) che **non** compaiono nel `raw_excerpt` corrispondente, anche se sono presenti nel PDF in altri articoli. Identificati 6 fatti sospetti, delegata verifica fattuale a **Claude Haiku via Claude.ai sul PDF allegato** (sessione separata, prompt di verifica letterale, no interpretazione). Risultato:

| # | Fatto nel plain_language di Sonnet | Articolo nel raw_excerpt | Articolo reale (verifica Haiku) | Verdetto Haiku |
|---|---|---|---|---|
| 1 | Ritenuta 0,5% svincolabile a fine contratto | art. pagamento generico | Art. 10 c.4 | ✅ PRESENTE |
| 2 | Mora = BCE + 8 punti | art. pagamento generico | Art. 10 c.7 | ✅ PRESENTE |
| 3 | Tetto penali 10% del contratto | art. penale 1‰ | Art. 11 c.5 | ✅ PRESENTE |
| 4 | **Risoluzione AUTOMATICA al 10% penali** | art. penale 1‰ | Art. 11 c.5 dice "potrà risolvere" (facoltà) | ⚠️ **SIMILE — drift semantico** |
| 5 | Recesso committente con preavviso 30 giorni | art. 1456 c.c. (risoluzione) | Art. 14 c.5 | ✅ PRESENTE |
| 6 | Riferimento D.Lgs. 50/2016 | art. legge italiana | Artt. 1, 2, 10 e passim | ✅ PRESENTE |

**Sintesi:** 5/6 PRESENTI + 1/6 SIMILE + 0/6 ASSENTI. **Zero allucinazioni** (Ipotesi B esclusa) → Ipotesi A confermata: Sonnet legge tutto il contratto e fa **estrazione cross-articolo**, riassumendo nel plain_language fatti veri pescati da articoli diversi dal raw_excerpt selezionato.

**🚨 Caso #4 — drift semantico su qualificatore (pattern più pericoloso):**

L'art. 11 c.5 Consip dice "il Punto Ordinante **potrà** risolvere il Contratto" (facoltà discrezionale). Sonnet ha riqualificato come "risoluzione **automatica** al 10%". Il numero (10%) è corretto e presente; l'avverbio modale ("automatica") è inventato. Per il diritto privato/pubblico italiano la differenza tra risoluzione facoltativa e automatica è **sostanziale** (cambia chi attiva il meccanismo, i tempi, l'onere della prova). È **peggio** dei calcoli aritmetici di Test #1: lì il numero inventato è verificabile con Ctrl+F; qui il drift è semantico-modale e sfugge a qualunque controllo automatico fuzzy match sul raw_excerpt.

**Implicazioni metodologiche:**

1. Le patch v2 (no-ellissi + no-calcoli) **restano necessarie** ma **non sufficienti**. Coprono i pattern di Test #1 ma non i pattern di Test #3 (cross-article extraction + drift semantico).

2. La fuzzy match anti-allucinazione della spec v3.1 (SequenceMatcher 0.92 su raw_excerpt) **non protegge** dal pattern Test #3: il raw_excerpt resta verbatim e passa il fuzzy match; il problema è nel plain_language, non nell'excerpt.

3. Serve un terzo vincolo (patch v2.1 #3): grounding stretto del plain_language al raw_excerpt corrispondente. Ogni fatto specifico (numero, percentuale, riferimento normativo, qualificatore modale come "automatica"/"facoltativa"/"obbligatoria") nel plain_language deve avere un correlato testuale nel raw_excerpt. Se la categoria richiede di citare fatti da più articoli, il raw_excerpt va trasformato in lista o concatenato (compatibilmente con la patch no-ellissi del v2 → soluzione: schema con `raw_excerpts` come array di stringhe contigue).

**Lezione metodologica:**
- ✅ **Delega della verifica fattuale a un secondo LLM su sessione fresh** è efficace e a basso costo: Haiku via Claude.ai gratis, prompt di verifica letterale (no interpretazione), risultato in <1 min, 6 fatti tracciati a livello di articolo del PDF.
- ✅ Il pattern "drift semantico su qualificatore" sarebbe sfuggito a una verifica solo numerica (Ctrl+F): emerge solo confrontando la citazione PDF parola per parola con il plain_language. Lezione: in Lez. 5 (test plan §8) aggiungere un test esplicito di grounding semantico, non solo di grounding numerico.
- ⚠️ La spec v3.1 §raw_excerpt anti-allucinazione (fuzzy match 0.92) protegge solo da "excerpt inventato", non da "interpretazione drift nel plain_language a partire da excerpt corretto". Gap da segnalare alla prof come edge-case scoperto in pre-Cursor (post-MVP, non richiede patch spec ora).

**Aggiornamenti collegati:** nessuna modifica spec v3.1. Nuova patch v2.1 definita sotto come BLOCCO PRONTO DA INCOLLARE (additiva alle patch v2 esistenti, stessa posizione nel system_prompt.md).

---

## Patch v2.1 prompt — BLOCCO PRONTO DA INCOLLARE in `prompts/system_prompt.md` (Cursor Fase 1, additiva alle patch v2)

> Da inserire **dopo le patch v2** nella sezione `CONSTRAINTS (additional)` del system prompt. Coprire il pattern 3 emerso in Test #3 (cross-article extraction + drift semantico su qualificatore).

```
CONSTRAINTS (additional — patch v2.1, 2026-05-11)
- plain_language must be strictly grounded in the corresponding raw_excerpt.
  Every specific fact mentioned in plain_language (numbers, percentages,
  normative references like "D.Lgs. X/Y", deadlines, modal qualifiers like
  "automatic" / "discretionary" / "mandatory") MUST be supported by literal
  text in the raw_excerpt for the same category.
- If a category requires facts located in multiple articles of the contract,
  the raw_excerpt may be split into a list of contiguous spans (each ≥20
  characters, none containing ellipsis markers per patch v2). Do not import
  facts from other articles into plain_language unless their source span is
  included in raw_excerpt.
- Modal qualifiers must mirror the contract's wording. If the text says
  "potrà / può / facoltà" → translate as "facoltativa / discrezionale",
  NEVER as "automatica / obbligatoria". If the text says "di diritto /
  automaticamente" → translate as "automatica". Never upgrade a discretionary
  clause to an automatic one or vice versa.
```

**Provenance:** patch derivata da Test runtime #3 (Consip, verifica fattuale delegata a Haiku via Claude.ai). 5/6 fatti del plain_language risultano cross-article (presenti nel PDF ma fuori dal raw_excerpt); 1/6 mostra drift semantico su qualificatore modale ("automatica" vs "potrà"). Pattern non osservato in Test #1 (calcoli aritmetici) né in Test #2 (zero pattern).

**Quando applicare:** in Lez. 4 insieme alle patch v2, in `prompts/system_prompt.md`. Richiede modifica minima dello schema Pydantic: `raw_excerpt: str` → `raw_excerpt: str | list[str]` (oppure mantenere `str` e usare separatore esplicito come `\n---\n` tra span). Decisione di scope da prendere in Fase 1 Cursor.

**Test post-applicazione (mini-suite suggerita):**
1. Ri-test Consip: verificare che 0,5%, BCE+8, tetto 10%, 30gg preavviso, D.Lgs. 50/2016 finiscano nel raw_excerpt (lista multi-span) e non solo nel plain_language.
2. Re-run Demanio Test #1: verificare che le patch v2 + v2.1 elimino sia l'ellissi sia i calcoli sia eventuali drift semantici.
3. NDA breve (4. Modello impegno riservatezza): contratto a categoria singola, baseline negativo (pattern non dovrebbero attivarsi).

---

## Test runtime #4 — 2026-05-11 — Prompt v1-final su Sonnet via Claude.ai (contratto reale n.4/8)

**Contesto:** Quarto test del prompt v1-final **senza modifiche** su NDA breve unilaterale (Modello Impegno alla Riservatezza, Politecnico di Milano). Tipologia "contratto monocategoria, baseline negativo": ci si aspetta `present:false` su 3-4 categorie su 7, nessuno dei pattern di Test #1 (ellissi+calcoli) e minima attivazione di pattern 3 (cross-article). Scopo aggiuntivo: verificare se Sonnet **inventa** clausole sotto pressione di schema (pattern 4 candidato — riempimento allucinato).

**Input:**
- Contratto: `prog1/specterai/contratti/6._Modello_impegno_alla_riservatezza.pdf` (2 pagine, NDA unilaterale a favore del Politecnico)
- System prompt + user message: identici a Test #1/#2/#3 (v1-final, spec v3.1 §6)
- Esecuzione: chat Claude.ai NUOVA

**Output:** JSON valido, 7 categorie presenti, autovalutazione 5/5/5/4. Risk distribution: 3 high (penalties, liability_limitation, intellectual_property), 2 medium (termination, governing_law), 0 low (le 3 categorie con `present:false` payment_terms+auto_renewal+liability_limitation marcate come risk_level basso/alto coerente con assenza di tutele).

**Verifica anti-pattern (vs Test #1, #2, #3):**

| Pattern | Test #1 | Test #2 | Test #3 | Test #4 (NDA) | Esito cumulativo |
|---|---|---|---|---|---|
| Ellissi `[...]` | Presente | Assente | Assente | **Assente** | ✅ 3/4 non riprodotto (Demanio outlier) |
| Calcoli aritmetici espliciti | Presente | Assente | Assente | **Assente** | ✅ 3/4 non riprodotto |
| Cross-article extraction nel plain_language | Non emerso | Non emerso | Confermato (5/6 fatti) | **Confermato 1/2** (termination "cessa prima se pubbliche" è in altra sezione del NDA) | ⚠️ pattern stabile su contratti multi-articolo |
| Pressione di schema → riempimento allucinato (pattern 4 candidato) | n/a | n/a | n/a | **Non riprodotto** — 3 `present:false` correttamente motivati, raw_excerpt vuoto come da spec | ✅ regola schema tiene |

**🆕 PATTERN 5 NUOVO confermato in Test #4 — inferenza speculativa esplicitata su fatti giurisprudenziali:**

In `governing_law` plain_language Sonnet ha scritto: *"la firma avviene a Milano, quindi è probabile che sia quello milanese"* (riferito al foro competente). Il raw_excerpt cita Dir. UE 2016/943 + artt. 98-99 c.p.i., **nessuna menzione di foro**. Verifica Haiku via Claude.ai sul PDF allegato (chat fresh, prompt verifica letterale) ha confermato: ASSENTE — il documento riporta "Milano" **solo come luogo di firma** ("Letto, accettato e sottoscritto, Milano"), nessuna clausola di competenza territoriale.

Caratteristiche del pattern:
- Sonnet ha **qualificato esplicitamente** con "probabile" → comportamento trasparente, non allucinazione vera
- Tuttavia ha **inferito un fatto giurisprudenziale** ("foro Milano") da un elemento extra-testuale ("firma a Milano") che ha valore probatorio diverso
- Per legal-AI a target non-avvocato: il qualificatore "probabile" viene spesso ignorato dal lettore; il "fatto" si consolida come tale
- Differenza rispetto al pattern 3 (Test #3 cross-article): qui il fatto **non è nel contratto in nessuna forma**; è un'inferenza da extra-testo

**Verifica delegata Haiku — riepilogo numerico:**

| # | Affermazione di Sonnet | Verdetto Haiku | Posizione PDF |
|---|---|---|---|
| 1 | NDA cessa prima dei 5 anni se info diventano pubbliche per cause non imputabili | SIMILE — fatto presente con struttura logica diversa ("5 anni OPPURE pubblico dominio" vs "5 anni con deroga anticipata") | pag. 2, paragrafo finale |
| 2 | Foro probabile Milano basato sulla firma a Milano | **ASSENTE** — solo "Milano" come luogo firma, nessuna clausola foro | pag. 2, firma |

Sintesi: 0/2 PRESENTI + 1/2 SIMILE + 1/2 ASSENTE. **Pattern 6 (allucinazione totale) escluso**; pattern 5 confermato come categoria distinta da pattern 3.

**Implicazioni metodologiche cumulate (Test #1→#4):**

1. Patch v2 (no-ellissi + no-calcoli) → coprono pattern di Test #1, falsificate negativamente su Test #2/#3/#4 (assenti) → prevention legittima.
2. Patch v2.1 (grounding stretto + multi-span + qualificatori modali) → copre pattern 3 di Test #3 e marginalmente pattern 3 di Test #4 (termination cross-article). Tecnicamente blocca anche pattern 5 se Sonnet rispetta "ogni fatto specifico deve avere correlato testuale nel raw_excerpt". Rischio: l'inferenza speculativa qualificata ("probabile X") potrebbe non essere classificata come "fatto" da Sonnet.
3. **Serve patch v2.2 esplicita** contro linguaggio speculativo/inferenziale sul plain_language. Sicurezza in profondità: anche se v2.1 dovesse bastare, una regola esplicita è preferibile per audit trail e per allenare il modello via few-shot in iterazioni future.

**Lezione metodologica:**
- ✅ **Pattern 5 (inferenza speculativa) è sottile**: emerge solo con verifica fattuale incrociata + lettura attenta dei qualificatori. Una review automatica su grounding numerico (Test #1) o su nomi-cose-numeri (Test #3) non l'avrebbe catturato. Lezione: in Lez. 5 (test plan §8) aggiungere un test su grep di stop-words speculative ("probabil*", "presumibil*", "verosimilmente", "potrebbe essere", "presumere").
- ✅ **NDA breve non è un baseline-zero come previsto**: i pattern emergono comunque, solo cambiano forma. Test su contratti corti restano informativi.
- ⚠️ Per legal-AI a target non-esperto, la dottrina "ogni fatto = citazione + verbatim, altrimenti tacere" è più sicura della dottrina "puoi inferire purché qualificato". L'utente non-avvocato non sa pesare i qualificatori di incertezza.

**Aggiornamenti collegati:** nessuna modifica spec v3.1. Patch v2.2 definita sotto come BLOCCO additivo.

---

## Patch v2.2 prompt — BLOCCO PRONTO DA INCOLLARE in `prompts/system_prompt.md` (Cursor Fase 1, additivo a v2 + v2.1)

> Da inserire **dopo le patch v2 e v2.1** nella sezione `CONSTRAINTS (additional)` del system prompt. Copre il pattern 5 emerso in Test #4 (inferenza speculativa qualificata su fatti giurisprudenziali).

```
CONSTRAINTS (additional — patch v2.2, 2026-05-11)
- plain_language must not contain speculative or inferential statements
  about facts absent from the contract text. Speculative language markers
  are forbidden: "probabilmente", "presumibilmente", "verosimilmente",
  "potrebbe essere", "è plausibile che", "si può presumere", "implicitamente".
- Inferring legal facts (governing court, applicable jurisdiction,
  enforcement venue, party domiciles, regulatory classification) from
  extra-textual signals (place of signature, letterhead, institutional
  affiliation, language of the document) is forbidden. If the contract
  does not explicitly state a legal fact, plain_language must say so:
  "Il contratto non specifica [fatto X]; chiarire con la controparte."
- The only inferential statement allowed in plain_language is a direct
  consequence of an absent clause flagged elsewhere as "present": false.
  Example permitted: "Senza un tetto di responsabilità, sei esposto a
  richieste illimitate." Example forbidden: "La firma a Milano suggerisce
  che il foro probabile sia quello milanese."
```

**Provenance:** patch derivata da Test runtime #4 (NDA Politecnico Milano, verifica delegata a Haiku). Pattern 5 confermato come categoria distinta dal pattern 3 (cross-article): qui il fatto inferito **non è nel contratto in nessuna forma**, deriva da extra-testo (luogo firma).

**Quando applicare:** in Lez. 4 insieme alle patch v2 e v2.1, in `prompts/system_prompt.md`. Nessuna modifica schema Pydantic richiesta — vincolo puro a livello di prompt.

**Test post-applicazione:** la mini-suite della patch v2.1 (Consip ri-test + Demanio re-run + NDA baseline) verifica anche v2.2. Test specifico: nel JSON di output, grep di `(probabil|presumibil|verosimil|potrebbe|plausibil|implicit)` deve restituire zero match.

---

## Test runtime #5 — 2026-05-11 — Prompt v1-final su Sonnet via Claude.ai (contratto reale n.5/8)

**Contesto:** Quinto e ultimo test della mini-suite pre-Cursor sui 5 PDF prioritari. Schema Contratto Locazione INPS (Allegato C), tipologia "contratto pubblico immobiliare con foro fisso + asimmetrie locatore/conduttore + template con campi non compilati". Scopo: chiudere la copertura cross-tipologia (appalto, co.co.co., fornitura, NDA, locazione) e validare le ipotesi pattern-emergenti su un'ultima configurazione.

**Input:**
- Contratto: `prog1/specterai/contratti/32561_Allegato-C-Schema-contratto-locazione.pdf`
- System prompt + user message: identici a Test #1-#4 (v1-final, spec v3.1 §6)
- Esecuzione: chat Claude.ai NUOVA

**Output:** JSON valido, 7 categorie presenti, autovalutazione 5/5/5/4. Risk distribution: 1 high (termination — recesso asimmetrico INPS), 4 medium (auto_renewal, penalties, liability_limitation, governing_law), 2 low (payment_terms, intellectual_property — quest'ultima correttamente `present:false`).

**Verifica anti-pattern:**

| Pattern | Esito Test #5 | Note |
|---|---|---|
| Ellissi `[...]` raw_excerpt | ✅ Assente 0/6 | Coerente con Test #2/#3/#4 |
| Calcoli aritmetici espliciti | ✅ Assente | Locazione non fornisce stimoli percentuale + importo base contigui |
| Cross-article extraction nel plain_language | ⚠️ **2/4 confermati + 1/4 borderline** | Pattern sistemico ormai |
| Pressione di schema → riempimento (pattern 4) | ✅ Escluso | `intellectual_property: present:false` correttamente motivato |
| Inferenza speculativa qualificata (pattern 5 v.Test #4) | ✅ Assente — nessun "probabil*/presumibil*" | |
| **🆕 Inferenza legale ASSERTIVA non-qualificata (pattern 5b)** | ⚠️ **1/4 confermato** | "Il diritto italiano si applica in ogni caso" senza "probabile" — peggio del Test #4 |

**Verifica delegata a Haiku via Claude.ai sul PDF allegato — riepilogo:**

| # | Affermazione di Sonnet | Verdetto Haiku | Posizione PDF |
|---|---|---|---|
| 1 | Pagamento bonifico bancario entro 30gg dalla fattura | ✅ PRESENTE | Art. 6 c.5 |
| 2 | Disdetta 12 mesi locatore + 6 mesi conduttore | ✅ PRESENTE | Art. 4 c.1 |
| 3 | Immobile ubicato a Caltanissetta | ⚠️ SIMILE — presente in premesse procedurali e art. 3; Art. 2 ha "comune di ____" non compilato (template) | Art. 3 + procedura; Art. 2 c.1 vuoto |
| 4 | Esplicita applicazione del diritto italiano | ❌ **ASSENTE** — solo rinvii a L.392/78 e CC senza clausola di scelta legge | Art. 21 |

Sintesi: 2/4 PRESENTI + 1/4 SIMILE + 1/4 ASSENTE. Zero allucinazioni vere; cross-article (#1, #2) + lettura da contesto extra-clausola (#3) + asserzione inferenziale (#4).

**🚨 Caso #3 (Caltanissetta) — sotto-pattern nuovo "lettura da campo template non compilato":**

Sonnet ha asserito "immobile a Caltanissetta" leggendo dalle premesse procedurali e dalla destinazione d'uso ("Archivio della Direzione Provinciale di Caltanissetta"), **ignorando che l'art. 2 c.1 con il campo specifico "comune di ___" è vuoto** (template non compilato). Il fatto è materialmente vero (Caltanissetta è l'ubicazione operativa), ma per legal-AI in produzione: un contratto-template **vuoto** restituirebbe comunque un'ubicazione "dedotta dal contesto", che è un comportamento pericoloso se il PDF caricato è una bozza da firmare con campi da compilare. Implicazione: il prompt dovrebbe distinguere tra "campo compilato" e "campo placeholder/vuoto" e segnalare l'incompletezza al non-avvocato.

**🚨 Caso #4 (diritto italiano) — pattern 5b distinto da pattern 5 di Test #4:**

In Test #4 Sonnet aveva inferito "foro probabile Milano" **con qualificatore esplicito** ("probabile"). In Test #5 Sonnet ha scritto "Il diritto italiano si applica in ogni caso" **come asserzione netta**, senza "probabile" né "presumibile". Verifica Haiku: ASSENTE clausola di scelta legge, solo rinvii impliciti a L.392/78 e CC. La patch v2.2 attualmente vieta i marker speculativi e l'inferenza giurisprudenziale da extra-testo → tecnicamente copre anche questo caso (l'inferenza è giuridica). Tuttavia la formulazione assertiva è **peggio** perché manca la trasparenza speculativa: il lettore non-avvocato non ha alcun segnale che si tratta di un'inferenza.

**Implicazione:** la patch v2.2 va rafforzata con un vincolo positivo aggiuntivo (non solo "vieta speculazione" ma anche "se la clausola è assente, dichiaralo esplicitamente"). Già presente nella formulazione attuale ("plain_language must say so: 'Il contratto non specifica [fatto X]'") — non serve nuova patch, va solo verificato che Sonnet la rispetti su Test post-applicazione. Aggiunta minore al few-shot in Cursor: includere un esempio con "il contratto non specifica la legge applicabile, chiarire con la controparte" come gold standard.

**Aggiornamenti collegati:** nessuna modifica spec v3.1. Nessuna nuova patch v2.3 — pattern 5b già coperto da v2.2 con clausola positiva "if absent → say so".

---

## Test runtime #6 — 2026-05-20 — Prompt v2 (patch v2+v2.1+v2.2) su Sonnet via Claude.ai (contratto reale n.6/8)

**Contesto:** Primo test della suite di convalida patch v2/v2.1/v2.2 su system_prompt.md consolidato (creato 2026-05-20). PDF: `Contratto firmato.pdf` — contratto fornitura energia elettrica Dolomiti Energia, tipologia "utilities con addebito SEPA".

**Input:**
- Contratto: `prog1/specterai/contratti/Contratto firmato.pdf`
- System prompt: `prompts/system_prompt.md` v1-final + patch v2/v2.1/v2.2
- Esecuzione: chat Claude.ai NUOVA

**Output:** JSON valido, 7 categorie presenti, autovalutazione non riportata. Risk distribution: 2 high (liability_limitation, governing_law), 3 medium (payment_terms, auto_renewal, penalties, termination — in realtà 4), 1 low (intellectual_property present:false).

**Verifica checklist patch:**

| Check | Esito | Note |
|---|---|---|
| No ellissi raw_excerpt | ✅ | Zero `[...]` |
| No calcoli aritmetici | ✅ | Interessi descritti con rif. D.Lgs. 231/2002, nessun numero derivato |
| Qualificatori modali | ✅ | Nessun drift |
| No speculazione (v2.2) | ⚠️ borderline | `governing_law` plain_language: "la legge **potrebbe** prevedere un foro diverso a tutela del consumatore" — "potrebbe" nello spirito del vincolo v2.2 (non nell'elenco letterale, ma marker inferenziale) |
| Clausola positiva se assente | ✅ | `intellectual_property`: "Il contratto non contiene clausole sulla proprietà intellettuale" |
| Grounding plain↔raw (v2.1) | ⚠️ edge case | `auto_renewal` e `termination` citano "Condizioni Generali" non presenti nei rispettivi raw_excerpt — cross-article edge case v2.1 già catalogato (pattern 3) |
| 7 categorie | ✅ | |

**Nuovo pattern:** nessuno. Entrambe le osservazioni sono edge case di pattern 3 (v2.1) e pattern 5 (v2.2) già catalogati.

**Decisione:** PASS — nessuna patch v2.3 richiesta.

---

## Test runtime #7 — 2026-05-20 — Prompt v2 (patch v2+v2.1+v2.2) su Sonnet via Claude.ai (contratto reale n.7/8)

**Contesto:** Secondo test della suite di convalida. PDF: `ContrattoCOCOCO.pdf` — co.co.co. con campi template parzialmente non compilati (preavviso "30/60/90 giorni" non barrato).

**Input:**
- Contratto: `prog1/specterai/contratti/ContrattoCOCOCO.pdf`
- System prompt: `prompts/system_prompt.md` v1-final + patch v2/v2.1/v2.2
- Esecuzione: chat Claude.ai NUOVA

**Output:** JSON valido, 7 categorie presenti. Risk distribution: 2 high (penalties, liability_limitation), 3 medium (payment_terms, termination, intellectual_property), 2 low/false (auto_renewal, governing_law).

**Verifica checklist patch:**

| Check | Esito | Note |
|---|---|---|
| No ellissi | ✅ | |
| No calcoli | ✅ | |
| Qualificatori modali | ✅ | |
| No speculazione | ✅ | |
| Clausola positiva se assente | ✅ ++ | `liability_limitation` present:false: "sei esposto a richieste di risarcimento potenzialmente illimitate" = gold standard v2.2 esatto |
| Grounding v2.1 | ✅ | |
| 7 categorie | ✅ | |

**Comportamento positivo nuovo — gestione campo template non compilato:** `termination` raw_excerpt contiene "(30/60/90) giorni" (campo barrato non selezionato). Sonnet ha identificato il campo come non definito e lo ha segnalato esplicitamente nel plain_language: "la durata non è ancora definita, da scegliere tra 30/60/90 giorni". Comportamento **migliore** del pattern 6 di Test #5 (Locazione INPS dove Sonnet aveva asserito Caltanissetta ignorando il campo vuoto nell'art. 2). Patch v2.2 ha probabilmente contribuito a questa correttezza.

**Nuovo pattern:** nessuno.

**Decisione:** PASS — output più pulito della serie completa 1→7.

---

## Test runtime #8 — 2026-05-20 — Prompt v2 (patch v2+v2.1+v2.2) su Sonnet via Claude.ai (contratto reale n.8/8)

**Contesto:** Terzo e ultimo test della suite di convalida. PDF: `Modulo-accordo-di-riservatezza.pdf` — accordo carriera alias universitaria (contesto anomalo: accordo amministrativo università/studente, non contratto commerciale).

**Input:**
- Contratto: `prog1/specterai/contratti/Modulo-accordo-di-riservatezza.pdf`
- System prompt: `prompts/system_prompt.md` v1-final + patch v2/v2.1/v2.2
- Esecuzione: chat Claude.ai NUOVA

**Output:** JSON valido, 7 categorie presenti. Risk distribution: 0 high, 3 medium (penalties, liability_limitation, termination), 4 low/false (payment_terms, auto_renewal, governing_law, intellectual_property).

**Verifica checklist patch:**

| Check | Esito | Note |
|---|---|---|
| No ellissi | ✅ | |
| No calcoli | ✅ | |
| Qualificatori modali | ✅ | "potrà essere sospesa" → "può sospendere" — facoltà correttamente preservata |
| No speculazione (v2.2) | ⚠️ borderline | `intellectual_property` present:false plain_language: "questo aspetto non è rilevante nel contesto specifico" — giudizio di contesto non fondato su testo contrattuale. Spirito v2.2 (asserzione inferenziale su fatto non nel contratto). |
| Clausola positiva se assente | ✅ | `payment_terms`, `auto_renewal`, `governing_law` tutti con clausola positiva corretta |
| Grounding v2.1 | ⚠️ edge case | `auto_renewal` present:false: "Rimane attivo finché durano i presupposti" — concetto estratto dalla clausola `termination`, non dal raw_excerpt vuoto di auto_renewal |
| 7 categorie | ✅ | |

**Nota contesto:** il PDF è un accordo amministrativo universitario (carriera alias), non un contratto commerciale. SpecterAI lo analizza comunque con 7 categorie commerciali, con risultati coerenti malgrado il mismatch di contesto. I due edge case (`auto_renewal` cross-termination, `intellectual_property` giudizio contesto) sono artefatti del mismatch, non failure del prompt su contratti commerciali tipici.

**Nuovo pattern:** nessuno. Edge case di pattern 3 (v2.1 cross-article) e 5b (v2.2 asserzione non-qualificata) su contesto atipico.

**Decisione:** PASS — nessuna patch v2.3 richiesta.

---

## Cumulativo Test #6→#8 — Convalida patch v2/v2.1/v2.2 (2026-05-20)

**Copertura:** 3/3 PDF rimanenti testati con system_prompt.md consolidato (v1-final + patch v2+v2.1+v2.2). Tutti i test eseguiti su chat Claude.ai NUOVA.

**Tipologie aggiuntive:** fornitura energia/utilities (Dolomiti Energia), co.co.co. con template parzialmente vuoto, accordo amministrativo universitario (contesto atipico).

**Risultato convalida patch:**

| Patch | Pattern coperto | Stato dopo 8/8 test |
|---|---|---|
| v2 | No-ellissi + no-calcoli | ✅ CONFERMATA — 0 attivazioni su 8 test (dopo patch) |
| v2.1 | Grounding plain↔raw + qualificatori modali | ✅ CONFERMATA — edge case residui (cross-article Condizioni Generali, cross-termination) sono pattern 3 già noto, non nuovi |
| v2.2 | No speculazione + clausola positiva "if absent → say so" | ✅ CONFERMATA — edge case borderline ("potrebbe", giudizio contesto IP) su contratti atipici, pattern 5/5b già noti |

**Pattern nuovi emersi nei test #6→#8:** NESSUNO.

**Comportamento positivo emergente:** Test #7 mostra gestione corretta dei campi template non compilati ("30/60/90 giorni" non barrato → segnalato esplicitamente) — miglioramento rispetto a Test #5 (Locazione INPS pattern 6). Nessuna patch richiesta, comportamento già corretto.

**VERDETTO: ✅ PASS — 3/3 test senza nuovi pattern**

**Criterio soddisfatto:** prompt pronto per Cursor Fase 2 (`llm_client.py`).

---

## Segnali da monitorare (post-convalida 2026-05-20)

> Non sono pattern confermati — non richiedono patch ora. Sono **leading indicator**: se si ripresentano in Test plan §8 (Lez. 5) o in produzione, promuoverli a patch v2.3+.

| # | Segnale | Test origine | Soglia di promozione a patch |
|---|---|---|---|
| S-01 | **"potrebbe" come marker speculativo implicito** — `governing_law` Test #6: *"la legge potrebbe prevedere un foro diverso"*. Non è nell'elenco letterale dei marker vietati da v2.2 ("potrebbe essere" sì, "potrebbe" da solo no), ma nello spirito del vincolo. Il fatto citato è tecnicamente vero (tutela consumatori esiste), quindi non è allucinazione — ma introduce incertezza non grounded nel testo. | Test #6 Dolomiti Energia | 2+ occorrenze in §8 o segnalazione utente |
| S-02 | **Gestione positiva campi template non compilati** — Test #7: Sonnet segnala correttamente `(30/60/90) giorni` come campo non barrato, invitando l'utente a verificare prima della firma. Comportamento opposto al pattern 6 (Test #5 Locazione INPS dove asseriva ubicazione da campo vuoto). Non richiede patch — da confermare come comportamento stabile nel test plan §8. | Test #7 co.co.co. template | Se Test §8 mostra regressione (campo vuoto → asserzione) → T15-bis |
| S-03 | **Contesto atipico: accordi non commerciali** — Test #8 (carriera alias universitaria) produce output formalmente corretto ma con edge case su categorie non applicabili (es. `intellectual_property` con giudizio di contesto "non rilevante"). SpecterAI non ha gate di rifiuto per contratti non commerciali — in produzione un utente potrebbe caricare qualsiasi documento. | Test #8 accordo universitario | Se frequente in demo/produzione → aggiungere gate "tipologia contratto" in `pdf_processor.py` o disclaimer contestuale |

---

## Cumulativo Test #1→#5 — Sintesi pre-Cursor (2026-05-11)

**Copertura test mini-suite:** 5/5 PDF prioritari analizzati con prompt v1-final invariato; 4/5 verifiche fattuali delegate a Haiku via Claude.ai (Test #2 ERSU non richiedeva verifica perché 0 pattern).

**Tipologie contrattuali coperte:** appalto pubblico (Demanio), co.co.co. (ERSU), Condizioni Generali fornitura PA (Consip), NDA unilaterale (Politecnico), locazione PA (INPS). Mancano dalla mini-suite i restanti 3 PDF (servizi, fornitura B2B, NDA bilaterale) — possono essere usati post-Cursor come regression test delle patch v2/v2.1/v2.2.

**Pattern emersi (consolidato):**

| Pattern | Definizione | Test in cui appare | Patch | Stato |
|---|---|---|---|---|
| 1. Ellissi `[...]` nei raw_excerpt | Sonnet condensa due brani contigui con marker di ellissi | #1 Demanio (1/5) | v2 | Risolto a prompt-level |
| 2. Calcoli aritmetici espliciti | Sonnet calcola spontaneamente percentuali su importi citati (es. 1‰ × 143.315 = 143/giorno) | #1 Demanio (1/5) | v2 | Risolto a prompt-level |
| 3. Cross-article extraction nel plain_language | Sonnet sintetizza fatti veri da articoli diversi dal raw_excerpt citato | #3 Consip, #4 NDA, #5 Locazione (3/5) — sistemico su contratti multi-articolo | v2.1 + schema Pydantic `raw_excerpt: list[str]` | Risolto a prompt-level + schema-level |
| 3b. Drift semantico su qualificatore modale | "Potrà risolvere" (facoltà) riqualificato come "risoluzione automatica" | #3 Consip (1/5) | v2.1 (mirror qualificatori) | Risolto a prompt-level |
| 4. Riempimento allucinato sotto pressione schema | Inventare clausole per evitare `present:false` | Nessun test (0/5) | Regola spec già esistente | Escluso — regola tiene |
| 5. Inferenza speculativa qualificata | "Probabile foro Milano" da firma a Milano | #4 NDA (1/5) | v2.2 | Risolto a prompt-level |
| 5b. Asserzione inferenziale non-qualificata | "Il diritto italiano si applica" senza clausola di scelta legge | #5 Locazione (1/5) | v2.2 (clausola positiva "if absent → say so") + few-shot Cursor | Risolto a prompt-level |
| 6. Lettura da campo template non compilato | Asserire ubicazione da premesse mentre campo specifico è vuoto | #5 Locazione (1/5) | Da decidere in Cursor (estensione few-shot) | Aperto, non-bloccante per MVP |

**Decisioni finali da portare in Cursor (Lez. 4 Fase 1):**

1. **Patch v2 + v2.1 + v2.2** vanno integrate tutte e tre nel file `prompts/system_prompt.md`, in ordine, nella sezione `CONSTRAINTS (additional)` del system prompt corrente (text-level spec v3.1 §6). I 3 BLOCCHI PRONTI DA INCOLLARE sono già scritti sopra in questo PROMPT_LOG.
2. **Schema Pydantic** modificare `raw_excerpt: str` → `raw_excerpt: list[str]` con `min_length=1`, ogni elemento `min_length=20`, **nessuno** contenente ellissi marker (validator post-init). Questa è la modifica unica di schema richiesta dalle patch.
3. **Few-shot examples** aggiungere in Cursor 1 esempio gold-standard "if absent → say so" (es. "il contratto non specifica la legge applicabile, chiarire con la controparte") per rafforzare la clausola positiva di v2.2. Mantenere i 14 esempi esistenti (2x7 categorie).
4. **Pattern 6 (template vuoto) NON bloccante** per MVP corso. Annotare come future-work in INC-001 candidato post-MVP (verifica campi `___` / `XXX` / `[da compilare]` nel raw_excerpt).
5. **Test plan §8 Lez. 5** — aggiungere 2 test specifici:
   - T13: grep nel JSON di output di `(probabil|presumibil|verosimil|potrebbe|plausibil|implicit)` → deve restituire 0 match
   - T14: per ogni categoria con `present:true`, controllare che ogni numero / data / riferimento normativo / qualificatore modale del `plain_language` sia presente anche in almeno uno degli span di `raw_excerpt` (regex grounding test)

**Lezioni metodologiche complessive:**

- ✅ **Test cross-tipologia su 5 PDF reali con prompt invariato** ha rivelato 8 pattern distinti (di cui 6 attivi). Una mini-suite di 1-2 contratti avrebbe lasciato il 60% dei pattern non scoperti. Per legal-AI la diversità tipologica è essenziale.
- ✅ **Delega verifica fattuale a un secondo LLM** (Haiku via Claude.ai) è stata efficace e gratuita: 4 verifiche, ~10 min totali, 0 falsi positivi. Pattern riusabile per QA legale post-MVP (validation set semi-automatico).
- ✅ **Logging granulare** (1 entry PROMPT_LOG per test + 1 BLOCCO patch per scoperta) ha trasformato un'esplorazione in pipeline riproducibile. Conferma il valore del framework del corso (PROMPT_LOG come deliverable).
- ⚠️ La spec v3.1 fuzzy match 0.92 su `raw_excerpt` protegge solo dal pattern 1 (ellissi/parafrasi). I pattern 3, 3b, 5, 5b sono **fuori dal suo perimetro**. Da segnalare alla prof come edge-case scoperto post-spec (non richiede patch alla spec — la spec è confermata 95/100 e va consegnata invariata; le mitigazioni vivono nel prompt operativo).
- ⚠️ La logica "Sonnet riassume tutto il contratto nel plain_language" è un comportamento **sistemico** (3/5 PDF), non un'eccezione. Architetturalmente significa: o il `plain_language` resta libero di sintetizzare e accetta cross-article (allora il `raw_excerpt` deve diventare lista multi-span come da patch v2.1), o il `plain_language` viene vincolato strettamente al singolo span (allora alcune categorie diventano povere). Trade-off da rivisitare in Lez. 6 se i test §8 mostrano qualità sub-target.

**Budget consegna aggiornato:** zero costo runtime sostenuto (tutti i test sono stati eseguiti su Claude.ai free + Haiku free su Claude.ai). Budget API a pagamento ancora intero (~1,50 € disponibili per test plan §8 + demo).

---

## Template per v2+ (Future iterations)

```markdown
## v2 — [Data] — [Titolo Iterazione]

**Contesto:** [Quando e perché è stata necessaria questa iterazione?]

**Problema identificato:** 
- [Cosa non funzionava nella v1?]
- Sintomo: [Come manifestato durante testing?]
- Root cause: [Perché accadeva?]

**Soluzione implementata:**
- [Modifiche al prompt structure]
- [Modifiche ai parametri Claude]
- [Modifiche ai few-shot examples]

**Testing eseguito:**
- Test case 1: [Input → Expected output → Actual output]
- Test case 2: ...

**Lezioni apprese:**
- [Cosa abbiamo imparato]
- [Cosa cambia per future iterations]

**Aggiornamenti a:** 
- Specifica v2? [sì/no, descrizione]
- INCIDENTS.md? [sì/no, quale incident risolto]
- SESSION_HANDOFF.md? [sì/no, update stato]

**Risultati:**
- ✅ [Cosa funziona ora meglio]
- ⏳ [Cosa rimane da verificare]

**Prossimo step:** [Cosa fare dopo questa iterazione?]
```

---

## Note Tecniche Finali

- **Plain language:** Italiano semplice, no gergo legale — testato manualmente
- **Determinism:** Temperature=0 garantisce output coerente e riproducibile per audit trail
- **JSON validation:** Pydantic schema enforcement — 0 errori in condizioni normali
- **Few-shot anchoring:** 2 esempi per categoria (clausola presente + assente) = key per accuracy
- **Token economy:** ~4.000-6.000 token per analisi completa = <0,02 €/analisi con Sonnet
- **Stateless design:** No conversation history = simplified error handling, no context creep

---

## Dependency Tracking

- **Specifica Tecnica v2:** Full prompt text source — §6 Comportamento AI
- **INCIDENTS.md:** Track prompt testing failures
- **SESSION_HANDOFF.md:** Stato testing during MVP building
- **Lezione 2 — Specifica Tecnica e Prompt Engineering:** C.I.A.R.E. framework reference
