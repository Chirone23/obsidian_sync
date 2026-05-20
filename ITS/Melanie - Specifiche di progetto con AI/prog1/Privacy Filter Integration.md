# Privacy Filter Integration — SpecterAI

**Data decisione:** 2026-05-20
**Stato:** Design **promosso a requisito GDPR** + **pivot tecnico: OPF → Hybrid (regex IT + spaCy)** dopo smoke test sul campo
**Motivazione utente:** *"Non voglio dare dati importanti a chi non li deve avere; ne vale l'affidabilità del progetto."*
**Motivazione regolatoria (P3):** GDPR Art. 5 (data minimization) + Art. 25 (data protection by design) → redazione PII pre-LLM è *"mandatory in practice"* per SMB italiano che invia contratti a Anthropic. DPA + retention 7gg da **soli** sono **insufficienti**.
**Motivazione pivot tecnico:** OPF testato su 2 PDF reali, qualità detection IT confermata (P1 OK) ma **velocità inaccettabile per UX MVP**: 55s di pura inferenza su 3KB → ~10 min per contratto medio. Architettura custom MoE+Triton non supporta path ONNX rapido. Decisione: tenere OPF come PoC, pivot a Hybrid (regex deterministico per PII strutturate IT + spaCy `it_core_news_lg` per NER nomi/luoghi).
**Link a:** [[Progettistica AI MOC]] · [[SESSION_HANDOFF]] · [[Specifica Tecnica v3 - SpecterAI]] · [[PROMPT_LOG]] · [[INCIDENTS]]

---

## Obiettivo

Impedire che PII (nomi, indirizzi, email, IBAN, codici fiscali, date sensibili, segreti) dei contratti caricati dall'utente vengano trasmesse ad Anthropic in chiaro durante l'analisi LLM. Il sistema resta privacy-by-design anche con un provider cloud nel loop.

---

## ⚠️ PIVOT — Decisione finale tool

**Dopo smoke test 2026-05-20:** pivot da OPF a **stack Hybrid italiano** (vedi sezione "Smoke Test Results" più sotto).

**Tool finale selezionato (D — Hybrid pragmatico):**
- **Regex deterministico** per PII strutturate italiane: CF (codice fiscale 16 char), P.IVA (11 cifre), IBAN (`IT` + 25 char), telefono (+39…), email
- **spaCy `it_core_news_lg`** per NER nomi propri + luoghi/indirizzi
- **Latency target:** <1s per contratto (vs ~10min con OPF)

**OPF mantenuto come:** PoC dimostrativo + fallback opzionale per detection categorie più sfumate (post-MVP). Codice resta in `C:\Users\Chirone\Desktop\Progetti\privacy-filter`.

---

## Tool inizialmente valutato (PoC)

**`openai/privacy-filter`** ([github.com/openai/privacy-filter](https://github.com/openai/privacy-filter))

| Caratteristica | Valore |
|---|---|
| Tipo | Transformer 1.5B (50M active, sparse MoE), context 128K |
| Categorie PII | 8: account numbers, addresses, emails, person names, phones, URLs, dates, secrets |
| Distribuzione | `pip install opf` + CLI `opf` |
| Licenza | Apache 2.0 (uso commerciale OK) |
| Maturità | ⚠️ Solo 3 commit su main — sperimentale |

**Fallback** (se Step A fallisce su performance): `presidio` di Microsoft — maturo, ma **inferiore in italiano** (vedi sotto P1).

---

## Smoke Test Results (2026-05-20) — OPF on real Italian PDFs

**Setup:**
- OPF v0.1.0 editable install in `C:\Users\Chirone\Desktop\Progetti\privacy-filter`
- Modello già scaricato in HF cache (06/05/2026)
- CLI: `opf redact --device cpu --format json --output-mode typed`
- Hardware: Ryzen 5 PRO 5650U (Zen3, no GPU dedicata), 16GB RAM

**Test #1 — NDA Politecnico (template)**
- Chars: 3283 — Tempo: **83.5s** (cold + inference)
- Latency modello: 57.0s
- Span rilevati: **0** ✅ atteso (template con campi `___________`)

**Test #2 — Locazione INPS slice (3K con PII reali)**
- Chars: 3000 — Tempo: **64.7s**
- Latency modello: 55.4s
- Span rilevati: **6** ✅ → `account_number: 4` (incl. CF `80078750587`), `private_address: 2` (Via Senatore Alessi 14, Via Maggiore Pietro Toselli 5)

**Qualità detection IT:** ✅ Confermata P1 — OPF cattura PII italiane reali (CF + indirizzi)

**Velocità:** ❌ **Inaccettabile per MVP** — throughput ~46 char/s
- Estrapolazione: contratto medio (26K) ≈ 9-10 min; denso (50K) ≈ 18 min
- 90% del tempo è pura inferenza modello (non I/O)
- bfloat16 emulato su Zen3 (Zen4+ per nativo) penalizza ulteriormente

**Tentativo Path A (ONNX) abbandonato:**
- OPF source senza riferimenti a onnx — architettura custom MoE+Triton, non `AutoModelForTokenClassification` standard
- Cartella `/onnx` su HF dedicata a Transformers.js (browser), non onnxruntime Python
- Custom inference script richiederebbe ore (replicare Viterbi decoding + span extraction post-hoc)

---

## Verifica Perplexity (2026-05-20) — 3 query mirate

### P1 — Benchmark OPF vs Presidio su italiano (PERSON + ADDRESS + IBAN)

- **OPF:** ~95-97% F1 su PII-Masking-300k (include italiano), forte su `private_person`, `private_address`, `account_number` (IBAN-like). Multilingue nativo.
- **Presidio:** italiano supportato solo via community/custom recognizers, ~mid-80s F1 con tuning manuale, peggio out-of-the-box. Richiede spaCy IT + regex IBAN custom per essere usabile.
- **Verdetto:** **OPF vince nettamente per il nostro caso d'uso** (contratti italiani con nomi + indirizzi + IBAN).

### P2 — OPF su Ryzen senza GPU, 16GB RAM

- ✅ **Gira CPU-only** (WASM fallback nativo, no GPU richiesta)
- ✅ **1.5B params + MoE sparsity** → working set attivo molto più piccolo di un 1.5B denso, fattibile in 16GB
- ⚠️ **Throughput basso:** community report ~2-3 samples/sec su CPU desktop tipica
- ⚠️ **Serve build quantizzato** (4-bit/INT8 o WASM packaged) — NON caricare fp16 raw
- ⚠️ **NVMe SSD raccomandato** + chiudere altre app per evitare swap
- **Verdetto:** **fattibile ma con caveat** — usare quantized build, accettare ~5-10s di redazione per contratto medio (latency totale ~20-25s)

### P3 — GDPR + DPA Anthropic: redazione PII è obbligatoria?

- **Risposta breve:** *"PII redaction is not strictly mandatory by GDPR itself, but is almost always required in practice"*
- **GDPR base:** Art. 5 (minimizzazione) + Art. 25 (privacy by design) + Art. 35 (DPIA per processing high-risk)
- **DPA + retention 7gg da soli = INSUFFICIENTI:** un DPA non rende lecito un processing eccessivo. Bulk contract-analysis spesso classificato high-risk → DPIA potenzialmente richiesta.
- **Cosa si aspettano i regolatori (Garante incluso):**
  1. Data minimization: solo PII minime al modello
  2. **Redaction/pseudonymization pre-cloud-LLM**
  3. DPIA documentata se high-risk
- **Verdetto:** ⚠️ **Cambia status del privacy-filter da "feature" a "compliance requirement"** per SMB italiano. La spec v3.1 ha già un §sull'AI Act (rischio limitato Art. 6(3)) ma **non copriva esplicitamente GDPR Art. 5/25** — questa è una **gap chiusa** dall'addendum §3.bis.

---

---

## Architettura finale — Hybrid Map-back Pattern (D)

**Pipeline `privacy_filter.py` (post-pivot):**

```
text_raw ──► regex_layer_pii (deterministico)
                  │
                  ├─ Codice Fiscale (16 alfanum, RGX validato Agenzia Entrate)
                  ├─ Partita IVA (11 cifre + checksum Luhn IT)
                  ├─ IBAN (IT + 25 alfanum, checksum mod-97)
                  ├─ Email (RFC 5322 light)
                  ├─ Telefono italiano (+39, 39, 0…, 3…)
                  └─ Account/numeri conto generici
                  │
                  ▼
            text_partial_redacted
                  │
                  ▼
           spaCy NER it_core_news_lg
                  │
                  ├─ PERSON (nomi propri)
                  ├─ LOC / GPE (luoghi, indirizzi)
                  └─ ORG (organizzazioni — opzionale, configurabile)
                  │
                  ▼
            text_fully_redacted + pii_mapping
                  │
                  ▼
         [resto pipeline identica a sotto]
```

**Vantaggi vs OPF:**
- ⚡ **<1s per contratto** (vs ~10 min)
- 🎯 **100% determinismo** sulle categorie regex (CF/IVA/IBAN sono pattern matematici, no probabilità)
- 🔒 **Coverage GDPR**: cattura le PII *più sensibili* per SMB italiano (identificatori fiscali/bancari)
- 🇮🇹 **NER italiano nativo** (spaCy IT è maturo, addestrato su corpus IT)
- 📦 **Zero LLM nel filtro** → no provider dipendenze, no cold start, no allucinazioni nel filtro stesso

**Trade-off accettato:**
- Coverage entità "fuzzy" (es. soprannomi, ruoli con nome nascosto) inferiore a OPF
- spaCy NER su nomi rari/stranieri può perdere → mitigato da regex pattern già catturati prima

---

## Architettura legacy — Map-back Pattern (OPF, abbandonato)

```
PDF ─► pdf_processor.py ─► contratto_raw.txt
                                  │
                                  ▼
                       privacy_filter.py (opf)
                                  │
                ┌─────────────────┴────────────────┐
                ▼                                  ▼
   contratto_redacted.txt                pii_mapping (in-memory)
   ([NAME_1], [DATE_2]...)                {placeholder: valore_reale}
                │                                  │
                ▼                                  │
   ╔═══════════════════════╗                       │
   ║ ANTHROPIC API (cloud) ║                       │
   ║ Riceve SOLO redatto   ║                       │
   ╚═══════════╤═══════════╝                       │
               ▼                                   │
   JSON con raw_excerpt = "[NAME_1] paga [...]"    │
               │                                   │
               ▼                                   │
   llm_client.py: privacy_filter.restore() ◄───────┘
               │
               ▼
   JSON con raw_excerpt verbatim
               │
               ▼
   fuzzy_match 0.92 vs contratto_raw.txt ✅
               │
               ▼
   report.html → utente
```

**Garanzia chiave:** il `pii_mapping` non viene mai scritto su disco né esposto fuori dal processo Python. Vive solo in RAM per la durata della singola analisi.

---

## Modifiche alla codebase

| Modulo | Modifica |
|---|---|
| `pdf_processor.py` | Invariato |
| **`privacy_filter.py`** | **NUOVO** — funzioni `redact(text)` e `restore(redacted, mapping)` |
| `regex_layer.py` | Estrae date/importi/scadenze **prima** del filtro → li passa come metadati strutturati a `llm_client.py` (vedi risoluzione Rischio #3) |
| `llm_client.py` | Pipeline: `redact()` → Anthropic call → `restore()` su tutti i campi `raw_excerpt` dell'output JSON |
| `schemas.py` | Invariato (`raw_excerpt` resta `str \| list[str]`) |
| Test plan §8 | **+T15** — grep PII sul payload outbound: 0 occorrenze di valori reali nelle 8 categorie |

---

## Risoluzione dei 5 Rischi

### Rischio 1 — `opf` è sperimentale (3 commit)

**Soluzione: Cascata di fallback con fail-closed.**

```python
def redact_safe(text: str) -> tuple[str, dict]:
    try:
        return opf_redact(text)              # primario
    except Exception:
        try:
            return presidio_redact(text)     # fallback maturo
        except Exception:
            raise PIIFilterUnavailable(
                "Analisi interrotta: filtro PII non disponibile. "
                "Riprovare più tardi."
            )                                # NO degrade silenzioso
```

- ✅ Mai degrade silenzioso (il sistema rifiuta l'analisi piuttosto che mandare PII in chiaro)
- ✅ Pinned version in `requirements.txt`: `opf==<versione_testata>`
- ✅ INCIDENTS.md: aprire `INC-004` per tracciare regressioni opf

---

### Rischio 2 — 1.5B parametri = lento/RAM-heavy

**Soluzione: Benchmark vincolante prima di committarsi.**

**Step A — Sanity check (eseguibile in 30 min):**
1. `pip install opf` su Ryzen 5650U / 16GB RAM
2. Run `opf` su 3 contratti dei 8 reali (un breve, un medio, un denso)
3. Misurare: tempo di redazione, picco RAM, qualità detection in italiano

**Decisione automatica:**

| Risultato Step A | Azione |
|---|---|
| Tempo <5s, RAM <8GB | ✅ Adottare opf come primario senza riserve |
| Tempo 5-15s o RAM 8-12GB | ✅ Adottare opf con caveat latency in spec (target `<25s`) |
| Tempo >15s o RAM >12GB | ⚠️ Cercare build più quantizzato (4-bit) prima di scartare opf |
| OPF impossibile da far girare | ❌ Fallback `presidio + spaCy IT + regex IBAN custom` (più lavoro ma copre)|

**Update post-P1:** italiano di OPF è confermato buono (~95-97% F1). Il `presidio` come fallback va con custom recognizers IT — più lavoro. **Quindi: priorità a far funzionare OPF**, anche accettando latency più alta.

**Setup raccomandato (da P2):**
- Build quantizzato (4-bit/INT8 o WASM), no fp16 raw
- Chiudere browser/app pesanti durante l'analisi
- NVMe SSD per il modello (già presente)

- ✅ Decisione data-driven, non basata su preferenze
- ✅ Spec aggiornata solo dopo Step A (no over-commit)

---

### Rischio 3 — Date redatte rompono `payment_terms` e `auto_renewal`

**Soluzione: Estrazione strutturata pre-filtro + passaggio parallelo.**

`regex_layer.py` estrae **prima del filtro** tutte le date/importi/scadenze come **metadati strutturati**. Poi il filtro può redarre le date nel testo libero, ma Claude riceve i metadati separatamente.

```python
# pseudocodice
metadata = regex_layer.extract(text_raw)
# metadata = {
#   "dates": ["15/03/2026", "31/12/2026"],
#   "amounts": ["€143.315,16", "€2.282.328,93"],
#   "deadlines": ["entro 30 giorni", "rinnovo annuale"]
# }

redacted, mapping = privacy_filter.redact(text_raw)

claude_input = {
    "contract_text": redacted,           # date qui sono [DATE_N]
    "structured_metadata": metadata      # date qui sono in chiaro MA non sono PII personali
}
```

**Razionale:** date contrattuali (scadenze, durate) **non sono PII personali** — sono parametri di contratto. La data di nascita del firmatario è PII; la data di scadenza del contratto no. Distinguiamo i due casi a livello di estrazione.

- ✅ `payment_terms` e `auto_renewal` mantengono il contesto temporale
- ✅ PII personali (date di nascita, anniversari) restano redatte nel body
- ✅ Da formalizzare in `INCIDENTS.md` come `INC-005` (decisione architetturale, non bug)

---

### Rischio 4 — Latency aumenta (target <15s)

**Soluzione: Rilassare target + ottimizzazioni mirate.**

**Update post-P2:** community reporta ~2-3 samples/sec su CPU desktop → contratto medio (~5-10 chunk) = ~5-10s di sola redazione. Target realistico aggiornato.

**Tre azioni:**
1. **Misurare nello Step A** quanto aggiunge il filtro (T_redact + T_restore)
2. **Aggiornare target spec** da `<15s` a **`<25s`** (P2 ha alzato l'aspettativa: ~5-10s solo per redact)
3. **Ottimizzazioni se sfora `<25s`:**
   - Chunking del testo (filtri parallelizzabili su passaggi indipendenti)
   - Cache del modello opf in memoria (no reload tra richieste)
   - Skip filtro su contratti <1000 caratteri (rare, basso valore)

- ✅ Tradeoff esplicito nella spec: privacy > 5s latency
- ✅ Argomento di valore in demo: *"un 25% di latency in più per zero PII leak"*

---

### Rischio 5 — Spec v3.1 congelata 95/100 dalla prof

**Soluzione: Addendum §3.bis con framing GDPR (non "feature add", "gap compliance chiusa").**

**Aggiornamento post-P3:** la verifica regolatoria cambia il framing. **Non è più "aggiungo una feature di privacy"**, è **"chiudo un gap di compliance GDPR Art. 5/25 che la spec v3.1 non copriva"**.

**Approccio:**
1. **Non riapro la spec** — la prof ha detto *"non c'è nulla da aggiungere"*
2. **Aggiungo `§3.bis Privacy-First Architecture (GDPR Art. 5+25 compliance)`** come **addendum** numerato post-v3.1
3. **Messaggio alla prof:**

   > *"Spec v3.1 confermata, grazie. Verifica Perplexity su requisiti GDPR per SMB italiano (P3): DPA + retention 7gg sono insufficienti senza data minimization (Art. 5) e privacy by design (Art. 25). Aggiungo un addendum §3.bis con layer di redazione PII locale (OpenAI Privacy Filter) pre-chiamata Anthropic. Non cambia architettura LLM, chiude un gap di compliance. Vede problemi?"*

4. **Risposta attesa:** verde alta probabilità (è compliance, non gold-plating). Se gialla/rossa → fallback Stretch Goal §2.bis post-demo con DPIA notata come futuro lavoro.

- ✅ Framing legale (Art. 5/25), non estetico → difficile dire "no" a compliance
- ✅ Trasparenza con la docente
- ✅ Mai modifica retroattiva di un deliverable validato

---

## Step Operativi

| # | Step | Quando | Dipendenze | Reversibilità |
|---|---|---|---|---|
| A | Sanity check opf (benchmark) | Ora, prima di Cursor | Nessuna | Totale |
| B | Messaggio prof per OK su §3.bis | Dopo Step A se opf/presidio passa | Step A | N/A |
| C | Aggiornare spec v3.1 → addendum §3.bis | Dopo Step B | Step B verde | Reversibile |
| D | Implementare `privacy_filter.py` | Fase 2 Cursor (Lez. 4) | Step C | Reversibile |
| E | Modificare `llm_client.py` (redact/restore) | Fase 2 Cursor | Step D | Reversibile |
| F | Aggiungere test T15 al test plan | Fase 4 testing (Lez. 5) | Step E | N/A |

---

## Open Questions (da decidere)

| # | Domanda | Quando |
|---|---|---|
| ~~Q5~~ | ~~opf risponde bene all'italiano?~~ | ✅ Risolta P1: sì, 95-97% F1 |
| Q6 | Mappatura placeholder: schema `[NAME_1]` o UUID? (UUID più resistente a collisioni) | Step D |
| Q7 | Cache del modello opf cross-richiesta o stateless per sessione? | Step D |
| Q8 | Test T15 cosa loggare? Solo "0 PII leak" o anche tipo+count di placeholder generati? | Step F |
| Q9 | DPIA necessaria? Bulk contract-analysis spesso classificato high-risk GDPR Art. 35 | Da chiedere alla prof + nota in spec come "scope post-MVP per uso commerciale" |
| Q10 | Build OPF: quantized 4-bit ufficiale esiste o serve conversione manuale via llama.cpp? | Step A — primo check prima di pip install |

---

## Connessioni

- [[Progettistica AI MOC]] — MOC della materia ITS Melanie
- [[SESSION_HANDOFF]] — handoff progetto SpecterAI
- [[Specifica Tecnica v3 - SpecterAI]] — spec congelata 95/100
- [[PROMPT_LOG]] — log iterazioni prompt v1-final + patch v2/v2.1/v2.2
- [[INCIDENTS]] — log errori risolti (INC-000a..g)
