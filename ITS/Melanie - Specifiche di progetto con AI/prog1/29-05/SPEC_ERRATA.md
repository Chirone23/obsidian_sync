# SPEC_ERRATA — SpecterAI

**Progetto:** SpecterAI — AI Contract Analyzer per Non-Avvocati
**Spec di riferimento congelata:** [[Specifica Tecnica v3 - SpecterAI]] v3.1 — confermata finale dalla prof (95/100)
**Data apertura errata:** 2026-05-29
**Test di verifica:** 2026-05-29 (codice canonico `prog1/specterai/specter-ai/` — 4 unit test pass, redazione PII reale zero-leak, app FastAPI carica)
**Esito:** ha portato alla **[[Specifica Tecnica v4 - SpecterAI]]** (2026-05-29) — vedi Parte 3.
**Stato:** documento vivo — registra le correzioni emerse **dopo** il congelamento della v3.1 e che hanno motivato la v4
**Corso:** AI Projects Development · [[Progettistica AI MOC]]

---

## Perché esiste questo file

Regola del corso ([[Lezione 2 - Specifica Tecnica e Prompt Engineering]], `Technical_Spec_Guide.md` §"La regola del congelamento"):

> Una specifica si scrive, si rivede, si congela. Dopo il congelamento, ogni modifica importante va registrata — in una nuova versione, **in `SPEC_ERRATA.md`**, o nel registro versioni. In contesti regolamentati, modificare la "Specifica Approvata" senza errata corrige le fa perdere valore.

La spec v3.1 è stata congelata e validata 95/100. **Non viene riaperta.** Tutto ciò che è cambiato in fase di building (Lez. 4-5) rispetto a quella versione è documentato qui, con motivazione e impatto. Questo file è il ponte onesto tra "spec approvata" e "codice realmente costruito".

---

## Parte 1 — L'evoluzione delle specifiche (v1 → v3)

Per capire *da dove vengono* le correzioni serve vedere la traiettoria. SpecterAI è passato da un'idea minima a un sistema indurito, e ogni salto è documentato nei changelog delle rispettive versioni.

### v1 (2026-05-04) — "Basta un system prompt + un LLM efficace"

La prima spec era essenzialmente: **prendi il PDF, manda il testo a Claude con un buon prompt, ricevi JSON, mostralo**. Il cuore era la combinazione *prompt strutturato + modello accurato*. Elementi presenti:

- 7 categorie di red flag, schema JSON fisso, disclaimer AI Act
- Pipeline lineare: PyMuPDF → regex date/importi → Claude → Jinja2
- GDPR trattato in 4 righe ("niente persistenza, Anthropic non traina su API")
- AI Act classificato genericamente **"potenzialmente alto rischio"**
- Anti-allucinazione = "Pydantic valida lo schema + excerpt obbligatorio"

**Limite implicito:** la qualità del sistema coincideva con la qualità del prompt. Nessuna difesa strutturale se il modello sbagliava nel merito (non solo nella forma).

### v2 (2026-05-04) — Il prompt diventa ingegneria

Driver: re-analisi critica indipendente (Haiku) → gap detectati. Changelog v1→v2:

- **Competitive positioning** (Mikeoss, Harvey, Legora) → la sub-niche difendibile
- **System prompt completo** in struttura C.I.A.R.E. (Ruolo → Task → Formato → Vincoli → Esclusioni)
- **Few-shot examples** (clausola presente / assente) per ancorare il formato
- **Multi-model routing** + parametri modello (temp=0, max_tokens=2048)
- **Green AI** / token optimization (troncamento 40k, no-LLM sui task deterministici)
- Edge case input formalizzati + sezione documentazione di progetto

Qui il progetto smette di essere "un prompt" e diventa **un sistema di prompting con regole, esempi e routing**. Ma la fiducia è ancora quasi tutta sull'LLM.

### v3 / v3.1 (2026-05-07 → 05-10) — Il sistema si indurisce

Driver: review interna + meta-review multi-agent + 4 query Perplexity di verifica fattuale + feedback prof + audit Opus. 18 modifiche. I salti concettuali che contano:

| Da (v1/v2) | A (v3.1) | Perché |
|---|---|---|
| "Pydantic valida lo schema" | **Verifica anti-allucinazione `raw_excerpt`** con normalizzazione + fuzzy match 0.92 (§6) | Pydantic valida la *forma*, non se la citazione *esiste* nel contratto. Single point of failure chiuso. |
| AI Act "alto rischio" | **Limited-risk via Art. 6(3)** (§7) | Verifica normativa: Annex III copre l'amministrazione della giustizia, non i tool di lettura per non-avvocati. Niente conformity assessment. |
| GDPR in 4 righe | **§7 GDPR esteso** — ToS Anthropic verbatim, retention 7gg, DPA pre-deploy, SCC post-Schrems II | Verifica fattuale Perplexity |
| Costo "<0,02 €/analisi" | **~0,04 €/analisi** ricalcolato per Sonnet + tabella scenari | v2 sottostimava (era pricing Haiku) |
| "Test con 5 contratti" | **Test plan eseguibile T1-T12** con criteri pass/fail + metriche precision/recall | Misurabilità |
| Lingua non IT/EN: "elabora con avviso" | **Gate bloccante IT/EN** | No fallback silenzioso su lingue non validate |

**Sintesi dell'arco:** da *"un prompt che parla a un LLM"* (v1) a *"un sistema che non si fida ciecamente dell'LLM"* (v3): verifica le citazioni, blocca gli input fuori perimetro, dichiara i limiti normativi, misura la qualità. Quello che la v3.1 **ancora non aveva** era la difesa sui **dati in uscita verso il cloud** — ed è esattamente ciò che il building ha aggiunto (vedi ERR-01).

> Nota: l'audit trail completo v1→v2→v3 è anche in [[Specifica Tecnica v3 - SpecterAI]] §13 (Provenance & Versioning). Qui sopra è la lettura "narrativa" del *perché* di ogni salto.

---

## Parte 2 — Errata corrige (divergenze codice ↔ spec v3.1 congelata)

Ogni voce: cosa dice la spec, cosa fa davvero il codice, perché, impatto. Le voci `ERR-0x` sono le modifiche post-congelamento da poter spiegare in sede di consegna.

### ERR-01 — Layer di redazione PII pre-LLM (la divergenza maggiore)

- **Spec v3.1:** lo stack (§4) **non prevede** alcun filtro PII. Il §7 garantisce "no logging del contenuto" ma il testo del contratto viene inviato a Anthropic **in chiaro**. Nessun `privacy_filter.py` esiste nella spec.
- **Codice reale:** esiste `privacy_filter.py` — un layer di **pseudonimizzazione** che redige nomi, indirizzi, email, telefoni, CF, P.IVA, IBAN **prima** della chiamata cloud, e ripristina i valori (`restore()`) sull'output. Map-back pattern, mapping solo in RAM.
- **Motivazione (regolatoria, non estetica):** verifica Perplexity P3 del 2026-05-20 → GDPR **Art. 5** (minimizzazione) + **Art. 25** (privacy by design) rendono la redazione PII pre-cloud-LLM *"mandatory in practice"* per un SMB italiano. DPA + retention 7gg **da soli sono insufficienti**. Framing corretto: **non è una feature aggiunta, è un gap di compliance GDPR chiuso** — gap che la v3.1 (forte su AI Act, debole su GDPR Art. 5/25) non copriva.
- **Trattamento spec:** addendum **§3.bis Privacy-First Architecture** post-v3.1, **senza riaprire** la spec congelata (la prof aveva detto "non c'è nulla da aggiungere"). Decisione e dettaglio in [[Privacy Filter Integration]].
- **Impatto:** architettura LLM invariata; aggiunto uno stadio pre/post; nuovo test **T15** (grep PII sul payload outbound = 0 occorrenze di valori reali).
- **✅ Verifica empirica 2026-05-29 — ERR-01 CONFERMATO.** Test su campione con PII italiane reali (nome, indirizzo, CF `RSSMRA80A01H501U`, P.IVA, IBAN, email, tel +39): **zero PII nel testo redatto** che andrebbe a Claude; `restore()` ricostruisce l'originale identico (roundtrip ✓). La pipeline `redact → call → restore` è in `llm_client.py:64-106`. **Caveat di qualità** (non leak, direzione sicura = over-redaction): spaCy etichetta male alcune entità (società "Acme S.r.l"→`[PER]`, parola "IBAN"→`[ORG]`, nome comune "CONSULENZA"→`[ORG]`) e un protocollo a 11 cifre ha passato il checksum Luhn finendo redatto come `[PIVA]`. Sporca il testo letto da Claude ma non espone dati. Tracciato in [[INCIDENTS]] INC-006.

### ERR-02 — Pivot tecnico del filtro: OPF → Hybrid (regex IT + spaCy), e modello `lg` → `sm`

- **Design iniziale (Privacy Filter Integration):** OpenAI Privacy Filter (OPF), modello 1.5B MoE.
- **Codice reale:** **stack Hybrid** — regex deterministico per PII strutturate italiane (CF, P.IVA con Luhn, IBAN con mod-97, email, telefono +39) + **spaCy** NER per nomi/luoghi.
- **Doppia correzione rispetto al doc di design:**
  1. **OPF abbandonato** dopo smoke test: ~55s di sola inferenza su 3KB → ~10 min per contratto medio, inaccettabile per UX MVP. OPF resta solo come PoC.
  2. Il doc indicava spaCy **`it_core_news_lg`**; il codice usa **`it_core_news_sm`**. Motivo: su Windows il modello `lg` (550MB) va in timeout, `sm` (13MB) è sufficiente per l'MVP. Vedi [[feedback_spacy_model]].
- **Impatto:** latency filtro <1s (vs ~10 min), 100% determinismo sulle PII strutturate, zero LLM nel filtro. Trade-off: coverage entità "fuzzy" inferiore.

### ERR-03 — Patch al system prompt v2 / v2.1 / v2.2 (vivono nel codice, non nella spec)

- **Spec v3.1:** il system prompt in §6 è il source-of-truth a livello testuale (versione v1-final).
- **Codice reale:** `prompts/system_prompt.md` contiene v1-final **+ tre blocchi di patch** definiti durante i test runtime #1-#8 su 8 contratti reali:
  - **v2** — no ellissi `[...]` nei `raw_excerpt` (brano contiguo); no calcoli aritmetici nei `plain_language` (numeri solo se verbatim).
  - **v2.1** — grounding stretto `plain_language` ↔ `raw_excerpt` (numeri, percentuali, riferimenti normativi, **qualificatori modali**); `raw_excerpt` come lista multi-span per categorie cross-article.
  - **v2.2** — anti-speculazione, no inferenza giurisprudenziale, clausola positiva "if absent → say so".
- **Motivazione:** pattern di drift rilevati empiricamente (es. drift semantico su qualificatore modale "risoluzione automatica" vs "potrà risolvere" — più pericoloso dei calcoli per un legal-AI). Dettaglio in [[PROMPT_LOG]].
- **Decisione consapevole:** la spec **non** è stata modificata; le patch vivono nel codice. Divergenza dichiarata qui.

### ERR-04 — Schema `raw_excerpt`: `str` → `str | list[str]`

- **Spec v3.1:** lo schema JSON in §6 mostra `raw_excerpt` come `str`.
- **Codice reale:** `schemas.py` accetta `raw_excerpt: str | list[str]` (multi-span).
- **Motivazione:** conseguenza di ERR-03 v2.1 — le clausole cross-article richiedono di citare più passaggi non contigui senza usare ellissi.

### ERR-05 — Gap del fuzzy match 0.92 (limite noto, da segnalare alla prof)

- **Spec v3.1 §6:** `excerpt_is_grounded()` verifica i `raw_excerpt` con fuzzy match a soglia 0.92.
- **Limite reale:** il check protegge **solo** `raw_excerpt`, **non** i `plain_language`. Numeri calcolati, percentuali e qualificatori modali nelle spiegazioni non sono coperti dal grounding automatico (mitigati solo via prompt, ERR-03 v2.1).
- **Stato:** edge-case post-MVP. Soglia 0.92 da calibrare empiricamente in **T11** e annotare in [[PROMPT_LOG]] (Q1 aperta).

### ERR-06 — Bug emersi nel codice ma assenti dalla spec (trovati e risolti)

Code review del 2026-05-21 ([[CODE_REVIEW_SPECTERAI_20260521]]). Tre bug **non** previsti da nessuna spec — propri dell'implementazione, non della definizione del sistema:

| Bug | Natura | Fix |
|---|---|---|
| Offset spaCy corrotti (placeholder annidati `[PER[CF_1]_2]`) | ordering NER vs regex | spaCy sul testo originale **prima**, regex **dopo** |
| Event loop FastAPI bloccato | `async def` con subprocess sincrono | `await asyncio.to_thread(analyze, ...)` |
| JSON parsing fragile | regex greedy sull'output LLM | `json.JSONDecoder().raw_decode()` |

Più 5 minori (backoff mancante, MAX_CHARS silenzioso, phone regex permissivo, modello hardcoded, disclaimer nel data model) — tutti risolti. **Tutti i fix B1-B5 + minori sono già applicati e committati** (sessione fix 2026-05-21). Verificato sul codice 2026-05-29: nessuna errata aperta lato bug. I 3 bug della tabella + i 2 di rete/concorrenza sono tracciati granularmente in [[INCIDENTS]] **INC-007…INC-011** (tutti ✅ Risolti 2026-05-21).

### ERR-07 — Test plan ampliato: +T13, +T14, +T15

- **Spec v3.1 §8:** test plan T1-T12.
- **Codice/processo reale:** aggiunti **T13** (grep speculazione = 0), **T14** (grounding `plain_language` ↔ `raw_excerpt`) post test runtime, e **T15** (grep PII sul payload outbound) come conseguenza di ERR-01.
- **Stato:** T13/T14/T15 ⏳ da eseguire formalmente in Lez. 5.

### ERR-08 — Chiamata a Claude via CLI subprocess, non via SDK Anthropic (trovato 2026-05-29)

- **Spec v3.1 §6:** i "Parametri modello" mostrano l'SDK Anthropic — `response = client.messages.create(model="claude-sonnet-4-6", max_tokens=2048, temperature=0, system=..., messages=[...])`.
- **Codice reale:** `llm_client.py:26-44` chiama `subprocess.run(["claude", "-p", "--system-prompt", ..., "--model", ..., "--output-format", "json"], input=user_message, timeout=300)` — usa la **CLI di Claude Code**, non l'SDK. Il pacchetto `anthropic==0.34.2` è in `requirements.txt` ma **non importato da nessun modulo**.
- **Motivazione (coerente con la spec stessa):** allinea il runtime alla strategia "dev workflow zero-cost" di §7 (Claude Code CLI invece dell'API a pagamento → costo marginale €0, no drift di modello). Decisione sensata, ma **non riflessa** nei §6/§7 testuali.
- **Conseguenze sui claim della spec da rivedere:**
  - §7 modello costi (~0,04 €/analisi su billing API) → via CLI il costo marginale è coperto dall'abbonamento, non dall'API a consumo.
  - §7 GDPR/ToS: il ragionamento "Commercial Terms API a pagamento → no training" va riverificato per il percorso Claude Code CLI (ToS potenzialmente diverso). Da chiarire prima di qualsiasi deploy.
  - `max_tokens`/`temperature=0` di §6 **non sono passati** alla CLI → i parametri di determinismo dichiarati non sono attivi come scritto.
- **Nota di coerenza:** [[INCIDENTS]] INC-004 afferma *"la spec §6 indica correttamente subprocess"* — **inesatto**: §6 mostra l'SDK. Corretto in INC-004.
- **✅ RISOLTO 2026-05-29 — backend configurabile.** Aggiunto `config.py` (`LLM_BACKEND` ∈ {cli, sdk}, caricamento `.env`) + switch in `llm_client.py` (`_call_cli` ↔ `_call_sdk`). Il path `sdk` usa l'SDK Anthropic con `temperature=0` e `max_tokens=2048` (i parametri §6 ora applicati davvero). Setup guidato in `setup.py`, template `.env.example`. Conseguenze risolte: **cli** = sviluppo/demo costo €0 (solo macchina autenticata); **sdk** = deploy/condivisione con API key e Commercial Terms (su cui regge il §7 GDPR). Allinea il codice a quanto la spec già prometteva in §7 (dev CLI / runtime API) e §10 (provider-agnostic).

### ERR-09 — Gate lingua: `langdetect` non implementato; rilevamento spostato post-LLM (trovato 2026-05-29)

- **Spec v3.1 §3/§4/§6:** rilevamento lingua deterministico con **`langdetect` (no LLM)** come **gate bloccante PRIMA** della chiamata a Claude — zero token spesi su lingue fuori perimetro (motivazione Green-AI §7).
- **Codice reale:** `langdetect` **non è** in `requirements.txt` né importato in alcun modulo. La lingua è letta da `analysis.language_detected` (output dell'LLM) e controllata **dopo** la chiamata (`llm_client.py:103-104`). Un contratto in lingua non IT/EN viene comunque inviato a Claude e rifiutato solo a posteriori.
- **Impatto:** il gate "blocca prima dell'API" e il relativo risparmio token (Green-AI, routing §6) **non valgono come scritti**. Funzionalmente il blocco esiste, ma a valle e a costo token già speso. Edge-case T10 (contratto in tedesco) va ri-testato su questo comportamento reale.
- **✅ DECISIONE 2026-05-29 (opzione B — allineare la spec al codice):** il gate lingua resta **post-LLM** (`language_detected` dall'output di Claude, controllo dopo la chiamata). Si **rinuncia** a `langdetect` e al claim Green-AI "blocco a costo zero token" per la lingua. Rettifiche da riportare:
  - §3 flusso: il rilevamento lingua **non** è uno step `langdetect` pre-call; la lingua è restituita dall'LLM e il gate IT/EN è applicato a valle (`llm_client.py:103-104`).
  - §4 stack + §6 routing: rimuovere `langdetect` dalla riga "rilevamento lingua"; il task è svolto dall'LLM stesso.
  - §7 Green-AI: il punto "task semplici → no LLM (lingua)" non si applica più al gate lingua; resta valido per validazione input/dimensione.
  - Test T10: il contratto non-IT/EN viene comunque inviato a Claude e **rifiutato a valle** — criterio pass/fail invariato (blocco con messaggio), meccanismo aggiornato.
- **Razionale della scelta:** lo schema JSON restituito da Claude già contiene `language_detected`; reintrodurre `langdetect` duplicherebbe una funzione con una dipendenza in più. Trade-off accettato: si perde il micro-risparmio token sul caso (raro) di lingua fuori perimetro, in cambio di codice più semplice e una sola fonte di verità sulla lingua.
- **Stato:** ✅ deciso (B). [[INCIDENTS]] INC-005 chiuso.

---

## Quadro sintetico

| ID | Divergenza | Impatto spec | Stato |
|---|---|---|---|
| ERR-01 | Layer redazione PII pre-LLM (§3.bis) | addendum GDPR Art. 5/25 | ✅ implementato |
| ERR-02 | Pivot OPF→Hybrid + spaCy `lg`→`sm` | nota tecnica | ✅ implementato |
| ERR-03 | Patch prompt v2/v2.1/v2.2 | divergenza dichiarata | ✅ nel codice |
| ERR-04 | `raw_excerpt: str → str\|list[str]` | nota schema | ✅ implementato |
| ERR-05 | Fuzzy 0.92 copre solo excerpt | limite noto | ⏳ calibrare T11 |
| ERR-06 | 3 bug codice non in spec + 5 minori | nessuno (impl.) | ✅ risolti |
| ERR-07 | +T13/T14/T15 | test plan esteso | ⏳ eseguire Lez. 5 |
| ERR-08 | Claude via CLI subprocess, non SDK | rivedere §6 + costi/GDPR §7 | ✅ risolto — backend configurabile (`config.py`/`setup.py`) |
| ERR-09 | `langdetect` gate non implementato (post-LLM) | rivedere §3/§6 + Green-AI §7 | ✅ deciso B — spec allineata al codice |

---

## Test di verifica eseguiti (2026-05-29)

| Test | Comando / metodo | Esito |
|---|---|---|
| Unit test filtro privacy | `pytest tests/test_privacy_filter.py` | ✅ 4/4 pass |
| Redazione PII reale (zero-leak) | `redact()` su campione con CF/P.IVA/IBAN/email/tel/nome/indirizzo | ✅ 0 PII nel payload |
| Roundtrip `redact`→`restore` | confronto stringa | ✅ identico |
| Avvio app | `import main` (FastAPI) | ✅ carica |
| Dipendenze core | import fastapi/pymupdf/anthropic/pydantic/jinja2 | ✅ (langdetect assente → ERR-09; anthropic presente ma inutilizzato → ERR-08) |
| spaCy `it_core_news_sm` | `spacy.load` | ✅ caricato |

> Non eseguito in questa sessione: E2E completo via `uvicorn` + PDF reale con chiamata Claude (richiede run interattivo). Già coperto 7/8 PDF nella sessione 2026-05-20 (vedi [[SESSION_HANDOFF]]).

---

## Parte 3 — Esito: cambio di rotta e promozione a v4 (2026-05-29)

Le voci ERR-01…ERR-09 sopra erano partite come *errata corrige* della v3.1 congelata. Analizzandole nel complesso è emerso che **non sono tutte semplici correzioni**: due di esse (ERR-01 e ERR-08) modificano l'**architettura** del sistema, non solo dettagli implementativi. Questo ha motivato la decisione di **aprire la [[Specifica Tecnica v4 - SpecterAI]]** invece di restare su errata.

### Perché un'errata non bastava più

La regola del corso prevede, dopo il congelamento, *nuova versione* **oppure** *SPEC_ERRATA*. La soglia per scegliere la nuova versione è: **il documento congelato non descrive più il sistema reale.** Eravamo a quel punto:

| Cosa è cambiato | Natura | Sezioni spec toccate |
|---|---|---|
| **ERR-01 — Layer redazione PII pre-LLM** | **Architetturale**: stadio nuovo e obbligatorio nel data flow. Da `estrai → LLM → mostra` a `estrai → redigi PII → LLM (solo redatto) → ripristina → mostra`. | §3 flusso, §5 diagramma, §7 GDPR Art. 5/25, §4 stack (spaCy) |
| **ERR-08 — Backend LLM configurabile** | **Architetturale**: cambia l'integrazione col modello (CLI/SDK) e il modello costi/ToS. | §4, §6, §7, §10 |
| ERR-09 — gate lingua post-LLM | Rettifica del flusso | §3, §4, §6 |
| ERR-03/04 — patch prompt + schema multi-span | Comportamento/schema | §6 |
| ERR-07 — +T13/T14/T15 | Test | §8 |

Un lettore della sola v3.1 si sarebbe fatto un'idea **materialmente sbagliata** del sistema (non avrebbe saputo della redazione PII né del backend configurabile). Questa è la definizione operativa di "serve una nuova versione".

### Cosa NON è cambiato (perché è una v4, non un nuovo progetto)

Missione, utenti target, le 7 categorie, l'output plain-language, il posizionamento AI Act limited-risk: **identici**. È cambiato il *come* (data flow + compliance + integrazione LLM), non il *cosa*. Per questo è un avanzamento di versione, non un pivot di prodotto.

### Ruolo di questo file rispetto alla v4

SPEC_ERRATA **non è superato dalla v4**: ne è il *driver documentato*. Come le Review-docs hanno guidato la v3, queste 9 voci hanno guidato la v4. Resta la traccia di *come e perché* si è passati da v3.1 a v4 — audit trail richiesto dal corso. La v3.1 resta congelata e preservata; la v4 è la versione corrente di riferimento.

### Azione verso la docente (da Privacy Filter Integration, step B)

La v4 è motivata da un **gap di compliance GDPR (Art. 5/25)**, non da gold-plating. Da comunicare alla prof: *"v3.1 confermata; in fase di build è emerso che la redazione PII pre-cloud è requisito GDPR per SMB italiano (DPA+7gg insufficienti). Ho promosso il cambiamento architetturale a v4, mantenendo v3.1 congelata e tracciando ogni delta in SPEC_ERRATA."*

---

## Connessioni

- [[Specifica Tecnica v4 - SpecterAI]] — **versione corrente** (esito di questo file)
- [[Specifica Tecnica v3 - SpecterAI]] — spec congelata 95/100 (baseline precedente)
- [[Specifica Tecnica v1 - SpecterAI]] · [[Specifica Tecnica v2 - SpecterAI]] — versioni precedenti (audit evoluzione)
- [[Privacy Filter Integration]] — design e pivot del layer PII (ERR-01, ERR-02)
- [[PROMPT_LOG]] — patch prompt v2/v2.1/v2.2 (ERR-03), calibrazione 0.92 (ERR-05)
- [[CODE_REVIEW_SPECTERAI_20260521]] — bug impl. (ERR-06)
- [[INCIDENTS]] · [[SESSION_HANDOFF]]
- [[Progettistica AI MOC]]
