# INCIDENTS — SpecterAI

**Progetto:** SpecterAI (AI Contract Analyzer per Non-Avvocati in Italiano)  
**Data inizio:** 2026-04-28  
**Data prima sessione:** 2026-05-07  

---

## Tabella Incidents

| ID | Data | Componente | Descrizione breve | Severità | Status |
|---|---|---|---|---|---|
| **INC-000a** | **2026-04-28** | **PDF tool (ricerca)** | **PDF extraction tool failure on course materials** | **Medium** | **✅ RESOLVED** |
| **INC-000b** | **2026-04-29** | **MCP obsidian** | **Obsidian MCP connection refused on vault write** | **Medium** | **✅ RESOLVED** |
| **INC-000c** | **2026-05-02** | **AI methodology** | **AI prompting approach (directed vs independent)** | **Methodology** | **✅ RESOLVED** |
| **INC-000d** | **2026-05-07** | **Spec — AI Act classification** | **Sovraclassificazione AI Act (high-risk → limited-risk)** | **High (compliance)** | **✅ RESOLVED** |
| **INC-000e** | **2026-05-07** | **Spec — cost estimate** | **Sottostima costo Sonnet (~0,02 → ~0,04 €/analisi)** | **Medium** | **✅ RESOLVED** |
| **INC-000f** | **2026-05-10** | **Spec — Anthropic retention drift** | **Retention log API 30gg obsoleta — policy aggiornata a 7gg da set 2025** | **Low (fact drift)** | **✅ RESOLVED** |
| **INC-000g** | **2026-05-11** | **Test data — Perplexity free ricerca contratti** | **8/10 PDF trovati (1/2 servizi e locazione, 2/2 altre 3 tipologie) + 2 citazioni allucinate (wikipedia) + 1 falso negativo (URL "da verificare" valido)** | **Low (test data quality)** | **✅ RESOLVED** |
| INC-001 | 2026-05-12 (Lez. 3) | PyMuPDF text extraction | PDF parsing errors on scanned/complex PDFs | Critical | 🔴 Open |
| INC-002 | 2026-05-13 (Lez. 4) | Claude API timeout | Timeout su batch processing di contratti | High | 🔴 Open |
| INC-003 | 2026-05-13 (Lez. 4) | JSON parsing / encoding | Caratteri speciali italiani (accenti, €) corrotti nel JSON | High | 🔴 Open |
| **INC-004** | **2026-05-20** | **llm_client.py — subprocess argv** | **WinError 206 su Windows con testi >40k char passati come argv** | **High** | **✅ RESOLVED** |
| INC-005 | 2026-05-29 | Gate lingua — `langdetect` | Gate lingua non implementato come da spec: rilevamento post-LLM invece che pre-call deterministico | Medium | ✅ RESOLVED (decisione B) |
| INC-006 | 2026-05-29 | privacy_filter.py — spaCy NER | Over-redaction e label errate (società→PER, nome comune→ORG, protocollo→PIVA via Luhn) | Low | 🟡 Open (qualità) |
| **INC-007** | **2026-05-21** | **privacy_filter.py — CF regex falso positivo** | **CF con `re.IGNORECASE` globale cattura sequenze lowercase → clausola legale redatta come `[CF_1]`** | **High** | **✅ RESOLVED** |
| **INC-008** | **2026-05-21** | **privacy_filter.py — PIVA `\d{11}` senza Luhn/context** | **Pattern `\b\d{11}\b` matcha importi, protocolli, CIG → perdita dati legittimi nel payload** | **High** | **✅ RESOLVED** |
| **INC-009** | **2026-05-21** | **privacy_filter.py — Ordering bug spaCy offset** | **Offset spaCy pre-regex applicati post-regex → placeholder corrotti `[PER[CF_1]_2]`** | **Critical** | **✅ RESOLVED** |
| **INC-010** | **2026-05-21** | **llm_client.py — subprocess senza timeout** | **`subprocess.run()` senza `timeout=` → hang indefinito, DoS su worker uvicorn** | **High** | **✅ RESOLVED** |
| **INC-011** | **2026-05-21** | **main.py — async def con subprocess sincrono** | **Endpoint `async def` con subprocess sync blocca event loop, zero concorrenza** | **High** | **✅ RESOLVED** |
| **INC-012** | **2026-06-04** | **main.py — `async for chunk in file` su UploadFile** | **`UploadFile` non è async-iterabile → `TypeError` → 500 su OGNI upload dalla web UI. Primo E2E reale via browser mai funzionato.** | **Critical** | **✅ RESOLVED** |
| **INC-013** | **2026-06-24** | **llm_client.py — extended thinking CLI** | **Analisi ~163s per ~1700 token di ragionamento nascosto del CLI. Disattivato (`MAX_THINKING_TOKENS=0`) → 13s (12×).** | **High (latenza)** | **✅ RESOLVED** |
| INC-001 | 2026-05-12 (Lez. 3) | PyMuPDF text extraction | PDF parsing errors on scanned/complex PDFs | Critical | 🟡 Mitigato (detector 2026-06-24, fix OCR in roadmap) |
| INC-006 | 2026-05-29 | privacy_filter.py — spaCy NER | Over-redaction (nuova occorrenza 2026-06-24: foro "Roma" oscurato → prosa/citazione contraddittorie nel co.co.co.) | Low | 🟡 Open (qualità) |

---

## INC-000a — PDF Tool Failure (Research Phase)

**Data:** 2026-04-28 (durante Lezione 1 — Course material extraction)

**Componente:** PDF processing tool (ricerca, non code production)

**Descrizione:** Durante l'estrazione di contenuti da PDF del corso (2 file grandi: "Lezione 1 - Case Study e Setup.md" e "Lezione 2 - Specifica Tecnica e Prompt Engineering.md"), il tool Read non poteva estrarre testo direttamente. Errore: `pdftoppm` not available in environment.

**Severità:** Medium (blocca ricerca, ma non production code)

**Root cause:** 
- Tool Read non supporta PDF natively (richiede conversione a testo)
- Environment non ha librerie sistema (pdftoppm, poppler) installate

**Soluzione:** 
- Pivot a PyPDF2 via Bash command (`python -c "import PyPDF2..."`)
- Estrazione in batch dei 2 file PDF in parallelo via Bash
- Contenuto poi processato per MOC creation

**Lezioni apprese:**
- ✅ PyPDF2 è più portable di system tools (pdftoppm)
- ✅ Bash parallelization utile per multi-file extraction
- ✅ "Text extraction" è component critico — mai assumerlo scontato

**Aggiornamenti Specifica:** 
- Specifica v1 §4 (Stack Tecnologico): Confermato PyMuPDF come PDF parser per production (più veloce e affidabile di PyPDF2)

**Status:** ✅ Resolved — uso PyMuPDF per MVP production

---

## INC-000b — MCP Obsidian Connection Issues

**Data:** 2026-04-29 (durante Lezione 2 — Vault writing)

**Componente:** MCP obsidian-mcp integration

**Descrizione:** Quando ho tentato di scrivere le prime note nel vault Obsidian (Brainstorming - Validazione Idea.md, Contract Analyzer - Validazione Idea.md), MCP obsidian-mcp ha restituito "connection refused" errors.

Sintomo: `obsidian-mcp is not connected` quando esecuzione `ReadMcpResourceTool` per letture/scritture.

**Severità:** Medium (blocca vault documentation, ma non project progress)

**Root cause:** 
- MCP connection timeout dopo session inactivity
- Obsidian desktop app non era in sync con MCP server
- Possibile token expiration

**Soluzione:**
- Attesa e retry automatico (MCP reconectò entro 2-3 minuti)
- Una volta riconnesso, scritture funzionarono normalmente
- Nessun codice cambiato, solo timing issue

**Lezioni apprese:**
- ✅ MCP connections sono ephemeral — OK aspettare retry
- ✅ Vault sync è async — non assumerlo istantaneo
- ✅ Per file importanti (.md), verificare sync con `git status` dopo write

**Aggiornamenti Specifica:** 
- Nessuno (issue era infrastructure, non project design)

**Status:** ✅ Resolved — MCP funziona ora normalmente

---

## INC-000c — AI Prompting Methodology (Design Decision)

**Data:** 2026-05-02 (durante Specifica v2 preparation)

**Componente:** AI supervision methodology

**Descrizione:** Quando ho preparato Specifica v2, ho proposto una lista di 5 improvement gap per la v1 (Competitive Positioning, Full prompt text, Few-shot examples, Green AI, Multi-model routing) e l'ho data a Haiku da implementare.

User feedback: "assolutamente non va bene deve rianalizzare lui i file e trovare i problemi non glieli devi dare tu" (unacceptable — AI must re-analyze files independently, not be told what to look for).

**Severità:** Methodology / Process (non technical bug)

**Root cause:** 
- ❌ **Approccio sbagliato:** Directing AI su cosa cercare riduce qualità di analisi
- ❌ **Principio violato:** Pilastro III corso richiede "Supervisione umana critica", non "Direzione umana di cosa fare"
- ✅ **Corrizione:** Haiku ha fatto independent re-analysis e trovato gaps autonomamente

**Soluzione:**
- Fresh sessione con Haiku, solo briefing del contesto (no suggestions)
- Haiku ha identified 5 gaps + dimensione delle modifiche (full prompt era 250 lines)
- Alcuni gaps overlap con mio elenco (good sign), altri sono diversi (value add)

**Lezioni apprese:**
- ✅ Independent AI analysis è **più accurato** di directed analysis
- ✅ "Don't tell AI what to look for, give it context e let it explore"
- ✅ Supervision ≠ Direction — differenza critica
- ✅ Fresh session senza prior "suggestions" genera better insights

**Aggiornamenti Specifica:** 
- No technical changes to spec, ma PROCESS lesson learned
- Documented in PROMPT_LOG.md as "User feedback on design methodology"

**Status:** ✅ Resolved — Methodology corrected, Specifica v2 completed with independent Haiku analysis

---

## INC-000d — Sovraclassificazione AI Act (high-risk → limited-risk)

**Data:** 2026-05-07 (durante review Spec v2 + verifica Perplexity)

**Componente:** Specifica §1, §7, §11 — posizionamento normativo AI Act

**Descrizione:** Spec v1 e v2 classificavano SpecterAI come "AI Act high-risk decision-support" basandosi su un'interpretazione conservativa: "sistema legal-AI = automaticamente Annex III high-risk". Questa classificazione comportava (se non corretta): obbligo di conformity assessment, registrazione database UE, quality management system formale → carico compliance enorme per un MVP.

**Severità:** High (compliance) — non blocca il prototipo, ma se la spec fosse stata applicata in produzione avrebbe imposto obblighi non dovuti, con costi e tempistiche elevati.

**Root cause:** classificazione fatta "by analogy" senza leggere il testo dell'Annex III e dell'Art. 6(3). Annex III voce (5) copre "amministrazione della giustizia" ma è ristretto a sistemi a uso di **autorità giudiziarie** (giudici, pubblici ministeri, tribunali) per interpretazione del diritto, valutazione prove, ricerca giurisprudenziale per decisioni vincolanti. Un tool di **lettura contrattuale per non-avvocati** non rientra. Inoltre Art. 6(3) prevede 3 derogazioni esplicite (task procedurale ristretto + miglioramento attività umana + pattern detection senza sostituzione): SpecterAI le soddisfa tutte e tre.

**Soluzione:** verifica Perplexity su artificialintelligenceact.eu, EDPS guidance 2025, linee guida Commissione Art. 6(5) di feb 2026 → riclassificato come **limited-risk** con soli obblighi di trasparenza Art. 50 (disclaimer, già implementato). Niente conformity assessment, niente registrazione UE.

**Lezioni apprese:**
- ✅ Classificazioni normative vanno verificate sui testi originali, non per analogia
- ✅ "Conservative-by-default" su compliance può essere costoso quanto under-classification
- ✅ Verifica fattuale via Perplexity con citazione fonti = pattern critico per claim regolatori
- ✅ Documentare la riclassificazione esplicitamente in changelog (audit trail per la prof e per future revisioni)

**Aggiornamenti Specifica:** Spec v3 §1 + §7 + §11 + §13 changelog riga #5.bis (riclassificazione + motivazione tripla).

**Status:** ✅ Resolved 2026-05-07

---

## INC-000e — Sottostima costo Sonnet (~0,02 → ~0,04 €/analisi)

**Data:** 2026-05-07 (durante meta-review + verifica Perplexity pricing)

**Componente:** Specifica §7 — Stima costi e scenari

**Descrizione:** Spec v2 dichiarava "<0,02 €/analisi" come stima costo runtime. La cifra era plausibile per modelli di fascia bassa (Haiku) ma **non per Sonnet** (modello effettivamente scelto in §6 multi-model routing). Discrepanza tra modello scelto e prezzo dichiarato.

**Severità:** Medium (non blocca il prototipo per il corso — budget piccolo — ma compromette credibilità della stima per scenari MVP pubblico 100-1000 analisi/mese).

**Root cause:** numero "ricordato" da casi d'uso Haiku passati, non ricalcolato per Sonnet. Verifica pricing ufficiale non eseguita prima della stima v2.

**Soluzione:** verifica Perplexity 2026-05-07 su platform.claude.com/docs/en/about-claude/pricing → Sonnet 4.6: 3,00 $/M input, 15,00 $/M output. Ricalcolo con consumo tipico (4.600-6.100 token input + 1.500-2.000 output): media ~0,04 €/analisi (range 0,033-0,044 €). Aggiornata tabella scenari Demo / Testing / MVP pubblico in §7. Budget consegna ricalcolato: <2 € (margine ampio).

**Lezioni apprese:**
- ✅ Stime di costo vanno verificate sul listino vigente al momento della spec, mai a memoria
- ✅ Coerenza interna spec: il modello scelto in §6 deve essere quello prezzato in §7
- ✅ Ricalcolo per scenari (Demo / Testing / MVP) rende esplicito il delta a volume

**Aggiornamenti Specifica:** Spec v3 §7 tabella scenari + §13 changelog riga #4.

**Status:** ✅ Resolved 2026-05-07

---

## INC-000f — Anthropic retention drift (30gg → 7gg)

**Data:** 2026-05-10 (durante review Perplexity di validazione Spec v3)

**Componente:** Specifica §7 — GDPR / Anthropic ToS

**Descrizione:** Spec v3 (versione 2026-05-07) dichiarava retention log API Anthropic = 30 giorni. La policy era stata aggiornata a **7 giorni a partire da settembre 2025** — fact drift di 8 mesi non rilevato nella verifica Perplexity originale del 2026-05-07.

**Severità:** Low (fact drift, no compliance impact diretto — la retention più breve è *favorevole* per GDPR, non sfavorevole). Comunque incoerenza che indebolisce credibilità della spec.

**Root cause:** la verifica Perplexity originale (2026-05-07) ha citato la policy "storica" senza beccare l'update. Probabile causa: pagina policy non indicizzata recentemente o citation di un riferimento secondario.

**Soluzione:** review Perplexity di validazione finale (2026-05-10, sessione fresh isolata) ha rilevato il drift incrociando privacy.claude.com con char.com/blog/anthropic-data-retention-policy. Aggiornata spec v3 §7 con policy corrente (7gg) + nota esplicita sul cambio settembre 2025.

**Lezioni apprese:**
- ✅ **Conversazione Perplexity fresh per la validazione finale:** una sessione "calda" sui fix avrebbe probabilmente confermato per inerzia il numero originale
- ✅ I claim su policy esterne (ToS, retention, pricing) hanno scadenza implicita — vanno re-verificati a ogni iterazione spec
- ✅ Pattern: ogni numero/percentuale/policy citata deve avere data di verifica nel testo, così è ovvio quando è invecchiata

**Aggiornamenti Specifica:** Spec v3.1 §7 + §13 changelog riga #14.

**Status:** ✅ Resolved 2026-05-10

---

## INC-000g — Perplexity free: copertura parziale e allucinazioni nella ricerca contratti

**Data:** 2026-05-11 (durante setup test data — checklist pre-building Lez. 4)

**Componente:** Raccolta contratti reali per `tests/contratti/` via Perplexity free.

**Descrizione:** Eseguite 5 query (1 per tipologia: servizi, NDA, fornitura, co.co.co., locazione) chiedendo 2 PDF a query → 10 PDF target totali. Risultato effettivo: **8/10**.

Breakdown:
- Servizi: 1/2 (solo Agenzia del Demanio 2021, tutte e 7 le clausole)
- NDA: 2/2 (Polimi + Unitelma Sapienza)
- Fornitura: 2/2 (Consip/Comune Roma + Consiglio di Stato — il secondo con estrazione clausole parziale)
- Co.co.co.: 2/2 (Sapienza + ERSU Messina)
- Locazione: 1/2 (solo INPS gara 2026 — Perplexity inizialmente ha dichiarato 0 risultati, ma il link marcato "URL da verificare" si è rivelato valido alla verifica manuale)

In più, 2 allucinazioni di citazione: `it.wikipedia.org/wiki/Contratto_reale` inserita come fonte legale nelle citazioni di query 1 e 4. Non sono PDF e non sono fonti legali — rumore di ricerca.

**Severità:** Low (test data quality — non blocca, 8 PDF sono sufficienti per coprire il minimo della checklist di 3 contratti e tutte le 5 tipologie del test plan Fase 4).

**Root cause:**
- Perplexity free usa ricerca web shallow senza Sonar Pro → meno copertura, citazioni meno selettive.
- Falso negativo locazione: il modello ha applicato un giudizio conservativo sul proprio output ("URL da verificare") senza realmente fallire il fetch.
- Allucinazioni wikipedia: pattern noto di "padding" delle bibliografie quando il dominio della query è giuridico.

**Soluzione:**
- Verifica manuale di tutti gli URL "candidato PDF" cliccando uno per uno → 100% di tasso di validità.
- Ignorate le citazioni numerate verso wikipedia, blog generalisti, siti immobiliare/idealista.
- 8 PDF scaricati in `prog1/specterai/contratti di prova/` (staging) — verranno copiati e rinominati in `prog1/specterai/tests/contratti/` durante la creazione struttura progetto (Lez. 4).

**Lezioni apprese:**
- ✅ Su Perplexity free, splittare in N query brevi (1 per intento) batte 1 query massiva: meno allucinazioni, meno deriva.
- ✅ Mai fidarsi del *self-assessment* di Perplexity sulla validità dei propri URL — verificare a mano è 30 secondi per link.
- ✅ Le citazioni numerate `[^N_X]` sono spesso rumore: contano solo i link nominalmente associati a "PDF 1" / "PDF 2" nel corpo della risposta.
- ✅ Per tipologie a basso indice web pubblico (es. locazione PA), aspettarsi 0-1 hit anche con prompt ben strutturato → preparare fallback Google `site:` come piano B.

**Aggiornamenti Specifica:** nessuno (non impatta architettura — solo qualità dataset di test).

**Status:** ✅ Resolved 2026-05-11 — 8 PDF coprono tutte e 5 le tipologie. Locazione ha solo 1 esemplare invece di 2: accettabile per il test plan (Fase 4 richiede 1 contratto per tipologia, non 2).

---

## INC-001 — PyMuPDF Text Extraction

**Data:** 2026-05-05 (Expected — Fase 2 PDF Input Layer)

**Componente:** PDF parsing layer (PyMuPDF)

**Descrizione:** Durante MVP testing, PyMuPDF potrebbe non estrarre correttamente testo da:
- PDF scansionati (OCR-required)
- PDF con form fields nascosti o non compilati
- PDF con encoding non-standard (font personalizzati)
- PDF multi-colonna con layout complesso
- PDF protetti da password

**Severità:** Critical (blocca core functionality, utenti non possono caricare interi file)

**Expected Root cause:** 
- PyMuPDF non supporta OCR natively — scansioni = immagini grezze = no text layer
- Encoding personalizzati potrebbero avere mappature Unicode non standard
- PDF protetti richiedono password per parsing

**Potential Soluzione:** 
- **Opzione A:** Fallback a Tesseract OCR per PDF scansionati
- **Opzione B:** Integrazione pdfplumber (hybrid extraction)
- **Opzione C:** Reject scanned PDFs early con messaggio chiaro

**Testing plan quando si verifica:**
1. Load 5 real contratti: scansionati, formato-locked, protetti
2. Misura accuracy text extraction vs manual reference
3. Benchmark PyMuPDF vs pdfplumber vs hybrid approach
4. Document supported/unsupported in Specifica v2

**Lezioni apprese:** [TBD — pending discovery]

**Aggiornamenti Specifica:** [Dipende dalla soluzione scelta]

**Status:** 🔴 Open (pending MVP Fase 2 testing)

---

## INC-002 — Claude API Rate Limits & Timeouts

**Data:** 2026-05-05 (Expected — Fase 4 Claude Integration)

**Componente:** Claude API integration layer

**Descrizione:** Durante MVP testing con batch di contratti, possibile raggiungimento rate limit API (429 Too Many Requests) se si invia > N richieste/min. Claude API timeout su contratti lunghi (>40K chars even after truncation).

**Severità:** High (blocca parallelizzazione, impacta UX su multi-document scenarios)

**Expected Root cause:** 
- Anthropic rate limits dipendono da tier (free/pro/enterprise)
- MVP non ha queueing o throttling — invia richieste in parallelo
- Mancano retry automatici con exponential backoff

**Potential Soluzione:**
- **Opzione A:** Queue-based queueing con throttling (1-2 req/sec)
- **Opzione B:** Batch API di Claude (se available in tier)
- **Opzione C:** Sequential processing (lento, ma safe)

**Testing plan quando si verifica:**
1. Load 10+ contratti in parallelo
2. Misura latency per contratto
3. Benchmark: sequential vs batched vs throttled
4. Define SLA (es: 30 sec per 5 contratti)

**Lezioni apprese:** [TBD — pending discovery]

**Aggiornamenti Specifica:** [Dipende da rate limit discovery]

**Status:** 🔴 Open (pending MVP Fase 4 testing)

---

## INC-003 — JSON Encoding (Caratteri Italiani)

**Data:** 2026-05-05 (Expected — Fase 5 Output validation)

**Componente:** JSON serialization layer

**Descrizione:** Durante MVP testing output, caratteri speciali italiani (é, à, ü, €, °) potrebbero corruttarsi o non visualizzarsi nel JSON finale.

Sintomo atteso: "explanation": "Clausola con limite di danno (massimo € corrotto)" oppure Unicode escape sequences non parsate.

**Severità:** High (degradazione UX per utenti italiani, output non leggibile)

**Expected Root cause:** 
- JSON encoder potrebbe usare ASCII safe encoding (escape Unicode characters)
- Client-side frontend potrebbe non dichiarare UTF-8 content-type
- Database storage potrebbe troncate multi-byte Unicode se collation non UTF-8MB4

**Potential Soluzione:**
- **Opzione A (Quickest):** `json.dumps(data, ensure_ascii=False)` — native UTF-8
- **Opzione B:** Assicurare Claude API returns UTF-8 (default per modern APIs)
- **Opzione C:** Database collation UTF-8MB4 se storage aggiunto post-MVP

**Testing plan quando si verifica:**
1. Generate output intenzionalmente con accenti italiani
2. Verifica JSON parsing (valid JSON?)
3. Verifica frontend rendering (leggibile?)
4. Test database roundtrip (se storage necessario)

**Lezioni apprese:** [TBD — pending discovery]

**Aggiornamenti Specifica:** [Dipende da soluzione scelta]

**Status:** 🔴 Open (pending MVP Fase 5 testing)

---

## INC-004 — WinError 206 su subprocess argv (testi lunghi)

**Data:** 2026-05-20

**Componente:** `llm_client.py` — `_call_claude()` via Claude Code CLI subprocess

**Descrizione:** Su Windows, i contratti con testo estratto vicino al limite di troncamento (40k char) causavano `WinError 206: The filename or extension is too long` quando il messaggio utente veniva passato come argomento argv al subprocess Claude CLI. Il comando `["claude", "-p", user_message, ...]` falliva silenziosamente per testi lunghi. Contratti affetti: Consip Condizioni Generali Fornitura, Capitolato Tecnico Demanio (i 2 PDF più lunghi della suite). I 5 contratti brevi passavano correttamente.

**Severità:** High (blocca analisi di contratti densi/lunghi — esattamente quelli più critici per un contract analyzer)

**Root cause:** Windows ha un limite di lunghezza sulla riga di comando (MAX_PATH / CreateProcess). Testi di 40k char superano ampiamente questo limite quando inlineati come singolo argomento argv.

**Soluzione:** Spostato `user_message` da argv a `input=` (stdin) nel `subprocess.run()`. Fix 2 righe:
- rimosso `user_message` dalla lista argv
- aggiunto `input=user_message` come parametro named

Il fix è trasparente: Claude CLI legge da stdin quando `--print` (`-p`) è attivo e nessun messaggio è passato come argv.

**Fix verificato:**
- Consip Condizioni Generali (40k char) → 268s, `language: italian`, 7/7 categorie parse OK
- Capitolato Tecnico Demanio (40k char) → 163s, `language: italian`, 5/7 categorie (atteso: appalto pubblico senza IP/governing_law esplicita)

**Lezioni apprese:**
- ✅ Su Windows, mai passare payload larghi come argv — usare sempre stdin
- ✅ Il limite Windows per argv è ~32k caratteri (limite variabile per processo); per sicurezza, qualsiasi input >1k char va in stdin
- ✅ L'errore WinError 206 è fuorviante (sembra un filesystem error) — mapparlo a "payload argv troppo lungo"

**Aggiornamenti Specifica:** Nessuno (fix implementativo, non architetturale). ⚠️ **Correzione 2026-05-29:** la nota originale affermava *"la spec §6 indica correttamente subprocess"* — **inesatto**: la spec §6 mostra l'SDK Anthropic (`client.messages.create`), non subprocess. La divergenza SDK→CLI è ora tracciata in [[SPEC_ERRATA]] ERR-08.

**Status:** ✅ Resolved 2026-05-20 — commit `d2dc4ba`

---

## INC-005 — Gate lingua non implementato come da spec (rilevamento post-LLM)

**Data:** 2026-05-29 (durante test di allineamento codice ↔ spec)

**Componente:** Gate lingua — spec §3/§4/§6 (`langdetect`) vs `llm_client.py`

**Descrizione:** La spec v3.1 prevede un gate deterministico con `langdetect` (no LLM) **prima** della chiamata a Claude: se la lingua non è IT/EN, blocco immediato a costo zero token (motivazione Green-AI §7). Nel codice `langdetect` non è presente (né in `requirements.txt` né importato). La lingua è invece letta dall'output dell'LLM (`analysis.language_detected`) e controllata **dopo** la chiamata (`llm_client.py:103-104`).

**Severità:** Medium (il blocco funzionalmente esiste, ma a valle e a costo token già speso; il razionale Green-AI/routing della spec non vale come scritto).

**Root cause:** in fase di building il rilevamento lingua è stato delegato all'LLM (che già restituisce `language_detected` nello schema), evitando una dipendenza in più. Scelta pragmatica non riportata nella spec.

**Soluzione (decisa 2026-05-29 — opzione B):** si allinea la **spec al codice**. Il gate lingua resta post-LLM (`language_detected` dall'output di Claude); si rinuncia a `langdetect` e al claim Green-AI "blocco a costo zero token" per la lingua. Razionale: lo schema JSON già restituisce `language_detected`, una sola fonte di verità sulla lingua, niente dipendenza extra. Trade-off accettato: si perde il micro-risparmio token sul caso raro di lingua fuori perimetro. T10 invariato come criterio (blocco con messaggio), meccanismo aggiornato (rifiuto a valle).

**Lezioni apprese:** un gate "deterministico pre-API" sulla carta può scivolare a "controllo post-output" in implementazione — verificare che i gate di risparmio token siano davvero a monte della chiamata. Quando la divergenza è benigna, allineare la spec al codice è più onesto che forzare il codice.

**Aggiornamenti Specifica:** §3/§4/§6/§7 da rettificare via [[SPEC_ERRATA]] ERR-09 (gate lingua post-LLM, rimozione `langdetect`).

**Status:** ✅ Resolved 2026-05-29 — decisione B

---

## INC-006 — Over-redaction e label errate nel filtro privacy (spaCy NER)

**Data:** 2026-05-29 (durante verifica empirica ERR-01)

**Componente:** `privacy_filter.py` — spaCy `it_core_news_sm` NER

**Descrizione:** Test di redazione su campione con PII italiane reali. La redazione delle PII sensibili è **corretta e completa** (zero leak; vedi [[SPEC_ERRATA]] ERR-01), ma emergono imprecisioni di etichettatura: società "Acme S.r.l" → `[PER]` (invece di ORG), nome comune "CONSULENZA" → `[ORG]`, parola letterale "IBAN" → `[ORG]`, email catturata da spaCy come `[ORG]` prima della regex email, e un numero di protocollo a 11 cifre (`20260529001`) ha superato il checksum Luhn finendo redatto come `[PIVA]`.

**Severità:** Low — è **over-redaction** (direzione sicura: si redige più del necessario, non meno). Nessuna PII esposta. Effetto collaterale: il testo che Claude legge è più "sporco" di placeholder, e metadati legittimi (es. numeri di protocollo) possono sparire dal contesto.

**Root cause:** (a) spaCy `sm` (modello leggero, scelto per performance Windows — vedi [[feedback_spacy_model]]) ha NER meno preciso su entità di dominio legale/aziendale; (b) il checksum Luhn filtra molti falsi positivi P.IVA ma non tutti — un numero a 11 cifre casuale ha ~10% di probabilità di passare Luhn.

**Soluzione (da valutare, non urgente):** restringere le label spaCy redatte (es. escludere ORG, o gestirlo separatamente), e/o aggiungere contesto-keyword per P.IVA come già fatto per il CF. Per l'MVP corso l'over-redaction è accettabile.

**Lezioni apprese:** un filtro privacy va valutato su **due assi** separati — recall (nessun leak: ✅) e precision (nessuna over-redaction: parziale). Per un MVP privacy-first, alta recall a scapito della precision è il trade-off giusto.

**Aggiornamenti Specifica:** nessuno (qualità implementativa, non architetturale). Vedi [[SPEC_ERRATA]] ERR-01 caveat.

**Status:** 🟡 Open — qualità, accettabile per MVP

---

## INC-007 — CF regex falso positivo (re.IGNORECASE)

**Data:** 2026-05-21 (identificato e risolto nella sessione code review — `CODE_REVIEW_SPECTERAI_20260521.md`)

**Componente:** `privacy_filter.py` — `_CF_RE` (oggi righe 43-45, 94-96)

**Descrizione:** Il pattern CF `\b[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]\b` compilato con `re.IGNORECASE` cattura qualsiasi sequenza alfanumerica della stessa forma, incluse stringhe lowercase come `articolo15bisXY99ZW123A` che compaiono in clausole legali italiane. Effetto: clausole legali legittime vengono redatte come `[CF_1]`, e Claude analizza il placeholder anziché la clausola reale → analisi falsata.

**Severità:** High — corrupts analisi su contratti con articoli numerati in formato alfanumerico denso

**Root cause:** IGNORECASE attivo senza validazione aggiuntiva. La spec `Privacy Filter Integration.md:118` richiedeva *"RGX validato Agenzia Entrate"* — non implementato. Il codice usa invece una regex morfologica senza context.

**Fix applicato (2026-05-21, commit `ec862f8`):** context-keyword obbligatorio + CF case-sensitive uppercase (nessun IGNORECASE sul codice, solo sul prefisso). Codice attuale `privacy_filter.py:43-45`:
```python
_CF_RE = re.compile(
    r'(?i:codice\s+fiscale|C\.?F\.?)\s*[:/-]?\s*([A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z])\b'
)
```
Il match scatta solo se preceduto da `codice fiscale`/`CF`; il CF stesso resta uppercase → le sequenze lowercase in clausole legali non matchano più. Verificato 2026-05-29 (test redazione + 4/4 unit test).

**Lezioni apprese:** regex morfologiche per documenti legali devono sempre essere vincolate da context-keyword — il pattern da solo è troppo permissivo su testi densi.

**Aggiornamenti Specifica:** nessuno — era gap implementazione vs spec già scritta correttamente. Vedi [[SPEC_ERRATA]] ERR-06.

**Status:** ✅ Resolved 2026-05-21 — commit `ec862f8`

---

## INC-008 — PIVA `\d{11}` senza Luhn + context (falso positivo massiccia)

**Data:** 2026-05-21 (identificato e risolto nella sessione code review)

**Componente:** `privacy_filter.py` — `validate_piva_luhn` + `_replace_piva` (righe 17-27, 98-100)

**Descrizione:** Il pattern PIVA `\b\d{11}\b` matcha qualsiasi sequenza di 11 cifre: importi in centesimi, numeri di protocollo, CIG, codici catastali. Esempio: "protocollo n. 20240015432" viene redatto come `[PIVA_1]`, perdendo un dato identificativo del contratto dal contesto LLM.

**Severità:** High — perdita di dati legittimi nel payload → LLM manca contesto per categorie `payment_terms` e `governing_law`

**Root cause:** spec `Privacy Filter Integration.md:119` esplicitamente richiedeva *"Partita IVA (11 cifre + checksum Luhn IT)"*. Il checksum Luhn è stato implementato in INC-006 per alcuni casi, ma il context-keyword non è stato aggiunto — il match avviene ancora su qualsiasi 11 cifre isolate.

**Fix applicato (2026-05-21, commit `b61baef`):** checksum Luhn IT ufficiale; la PIVA è redatta **solo** se il checksum passa. Codice attuale `privacy_filter.py:17-27` (`validate_piva_luhn`) + `:98-100` (`_replace_piva` con guard `if validate_piva_luhn(val) else val`). Verificato 2026-05-29.

**Limite residuo noto:** il Luhn da solo non elimina il 100% dei falsi positivi (~10% dei numeri a 11 cifre casuali passano — vedi [[INCIDENTS]] INC-006, dove un protocollo è stato over-redatto). Per l'MVP è over-redaction (direzione sicura), non leak. L'eventuale aggiunta di context-keyword come per il CF è tracciata in INC-006.

**Lezioni apprese:** Luhn senza context-keyword riduce i falsi positivi ma non li elimina. Per precision massima servono entrambi; per recall (zero leak) il Luhn è sufficiente.

**Aggiornamenti Specifica:** nessuno — gap implementazione. Vedi [[SPEC_ERRATA]] ERR-06.

**Status:** ✅ Resolved 2026-05-21 — commit `b61baef` (precision residua → INC-006)

---

## INC-009 — Ordering bug spaCy: offset post-regex applicati su testo già modificato

**Data:** 2026-05-21 (identificato e risolto nella sessione code review — problema genuinamente nuovo, non in spec)

**Componente:** `privacy_filter.py` — `redact()` Passo 1/Passo 2 (righe 63-110)

**Descrizione:** La pipeline in `redact()` esegue prima le sostituzioni regex (che modificano il testo e cambiano gli offset dei caratteri), poi lancia spaCy NER sul testo risultante. Tuttavia gli offset di spaCy sono calcolati sul testo post-regex, e poi vengono applicati con un loop `sorted(entities, reverse=True)` che modifica il testo a ogni iterazione, invalidando i boundary delle entity successive. Il check `before == "["` a riga 194 è una toppa parziale che non copre tutti i casi. Risultato: placeholder corrotti tipo `[PER[CF_1]_2]`, testo manomesso inviato a Claude.

**Severità:** Critical — corruzione dati, non riproducibile in modo deterministico (dipende dalla densità di PII nel contratto)

**Root cause:** mancanza di un approccio "collect all spans, apply once in reverse order on original text" — il testo viene mutato in-place durante l'iterazione.

**Fix applicato (2026-05-21, commit `999071f`):** invertito l'ordine — **Passo 1** spaCy NER gira sul testo **originale** (offset sempre validi, applicati in reverse), **Passo 2** le regex girano **dopo** sul testo già parzialmente redatto. Poiché al momento delle regex non ci sono ancora placeholder spaCy che si sovrappongono ai loro pattern, la corruzione `[PER[CF_1]_2]` è **impossibile per costruzione**. Codice attuale `privacy_filter.py:63-110`. Coperto da 4 test unitari dedicati (`tests/test_privacy_filter.py`: `test_no_nested_placeholders`, `test_spacy_receives_original_text`, ...). Verificato 2026-05-29.

**Lezioni apprese:** mai mutare un testo in-place durante un'iterazione sugli offset di quel testo. Far girare il layer NER (offset-based) per primo sul testo intatto e le sostituzioni stringa-based dopo è il pattern corretto per layered text substitution.

**Aggiornamenti Specifica:** nessuno — bug implementativo, non architetturale. Vedi [[SPEC_ERRATA]] ERR-06.

**Status:** ✅ Resolved 2026-05-21 — commit `999071f` (+ test unitari)

---

## INC-010 — subprocess.run() senza timeout (DoS / hang indefinito)

**Data:** 2026-05-21 (identificato e risolto nella sessione code review)

**Componente:** `llm_client.py` — `_call_cli` (oggi riga 36)

**Descrizione:** La chiamata `subprocess.run(["claude", "-p", ...], ...)` non specifica `timeout=`. Se Claude CLI hangga (rete, auth scaduta, OOM), il processo resta bloccato indefinitamente. Su FastAPI con 4 worker uvicorn default: 4 richieste in parallelo bastano per saturare tutti i worker e rendere il servizio non raggiungibile — DoS involontario o intenzionale.

**Severità:** High — blocca produzione, anche senza attaccante (es. disconnessione rete durante analisi)

**Root cause:** la spec v3.1 §9 richiedeva *"Claude API timeout → Retry automatico"* ma il `timeout=` parametro di subprocess non è stato aggiunto.

**Fix applicato (2026-05-21, commit `3b81fd1`):** aggiunto `timeout=300` a `subprocess.run` (`llm_client.py:36`). Il loop di retry in `analyze()` cattura `subprocess.TimeoutExpired` come errore transitorio (ritenta, poi 503 con `Retry-After`). Verificato 2026-05-29.

**Lezioni apprese:** qualsiasi subprocess su rete/LLM deve avere timeout esplicito. Il default "attendi per sempre" è quasi sempre sbagliato.

**Aggiornamenti Specifica:** nessuno — colma gap implementazione vs spec §9. Vedi [[SPEC_ERRATA]] ERR-06.

**Status:** ✅ Resolved 2026-05-21 — commit `3b81fd1`

---

## INC-011 — async def con subprocess sincrono blocca event loop FastAPI

**Data:** 2026-05-21 (identificato e risolto nella sessione code review — problema genuinamente nuovo, non in spec)

**Componente:** `main.py` — endpoint `/analyze` (oggi riga 66)

**Descrizione:** L'endpoint `async def analyze_contract(...)` chiama internamente `analyze(contract_text, metadata)` che a sua volta esegue `subprocess.run()` sincrono. In FastAPI, una coroutine `async def` che fa lavoro bloccante occupa l'event loop per tutta la durata — 163-268 secondi per contratto lungo. Durante questo tempo nessun altro endpoint risponde, anche se ci sono worker liberi. Zero concorrenza reale.

**Severità:** High — degrada UX a single-threaded de facto; qualsiasi secondo utente aspetta 3+ minuti anche su server con risorse libere

**Root cause:** FastAPI non sa distinguere "async che fa I/O non bloccante" da "async che fa CPU/subprocess bloccante". La distinzione va fatta esplicitamente con `asyncio.to_thread`.

**Fix applicato (2026-05-21, commit `3b81fd1`):** la chiamata bloccante è spostata fuori dall'event loop con `result = await asyncio.to_thread(analyze, contract_text, metadata)` (`main.py:66`). `asyncio.to_thread` esegue la funzione sync in un thread pool separato, liberando l'event loop per altre richieste. Verificato 2026-05-29.

**Lezioni apprese:** `async def` in FastAPI non è sufficiente per concorrenza reale se la funzione fa I/O bloccante (subprocess, file sync, DB sync). Serve `asyncio.to_thread` o `run_in_executor` espliciti.

**Aggiornamenti Specifica:** nessuno — best practice FastAPI, non coperta dalla spec. Vedi [[SPEC_ERRATA]] ERR-06.

**Status:** ✅ Resolved 2026-05-21 — commit `3b81fd1`

---

## INC-012 — `async for chunk in file` su UploadFile (500 su ogni upload web)

**Data:** 2026-06-04 (durante il primo avvio E2E reale dell'app via browser + uvicorn)

**Componente:** `main.py` — endpoint `/analyze`, lettura streaming del file caricato (righe ~42-53)

**Descrizione:** Al primo upload reale di un PDF dalla web UI (`ContrattoCOCOCO.pdf`), il server risponde **500 in ~0,1s**, prima ancora della chiamata LLM. Traceback:
```
File "main.py", line 45, in analyze_contract
    async for chunk in file:
TypeError: 'async for' requires an object with __aiter__ method, got UploadFile
```
`UploadFile` di Starlette/FastAPI **non implementa `__aiter__`**: non è async-iterabile. Il loop `async for chunk in file` (introdotto per leggere il file a chunk con limite 10MB) solleva `TypeError` su **qualsiasi** file caricato. Conseguenza: la pipeline web `/analyze` non ha **mai** processato un upload con successo — ogni PDF dava 500.

**Severità:** Critical — il percorso utente principale (carica PDF dal browser → report) era completamente rotto. Restava nascosto perché i test E2E precedenti (vedi [[SESSION_HANDOFF]], "7/8 PDF" del 2026-05-20) erano stati eseguiti chiamando la logica di analisi a monte dell'endpoint, **non** via upload HTTP multipart reale.

**Root cause:** assunzione errata che `UploadFile` fosse async-iterabile come uno stream. L'API corretta per leggere a chunk è `await file.read(size)` in loop (oppure iterare `file.file`, lo SpooledTemporaryFile sottostante, in modo sincrono).

**Fix applicato (2026-06-04):** sostituito il loop `async for` con lettura a chunk via `await file.read()`, mantenendo identico il limite 10MB:
```python
chunks: list[bytes] = []
total = 0
while True:
    chunk = await file.read(1024 * 1024)  # 1MB per volta
    if not chunk:
        break
    total += len(chunk)
    if total > MAX_SIZE:
        return HTMLResponse("...supera i 10MB...", status_code=413)
    chunks.append(chunk)
pdf_bytes = b"".join(chunks)
```

**Fix verificato (E2E reale, backend cli + Haiku):**
- `ContrattoCOCOCO.pdf` → **200 OK**, report HTML completo (3 top-rischi + 7/7 categorie), `language: italian`.
- Tempi osservati: 76,8s / 81,0s / 107,2s su run successivi (varianza intrinseca del backend `cli` Claude Code, non retry — log puliti, 1 tentativo).

**Lezioni apprese:**
- ✅ `UploadFile` non è async-iterabile: leggere con `await file.read(size)`, mai `async for`.
- ✅ **Testare il percorso E2E vero** (upload HTTP multipart), non solo la funzione `analyze()` a valle: un bug nell'I/O dell'endpoint resta invisibile se si testa solo il core. La copertura "7/8 PDF" non includeva l'upload reale.
- ✅ Un 500 immediato (~0,1s) prima della latenza LLM è un segnale che l'errore è nell'I/O/validazione, non nel modello.

**Aggiornamenti Specifica:** nessuno (bug implementativo, non architetturale). Aggiornata la nota E2E in [[SPEC_ERRATA]] (Test di verifica): E2E via uvicorn + PDF reale ora **eseguito** il 2026-06-04.

**Status:** ✅ Resolved 2026-06-04

---

## INC-013 — Extended thinking del CLI: analisi ~163s invece di ~13s

**Data:** 2026-06-24

**Componente:** `llm_client.py` — backend Claude Code CLI

**Descrizione:** Le analisi impiegavano 1-4 minuti (NDA corto: 166s). Inaccettabile per i contratti pesanti che l'utente deve processare. Le ipotesi iniziali (cold-start CLI, retry per JSON invalido, server MCP caricati, contesto del vault, lingua del system prompt) si sono rivelate **tutte sbagliate**.

**Severità:** High (latenza — rende l'app inusabile su contratti grandi)

**Root cause:** misure isolate hanno mostrato che (a) una chiamata CLI banale ("ciao") costa solo **6,3s** → il cold-start NON è il problema; (b) una **singola** analisi del contratto con thinking attivo costa **162,9s** con JSON valido al primo colpo → niente retry. Il tempo se ne andava in **~1700 token di extended thinking** (ragionamento nascosto) generati dal CLI prima della risposta. L'estrazione di clausole è un task meccanico/estrattivo che non richiede thinking.

**Soluzione:** passare `MAX_THINKING_TOKENS=0` nell'ambiente del subprocess CLI (`env={**os.environ, "MAX_THINKING_TOKENS": "0"}`). Risultato: **162,9s → 13,3s (12×)**, JSON valido, output anche più completo. Verificato E2E: NDA 13s, Capitolato 1MB 55s. Restando su backend `cli` a €0 (vincolo di progetto, vedi memoria `project-specterai-cli-only`).

Aggiunte anche due misure di igiene a costo zero (non risolutive sulla latenza ma corrette): `--strict-mcp-config` (niente server MCP) e `cwd` su dir vuota (niente CLAUDE.md/skill del vault).

**Lezioni apprese:**
- ✅ **Misurare prima di ottimizzare:** 5 ipotesi plausibili erano tutte errate. Il floor (chiamata banale) vs la singola call reale hanno isolato il colpevole in 2 misure.
- ✅ La varianza del CLI (166s vs 267s sullo stesso input) era più grande dei presunti guadagni delle micro-ottimizzazioni → segnale che si stava guardando il posto sbagliato.
- ✅ Per task estrattivi deterministici, l'extended thinking è puro overhead.

**Aggiornamenti Specifica:** nessun cambio architetturale. Da riflettere nella §6 (parametri/performance) e utile per la presentazione §6-7. Dettagli build in [[CHANGELOG_BUILD_24-06]].

**Status:** ✅ Resolved 2026-06-24

---

## Protocollo di Gestione Strutturata degli Errori

Quando un incident viene riscontrato durante il building:

1. **Leggere il messaggio di errore e stack trace** — acquisire tutte le informazioni
2. **Capire la causa radice** — non coprire il sintomo, capire cosa ha causato il comportamento
3. **Correggere e testare** — fix + validazione con caso di test specifico
4. **Documentare su INCIDENTS.md** — aggiungere entry nella tabella + sezione dettagliata
5. **Aggiornare la Specifica v2 se necessario** — se il fix implica cambio architetturale

---

## Template per Nuovi Incidents

```markdown
## INC-XXX — [Nome breve]

**Data:** [YYYY-MM-DD]

**Componente:** [Layer / Modulo]

**Descrizione:** [Cosa è successo, quando è stato notato]

**Severità:** [Critical / High / Medium / Low]

**Root cause:** [Analisi della causa radice]

**Soluzione:** [Cosa è stato cambiato/testato]

**Lezioni apprese:** [Cosa abbiamo imparato per futuro]

**Aggiornamenti Specifica:** [Modifiche applicate alla v2]

**Status:** [Open / Resolved]
```
