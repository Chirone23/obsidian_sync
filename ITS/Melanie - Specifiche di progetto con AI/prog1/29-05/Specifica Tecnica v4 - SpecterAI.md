# Specifica Tecnica v4 — SpecterAI

**Progetto:** SpecterAI — AI Contract Analyzer for Non-Lawyers
**Versione:** 4.0
**Data:** 2026-05-29
**Corso:** AI Projects Development · [[Progettistica AI MOC]]
**Stato:** Versione corrente di riferimento — **cambio architetturale** rispetto alla v3.1
**Sostituisce:** [[Specifica Tecnica v3 - SpecterAI]] (v3.1, congelata e preservata)
**Driver del cambiamento:** [[SPEC_ERRATA]] (divergenze codice↔spec emerse in fase di build) + [[Privacy Filter Integration]]

---

## Changelog v3.1 → v4

La v3.1 era stata congelata e validata (95/100). Durante il building sono emersi cambiamenti che **non sono semplici corrigenda ma una modifica dell'architettura**: per questo si apre una nuova versione invece di restare su errata. Le motivazioni di dettaglio e l'audit trail di ogni divergenza sono in [[SPEC_ERRATA]].

| # | Sezione | Modifica | Tipo | Fonte |
|---|---|---|---|---|
| 1 | §3, §3.bis (nuova), §5 | **Layer di redazione PII pre-LLM** promosso da assente a componente architetturale obbligatorio. Pipeline: `estrai → redigi PII → LLM riceve solo il redatto → ripristina → mostra`. | **Architetturale** | SPEC_ERRATA ERR-01 + Privacy Filter Integration |
| 2 | §4, §6, §10 | **Backend LLM configurabile** (`LLM_BACKEND` ∈ {cli, sdk}): Claude Code CLI (dev, €0) o SDK Anthropic (deploy, API key). | **Architetturale** | SPEC_ERRATA ERR-08 |
| 3 | §3, §4, §6 | **Gate lingua spostato post-LLM**: rilevamento via `language_detected` dell'output (non più `langdetect` pre-call). `langdetect` rimosso. | Rettifica | SPEC_ERRATA ERR-09 (decisione B) |
| 4 | §4, §6 | Stack: +spaCy `it_core_news_sm`, +python-dotenv, +anthropic (path sdk); −langdetect. | Stack | ERR-02 |
| 5 | §6 | `raw_excerpt` diventa `str \| list[str]` (citazioni multi-span per clausole cross-article). | Schema | ERR-04 |
| 6 | §6 | Patch prompt v2/v2.1/v2.2 (no-ellissi, grounding stretto, qualificatori modali, anti-speculazione) consolidate in `prompts/system_prompt.md`. | Prompt | ERR-03 |
| 7 | §7 | GDPR ampliato: **data minimization Art. 5 + privacy by design Art. 25** come base della redazione PII. | Compliance | Privacy Filter Integration P3 |
| 8 | §7 | Modello costi distinto per backend: cli (€0 marginale) vs sdk (~0,04 €/analisi). | Costi | ERR-08 |
| 9 | §8 | Test plan: +T13 (anti-speculazione), +T14 (grounding plain↔excerpt), +T15 (zero PII leak nel payload). | Test | ERR-07 |
| 10 | §9 | Nuovo failure mode: filtro PII non disponibile → fail-closed (mai inviare PII in chiaro). | Errori | Privacy Filter Integration |

> **Cosa NON è cambiato:** missione, utenti target, le 7 categorie di red flag, l'output plain-language, il posizionamento AI Act limited-risk. Lo *scope* è identico alla v3.1; è cambiato il *come* (data flow + compliance + integrazione LLM).

---

## 1. Sintesi del Progetto

**Problema:** Freelance, autonomi e piccoli imprenditori ricevono contratti che non capiscono appieno e spesso firmano senza sapere cosa rischiano. Gli strumenti esistenti (Spellbook, Harvey AI, Docusign IAM) sono pensati per avvocati e legal team enterprise.

**Soluzione:** SpecterAI analizza contratti in PDF e restituisce un report in linguaggio plain con i punti critici da verificare prima di firmare. Non dà consulenza legale — evidenzia i rischi e suggerisce le domande da porre. **Privacy-first:** i dati personali del contratto vengono pseudonimizzati localmente prima di qualsiasi invio a un LLM cloud.

**Utenti target:** Freelance, liberi professionisti, piccoli imprenditori italiani senza legal team interno. Stima mercato: 2,7M soggetti in Italia (Statista 2019).

**Valore prodotto:** Ridurre il rischio di firmare contratti con clausole sfavorevoli senza averle comprese, senza pagare un avvocato per ogni documento — e senza esporre dati personali a terze parti.

**Posizionamento normativo:** Sistema di *decision-support*, non di consulenza legale. **AI Act:** limited-risk (derogazioni Art. 6(3), vedi §7). **GDPR:** privacy by design via redazione PII pre-cloud (Art. 5 + Art. 25, vedi §3.bis e §7).

> ⚠️ **Limite di validazione dichiarato:** validazione di mercato esclusivamente desk research. **Zero user interview** con freelance reali. Vedi §11.bis.

### Competitive Positioning

SpecterAI occupa una **sub-niche indifesa**: Italian-speaking, non-lawyer, SMB-budget decision-support tool. Mikeoss/Harvey/Legora sono lawyer-centric, enterprise, English-first e per drafting/lifecycle; SpecterAI è SMB-first, Italian-first, plain-language, per *reading & risk-analysis*. La dimensione **privacy-first (PII mai in chiaro al cloud)** è un ulteriore elemento di differenziazione verso il mercato italiano sensibile al GDPR.

---

## 2. MVP e Funzionalità

### In scope (MVP)
- Upload di un PDF contratto (digitale, non scansione)
- **Redazione PII locale** del testo prima dell'invio al modello (pseudonimizzazione + ripristino)
- Estrazione e analisi di 7 categorie di red flag
- Output web strutturato in italiano con spiegazione plain-language per categoria
- Disclaimer AI Act visibile in ogni output
- Supporto contratti in italiano e inglese
- Gate IT/EN (lingue diverse → blocco con messaggio)
- Nessuna persistenza dei dati (processing on-request)
- Backend LLM selezionabile (CLI per sviluppo / SDK per deploy)

### Fuori scope (versioni future)
- OCR per PDF scansionati
- Confronto tra versioni del contratto
- Storico analisi / account utente
- Integrazione con firma digitale
- Dashboard multi-documento
- Supporto contratti in lingue diverse da IT/EN

### Stretch goals (solo se avanza tempo Lez. 6)
| # | Stretch | Tecnologia |
|---|---|---|
| 1 | Download report in PDF | Playwright headless `page.pdf()` |
| 2 | Confidence indicator su `risk_level` | self-consistency 3-run a temp=0 (triplica costo) |
| 3 | CSS report più curato | Tailwind CDN / CSS inline |

---

## 3. Flusso Operativo

```
[Utente] → Upload PDF
         → FastAPI: validazione input (dimensione, MIME type, densità testo)
         → PyMuPDF estrae il testo grezzo
         → Regex Layer estrae date/importi/scadenze come METADATI STRUTTURATI
                  (pre-redazione: i parametri contrattuali non sono PII personali)
         → [PRIVACY] privacy_filter.redact(): spaCy NER (nomi/luoghi) + regex IT
                  (CF, P.IVA-Luhn, IBAN-mod97, email, tel) → testo redatto + mapping (solo RAM)
         → Claude (backend cli o sdk) riceve SOLO il testo redatto + i metadati
         → Claude restituisce JSON strutturato (con language_detected)
         → Pydantic valida lo schema JSON
         → [GATE] language_detected ∈ {italian, english}? Se NO → ERRORE BLOCCANTE (post-LLM)
         → [GATE] Verifica raw_excerpt grounded (norm + fuzzy 0.92) sul testo redatto
         → privacy_filter.restore(): i placeholder negli excerpt tornano valori reali
         → FastAPI renderizza la risposta via Jinja2
         → [Utente] visualizza il report; tutto scartato dalla RAM (nessuna persistenza)
```

### Requisiti funzionali
- Processare un PDF entro 30 secondi (target <15s; +<1s per la redazione)
- Output su tutte e 7 le categorie (anche `present: false`)
- Disclaimer visibile senza scroll
- PDF non leggibile → messaggio di errore chiaro
- **Nessuna PII in chiaro deve raggiungere il backend LLM** (verificato T15)

### Input — edge case formali
| Condizione | Comportamento atteso |
|---|---|
| PDF digitale standard (≤10MB, >100 char) | Flusso normale |
| PDF >10MB | Errore bloccante pre-elaborazione (413) |
| PDF scansionato (<100 char) | Errore "supporta solo PDF digitali" |
| PDF protetto da password | Errore "rimuovi la password" |
| File non PDF (MIME errato) | Errore "formato non supportato" |
| Testo >50.000 char | Troncamento a 40.000 char + avviso |
| Contratto lingua non IT/EN | Errore bloccante (rilevato post-LLM da `language_detected`) |
| Filtro PII non disponibile | **Fail-closed**: analisi rifiutata, mai PII in chiaro al cloud (§9) |

### Requisiti non funzionali
- Nessun dato persistito su disco o DB; PDF e mapping PII vivono solo in RAM per la richiesta
- Il testo in chiaro non lascia mai il server; al cloud va solo il redatto via HTTPS
- Dimensione massima file: 10 MB

---

## 3.bis Privacy-First Architecture (GDPR Art. 5 + Art. 25) — NUOVO in v4

**Motivazione (compliance, non feature):** un contratto contiene tipicamente PII (nomi, indirizzi, CF, P.IVA, IBAN, email, telefono). Inviarle in chiaro a un LLM cloud (Anthropic, server US) è un trattamento eccessivo. Verifica regolatoria (Privacy Filter Integration, P3): **GDPR Art. 5 (minimizzazione) + Art. 25 (data protection by design)** rendono la redazione PII pre-cloud-LLM *"mandatory in practice"* per un SMB italiano. DPA + retention 7gg **da soli non bastano**.

**Pipeline (Hybrid Map-back Pattern):**
1. `regex_layer.extract_metadata()` estrae date/importi/scadenze **prima** della redazione → passati come metadati strutturati (sono parametri di contratto, non PII personali: la scadenza non è PII, la data di nascita sì).
2. `privacy_filter.redact(text)`:
   - **Passo 1 — spaCy NER** (`it_core_news_sm`) sul testo **originale**: redige PERSON / LOC / GPE / ORG. Gira per primo così gli offset sono validi (vedi nota ordering sotto).
   - **Passo 2 — regex deterministiche** sul testo post-NER: CF (context-aware + uppercase), P.IVA (checksum Luhn), IBAN (mod-97), email, telefono (+39). Ogni sostituzione genera un placeholder `[LABEL_n]` e una entry nel `mapping`.
3. Il `mapping {placeholder: valore_reale}` vive **solo in RAM**, mai su disco né fuori dal processo.
4. Il backend LLM riceve **solo** il testo redatto + i metadati.
5. `privacy_filter.restore()` rimette i valori reali nei `raw_excerpt` dell'output (l'utente vede le citazioni vere; il cloud non le ha mai viste).

**Nota ordering (bug critico evitato):** spaCy (offset-based) deve precedere le regex (string-based). Se le regex girassero prima, i placeholder inseriti sfaserebbero gli offset di spaCy → corruzione tipo `[PER[CF_1]_2]`. Ordine spaCy→regex = corruzione impossibile per costruzione (vedi [[INCIDENTS]] INC-009).

**Scelta modello spaCy:** `it_core_news_sm` (13MB), non `it_core_news_lg` (550MB) — il lg va in timeout su Windows e l'sm è sufficiente per l'MVP. Latency redazione <1s/contratto.

**Pivot tecnico:** valutato inizialmente OpenAI Privacy Filter (OPF, 1.5B), scartato per latency (~10 min/contratto) → Hybrid regex+spaCy. Dettaglio in [[Privacy Filter Integration]].

**Limite noto:** over-redaction possibile (NER `sm` etichetta male alcune entità; ~10% dei numeri a 11 cifre passa Luhn). Direzione sicura (recall su precision); tracciato [[INCIDENTS]] INC-006.

**Fallback (fail-closed):** se il filtro PII non è disponibile, l'analisi viene **rifiutata** — mai degrade silenzioso che invii PII in chiaro (§9).

---

## 4. Stack Tecnologico

| Componente | Tecnologia | Motivazione |
|---|---|---|
| Linguaggio | Python 3.12+ | Standard del corso |
| Web framework | FastAPI | Asincrono, nativo per API |
| Template HTML | Jinja2 | Integrato in FastAPI |
| PDF parsing | PyMuPDF (fitz) | Veloce, affidabile su PDF digitali |
| Layer deterministico | re (regex stdlib) | Estrazione date/importi + PII strutturate |
| **Motore privacy NER** | **spaCy `it_core_news_sm`** | NER italiano per nomi/luoghi (sm, non lg → no timeout Windows) |
| Validazione output AI | Pydantic v2 | Schema enforcement; `raw_excerpt: str \| list[str]` |
| Verifica citazione | difflib.SequenceMatcher | Fuzzy match excerpt vs testo (soglia 0.92) |
| **LLM — backend configurabile** | **Claude Code CLI** (`cli`) **o SDK Anthropic** (`sdk`) | `LLM_BACKEND`: cli = dev €0 / sdk = deploy con API key |
| Modello | claude-sonnet-4-6 | Accuratezza su testo legale |
| Config | python-dotenv | Carica `.env` (backend, modello, API key) |
| Version control | Git + GitHub | Tracciabilità, consegna corso |
| Deploy MVP | Locale (uvicorn) | Sufficiente per demo corso |

### Dipendenze Python
```
fastapi
uvicorn
pymupdf
anthropic          # usato dal backend sdk
pydantic
jinja2
python-multipart
python-dotenv
spacy              # + modello it_core_news_sm
```
> **Rimosso rispetto a v3.1:** `langdetect` (il rilevamento lingua è ora a carico dell'LLM, vedi §6 e [[SPEC_ERRATA]] ERR-09).

---

## 5. Architettura e Flusso Dati

```
┌─────────────┐  PDF upload   ┌──────────────┐
│   Browser   │ ────────────► │   FastAPI    │
│  (Jinja2)   │ ◄──────────── │   Server     │
└─────────────┘  HTML report  └──────┬───────┘
                                      │
                            ┌─────────▼────────┐
                            │  Input Validator │ (MIME, size, densità)
                            └─────────┬────────┘
                            ┌─────────▼────────┐
                            │  PDF Processor   │ (PyMuPDF)
                            └─────────┬────────┘
                                      │ testo grezzo
                            ┌─────────▼────────┐
                            │ Regex Layer      │ → metadati (date/importi)
                            └─────────┬────────┘
                            ┌─────────▼────────┐
                            │ Privacy Filter   │  spaCy NER (Passo 1)
                            │ redact()         │  + regex IT (Passo 2)
                            └─────────┬────────┘  → testo redatto + mapping (RAM)
                            ┌─────────▼────────┐
                            │  LLM Backend     │  cli (Claude Code) | sdk (Anthropic)
                            │  riceve REDATTO  │  temperature=0, max_tokens=2048
                            └─────────┬────────┘
                                      │ JSON
                            ┌─────────▼────────┐
                            │ Pydantic + gate  │  language IT/EN + excerpt grounding
                            │ lingua + excerpt │
                            └─────────┬────────┘
                            ┌─────────▼────────┐
                            │ restore()        │  placeholder → valori reali negli excerpt
                            └──────────────────┘
```

**Flusso dati e privacy:** PDF e mapping PII solo in RAM; al cloud va solo il testo redatto via HTTPS; nessuna persistenza; ogni richiesta è stateless.

---

## 6. Comportamento AI

### Backend LLM (nuovo in v4)
`config.LLM_BACKEND` seleziona l'implementazione in `llm_client.py`:
- **`cli`** — `subprocess` della CLI `claude` (Claude Code). Costo marginale €0 (abbonamento). Funziona solo su macchina autenticata. Input via stdin (fix WinError 206, INC-004).
- **`sdk`** — `anthropic.Anthropic().messages.create(...)` con `temperature=0`, `max_tokens=2048`. Richiede `ANTHROPIC_API_KEY`. Deployabile ovunque; Commercial Terms (no-training).

Setup guidato: `python setup.py`. Config in `.env` (gitignored), template `.env.example`. `config.validate()` fa fail-fast all'avvio.

### Multi-model routing
| Task | Modello | Motivazione |
|---|---|---|
| Validazione input | Nessun LLM — heuristica Python | Deterministico |
| Redazione PII | Nessun LLM — spaCy + regex | Deterministico/locale, privacy |
| Rilevamento lingua | **LLM (`language_detected`)** | Restituito nell'output; gate applicato post-LLM (no `langdetect`, ERR-09) |
| Analisi contratto (7 categorie) | claude-sonnet-4-6 | Task critico |
| Retry JSON malformato / excerpt non groundato | claude-sonnet-4-6, prompt restrittivo | Non declassare |

### Parametri modello (path sdk)
```python
client.messages.create(
    model="claude-sonnet-4-6",
    max_tokens=2048,
    temperature=0,            # task tecnico: determinismo
    system=SYSTEM_PROMPT,
    messages=[{"role": "user", "content": contract_text_redacted}],
)
```
(Il path cli passa system prompt e modello alla CLI; i parametri di sampling seguono i default della CLI.)

### Token optimization / Green AI
- `max_tokens=2048`; testo troncato a 40.000 char; system prompt <600 token; nessuna history; temperature=0.
- Routing: validazione/redazione senza LLM. *(Nota v4: il gate lingua NON è più pre-LLM — il micro-risparmio token su lingue fuori perimetro non si applica, vedi ERR-09.)*

### Verifica anti-allucinazione `raw_excerpt`
Pydantic valida la forma, non l'esistenza della citazione. `excerpt_is_grounded()` (normalizzazione + fuzzy match SequenceMatcher, soglia 0.92) verifica ogni excerpt `present:true` contro il testo. Fallimento → retry "verbatim only", poi flag `excerpt_unverified`. Soglia 0.92 da calibrare in T11. **Limite:** copre solo `raw_excerpt`, non i numeri/qualificatori nei `plain_language` (mitigati via prompt, vedi patch v2.1).

### System Prompt
Struttura Ruolo → Task → Formato → Vincoli → Esclusioni. Il testo base è quello della v3.1 §6; in `prompts/system_prompt.md` è consolidato con le **patch v2/v2.1/v2.2**:
- **v2** — no ellissi `[...]` negli excerpt; no calcoli aritmetici nei plain_language.
- **v2.1** — grounding stretto plain_language↔raw_excerpt (numeri, %, riferimenti normativi, **qualificatori modali**); excerpt multi-span per clausole cross-article.
- **v2.2** — anti-speculazione, no inferenza giurisprudenziale, clausola positiva "if absent → say so".

```
You are a contract analysis assistant specialized in identifying risks for
non-lawyers: freelancers, self-employed professionals, and small business owners.

TASK
Analyze the provided contract text and extract critical information in exactly
7 categories. For each: present (bool), verbatim excerpt (or empty), plain-language
explanation in Italian (max 50 words), risk level (low|medium|high), one concrete
question to ask before signing (in Italian).

OUTPUT FORMAT
Return ONLY a valid JSON object matching the schema (language_detected, categories
{payment_terms, auto_renewal, penalties, liability_limitation, termination,
governing_law, intellectual_property}, top_3_risks).

CONSTRAINTS
- category absent → present:false, raw_excerpt:""
- risk_level ∈ {low, medium, high}
- plain_language in Italian, max 50 words; question_to_ask a direct question in Italian
- raw_excerpt verbatim (no paraphrase), ≥20 char quando present:true; no ellissi
- numeri/percentuali/qualificatori modali nei plain_language solo se presenti nel raw_excerpt

DO NOT
- Invent clauses; use legal jargon; advise sign/don't sign; speculate on case law;
  return anything outside the JSON object.
```

> Nota: il testo del prompt vive in `prompts/system_prompt.md` (source of truth operativo); lo schema `raw_excerpt: str | list[str]` riflette le clausole multi-span.

### Le 7 categorie
1. Termini di pagamento · 2. Rinnovo automatico · 3. Penali e ritardi · 4. Limitazione di responsabilità · 5. Recesso e disdetta · 6. Foro competente · 7. Proprietà intellettuale.

---

## 7. Dati, Privacy e Vincoli Normativi

### GDPR

**Data minimization (Art. 5) + privacy by design (Art. 25) — base della §3.bis.** La redazione PII pre-cloud è il meccanismo con cui SpecterAI rispetta questi articoli: al processor cloud arrivano solo dati minimi (testo pseudonimizzato). Verifica regolatoria (Perplexity P3, 2026-05-20): per un SMB italiano DPA + retention 7gg **da soli sono insufficienti** senza minimizzazione.

| Aspetto | Trattamento |
|---|---|
| Storage disco/DB | ❌ Mai (RAM per la durata della richiesta) |
| **PII verso il cloud** | ❌ **Mai in chiaro** — redatte prima dell'invio (§3.bis) |
| Logging contenuto | ❌ Solo timestamp, dimensione, esito, categoria errore |
| Retention | 0 lato SpecterAI; mapping PII scartato dal GC |
| Inoltro a terzi | Solo Anthropic (testo redatto) via HTTPS |

**Anthropic ToS:** path `sdk` usa le **Commercial Terms** — *"Anthropic may not train models on Customer Content from Services."* Retention log API 7 giorni (policy set-2025). DPA con SCC post-Schrems II + DPF da sottoscrivere pre-deploy pubblico. *(Path `cli`/Claude Code: percorso ToS da verificare separatamente prima di un deploy — è uno strumento di sviluppo, vedi ERR-08.)*

**Dato sensibile:** non autorizzato per dati art. 9 GDPR. Disclaimer pre-upload: "Non caricare contratti con dati sanitari, biometrici o di minori".

### AI Act — limited-risk (invariato da v3.1)
Non rientra in Annex III (amministrazione della giustizia = sistemi a uso di autorità giudiziarie). Soddisfa le 3 derogazioni Art. 6(3): task procedurale ristretto + miglioramento attività umana + pattern detection senza sostituzione. Obblighi residui (trasparenza Art. 50, etichettatura output AI, HITL, citazioni verificate) già implementati. Niente conformity assessment né registrazione UE.

### Green AI
`max_tokens` cap, troncamento 40k, no-LLM su validazione/redazione, stateless, temperature=0, retry max 1.

### Costi (distinti per backend, nuovo in v4)
| Backend | Costo marginale | Uso |
|---|---|---|
| **cli** (Claude Code) | **€0** (abbonamento) | Sviluppo, test, demo sul PC dello sviluppatore |
| **sdk** (API Anthropic) | **~0,04 €/analisi** (Sonnet: 3 $/M in, 15 $/M out) | Deploy / condivisione |

Scenari sdk: Demo ~0,20–0,40 € · Testing ~0,60 € · MVP pubblico 100/mese ~4 €/mese. Budget consegna corso (cli): trascurabile.

### Disclaimer (UI)
> SpecterAI è uno strumento di supporto alla lettura dei documenti. Le informazioni fornite non costituiscono consulenza legale. Prima di firmare qualsiasi contratto, consulta un professionista qualificato.

---

## 8. Validazione e Quality Control

### Metriche MVP
| Metrica | Soglia |
|---|---|
| Copertura 7 categorie | 100% |
| Latenza E2E (+redazione) | <30s (target <15s) |
| Schema compliance Pydantic | ≥95% al primo tentativo |
| Excerpt grounding | ≥90% primo tentativo, 100% post-retry |
| Recall categorie (gold-standard) | ≥0,80 medio |
| Precision categorie | ≥0,85 medio |
| Risk level agreement | ≥0,70 (kappa indicativo, N=35) o agreement % ≥0,75 |
| **Zero PII leak nel payload** | 100% (T15) |

### Test Plan eseguibile
Dataset: 5 contratti (C1 servizi IT, C2 locazione IT, C3 NDA IT, C4 MSA EN, C5 subappalto lungo IT).

| Test | Input | Criterio pass/fail |
|---|---|---|
| T1 | C1 | Schema valido + recall ≥0,80 |
| T2 | C2 | `auto_renewal.risk_level=="high"`; `penalties.present==true` |
| T3 | C3 | `intellectual_property.present==false`; `payment_terms.present==false` |
| T4 | C4 (EN) | `language_detected=="english"`; plain_language in italiano |
| T5 | C5 | Avviso troncamento + latenza <30s |
| T6 | PDF corrotto | Errore "non leggibile" (422) |
| T7 | PDF >10MB | Errore pre-elaborazione (413) |
| T8 | PDF password | Errore "rimuovi password" |
| T9 | PDF scansionato | Errore "solo PDF digitali" |
| T10 | Contratto tedesco | Blocco lingua (rilevato post-LLM, ERR-09) |
| T11 | C1 excerpt corrotto | Flag `excerpt_unverified` o retry (calibra soglia 0.92) |
| T12 | 3 run su C1 | risk_level identico ≥2/3 (temp=0) |
| **T13** | C-denso | grep speculazione = 0 (anti-speculazione patch v2.2) |
| **T14** | C1 | grounding plain_language ↔ raw_excerpt (numeri/qualificatori) |
| **T15** | C con PII reali | grep payload outbound = **0 occorrenze di PII reali** (privacy §3.bis) |

Esecuzione loggata in [[PROMPT_LOG]] + [[INCIDENTS]] su failure.

---

## 9. Gestione Errori e Fallback

| Errore | Comportamento |
|---|---|
| PDF non leggibile/corrotto | "Il file non è leggibile…" (422) |
| PDF >10MB | "Supera i 10MB" (413) |
| PDF password | "Rimuovi la protezione" |
| Testo <100 char | "Solo PDF digitali" |
| Testo >50.000 char | Troncamento 40k + avviso |
| MIME errato | "Formato non supportato" |
| Lingua non IT/EN | Blocco con messaggio (post-LLM) |
| **Filtro PII non disponibile** | **Fail-closed**: analisi rifiutata, mai PII in chiaro al cloud |
| Claude timeout (`subprocess timeout=300` / SDK error) | Retry, poi modalità degradata (503 + Retry-After) |
| JSON malformato / Pydantic error | Retry con prompt restrittivo, poi errore + log |
| `raw_excerpt` non verificato | Retry "verbatim only", poi flag `excerpt_unverified` |

**Modalità degradata (API down):** messaggio chiaro, nessun output parziale spacciato per analisi, 503 + `Retry-After`, log `claude_api_unavailable`. **Single point of failure dichiarato:** dipendenza da Anthropic (mitigazione multi-provider post-MVP; il backend configurabile §6 è il primo passo).

---

## 10. Deploy, Manutenzione e Aggiornamenti

### MVP (corso)
- `python setup.py` → sceglie backend, configura credenziali/modello spaCy
- `python -m uvicorn main:app --reload`
- `.env` (gitignored): `LLM_BACKEND`, `CLAUDE_MODEL`, `ANTHROPIC_API_KEY`

### Futuro (post-corso)
- Deploy Render/Railway con `LLM_BACKEND=sdk`
- Rate limiting, monitoring (Sentry)
- Provider fallback (Anthropic/Mistral Medium 3 — EU data residency, ~3,3× più economico, raccomandato post-MVP)

### Provider-agnostic
Logica di estrazione separata dall'LLM; il backend configurabile (cli/sdk) e il routing multi-modello rendono lo switch di provider <1h.

---

## 11. Rischi, Assunzioni e Checklist

### Assunzioni
- Freelance medio: 10-20 contratti/anno (non verificato empiricamente, §11.bis)
- Contratti target: PDF digitali standard
- Anthropic non traina su Customer Content API (Commercial ToS)
- Troncamento 40k copre le clausole critiche dei contratti standard

### Rischi
| Rischio | Prob. | Impatto | Mitigazione |
|---|---|---|---|
| PDF parsing su layout non standard | Media | Alto | Layer deterministico + errore chiaro |
| Claude allucina clausole | Bassa | Alto | Pydantic + grounding fuzzy + retry |
| **PII in chiaro al cloud** | Bassa | Alto | **Redazione §3.bis + fail-closed + T15** |
| Over-redaction (precision PII) | Media | Basso | Direzione sicura; INC-006 post-MVP |
| AI Act riclassificazione | Bassa | Medio | Limited-risk Art. 6(3) + HITL |
| Dipendenza single-vendor | Bassa | Alto | Backend configurabile + modalità degradata |
| Scope creep | Alta | Medio | Spec congelata, deviazioni in SPEC_ERRATA |
| Filtro PII non disponibile | Bassa | Medio | Fail-closed (mai degrade silenzioso) |

### Checklist pre-build
Invariata rispetto a v3.1 (tutte ✅) + nuove: [x] redazione PII pre-LLM · [x] backend configurabile · [x] test T15 zero-leak.

---

## 11.bis Limiti della validazione

**Validazione di mercato: 3/5.** Desk research su competitor, TAM (2,7M, Statista 2019), stress-test obiezioni. **Non fatto:** zero user interview, zero validazione problema col segmento, nessun smoke test WTP. L'angolo "nessun competitor nella sub-niche" è basato su *assenza di concorrenza*, non su *domanda dimostrata*. Roadmap validazione post-MVP: 5 interviste qualitative + landing smoke test + beta 10 utenti.

---

## 12. Documentazione di Progetto

| File | Scopo |
|---|---|
| [[PROMPT_LOG]] | Iterazioni prompt (v0→v1-final + patch v2/v2.1/v2.2) |
| [[INCIDENTS]] | Registro errori (INC-000a…INC-011) |
| [[SESSION_HANDOFF]] | Stato tra sessioni |
| [[SPEC_ERRATA]] | **Divergenze post-freeze v3.1 + driver della v4** |
| [[Privacy Filter Integration]] | Design e pivot del layer PII (§3.bis) |
| [[CODE_REVIEW_SPECTERAI_20260521]] | Review production-hardening (bug INC-007…011) |

---

## 12.bis Build Roadmap (stato)

| Lezione | Moduli | Stato |
|---|---|---|
| Lez. 3-4 | schemas/pdf/regex/privacy_filter + llm_client + main + templates | ✅ completo |
| Lez. 4 | config.py + setup.py (backend cli/sdk) | ✅ completo |
| Lez. 5 | Test plan §8 (T1-T15) + PROMPT_LOG/INCIDENTS con dati reali | ⏳ |
| Lez. 6 | Polish + eventuali stretch + demo | ⏳ |

---

## 13. Provenance & Versioning

| Versione | Data | Driver | Output principale |
|---|---|---|---|
| v1 | 2026-04-30 | Lezione 2 | 11 sezioni, stack, 7 categorie |
| v2 | 2026-05-04 | Re-analysis indipendente | +competitive, +prompt C.I.A.R.E., +few-shot, +Green AI, +routing |
| v3 / v3.1 | 2026-05-07 / 05-10 | Review + meta-review + Perplexity + feedback prof | +anti-allucinazione, +test plan, +AI Act limited-risk, +GDPR esteso, +modalità degradata |
| **v4** | **2026-05-29** | [[SPEC_ERRATA]] (divergenze build) + [[Privacy Filter Integration]] | **+§3.bis privacy-first (Art. 5/25), +backend cli/sdk, +gate lingua post-LLM, +T13/T14/T15, schema multi-span** |

**Principio:** ogni versione preservata, mai sovrascritta. La v3.1 resta leggibile per audit; SPEC_ERRATA documenta perché si è passati a v4.

---

## Connessioni
- [[Progettistica AI MOC]]
- [[Specifica Tecnica v3 - SpecterAI]] — v3.1, superata (congelata, preservata)
- [[SPEC_ERRATA]] · [[Privacy Filter Integration]] · [[CODE_REVIEW_SPECTERAI_20260521]]
- [[PROMPT_LOG]] · [[INCIDENTS]] · [[SESSION_HANDOFF]]
- [[Lezione 2 - Specifica Tecnica e Prompt Engineering]]
