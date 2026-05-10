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
| INC-001 | 2026-05-12 (Lez. 3) | PyMuPDF text extraction | PDF parsing errors on scanned/complex PDFs | Critical | 🔴 Open |
| INC-002 | 2026-05-13 (Lez. 4) | Claude API timeout | Timeout su batch processing di contratti | High | 🔴 Open |
| INC-003 | 2026-05-13 (Lez. 4) | JSON parsing / encoding | Caratteri speciali italiani (accenti, €) corrotti nel JSON | High | 🔴 Open |

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
