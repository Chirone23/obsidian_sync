# INCIDENTS — SpecterAI

**Progetto:** SpecterAI (AI Contract Analyzer per Non-Avvocati in Italiano)  
**Data inizio:** 2026-04-28  
**Data prima sessione:** 2026-05-04  

---

## Tabella Incidents

| ID | Data | Componente | Descrizione breve | Severità | Status |
|---|---|---|---|---|---|
| **INC-000a** | **2026-04-28** | **PDF tool (ricerca)** | **PDF extraction tool failure on course materials** | **Medium** | **✅ RESOLVED** |
| **INC-000b** | **2026-04-29** | **MCP obsidian** | **Obsidian MCP connection refused on vault write** | **Medium** | **✅ RESOLVED** |
| **INC-000c** | **2026-05-02** | **AI methodology** | **AI prompting approach (directed vs independent)** | **Methodology** | **✅ RESOLVED** |
| INC-001 | 2026-05-05 (TBD) | PyMuPDF text extraction | PDF parsing errors on scanned/complex PDFs | Critical | 🔴 Open |
| INC-002 | 2026-05-05 (TBD) | Claude API timeout | Timeout su batch processing di contratti | High | 🔴 Open |
| INC-003 | 2026-05-05 (TBD) | JSON parsing / encoding | Caratteri speciali italiani (accenti, €) corrotti nel JSON | High | 🔴 Open |

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
