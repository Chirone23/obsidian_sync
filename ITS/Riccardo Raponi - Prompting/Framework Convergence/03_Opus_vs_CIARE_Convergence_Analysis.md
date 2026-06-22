# Opus Anatomy vs C.I.A.R.E. — Analisi dei Gap

**Tipo:** Confronto strutturale + adattamento multi-LLM  
**Data:** 22 giugno 2026  
**Fonte:** "The Anatomy of a Claude 4.6 Prompt" (Anthropic, maggio 2026)  
**Collegato a:** [[00_Opus_Anatomy_8Steps_Pattern]] | [[01_C-I-A-R-E_Pattern]] | [[Prompting MOC]] | [[Prompt Library]]

---

## I Due Framework

### C.I.A.R.E. (Riccardo Raponi)

Struttura **contenutistica** — cosa mettere nel prompt:
- **C** — Contesto (background, prerequisiti)
- **I** — Intento (obiettivo, uso dell'output)
- **A** — Audience + Output (destinatario, formato)
- **R** — Regole (vincoli di stile, lunghezza, tono)
- **E** — Esempi (Few-Shot, modelli di riferimento)

### Opus Anatomy (Claude 4.6 Prompt)

Struttura **conversazionale** — come costruire il dialogo uomo-IA:
1. **Task** — definisci cosa vuoi & successo
2. **Context Files** — carica file con contesto/expertise
3. **Reference** — mostra un esempio di quello che vuoi
4. **Brief** — tipo di output + lunghezza
5. **Rules** — standard e audience
6. **Conversation** — chiedi domande di chiarimento PRIMA di eseguire
7. **Plan** — lista 3 regole key + piano 5 step
8. **Alignment** — procedi solo se aligned

---

## Matrice di Sovrapposizione

| Elemento | C.I.A.R.E. | Opus Anatomy | Copertura |
|----------|-----------|--------------|-----------|
| Background / Contesto | ✅ **C** | ✅ **Step 2** (Context Files) | **Coperto** |
| Obiettivo | ✅ **I** | ✅ **Step 1** (Task) | **Coperto** |
| Formato output | ✅ **A** | ✅ **Step 4** (Success Brief) | **Coperto** |
| Vincoli | ✅ **R** | ✅ **Step 5** (Rules) | **Coperto** |
| Modelli | ✅ **E** | ✅ **Step 3** (Reference) | **Coperto** |
| **Domande di chiarimento prima** | ❌ | ✅ **Step 6** | **GAP 1** |
| **Piano di esecuzione** | ❌ | ✅ **Step 7** | **GAP 2** |
| **Alignment check** | ❌ | ✅ **Step 8** | **GAP 3** |

---

## I 3 Gap Principali

### GAP 1 — Conversation (Step 6)

**Cosa manca:** Prima di eseguire qualsiasi prompt, l'IA dovrebbe **fare domande di chiarimento** sul compito, non iniziare direttamente.

**Esempio di Step 6:**
```
Prima di procedere, fammi domande di chiarimento (usa AskUserQuestion):
- Qual è il livello di seniority del destinatario?
- Qual è il contesto aziendale esatto?
- Ci sono vincoli legali/compliance da considerare?

Non iniziare il task finché non abbiamo allineato i dettagli.
```

**Impatto:** Riduce il rischio di allucinazioni per mancanza di contesto; aumenta iteratività.

**Quando usare:**
- ✅ Task critici (business, legale, output public-facing)
- ❌ Task creativi/esplorativi (brainstorm, ideazione)

---

### GAP 2 — Plan (Step 7)

**Cosa manca:** Prima di scrivere, l'IA dovrebbe **pianificare il ragionamento** — elencando le 3 regole più importanti dal contesto e i 5 step di esecuzione.

**Esempio di Step 7:**
```
Prima di scrivere qualsiasi cosa:

1. Dimmi quali 3 regole dal mio contesto sono CRITICHE per questo task.
2. Dammi il tuo piano di esecuzione in max 5 step.
3. Aspetta il mio OK prima di procedere con l'output finale.
```

**Output atteso:**
```
REGOLE CRITICHE:
1. [Regola A dal contesto] — perché è critica
2. [Regola B dal contesto] — perché è critica
3. [Regola C dal contesto] — perché è critica

PIANO DI ESECUZIONE:
Step 1: [descrizione]
Step 2: [descrizione]
Step 3: [descrizione]
Step 4: [descrizione]
Step 5: [descrizione]
```

**Impatto:** Aumenta trasparenza del ragionamento; permette correzioni preventive.

**Quando usare:**
- ✅ Task complessi (analisi, design, sintesi)
- ✅ Output che richiede sequenzialità
- ❌ Task semplici (estrazione, classificazione)

---

### GAP 3 — Alignment (Step 8)

**Cosa manca:** Un checkpoint esplicito **prima di eseguire** in cui uomo e IA confermano che hanno lo stesso goal.

**Esempio di Step 8:**
```
Solo dopo che il piano è approvato e abbiamo allineato le 3 regole,
procedi con l'output finale.

Se riscontri una conflitto tra regole, fermati e chiedi chiarimento
PRIMA di procedere (non andare avanti con un'assunzione).
```

**Impatto:** Riduce il rework; aumenta fiducia nel risultato.

**Quando usare:**
- ✅ Sempre, come checkpoint finale pre-esecuzione
- ❌ Mai saltare se il prompt è critico

---

## Quale Framework per Quale Caso?

| Caso | Framework | Perché |
|------|-----------|--------|
| Prompt generico, rapido | C.I.A.R.E. solo | Veloce, sufficient |
| Prompt critico (business) | C.I.A.R.E. + Gap 1 (Conversation) | Chiedi prima di procedere |
| Prompt complesso (design/strategia) | C.I.A.R.E. + Gap 1 + Gap 2 (Plan) | Pianifica il ragionamento |
| Prompt mission-critical | C.I.A.R.E. + Gap 1 + Gap 2 + Gap 3 (Alignment) | Triplo checkpoint |

---

## Adattamento Multi-LLM

### Quali step sono Claude-specific?

| Step | Claude | GPT-4o | Gemini 2.0 | Note |
|------|--------|--------|-----------|------|
| 1-5 (C.I.A.R.E.) | ✅ | ✅ | ✅ | LLM-agnostic |
| 6 (Conversation) | ✅ AskUserQuestion | ⚠️ Custom tool | ⚠️ Custom tool | Claude ha il tool nativo; altri LLM richiedono simulazione |
| 7 (Plan / CoT) | ✅ | ✅ o1-reasoning | ✅ thinking-mode | Trasversale ma formulazione varia |
| 8 (Alignment) | ✅ | ✅ | ✅ | Trasversale |

---

## Adattamento per Claude (di base)

```markdown
Prima di procedere, chiedi domande di chiarimento:
[Usa AskUserQuestion tool]

Poi, prima di scrivere:
"Fammi vedere il piano:
- 3 regole critiche da [contesto]
- 5 step di esecuzione"

Aspetta OK.

Solo dopo: esegui il task.
```

---

## Adattamento per GPT-4o (o1-reasoning)

```markdown
Prima di procedere, lista le domande:
- [Q1]
- [Q2]
- [Q3]
Attendi risposte.

Poi, con o1-reasoning attivato:
"Pensa passo dopo passo:
- Quali 3 regole sono critiche?
- Quali 5 step per questo task?"

Mostra il ragionamento. Aspetta OK.

Solo dopo: esegui il task.
```

**Nota:** GPT-4o non ha AskUserQuestion nativo — simula con prompt testuale.

---

## Adattamento per Gemini 2.0 (con thinking-mode)

```markdown
Prima di procedere, elenca le domande di chiarimento:
- [Q1]
- [Q2]
- [Q3]
Attendi risposte.

Poi, con thinking-mode attivato:
"Ragiona internamente:
- Quali 3 regole dal contesto sono critiche?
- Quali 5 step di esecuzione?"

Mostra la sintesi del ragionamento. Aspetta OK.

Solo dopo: esegui il task.
```

**Nota:** Gemini 2.0 ha thinking-mode per il ragionamento; diverso da o1 di OpenAI.

---

## Template Unificato (Multimodello)

```markdown
### FASE 0 — CONVERSATION (GAP 1)

Prima di procedere al task, chiedi chiarimenti:
[LLM-specific: AskUserQuestion / prompt testuale / thinking mode]

Domande suggerite:
1. [Q1]
2. [Q2]
3. [Q3]

### FASE 1 — CONTEXT FILES (C.I.A.R.E. C)

[Contesto: background, prerequisiti]

### FASE 2 — TASK (C.I.A.R.E. I)

[Intento: obiettivo, uso dell'output]

### FASE 3 — REFERENCE (C.I.A.R.E. E)

[Esempi: Few-Shot di riferimento]

### FASE 4 — RULES & AUDIENCE (C.I.A.R.E. R + A)

[Vincoli di stile, lunghezza, tono, destinatario]

### FASE 5 — PLAN (GAP 2)

Prima di procedere, elenca:
- 3 regole critiche dal contesto e perché
- 5 step di esecuzione

Aspetta OK.

### FASE 6 — EXECUTION

[Solo dopo alignment: esegui il task]

### FASE 7 — CHECKPOINT (GAP 3)

Se riscontri conflitto tra regole, fermati e chiedi.
Non procedere con assunzioni.
```

---

## Test Previsto (prossima sessione)

Testeremo il template unificato con:
- ✅ Claude Haiku (baseline)
- ✅ GPT-4o (con o1-reasoning)
- ✅ Gemini 2.0 (con thinking-mode)

Metriche:
1. Chiarezza del piano (pre-esecuzione)
2. Tasso di allucinazioni (mancanza di contesto)
3. Aderenza alle regole critiche
4. Tempo totale (overhead della pianificazione)

---

## Connessioni

- [[00_Opus_Anatomy_8Steps_Pattern]] | [[01_C-I-A-R-E_Pattern]] — il framework di base (Riccardo)
- [[Prompt Library]] — prompt già strutturati per aggiornamento
- [[Prompting MOC]] — fondamenti
- [[Lab - Board Virtuale PMI AI Deployment]] — caso complesso per test
