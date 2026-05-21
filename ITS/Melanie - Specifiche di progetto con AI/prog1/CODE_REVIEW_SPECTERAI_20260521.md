# Code Review — SpecterAI (2026-05-21)

> Revisione critica di SpecterAI per corso ITS AI Projects Development.
> Modello: Claude Opus 4.7 | Contesto: MVP pre-beta, 6 moduli Python, 7/8 test PDF OK

---

## [C] Contesto

**Stack:** Python 3.12, FastAPI, PyMuPDF 1.27, spaCy it_core_news_sm, Pydantic v2, Jinja2, Claude Code CLI via subprocess (stdin).

**Risultati test E2E:**
- 7/8 PDF contrattuali superano il test (1 scansionato rifiutato by design, comportamento corretto)
- 5/5 contratti di noleggio auto analizzati correttamente (tipologia aggiuntiva non prevista nella spec)
- Latenza: 163–268s per contratti da 40k caratteri
- Bug risolto in produzione: WinError 206 su Windows (messaggio argv troppo lungo → migrato a stdin)

---

## 1. Giudizio sintetico

MVP **didatticamente eccellente** ma **non production-ready**. L'architettura a layer (PDF → regex → privacy → LLM → schema) è quella giusta e mostra maturità. Tuttavia ci sono **3 bug reali** in `privacy_filter` e `llm_client` che in produzione causerebbero leak PII, crash su input borderline, e hang indefiniti. Con 1-2 giorni di lavoro mirato il sistema diventa consegnabile a un cliente piccolo; così com'è, no.

**Livello:** prototipo avanzato / pre-beta. Sopra la media per uno studente ITS con 6 mesi di Python.

---

## 2. Pregi (max 5)

### **`schemas.py:45-56` — Validazione ellissi nel validator Pydantic**
Validator che rifiuta excerpt con ellissi (`[...]`, `...`) e <20 char è la scelta giusta: forza il LLM a quotare letterale, e fallisce esplicitamente nel layer dati invece che a valle nel rendering.

### **`schemas.py:29-35` — BeforeValidator per normalizzazione str → list[str]**
`BeforeValidator` per normalizzare `str → list[str]` è il pattern Pydantic v2 idiomatico; molti studenti farebbero questa coercion nel client LLM direttamente.

### **`llm_client.py:254-278` — Separazione redact → call → restore**
Separazione redact → call → restore con mapping mantiene le PII fuori dal payload LLM ma riemerge le quote letterali nel report finale. Architetturalmente corretto.

### **`pdf_processor.py:103-107` — Rifiuto PDF vuoti**
Rifiutare PDF con <100 char di testo invece di passare stringa vuota al LLM è una scelta difensiva matura.

### **`main.py:315-318` — Distinzione errori transitori vs permanenti**
Distinzione `RuntimeError` (503 + Retry-After) vs `ValueError` (500) mappa correttamente errori transitori (network) vs permanenti (schema).

---

## 3. Problemi critici (priorità ALTA)

### **`privacy_filter.py:162` — Falso positivo CF su parole comuni**

**Pattern:** `\b[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]\b` con `re.IGNORECASE`

**Problema:** Cattura qualunque sequenza alfanumerica della stessa forma — incluso testo lowercase. Più grave: con IGNORECASE attivo il pattern matcha anche stringhe come `articolo15bisXY99ZW123A` che possono comparire in clausole legali.

**Rischio:** Redazione di testo legittimo → l'LLM analizza `[CF_1]` invece della clausola reale → analisi falsata.

**Spec:** `Privacy Filter Integration.md:118` richiede *"CF (16 alfanum, RGX validato Agenzia Entrate)"*, ma codice non implementa validazione AdE.

---

### **`privacy_filter.py:163` — PIVA matcha qualsiasi 11 cifre (false positive massiccia)**

**Pattern:** `\b\d{11}\b`

**Problema:** Cattura importi, numeri di protocollo, codici interni. In un contratto con:
- "protocollo n. 20240015432" → redatto come PIVA
- Importi in centesimi "€ 10000000000,50" → redatto come PIVA

**Rischio:** Perdita di dati legittimi nel testo inviato al LLM.

**Spec:** `Privacy Filter Integration.md:119` esplicitamente richiede *"Partita IVA (11 cifre **+ checksum Luhn IT**)"*, ma codice non valida Luhn.

---

### **`privacy_filter.py:185-199` — Ordering bug nella sostituzione spaCy (data corruption)**

**Logica:**
1. Esegui regex substitution (testo mutato, offset cambiano)
2. Carica spaCy (che lavora su testo post-regex)
3. Itera `sorted(entities, reverse=True)` ma gli offset di spaCy si riferiscono al testo **già modificato**
4. Ogni iterazione modifica `text` di nuovo, invalidando le posizioni successive

**Problema reale:** Se due entità spaCy si sovrappongono o sono vicine a placeholders già inseriti, gli offset si mischiano. Esempio:
```
testo: "Mario Rossi, CF RSSMRA80A01H501U"
post-regex: "Mario Rossi, CF [CF_1]"
spaCy NER find: ("Rossi", start=6, end=11) — ma "Rossi" non è più a quella posizione!
```

**Rischio reale:** Corruzione del testo, placeholder spezzati tipo `[PER[CF_1]_2]`, testo manomesso inviato al LLM.

**Check alla riga 194** (`before == "["`) è una toppa, non una soluzione.

---

### **`llm_client.py:231-247` — Nessun timeout su subprocess (DoS + indefinite hang)**

**Codice:**
```python
result = subprocess.run(
    ["claude", "-p", "--system-prompt", ...],
    input=user_message,
    capture_output=True,
    text=True,
)
```

**Problema:** 
- `subprocess.run` senza `timeout=` può bloccare indefinitamente se Claude CLI hangga (rete, auth scaduta, OOM)
- Combinato con FastAPI sync endpoint (`main.py:302`), una richiesta che hangga **blocca un worker per sempre**
- Con 4 worker uvicorn default, 4 utenti malevoli DoS-ano il servizio (tutte le connessioni pendono)

**Spec:** `Specifica Tecnica v3:610` richiede *"Claude API timeout → Retry automatico"*, ma codice non ha timeout.

**Rischio:** DoS, halt indefinito su ambiente di produzione.

---

### **`llm_client.py:263` — Regex `\{.*\}` greedy con DOTALL (silent failure)**

**Codice:**
```python
json_match = re.search(r'\{.*\}', raw_response, re.DOTALL)
```

**Problema:** Se Claude restituisce testo + JSON + testo, `\{.*\}` cattura dal **primo `{`** all'**ultimo `}`** — che potrebbe includere markdown code fence content o esempi. 

Esempio fallita:
```
Risposta Claude:
"Ecco l'analisi: 
{
  "present": true,
  "raw_excerpt": ["Clausola penale..."]
}

Nel nostro sistema, usiamo {key: value} come pattern."
```
Regex cattura dal primo `{` al **`}`** della frase finale → JSON malformato.

**Spec:** `Specifica Tecnica v3:612` richiede *"JSON malformato da Claude → Retry con prompt restrittivo"*, ma il fallback non è robusto.

**Rischio:** JSON malformato, fallback su retry a volte basta, a volte no.

---

### **`main.py:301-319` — Endpoint sync blocca event loop (zero concorrenza)**

**Codice:**
```python
@app.post("/analyze", response_class=HTMLResponse)
async def analyze_contract(request: Request, file: UploadFile = File(...)):
    ...
    result = analyze(contract_text, metadata)  # Sync call!
    ...
```

**Problema:**
- `analyze()` chiama `subprocess.run()` sincrono che dura 163-268 secondi
- Definito come `async def` ma fa lavoro sync bloccante → **blocca l'event loop FastAPI per 3+ minuti**
- Una seconda richiesta concorrente **non parte** finché la prima non finisce, anche se uvicorn ha worker liberi
- Tutti gli altri endpoint (compreso static files) bloccati

**Spec:** Niente di esplicito, ma best practice FastAPI.

**Rischio:** Single-threaded de facto, nessuna concorrenza anche con 4 worker disponibili.

---

## 4. Problemi minori (MEDIA/BASSA)

### **`privacy_filter.py:166` — Phone regex troppo permissivo**
`(?:\+39|39)?[\s\-]?(?:0\d{1,4}[\s\-]?\d{4,8}|3\d{2}[\s\-]?\d{6,7})\b` rende il prefisso opzionale → matcha qualsiasi numero italiano-shaped, incluso partite IVA spezzate o numeri civici.

### **`privacy_filter.py:158` — Caricamento spaCy a import-time (no fallback)**
Crash dell'app se modello assente, invece di degradare a "solo regex". Per un MVP didattico OK, per prod meglio lazy-load + fallback.

### **`llm_client.py:222` — Modello hardcoded**
`_MODEL = "claude-sonnet-4-6"` dovrebbe stare in env var / config file.

### **`llm_client.py:267-275` — Retry senza backoff né limite**
Un solo retry, senza distinguere errori transitori (rate limit, 529) da permanenti (schema violation). Su rate limit dovresti aspettare; su schema violation un secondo tentativo identico spesso fallisce uguale.

**Spec:** `Specifica Tecnica v3:611` richiede *"Retry con **backoff esponenziale** (max 2)"*, codice fa 1 retry senza delay.

### **`pdf_processor.py:90` — MAX_CHARS silenzioso**
`MAX_CHARS = 40_000` trunca senza avvisare l'utente. Un contratto da 60k char viene analizzato a metà e l'utente non lo sa.

**Spec:** `Specifica Tecnica v3:607` richiede *"troncamento **+ avviso** 'Contratto lungo'"*, codice no.

### **`regex_layer.py:124` — Validazione format non rigorosa**
`€\s*\d[\d\.,]+` non valida formato (es. `1.2.3,45` matcha comunque). Per metadati passati al LLM va bene, per uso downstream no.

### **`main.py:295` — MAX_SIZE controllato dopo read()**
Un utente può uploadare 1GB e tu lo carichi tutto in RAM prima di rifiutare. Usa `Content-Length` header o streaming check.

### **`main.py` — Nessun rate limiting**
Ogni call costa token API. Senza throttling, costo non controllato.

**Spec:** `Specifica Tecnica v3:645` dice *"Aggiunta rate limiting per prevenire abusi"* ma è esplicitamente **post-MVP**.

### **`schemas.py:62` — Disclaimer hardcoded nel modello dati**
Mescola layer (presentation in data model). Sposta in template.

### **`llm_client.py:250-252` — Mutazione in-place del Pydantic model**
Funziona ma Pydantic v2 preferisce `model_copy(update=...)`.

---

## 5. Top 3 miglioramenti (alto impatto / basso sforzo)

### **1. Aggiungi `timeout=300` a subprocess + `await asyncio.to_thread(analyze, ...)`** 
- **Sforzo:** 5 minuti
- **Impatto:** Elimina DoS + sblocca concorrenza reale  
- **ROI:** Massimo

**Codice:**
```python
@app.post("/analyze", response_class=HTMLResponse)
async def analyze_contract(request: Request, file: UploadFile = File(...)):
    ...
    result = await asyncio.to_thread(analyze, contract_text, metadata)
    ...
```

E in `llm_client.py`:
```python
result = subprocess.run(
    ["claude", "-p", ...],
    input=user_message,
    capture_output=True,
    text=True,
    timeout=300,  # 5 min
)
```

### **2. Restringi PIVA e CF con context-awareness + Luhn**
- **Sforzo:** 30 minuti
- **Impatto:** Elimina la classe più grande di falsi positivi
- **Esempi di fix:**

```python
# PIVA con checksum Luhn IT (da Privacy Filter Integration.md:119)
def validate_piva_luhn(piva_candidate: str) -> bool:
    if len(piva_candidate) != 11 or not piva_candidate.isdigit():
        return False
    # Checksum Luhn IT algorithm
    s = sum(int(piva_candidate[i]) * (1 if i % 2 == 0 else 2) 
            for i in range(10))
    check = (10 - (s % 10)) % 10
    return int(piva_candidate[10]) == check

# CF con keyword context (non isolato)
_CF_RE = re.compile(
    r'(?:codice\s+fiscale|CF|c\.?f\.?)\s*[:\-]?\s*([A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z])',
    re.IGNORECASE
)
```

### **3. Sostituisci `re.search(r'\{.*\}')` con parsing strutturato**
- **Sforzo:** 20 minuti
- **Impatto:** Elimina una classe di silent failure

**Opzione A — json.JSONDecoder:**
```python
from json import JSONDecoder
decoder = JSONDecoder()
try:
    json_obj, idx = decoder.raw_decode(raw_response)
except json.JSONDecodeError:
    # fallback: retry
```

**Opzione B — force tag markers (meglio):**
Modifica system prompt per rispondere in `<json>...</json>` tag, poi estrai per regex su tag.

---

## 6. Problemi codice ↔ Spec (mapping)

| Problema codice | Documentato in | Status |
|---|---|---|
| PIVA falso positivo (`\d{11}`) | `Privacy Filter Integration.md:119` — *"Partita IVA (11 cifre **+ checksum Luhn IT**)"* | ⚠️ Spec richiede Luhn, codice no |
| CF pattern debole | `Privacy Filter Integration.md:118` — *"CF (16 alfanum, **RGX validato Agenzia Entrate**)"* | ⚠️ Spec richiede AdE, codice usa regex laxa con IGNORECASE |
| Nessun timeout subprocess | `Specifica Tecnica v3:610` — *"Claude API timeout → Retry automatico"* | ⚠️ Spec prevede gestione timeout, codice no |
| Nessun backoff | `Specifica Tecnica v3:611` — *"Retry con **backoff esponenziale** (max 2)"* | ⚠️ Spec dice exp backoff max 2, codice fa 1 retry senza delay |
| MAX_CHARS silenzioso | `Specifica Tecnica v3:607` — *"troncamento **+ avviso** 'Contratto lungo'"* | ⚠️ Spec richiede avviso, codice trunca silenzioso |
| Rate limiting assente | `Specifica Tecnica v3:645` — *"Aggiunta rate limiting"* | ✅ Esplicitamente **post-MVP**, non un bug |
| spaCy `sm` invece di `lg` | `Privacy Filter Integration.md:129` dice `lg` | ⚠️ Downgrade noto (timeout su Win) — trade-off accettato per MVP |

---

## 7. Problemi genuinamente nuovi (non nella spec)

### **`privacy_filter.py:185-199` — Ordering bug nella sostituzione spaCy**
Spec descrive pipeline regex → spaCy ma non specifica come gestire offset dopo sostituzioni regex. Bug architetturale introdotto in implementazione.

### **`main.py:302` — async def con subprocess sync blocca event loop**
Spec dice "uvicorn", non come gestire concorrenza. Bug FastAPI-specifico non documentato.

### **`llm_client.py:263` — Regex greedy per parsing JSON**
Spec dice "JSON malformato → retry" ma non come parsare. Implementation detail fragile.

---

## 8. Auto-valutazione confidence

**4/5.** 

Sono confidente sui 6 problemi critici (li ho ricostruiti dal codice). L'unica incertezza è sul comportamento esatto di **`privacy_filter.py:185-199`** sotto entità sovrapposte — andrebbe testato con un PDF reale contenente "Mario Rossi, CF RSSMRA80A01H501U" per vedere se il placeholder CF interferisce con la NER di spaCy. Sospetto di sì ma non l'ho verificato runtime.

---

## Conclusione

SpecterAI è un **MVP solido dal punto di vista architetturale**. I layer sono ben separati, la strategia privacy è corretta (redact → LLM → restore), e la gestione errori è trasparente (fail explicit).

Tuttavia, **l'implementazione ha 3 bug critici che rendererebbero il sistema fragile in produzione:**
1. Privacy filter che redatta dati legittimi (CF/PIVA false positive)
2. No timeout che consente indefinite hang / DoS
3. Event loop bloccato che paralizza concorrenza

**Suggerimento per il prossimo step:** Prima di passare a nuove features, fix i 3 bug critici + top 3 miglioramenti. Richiede 1-2 giorni di lavoro, trasforma il sistema da "prototipo avanzato" a "consegnabile a un cliente".

---

## Riferimenti

- **Spec v3:** `Specifica Tecnica v3 - SpecterAI.md` (sezioni 9.1, 9.2, 10)
- **Privacy layer design:** `Privacy Filter Integration.md` (architettura hybrid map-back)
- **Test results:** `PROMPT_LOG.md` (round 1-5 esecuzione test E2E)
- **Incident tracking:** `INCIDENTS.md` (INC-004 WinError 206 risolto)

