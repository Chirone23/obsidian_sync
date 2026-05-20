# Privacy Filter Integration — SpecterAI

**Data decisione:** 2026-05-20
**Stato:** Design proposto, in attesa di sanity check (Step A)
**Motivazione utente:** *"Non voglio dare dati importanti a chi non li deve avere; ne vale l'affidabilità del progetto."*
**Link a:** [[Progettistica AI MOC]] · [[SESSION_HANDOFF]] · [[Specifica Tecnica v3 - SpecterAI]] · [[PROMPT_LOG]] · [[INCIDENTS]]

---

## Obiettivo

Impedire che PII (nomi, indirizzi, email, IBAN, codici fiscali, date sensibili, segreti) dei contratti caricati dall'utente vengano trasmesse ad Anthropic in chiaro durante l'analisi LLM. Il sistema resta privacy-by-design anche con un provider cloud nel loop.

---

## Tool selezionato (primario)

**`openai/privacy-filter`** ([github.com/openai/privacy-filter](https://github.com/openai/privacy-filter))

| Caratteristica | Valore |
|---|---|
| Tipo | Transformer 1.5B (50M active, sparse MoE), context 128K |
| Categorie PII | 8: account numbers, addresses, emails, person names, phones, URLs, dates, secrets |
| Distribuzione | `pip install opf` + CLI `opf` |
| Licenza | Apache 2.0 (uso commerciale OK) |
| Maturità | ⚠️ Solo 3 commit su main — sperimentale |

**Fallback** (se Step A fallisce): `presidio` di Microsoft — maturo, multilingue, CPU-friendly, già usato in produzione enterprise.

---

## Architettura — Map-back Pattern

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
| Tempo <5s, RAM <8GB, IT decente | ✅ Adottare opf come primario |
| Tempo 5-15s o RAM 8-12GB | ⚠️ Adottare opf con caveat in spec |
| Tempo >15s o RAM >12GB | ❌ Switch a `presidio` come primario |
| Italiano scarso (FN >30% su nomi) | ❌ Switch a `presidio` (multilingue maturo) |

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

**Tre azioni:**
1. **Misurare nello Step A** quanto aggiunge il filtro (T_redact + T_restore)
2. **Aggiornare target spec** da `<15s` a `<20s` se necessario — costo accettabile per privacy
3. **Ottimizzazioni se sfora `<20s`:**
   - Chunking del testo (filtri parallelizzabili su passaggi indipendenti)
   - Cache del modello opf in memoria (no reload tra richieste)
   - Skip filtro su contratti <1000 caratteri (rare, basso valore)

- ✅ Tradeoff esplicito nella spec: privacy > 5s latency
- ✅ Argomento di valore in demo: *"un 25% di latency in più per zero PII leak"*

---

### Rischio 5 — Spec v3.1 congelata 95/100 dalla prof

**Soluzione: Estensione, non modifica. Comunicazione preventiva.**

**Approccio:**
1. **Non riapro la spec** — la prof ha detto *"non c'è nulla da aggiungere"*
2. **Aggiungo `§3.bis Privacy-First Architecture`** come **addendum** numerato — preserva la versione 3.1 originale, marca chiaramente l'estensione post-feedback
3. **Email/messaggio breve alla prof PRIMA di implementare:**

   > *"Spec v3.1 confermata, grazie. Sto valutando di aggiungere un layer di redazione PII locale (opf/presidio) prima della chiamata Anthropic — non cambia architettura, è un addendum §3.bis. Vede problemi o lo considera fuori scope MVP?"*

4. **Risposta attesa:** verde (privacy = sempre bene), giallo (rinvia post-MVP come stretch goal), rosso (non lo vuole). Decisione resta in mano alla prof.

- ✅ Trasparenza con la docente
- ✅ Mai modifica retroattiva di un deliverable validato
- ✅ Se la prof dice "no", → `§2.bis Stretch Goal` post-demo

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
| Q5 | opf risponde bene all'italiano? (corpus training prevalente EN) | Step A |
| Q6 | Mappatura placeholder: schema `[NAME_1]` o UUID? (UUID più resistente a collisioni) | Step D |
| Q7 | Cache del modello opf cross-richiesta o stateless per sessione? | Step D |
| Q8 | Test T15 cosa loggare? Solo "0 PII leak" o anche tipo+count di placeholder generati? | Step F |

---

## Connessioni

- [[Progettistica AI MOC]] — MOC della materia ITS Melanie
- [[SESSION_HANDOFF]] — handoff progetto SpecterAI
- [[Specifica Tecnica v3 - SpecterAI]] — spec congelata 95/100
- [[PROMPT_LOG]] — log iterazioni prompt v1-final + patch v2/v2.1/v2.2
- [[INCIDENTS]] — log errori risolti (INC-000a..g)
