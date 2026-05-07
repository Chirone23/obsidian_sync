# Specifica Tecnica v3 — SpecterAI

**Progetto:** SpecterAI — AI Contract Analyzer for Non-Lawyers
**Versione:** 3.0
**Data:** 2026-05-07
**Corso:** AI Projects Development · [[Progettistica AI MOC]]
**Stato:** Pre-consegna — versione corrente di riferimento
**Sostituisce:** [[Specifica Tecnica v2 - SpecterAI]]

---

## Changelog v2 → v3

Questa versione integra i fix di [[Review Spec v2 - Gap e Roadmap Pre-Consegna]] e [[Meta-Review Multi-Agent - Validazione della Review]]. Tutti i 13 fix richiesti sono incorporati.

| # | Sezione | Modifica | Fonte |
|---|---|---|---|
| 1 | §1 + §11.bis | Dichiarazione esplicita "zero user research condotte" | Review Fix #4, #7 |
| 2 | §3, §6 | Decisione su lingue non IT/EN: blocco esplicito con messaggio chiaro | Review Fix #5 |
| 3 | §6, §8 | Verifica anti-allucinazione `raw_excerpt` con normalizzazione + fuzzy match | Review Fix #1 + Meta Fix #12 |
| 4 | §7 | Tabella scenari di costo (Demo / Testing / Pubblico) ricalcolata per modello specifico | Review Fix #3 + Meta Fix #9 |
| 5 | §7 | Sezione GDPR ampliata: privacy PDF, ToS Anthropic, retention policy | Meta Fix #8 |
| 6 | §8 | Test Plan eseguibile: tabella `[Test ID · Input · Output atteso · Criterio pass/fail]` con dataset 5 contratti | Review Fix #2 |
| 7 | §8 | Metriche qualità misurabili: precision/recall su categorie, soglie di accettazione | Meta Fix #10 |
| 8 | §9 | Sezione fallback "API down": degraded mode + comunicazione utente | Meta Fix #13 |
| 9 | §11.bis | Limiti della validazione di mercato dichiarati, score auto-corretto a 3/5 | Review Fix #4 + Meta Fix #11 |
| 10 | §13 (nuovo) | Sezione Provenance & Versioning della spec (audit trail v1 → v2 → v3) | Coerenza interna |

---

## 1. Sintesi del Progetto

**Problema:** Freelance, autonomi e piccoli imprenditori ricevono contratti che non capiscono appieno e spesso firmano senza sapere cosa rischiano. Gli strumenti esistenti (Spellbook, Harvey AI, Docusign IAM) sono pensati per avvocati e legal team enterprise: output in linguaggio giuridico, pricing fuori portata, workflow complessi.

**Soluzione:** SpecterAI è un sistema che analizza contratti in formato PDF e restituisce un report in linguaggio plain con i punti critici da verificare prima di firmare. Non dà consulenza legale — evidenzia i rischi e suggerisce le domande da porre.

**Utenti target:** Freelance, liberi professionisti, piccoli imprenditori italiani senza legal team interno. Stima mercato: 2,7M soggetti in Italia (Statista 2019).

**Valore prodotto:** Ridurre il rischio di firmare contratti con clausole sfavorevoli senza averle comprese, senza pagare un avvocato per ogni documento.

**Posizionamento AI Act:** Sistema di *decision-support*, non di consulenza legale. Ogni output include disclaimer esplicito. L'utente mantiene piena responsabilità decisionale.

> ⚠️ **Limite di validazione dichiarato:** la validazione di mercato è esclusivamente desk research (Perplexity, Statista, Eurostat). **Zero user interview condotte** con freelance reali. L'assunzione "10-20 contratti/anno" e l'utilità percepita del tool sono ipotesi conservative da verificare in fase post-MVP. Vedi §11.bis.

### Competitive Positioning

**Landscape:** Il mercato legal-AI include competitor come Harvey, Legora (proprietari, enterprise-focused), e Mikeoss (open-source, self-hosted, lawyer-centric).

**Differenziazione:** SpecterAI non compete direttamente in quella categoria perché:
- **Target diverso**: Mikeoss/Harvey/Legora sono lawyer-centric e firm-scale; SpecterAI è SMB-first e non-lawyer-focused
- **Linguaggio e UX**: SpecterAI è Italian-first con plain-language output; Mikeoss/Harvey/Legora sono English-prioritized e legal-jargon-heavy
- **Use case**: Harvey/Legora sono per drafting e full contract lifecycle; SpecterAI è per reading and risk-analysis (decision-support)
- **Audience localization**: Nessun competitor mainstream ha validato traction nel mercato italiano SMB/freelancer

**Conclusione:** SpecterAI occupa una **sub-niche indifesa**: Italian-speaking, non-lawyer, SMB-budget decision-support tool. Non è cannibalizzata da competitor in quella fascia.

---

## 2. MVP e Funzionalità

### In scope (MVP)
- Upload di un PDF contratto (digitale, non scansione)
- Estrazione e analisi di 7 categorie di red flag
- Output web strutturato in italiano con spiegazione plain-language per ogni categoria
- Disclaimer AI Act visibile in ogni output
- Supporto contratti in italiano e inglese (processing interno in inglese)
- **Blocco esplicito** dei contratti in lingue diverse da IT/EN (vedi §3)
- Nessuna persistenza dei dati (processing on-request)

### Fuori scope (versioni future)
- OCR per PDF scansionati
- Confronto tra versioni del contratto
- Download output in PDF
- Storico analisi / account utente
- Integrazione con firma digitale
- Dashboard multi-documento
- Notifiche scadenze contrattuali
- Supporto contratti in lingue diverse da IT/EN

---

## 3. Flusso Operativo

```
[Utente] → Upload PDF
         → FastAPI: validazione input (dimensione, MIME type, densità testo)
         → PyMuPDF estrae il testo grezzo
         → Layer deterministico estrae date, importi, scadenze (regex)
         → Rilevamento lingua (langdetect, no LLM)
         → [GATE] Lingua ∈ {italian, english}? Se NO → ERRORE BLOCCANTE
         → Testo inviato a Claude API (claude-sonnet-4-6, prompt in inglese)
         → Claude restituisce JSON strutturato con analisi per categoria
         → Pydantic valida lo schema JSON
         → [GATE] Verifica raw_excerpt: ogni excerpt ∈ contract_text (norm + fuzzy)?
                  Se NO → flag "citazione non verificata" o retry
         → FastAPI renderizza la risposta via Jinja2
         → [Utente] visualizza il report nel browser
         → File eliminato dalla memoria (nessuna persistenza)
```

### Requisiti funzionali
- Il sistema deve processare un PDF entro 30 secondi
- L'output deve coprire tutte e 7 le categorie (anche se non presenti: `"present": false`)
- Il disclaimer deve essere visibile senza scroll nella pagina di output
- In caso di PDF non leggibile, il sistema deve restituire un messaggio di errore chiaro

### Input — edge case formali

| Condizione | Comportamento atteso |
|---|---|
| PDF digitale standard (≤10MB, >100 char testo) | Flusso normale |
| PDF >10MB | Errore bloccante prima dell'elaborazione |
| PDF scansionato (testo <100 char estratto) | Errore con spiegazione: "supporta solo PDF digitali" |
| PDF con campi compilabili non riempiti | Estrazione parziale, avviso nell'output |
| PDF protetto da password | Errore: "il file è protetto, rimuovi la password" |
| File non PDF (MIME type errato) | Errore: "formato non supportato" |
| Testo estratto >50.000 caratteri | Troncamento agli ultimi 40.000 char + avviso in output |
| **Contratto in lingua non IT/EN** | **Errore bloccante: "SpecterAI supporta solo contratti in italiano o inglese. La qualità di analisi su altre lingue non è validata."** (decisione v3: blocco esplicito, no fallback silenzioso) |

### Requisiti non funzionali
- Nessun dato persistito su disco o database
- Il file PDF non deve mai uscire dal server (no forward a terze parti eccetto Claude API via HTTPS)
- Dimensione massima file accettata: 10 MB

---

## 4. Stack Tecnologico

| Componente | Tecnologia | Motivazione |
|---|---|---|
| Linguaggio | Python 3.12+ | Standard del corso, ecosistema ricco per PDF e LLM |
| Web framework | FastAPI | Leggero, asincrono, nativo per API, consigliato dal corso |
| Template HTML | Jinja2 | Integrato in FastAPI, zero frontend framework per MVP |
| PDF parsing | PyMuPDF (fitz) | Open source, veloce, affidabile su PDF digitali |
| Layer deterministico | re (regex stdlib) | Estrazione date/importi/scadenze senza dipendere dall'LLM |
| Validazione output AI | Pydantic v2 | Schema enforcement sul JSON restituito da Claude |
| Verifica citazione | difflib.SequenceMatcher (stdlib) | Fuzzy match `raw_excerpt` vs contract_text — nessuna dipendenza extra |
| LLM principale | Claude API (claude-sonnet-4-6) | Migliore accuratezza su testo legale, pricing sostenibile |
| Rilevamento lingua | langdetect | Heuristica veloce, sufficiente per gate IT/EN |
| AI code editor | Cursor | Ambiente di sviluppo con AI integrata |
| Version control | Git + GitHub | Backup, tracciabilità, consegna corso |
| Deploy MVP | Locale (localhost) | Sufficiente per demo corso |

### Dipendenze Python principali
```
fastapi
uvicorn
pymupdf
anthropic
pydantic
jinja2
python-multipart
python-dotenv
langdetect
```

(Tutte già installate — vedi [[Verifica PC - personale]].)

---

## 5. Architettura e Flusso Dati

### Componenti
```
┌─────────────┐    PDF upload    ┌──────────────┐
│   Browser   │ ───────────────► │   FastAPI    │
│  (Jinja2)   │ ◄─────────────── │   Server     │
└─────────────┘    HTML report   └──────┬───────┘
                                        │
                              ┌─────────▼────────┐
                              │  Input Validator │
                              │  (MIME, size,    │
                              │   text density)  │
                              └─────────┬────────┘
                                        │
                              ┌─────────▼────────┐
                              │  PDF Processor   │
                              │  (PyMuPDF)       │
                              └─────────┬────────┘
                                        │ testo grezzo
                              ┌─────────▼────────┐
                              │ Regex Layer      │
                              │ (date, importi)  │
                              └─────────┬────────┘
                                        │
                              ┌─────────▼────────┐
                              │ Language Gate    │
                              │ (IT/EN only)     │
                              └─────────┬────────┘
                                        │ testo + metadati
                              ┌─────────▼────────┐
                              │  Claude API      │
                              │ (claude-sonnet)  │
                              └─────────┬────────┘
                                        │ JSON
                              ┌─────────▼────────┐
                              │ Pydantic Schema  │
                              │ + Excerpt Check  │
                              └──────────────────┘
```

### Flusso dati e privacy
- Il PDF viene caricato in memoria RAM, non scritto su disco
- Il testo estratto viene inviato a Claude API via HTTPS
- Nessun dato viene salvato dopo la risposta
- Ogni richiesta è stateless e indipendente

---

## 6. Comportamento AI

### Multi-model routing

| Task | Modello | Motivazione |
|---|---|---|
| Validazione input (testo sufficiente?) | Nessun LLM — heuristica Python | Task deterministico, zero token consumati |
| Rilevamento lingua (IT vs EN vs altro) | Nessun LLM — `langdetect` | Task semplice; gate solo binario IT/EN, no fallback |
| Analisi contratto (7 categorie) | claude-sonnet-4-6 | Task critico e ambiguo, richiede modello bilanciato |
| Retry su JSON malformato | claude-sonnet-4-6 con prompt più restrittivo | Stessa classe, non declassare su task già fallito |
| Retry su `raw_excerpt` non verificato | claude-sonnet-4-6 con prompt "quote verbatim only" | Forza il modello a citare solo testo presente |

### Parametri modello
```python
response = client.messages.create(
    model="claude-sonnet-4-6",
    max_tokens=2048,
    temperature=0,
    system=SYSTEM_PROMPT,
    messages=[{"role": "user", "content": contract_text}]
)
```

**Nota temperatura:** 0 (non 1) perché l'analisi contrattuale è un task tecnico che richiede coerenza e riproducibilità, non creatività.

### Token optimization
- `max_tokens=2048` — sufficiente per 7 categorie JSON, evita output verbosi inutili
- Testo contratto troncato a 40.000 char prima dell'invio (risparmio ~30% sui contratti lunghi)
- Il prompt di sistema è fisso e conciso (< 600 token)
- Nessuna history conversazionale inviata — ogni analisi è una chiamata singola stateless

### Verifica anti-allucinazione di `raw_excerpt` (nuovo in v3)

**Problema affrontato:** Pydantic valida la struttura del JSON, **non** se la stringa `raw_excerpt` esista davvero nel testo del contratto. Claude può inventare una citazione plausibile e Pydantic la accetta. Senza questo check, il posizionamento AI Act ("decision-support con citazioni verbatim") ha un single point of failure.

**Algoritmo di verifica:**

```python
import re
from difflib import SequenceMatcher

def normalize(text: str) -> str:
    # collapse whitespace, strip, lowercase
    return re.sub(r"\s+", " ", text).strip().lower()

def excerpt_is_grounded(excerpt: str, contract_text: str,
                        threshold: float = 0.92) -> bool:
    if not excerpt:
        return True  # categoria assente: excerpt vuoto è valido
    norm_excerpt = normalize(excerpt)
    norm_contract = normalize(contract_text)
    # Match esatto dopo normalizzazione
    if norm_excerpt in norm_contract:
        return True
    # Fuzzy match (tollera piccole variazioni di punteggiatura/OCR)
    matcher = SequenceMatcher(None, norm_excerpt, norm_contract)
    return matcher.ratio() >= threshold or \
           any(SequenceMatcher(None, norm_excerpt, norm_contract[i:i+len(norm_excerpt)+50]).ratio() >= threshold
               for i in range(0, len(norm_contract), 200))
```

**Comportamento del sistema:**
1. Per ogni categoria con `present: true`, esegui `excerpt_is_grounded()`
2. Se almeno una verifica fallisce: retry una volta con prompt restrittivo "quote verbatim from the provided text only"
3. Se il retry fallisce ancora: la categoria è marcata con flag `excerpt_unverified: true` nell'UI, con avviso esplicito all'utente "questa citazione non è stata verificata nel testo originale"
4. Tutti i fallimenti sono loggati in `INCIDENTS.md`

**Limiti del check:** la normalizzazione gestisce whitespace e case; il fuzzy match a soglia 0.92 tollera differenze di punteggiatura (es. virgolette curly vs straight) e piccoli errori OCR. Il check **non** rileva paraphrasing intenzionale del modello: se Claude riformula leggermente il testo, il fuzzy match potrebbe ancora passare. Mitigazione: prompt esplicito + retry.

---

### Prompt di Sistema (System Prompt)

Il prompt segue la struttura: **Ruolo → Task → Formato output → Vincoli → Esclusioni**.

```
You are a contract analysis assistant specialized in identifying risks for
non-lawyers: freelancers, self-employed professionals, and small business owners.

TASK
Analyze the provided contract text and extract critical information in exactly
7 categories. For each category, extract:
- whether the clause is explicitly present in the text
- the verbatim excerpt from the contract (empty string if absent)
- a plain-language explanation in Italian (max 50 words)
- a risk level: "low", "medium", or "high"
- one concrete question the user should ask before signing (in Italian)

OUTPUT FORMAT
Return ONLY a valid JSON object. No prose, no markdown fences, no explanation
outside the JSON. Match this schema exactly:

{
  "language_detected": "italian" | "english",
  "categories": {
    "payment_terms":        { "present": bool, "raw_excerpt": str, "plain_language": str, "risk_level": str, "question_to_ask": str },
    "auto_renewal":         { "present": bool, "raw_excerpt": str, "plain_language": str, "risk_level": str, "question_to_ask": str },
    "penalties":            { "present": bool, "raw_excerpt": str, "plain_language": str, "risk_level": str, "question_to_ask": str },
    "liability_limitation": { "present": bool, "raw_excerpt": str, "plain_language": str, "risk_level": str, "question_to_ask": str },
    "termination":          { "present": bool, "raw_excerpt": str, "plain_language": str, "risk_level": str, "question_to_ask": str },
    "governing_law":        { "present": bool, "raw_excerpt": str, "plain_language": str, "risk_level": str, "question_to_ask": str },
    "intellectual_property":{ "present": bool, "raw_excerpt": str, "plain_language": str, "risk_level": str, "question_to_ask": str }
  },
  "top_3_risks": [str, str, str],
  "disclaimer": "Questo report non costituisce consulenza legale. Prima di firmare, consulta un professionista qualificato."
}

CONSTRAINTS
- If a category is absent from the contract: "present": false, "raw_excerpt": ""
- risk_level must be exactly one of: "low", "medium", "high" — no other values
- plain_language must be in Italian, plain language, max 50 words
- question_to_ask must be in Italian, phrased as a direct question
- top_3_risks must reference the 3 highest-risk categories actually present in the contract
- raw_excerpt must be a verbatim quote from the contract text, not a paraphrase
- raw_excerpt must be at least 20 characters when "present": true (avoid trivial fragments)

DO NOT
- Invent clauses not explicitly present in the contract text
- Use legal jargon in plain_language
- Advise whether to sign or not to sign the contract
- Return any text, explanation, or formatting outside the JSON object
```

### Few-Shot Examples

I few-shot vengono iniettati come `user/assistant` messages prima del contratto reale, per ancorare il modello sul formato esatto dell'output.

**Esempio 1 — clausola presente, rischio alto**

*Input excerpt:*
```
Art. 5 — Rinnovo del contratto
Il presente contratto si rinnova automaticamente per ulteriori 12 mesi
salvo disdetta da comunicarsi almeno 90 giorni prima della scadenza
a mezzo raccomandata A/R.
```

*Output atteso (solo categoria `auto_renewal`):*
```json
"auto_renewal": {
  "present": true,
  "raw_excerpt": "Il presente contratto si rinnova automaticamente per ulteriori 12 mesi salvo disdetta da comunicarsi almeno 90 giorni prima della scadenza a mezzo raccomandata A/R.",
  "plain_language": "Il contratto si rinnova da solo ogni anno. Per uscire devi avvisare 90 giorni prima con raccomandata — quasi 3 mesi di anticipo.",
  "risk_level": "high",
  "question_to_ask": "Possiamo ridurre il preavviso di disdetta da 90 a 30 giorni?"
}
```

**Esempio 2 — clausola assente**

*Input excerpt:* (nessun riferimento a proprietà intellettuale nel testo)

*Output atteso (solo categoria `intellectual_property`):*
```json
"intellectual_property": {
  "present": false,
  "raw_excerpt": "",
  "plain_language": "Il contratto non specifica chi possiede i materiali prodotti al termine del rapporto. Questa assenza è un rischio.",
  "risk_level": "medium",
  "question_to_ask": "Chi è proprietario dei materiali, del codice o dei contenuti che produco nell'ambito di questo contratto?"
}
```

### Le 7 categorie analizzate
1. **Termini di pagamento** — scadenze, modalità, interessi di mora
2. **Rinnovo automatico** — clausole di rinnovo tacito, tempistiche disdetta
3. **Penali e ritardi** — sanzioni per ritardi di consegna o pagamento
4. **Limitazione di responsabilità** — cap sui danni, esclusioni
5. **Recesso e disdetta** — condizioni e tempistiche per uscire dal contratto
6. **Foro competente** — dove si risolvono le controversie
7. **Proprietà intellettuale** — chi possiede cosa al termine del contratto

---

## 7. Dati, Privacy e Vincoli Normativi

### GDPR (versione estesa v3)

**Dati personali nel PDF.** Un contratto può contenere dati personali (nomi, indirizzi, P.IVA, IBAN, codice fiscale). SpecterAI tratta questi dati come segue:

| Aspetto | Trattamento |
|---|---|
| Storage su disco | ❌ Mai. Il PDF resta in memoria RAM per la durata della richiesta |
| Storage in DB | ❌ Nessun database |
| Logging | ❌ Nessun log del contenuto del contratto. Loggati solo: timestamp, dimensione file, esito (ok/errore), categoria errore |
| Retention | 0 — il file è scartato dal Garbage Collector subito dopo la risposta HTTP |
| Inoltro a terzi | Solo Anthropic (Claude API) via HTTPS, necessario per l'analisi |
| Profilazione | ❌ Nessuna |
| Cookie / tracking | ❌ Nessuno |

**Anthropic ToS (Claude API):** Anthropic dichiara di **non usare** le chiamate API a pagamento per addestrare i modelli (Anthropic Privacy Policy & Commercial ToS, sezione "API Inputs and Outputs"). Questo è il fondamento giuridico del flusso. Da verificare al momento del deploy pubblico (le policy possono cambiare).

**Base giuridica (GDPR art. 6):** consenso informato dell'utente al momento dell'upload + legittimo interesse a fornire il servizio richiesto. Il disclaimer pre-upload informa esplicitamente che il testo del contratto sarà inviato ad Anthropic via HTTPS.

**Diritti dell'interessato:** essendo il sistema stateless senza account utente, i diritti GDPR (accesso, cancellazione, portabilità) non sono applicabili nel senso classico — non esistono dati conservati. La privacy by design coincide con la non-conservazione.

**Dato sensibile particolare:** il sistema non è autorizzato per dati di cui all'art. 9 GDPR (sanitari, biometrici, etc.). Disclaimer pre-upload: "Non caricare contratti contenenti dati sanitari, biometrici o di minori".

### AI Act
- Classificazione: sistema potenzialmente **alto rischio** (analisi documenti legali)
- Mitigazione: posizionamento come *decision-support tool*, non consulenza legale
- Disclaimer obbligatorio in ogni output
- Human-in-the-loop: l'utente legge il report e decide autonomamente
- Nessuna decisione automatizzata che impatta diritti o obbligazioni
- **Garanzia di citazione verificata:** ogni `raw_excerpt` è validato contro il testo originale (vedi §6) — l'utente può fare audit della provenienza di ogni informazione

### AI Sustainability / Green AI

> Ogni chiamata API consuma energia. Ottimizzare i token è una scelta di sostenibilità progettuale, destinata a diventare sempre più rilevante nei criteri di valutazione dei sistemi AI. — Lezione 2

| Scelta progettuale | Impatto sostenibilità |
|---|---|
| `max_tokens=2048` (cap esplicito) | Riduce energia consumata per generare output inutilmente lunghi |
| Troncamento testo a 40.000 char | Riduce token input del ~30% sui contratti lunghi |
| Routing: task semplici → no LLM | Evita chiamate API per operazioni deterministiche (lingua, validazione) |
| Stateless: nessuna history | Ogni richiesta è una singola chiamata, nessun contesto accumulato inutilmente |
| Temperature=0 | Riduce il campionamento probabilistico: meno "tentativi" interni del modello |
| Retry max 1 volta | Cap esplicito al re-spending energetico in caso di fallimento |

### Stima costi e scenari (ricalcolati v3)

**Premessa.** La stima v2 di "<0,02 €/analisi" era plausibile per modelli di fascia bassa (Haiku) ma **sottostimata per Sonnet**. Ricalcolo basato sul listino pubblico Anthropic (al 2026-05-07; verificare prima del deploy):

**Pricing claude-sonnet-4-6 (riferimento listino Anthropic):**
- Input: ~3 $/M token
- Output: ~15 $/M token

**Consumo per analisi tipica:**
- Input: prompt sistema (600) + few-shot (~1.500) + contratto (~2.500–4.000) = ~4.600–6.100 token
- Output: ~1.500–2.000 token (7 categorie JSON)

**Costo per analisi (Sonnet):**
- Min: (4.600 × 3 + 1.500 × 15) / 1.000.000 = 13,8 + 22,5 = **~0,036 $** ≈ **~0,033 €**
- Max: (6.100 × 3 + 2.000 × 15) / 1.000.000 = 18,3 + 30 = **~0,048 $** ≈ **~0,044 €**
- **Media operativa: ~0,04 €/analisi** (non 0,02 come in v2)

**Tabella scenari:**

| Scenario | Volume | Stima costo (Sonnet) | Stima costo (Haiku, fallback) |
|---|---|---|---|
| Demo presentazione corso | 5–10 analisi | ~0,20–0,40 € | <0,05 € |
| Testing pre-consegna (5 contratti × 3 iterazioni prompt) | ~15 analisi | ~0,60 € | <0,10 € |
| Testing esteso (20 contratti) | 20 analisi | ~0,80 € | <0,20 € |
| MVP pubblico (100 analisi/mese) | 100 | ~4 €/mese | ~0,80 €/mese |
| MVP pubblico (1.000 analisi/mese) | 1.000 | ~40 €/mese | ~8 €/mese |

**Budget complessivo per la consegna corso:** **<2 €** (margine generoso). Compatibile con piano API personale già configurato.

**Strategia multi-tier (post-MVP):**
- **Free tier utente:** 3 analisi/mese su Haiku (degraded mode con accuratezza inferiore dichiarata)
- **Tier paid:** analisi su Sonnet, ~5–10 €/mese a copertura costi + margine

### Disclaimer (testo UI)
> SpecterAI è uno strumento di supporto alla lettura dei documenti. Le informazioni fornite non costituiscono consulenza legale. Prima di firmare qualsiasi contratto, consulta un professionista qualificato.

---

## 8. Validazione e Quality Control

### Metriche di qualità MVP (ridefinite v3)

| Metrica | Definizione operativa | Soglia di accettazione MVP |
|---|---|---|
| **Copertura categorie** | % output con tutte e 7 categorie presenti (anche `present: false`) | 100% |
| **Latenza E2E** | Tempo upload → render report (contratti ≤10 pag) | <30 s (target <15 s) |
| **Schema compliance** | % output che passano validazione Pydantic al primo tentativo | ≥95% |
| **Excerpt grounding** | % `raw_excerpt` verificati nel testo originale (su categorie `present: true`) | ≥90% al primo tentativo, 100% dopo retry |
| **Recall categorie presenti** | Su dataset gold-standard, % di clausole realmente presenti correttamente identificate | ≥0,80 (medio sulle 7 categorie) |
| **Precision categorie presenti** | Su dataset gold-standard, % di categorie marcate `present: true` che lo sono davvero | ≥0,85 (medio sulle 7 categorie) |
| **Risk level agreement** | Accordo tra `risk_level` predetto e annotazione manuale (3 livelli) | ≥0,70 (kappa di Cohen) |
| **Disclaimer visibilità** | Presente senza scroll a 1366×768 | 100% |

**Dataset gold-standard:** 5 contratti (vedi sotto) annotati manualmente da Chirone prima del test, con etichette per ogni categoria (presente sì/no, severità). Le metriche precision/recall sono calcolate su queste annotazioni.

### Test Plan eseguibile (sostituisce l'elenco discorsivo v2)

**Dataset di test (5 contratti reali o realistici):**

| ID | Tipo contratto | Lingua | Lunghezza | Caratteristiche |
|---|---|---|---|---|
| C1 | Servizi (consulenza freelance) | IT | ~3 pagine | Standard, contiene tutte e 7 le categorie |
| C2 | Locazione (commerciale) | IT | ~6 pagine | Auto-renewal aggressivo, penali alte |
| C3 | NDA bilaterale | IT | ~2 pagine | Mancano IP e payment_terms (test "categoria assente") |
| C4 | MSA software | EN | ~8 pagine | Test multilingua → output IT |
| C5 | Subappalto / collaborazione | IT | ~10 pagine | Lungo, vicino al troncamento 40k char |

**Tabella test eseguibile:**

| Test ID | Input | Output atteso | Criterio pass/fail |
|---|---|---|---|
| T1 | C1 (PDF valido IT) | JSON con 7 categorie, ≥4 con `present: true` | Schema valido + recall ≥0,80 vs annotazione manuale |
| T2 | C2 | `auto_renewal.risk_level == "high"`; `penalties.present == true` | Match esatto su queste due asserzioni |
| T3 | C3 | `intellectual_property.present == false`; `payment_terms.present == false` | Match esatto |
| T4 | C4 (EN) | `language_detected == "english"`; `plain_language` in italiano | Lingua rilevata corretta + check manuale italiano output |
| T5 | C5 | Avviso troncamento presente; latenza <30 s | Stringa avviso in HTML + tempo misurato |
| T6 | PDF corrotto (4 byte random) | Errore "PDF non leggibile" | Status 400 + messaggio specifico |
| T7 | PDF >10MB | Errore pre-elaborazione | Status 413 + messaggio specifico |
| T8 | PDF protetto da password | Errore "rimuovi password" | Status 400 + messaggio specifico |
| T9 | PDF scansionato (testo <100 char) | Errore "supporta solo PDF digitali" | Status 400 + messaggio specifico |
| T10 | Contratto in tedesco | **Errore bloccante** lingua non supportata | Status 400 + messaggio specifico (test gate v3) |
| T11 | C1 con `raw_excerpt` artificialmente corrotto in mock | Flag `excerpt_unverified` o retry | Almeno una delle due risposte presenti |
| T12 | 3 esecuzioni consecutive su C1 | Coerenza temperatura=0 | `risk_level` identico in ≥2/3 esecuzioni per categoria |

**Esecuzione e log:** ogni run del test plan è loggato in [[PROMPT_LOG]] con timestamp, modello, esito per test, e in [[INCIDENTS]] in caso di failure.

### Validazione output AI (riassunto)
- Il JSON restituito da Claude deve matchare lo schema Pydantic
- Ogni `raw_excerpt` con `present: true` deve passare `excerpt_is_grounded()` (§6)
- Se JSON malformato: retry una volta con prompt più restrittivo
- Se excerpt non groundato: retry una volta con prompt "verbatim quote only"
- Se i retry falliscono: errore trasparente all'utente + log in INCIDENTS.md

---

## 9. Gestione Errori e Fallback

| Errore | Comportamento |
|---|---|
| PDF non leggibile / corrotto | "Il file non è leggibile. Assicurati che sia un PDF digitale, non una scansione." |
| PDF troppo grande (>10MB) | "Il file supera i 10MB. Carica un documento più leggero." |
| PDF protetto da password | "Il file è protetto da password. Rimuovi la protezione e riprova." |
| Testo estratto < 100 caratteri | "Il documento sembra vuoto o è una scansione. SpecterAI supporta solo PDF digitali." |
| Testo estratto > 50.000 char | Elaborazione con troncamento + avviso "Contratto lungo: analizzate le prime 40.000 parole" |
| Tipo file non PDF (MIME errato) | "Formato non supportato. Carica un file PDF." |
| Lingua diversa da IT/EN | "SpecterAI supporta solo contratti in italiano o inglese." (gate esplicito v3) |
| Claude API timeout | Retry automatico (1 volta), poi → modalità degradata (vedi sotto) |
| Claude API rate limit (429) | Retry con backoff esponenziale (max 2), poi → modalità degradata |
| JSON malformato da Claude | Retry con prompt restrittivo (1 volta), poi errore generico + log in INCIDENTS.md |
| `raw_excerpt` non verificato | Retry con prompt "verbatim only" (1 volta), poi flag `excerpt_unverified` nell'UI |
| Pydantic validation error | Come JSON malformato — retry, poi errore generico |
| Errore generico server | "Qualcosa è andato storto. Nessun tuo dato è stato salvato." |

### Modalità degradata: Claude API down (nuovo in v3)

**Trigger:** API Anthropic irraggiungibile (timeout ripetuto, 5xx, rate limit persistente) dopo i retry standard.

**Comportamento MVP:**
1. L'utente riceve un messaggio chiaro: *"Il servizio di analisi è temporaneamente non disponibile. SpecterAI si appoggia all'API di Anthropic, attualmente irraggiungibile. Riprova tra qualche minuto. Nessun tuo dato è stato salvato."*
2. Il sistema **non offre output parziali da regex layer come surrogato di analisi**: l'utente potrebbe credere di aver ricevuto l'analisi completa. Better fail explicit than fake-success.
3. Status HTTP 503 (Service Unavailable) con `Retry-After: 60` header.
4. Log in `INCIDENTS.md` (categoria `claude_api_unavailable`) per tracciare frequenza.

**Roadmap post-MVP (fuori scope corso):**
- **Provider fallback:** in caso di Anthropic down, switch a GPT-4o (provider-agnostic per design, vedi §10). Richiede riscrittura prompt per il provider specifico (~1 ora di lavoro).
- **Coda offline:** persistenza temporanea della richiesta (con consenso utente) e processing differito quando l'API torna up. Implica abbandonare il design stateless — fuori scope MVP.
- **Status page interna:** dashboard real-time della disponibilità API per gli utenti.

**Single point of failure dichiarato:** SpecterAI dipende oggi al 100% da Anthropic. Questo è un rischio noto (vedi §11) e accettato per il MVP corso. La mitigazione strutturale (multi-provider) è esplicita nel post-corso.

---

## 10. Deploy, Manutenzione e Aggiornamenti

### MVP (corso)
- Deploy: localhost con `uvicorn main:app --reload`
- Variabili d'ambiente: `ANTHROPIC_API_KEY` in `.env` (mai committato)
- Avvio: `python -m uvicorn main:app --host 0.0.0.0 --port 8000`

### Futuro (post-corso)
- Deploy su Render o Railway (free tier, ~5-8 €/mese)
- Aggiunta rate limiting per prevenire abusi
- Monitoring errori con Sentry
- Provider fallback configurabile (Anthropic / OpenAI)

### Aggiornamenti modello
- Il sistema è provider-agnostic: la logica di estrazione è separata dall'LLM
- Switch da Claude a GPT-4o richiede solo cambio client e adattamento del prompt (< 1 ora)
- Il routing multi-modello permette di aggiornare singoli step senza toccare gli altri

---

## 11. Rischi, Assunzioni e Checklist Pre-Build

### Assunzioni dichiarate
- Il freelance medio gestisce 10-20 contratti/anno (nessun dato pubblico — assunzione conservativa, **non verificata empiricamente**, vedi §11.bis)
- I contratti target sono PDF digitali standard (non scansioni, non PDF con campi compilabili complessi)
- Anthropic non usa le chiamate API a pagamento per training (Anthropic Commercial ToS — verificare prima del deploy pubblico)
- Il troncamento a 40.000 char copre le clausole critiche nella maggior parte dei contratti standard sotto le ~30 pagine

### Rischi identificati

| Rischio | Probabilità | Impatto | Mitigazione |
|---|---|---|---|
| PDF parsing fallisce su layout non standard | Media | Alto | Layer deterministico + messaggio errore chiaro |
| Claude allucina clausole inesistenti | Bassa | Alto | Pydantic + **verifica `raw_excerpt` con fuzzy match (§6)** + retry |
| AI Act: classificazione alto rischio | Presente | Medio | Disclaimer + posizionamento decision-support + audit trail citazioni |
| GDPR: dati personali nel contratto | Presente | Medio | Stateless, no logging del contenuto, ToS Anthropic verificato (§7) |
| Dipendenza single-vendor da Claude API | Bassa | Alto | Architettura provider-agnostic (switch <1h); modalità degradata definita (§9) |
| Costi API sottostimati | Bassa | Basso | Stima ricalcolata (§7), budget consegna <2 €, tier Haiku come fallback economico |
| Scope creep durante sviluppo | Alta | Medio | Specifica congelata — nessuna feature non listata in scope |
| Troncamento a 40.000 char taglia clausola critica | Bassa | Alto | Avviso in output + futura versione con chunking semantico |
| `langdetect` fallisce su contratti multilingua/giuridici misti | Media | Basso | Gate "IT or EN, else block" → falso negativo blocca (UX accettabile vs falso positivo che produce output sbagliato) |
| JSON retry fallisce → utente senza output | Molto bassa | Medio | Log in INCIDENTS.md, errore trasparente all'utente |

### Checklist pre-build
- [x] Obiettivo principale definito in una frase
- [x] Perimetro: cosa fa / cosa non fa
- [x] Utenti target identificati
- [x] Input formalmente descritti (tipo, formato, vincoli, edge case)
- [x] Output formalmente descritti (struttura, formato, range)
- [x] Almeno 3 metriche di qualità quantitative definite (precision/recall/grounding)
- [x] Test plan eseguibile con criteri pass/fail
- [x] Architettura a componenti schematizzata
- [x] Dipendenze esterne elencate
- [x] Minimo 3 rischi identificati con strategia di mitigazione
- [x] Assunzioni esplicitate
- [x] Vincoli GDPR e AI Act inseriti
- [x] AI Sustainability / Green AI inserita
- [x] Prompt di sistema completo (Ruolo + Task + Formato + Vincoli + Esclusioni)
- [x] Few-shot examples specificati
- [x] Multi-model routing definito
- [x] Token optimization strategy definita
- [x] Stima costi per scenari operativi
- [x] Verifica anti-allucinazione `raw_excerpt`
- [x] Modalità degradata Claude API down
- [x] File di documentazione di progetto referenziati
- [x] Limiti di validazione dichiarati (§11.bis)

---

## 11.bis Limiti della validazione (nuovo in v3)

Per coerenza interna e onestà metodologica:

**Validazione di mercato — score corretto: 3/5 (era 4/5 in review v2).**

**Cosa è stato fatto:**
- Desk research su competitor (Spellbook, Harvey, Legora, Mikeoss, Docusign IAM) tramite Perplexity con citazioni
- Stress-test di 6 obiezioni su mercato IT/EU
- Quantificazione TAM (2,7M individual entrepreneurs in Italia, Statista 2019)
- Identificazione esplicita di "no data" zones (frequenza contratti, WTP italiana SMB)

**Cosa NON è stato fatto:**
- ❌ **Zero user interview con freelance reali**
- ❌ **Zero validazione del problema con il segmento target** (freelance/SMB italiani non-lawyer)
- ❌ Nessun A/B test, landing page test, o smoke test della willingness to pay
- ❌ Nessuna prova empirica che il segmento "non-lawyer SMB italiano" sia disposto a usare un tool del genere

**Implicazione:** L'angolo difendibile ("nessun competitor in questa sub-niche") è **basato su assenza di concorrenza**, non su domanda dimostrata. Potrebbe esistere una ragione per cui nessuno occupa quella nicchia — non è stata investigata.

**Roadmap di validazione post-MVP (fuori scope corso, ma necessaria pre-monetizzazione):**
1. N=5 interviste qualitative con freelance italiani (1 ora ciascuna)
2. Smoke test con landing page + form di interesse (2 settimane di traffic da LinkedIn organico)
3. Beta test gratuito su 10 utenti reali con interviste post-uso

Senza questi step, qualsiasi proiezione di adoption è speculativa. La spec è onesta su questo limite.

---

## 12. Documentazione di Progetto

Durante lo sviluppo vengono mantenuti i seguenti file nella cartella `prog1/`:

| File | Scopo | Quando aggiornarlo |
|---|---|---|
| [[PROMPT_LOG]] | Diario delle iterazioni: prompt testati, cosa ha funzionato e perché | Dopo ogni test significativo del prompt |
| [[INCIDENTS]] | Registro errori: cosa ha fallito, causa, soluzione applicata | Ogni volta che un errore inatteso si verifica |
| [[SESSION_HANDOFF]] | Stato del progetto tra sessioni: decisioni prese, problemi aperti, prossimi step | All'inizio e alla fine di ogni sessione di lavoro |
| [[Review Spec v2 - Gap e Roadmap Pre-Consegna]] | Review interna pre-consegna che ha generato i fix integrati in v3 | Storica — non più aggiornata |
| [[Meta-Review Multi-Agent - Validazione della Review]] | Validazione multi-agent della review che ha aggiunto 6 fix ulteriori | Storica — non più aggiornata |

Questi file fanno parte della valutazione del corso — documentare il processo di revisione (v1, v2, v3…) ha lo stesso peso del prodotto finale.

---

## 13. Provenance & Versioning della Specifica (nuovo in v3)

Audit trail della spec, per trasparenza didattica:

| Versione | Data | Driver del cambiamento | Output principale |
|---|---|---|---|
| v1 | 2026-04-30 | Lezione 2 — prima specifica completa | 11 sezioni, stack definito, 7 categorie, edge case base |
| v2 | 2026-05-04 | Independent re-analysis (Haiku) → gap detectati | +Competitive positioning, +full prompt C.I.A.R.E., +few-shot, +Green AI, +multi-model routing |
| **v3** | **2026-05-07** | [[Review Spec v2 - Gap e Roadmap Pre-Consegna]] + [[Meta-Review Multi-Agent - Validazione della Review]] | +verifica raw_excerpt, +test plan eseguibile, +scenari costo ricalcolati, +GDPR esteso, +modalità degradata, +limiti validazione, +metriche precision/recall, +gate lingua |

**Principio di processo:** ogni versione è preservata, mai sovrascritta. La v1 e la v2 restano leggibili per audit del processo di iterazione (parte della valutazione del corso).

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Brainstorming - Validazione Idea]]
- [[Contract Analyzer - Validazione Idea]]
- [[Specifica Tecnica v1 - SpecterAI]] — superata
- [[Specifica Tecnica v2 - SpecterAI]] — superata
- [[Review Spec v2 - Gap e Roadmap Pre-Consegna]]
- [[Meta-Review Multi-Agent - Validazione della Review]]
- [[PROMPT_LOG]]
- [[INCIDENTS]]
- [[SESSION_HANDOFF]]
- [[Lezione 2 - Specifica Tecnica e Prompt Engineering]]
- [[Template - Specifica Tecnica]]
- [[ChristianG_File2_Valutazione_Studente]]
- [[ChristianG_File3_Stack_Tecnico]]
