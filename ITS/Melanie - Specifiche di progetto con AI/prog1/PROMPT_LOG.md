# PROMPT_LOG — SpecterAI

**Progetto:** SpecterAI — AI Contract Analyzer per Non-Avvocati (Italiano)  
**Data inizio progetto:** 2026-04-28 (brainstorming)  
**Data prompt v1:** 2026-04-30
**Versione prompt attuale:** v1-final (text-level invariato dalla Spec v2; spec attuale: v3.1 al 2026-05-10)
**Stato:** Operativo — pronto per MVP testing (Lez. 3)
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
