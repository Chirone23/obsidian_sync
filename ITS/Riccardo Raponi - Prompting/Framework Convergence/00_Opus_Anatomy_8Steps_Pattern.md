# Opus Anatomy — The 8-Step Prompt Pattern

**Fonte:** "The Anatomy of a Claude 4.6 Prompt" (Anthropic, maggio 2026)  
**Tipo:** Struttura conversazionale per prompt efficaci  
**Applicabilità:** Claude 4.6 Opus (adattabile a altri LLM)

---

## Definizione

Opus Anatomy è un framework a 8 step che struttura il **flusso conversazionale** tra uomo e IA, non il contenuto del prompt.

Enfasi: costruire il dialogo in modo che l'IA faccia domande, pianifichi, e chieda allineamento **prima** di eseguire, non dopo.

---

## I 8 Step

### STEP 1 — TASK

**Obiettivo:** Definisci cosa vuoi e cosa significa "successo".

**Formula:**
```
I want to [TASK] so that [SUCCESS CRITERIA].
```

**Non** usare: "Act as a senior expert." Quel paradigma è superato.

**Esempio:**
```
I want to design a team structure for a B2B SaaS MVP
so that we can ship in 4 months without technical debt or hiring misses.
```

**Perché funziona:** 
- Chiaro obiettivo + metrica di successo
- L'IA comprende il vincolo (4 mesi) e il trade-off (velocità vs. qualità)

---

### STEP 2 — CONTEXT FILES

**Obiettivo:** Carica file con contesto, expertise, e vincoli operativi.

**Formula:**
```
First, read these files completely before responding:
[filename.md] — [what it contains]
[filename.md] — [what it contains]
```

**Non** spiegare il tuo approccio nel prompt inline. Metti tutto in file.

**Esempio:**
```
First, read these files completely before responding:
company-values.md — our engineering philosophy and constraints
hiring-budget.md — salary ranges and headcount limits
technical-stack.md — required skills and tech debt we're addressing
```

**Perché funziona:**
- L'IA legge il contesto PRIMA di ragionare
- File separati = facile da aggiornare senza riscrivere il prompt

---

### STEP 3 — REFERENCE

**Obiettivo:** Mostra esattamente cosa vuoi. Upload un esempio o paste un modello.

**Formula:**
```
Here is a reference to what I want to achieve:
[Upload reference file as markdown, or paste it here]
Here's what makes this reference work:
[Paste your reverse-engineered blueprint — the patterns, tone, 
structure, and rules you extracted from the reference. Format 
each one as a rule starting with "Always" or "Never."]
```

**Non** dire "give me something like...". Mostra il risultato esatto che cerchi.

**Esempio:**
```
Here is a reference team structure I want to emulate:

[PASTE: Example team org chart from a successful startup]

Here's what makes this reference work:
- Always: Senior engineer as tech lead, not manager
- Always: Clear separation between IC (individual contributor) and manager roles
- Never: One person doing both hiring and code review
- Always: Budget allocated per role, not per phase
```

**Perché funziona:**
- L'IA vede il pattern concreto, non interpreta "professionale"
- Reverse-engineering = trasparenza sul tuo stile

---

### STEP 4 — SUCCESS BRIEF

**Obiettivo:** Specifica il deliverable: tipo, lunghezza, format, come sarà usato.

**Formula:**
```
Here's what I need for my version:
SUCCESS BRIEF
Type of output: [contract / email / proposal / meeting agenda / decision matrix]
Length: [200 words / 5 slides / 3 tables / 10-page doc]
Recipient's reaction: [What should they think/feel/do after reading?]
Does NOT sound like: [What to avoid — generic AI, too casual, formal jargon]
Success means: [They sign? They reply? They take action?]
```

**Non** dire "make it good" o "make it professional".

**Esempio:**
```
Type of output: Decision matrix + recommendation narrative
Length: 2-page document (1 matrix, 1 page prose)
Recipient's reaction: CEO reads in 8 minutes, feels confident in the decision
Does NOT sound like: Generic consultant advice, academic jargon, hedging language
Success means: CEO approves hiring sequence by Friday, no further clarification needed
```

**Perché funziona:**
- Metrica concreta (tempo di lettura, azione)
- Tono negativo = più facile da colpire ("non casual" vs. "be professional")

---

### STEP 5 — RULES

**Obiettivo:** Elenca standard, vincoli, audience, linee guida di stile.

**Formula:**
```
My context file contains my standards, taste & audience.
Read it fully before starting. If you're about to break one of my rules, 
stop and tell me.
```

**Non** ripetere le regole inline. Mettile in file (Step 2).

**Esempio:**
```
My context file contains:
- Our engineering values (no shortcuts on security, ship fast)
- Our hiring philosophy (junior devs grow with mentorship, not hired as junior)
- Our communication style (numbers before words, show trade-offs)

If you're about to recommend something that violates one of these, stop and tell me.
```

**Perché funziona:**
- L'IA si auto-interrompe se la cattura
- Accountability trasparente

---

### STEP 6 — CONVERSATION

**Obiettivo:** PRIMA di eseguire, l'IA fa domande di chiarimento.

**Formula (Claude-specific):**
```
DO NOT start executing yet. Instead, ask me clarifying questions 
(use 'AskUserQuestion' tool) so we can refine the approach 
together step by step.
```

**Formula (GPT-4o / Gemini):**
```
DO NOT start executing yet. Instead, list 3-5 clarifying questions 
you need answered before proceeding. Wait for my responses.
```

**Esempio di domande buone:**
```
1. When you say "4 months," does that include hiring time or just coding?
2. Is the 30% cost reduction target absolute or aspirational?
3. Are remote and on-site engineers paid the same, or is there a regional adjustment?
4. Do you have an existing team I'm restructuring, or hiring from zero?
```

**Perché funziona:**
- Riduce allucinazioni per mancanza di contesto
- L'uomo fornisce informazioni critiche PRIMA, non dopo

---

### STEP 7 — PLAN

**Obiettivo:** Prima di scrivere, l'IA mostra il piano: 3 regole critiche + 5 step di esecuzione.

**Formula:**
```
Before you write anything, list the 3 rules from my context file 
that matter most for this task. Then give me your execution plan 
(5 steps maximum).

Only begin work once we've aligned.
```

**Output atteso:**
```
TOP 3 RULES FOR THIS TASK:
1. [Rule A from context] — why it's critical here
2. [Rule B from context] — why it's critical here
3. [Rule C from context] — why it's critical here

EXECUTION PLAN:
Step 1: [description]
Step 2: [description]
Step 3: [description]
Step 4: [description]
Step 5: [description]
```

**Perché funziona:**
- Trasparenza pre-esecuzione
- Permette correzioni preventive invece di rework
- Cattura conflitti tra regole PRIMA di procedere

---

### STEP 8 — ALIGNMENT

**Obiettivo:** Checkpoint finale. Procedi solo se uomo e IA hanno lo stesso goal.

**Formula:**
```
Only begin work once we've aligned.
If you encounter a conflict between rules, stop and tell me 
before proceeding. Do not assume.
```

**Non** procedere se c'è ambiguità.

**Esempio di stop-point:**
```
⛔ CONFLICT DETECTED:
Rule 1 says: "Hire senior engineers to lead, not manage"
Rule 2 says: "Minimize headcount in first year"

These conflict for the CTO role. Should the CTO be:
(A) Senior IC + external contractor for hiring?
(B) Senior engineer doubling as part-time manager?
(C) Hire full-time manager from day 1?

I'm not proceeding until you clarify.
```

**Perché funziona:**
- Evita il rework massiccio
- Aumenta fiducia nel risultato finale
- Non procedere = principio etico

---

## Template Completo (Copy-Paste Ready)

```markdown
# [Task Title]

## STEP 1 — TASK
I want to [TASK] so that [SUCCESS CRITERIA].

## STEP 2 — CONTEXT FILES
First, read these files completely before responding:
[filename.md] — [what it contains]
[filename.md] — [what it contains]

## STEP 3 — REFERENCE
Here is a reference to what I want to achieve:
[PASTE REFERENCE]

Here's what makes this reference work:
- Always: [pattern]
- Always: [pattern]
- Never: [pattern]

## STEP 4 — SUCCESS BRIEF
Type of output: [type]
Length: [length]
Recipient's reaction: [what should they think/feel/do]
Does NOT sound like: [what to avoid]
Success means: [concrete success metric]

## STEP 5 — RULES
My context file contains my standards, taste & audience.
Read it fully before starting. If you're about to break one of my rules, 
stop and tell me.

## STEP 6 — CONVERSATION
DO NOT start executing yet. Instead, ask me clarifying questions 
(use 'AskUserQuestion' tool) so we can refine the approach 
together step by step.

## STEP 7 — PLAN
Before you write anything, list the 3 rules from my context file 
that matter most for this task. Then give me your execution plan (5 steps maximum).

Only begin work once we've aligned.

## STEP 8 — ALIGNMENT
Only begin work once we've aligned.
If you encounter a conflict between rules, stop and tell me 
before proceeding. Do not assume.
```

---

## Adattamento per Diversi LLM

### Claude (Nativo)
- ✅ Step 6 usa `AskUserQuestion` tool
- ✅ Step 7-8 native (CoT, reasoning trasparente)

### GPT-4o (o1-reasoning)
- ⚠️ Step 6: lista domande in testo, attendi risposte
- ✅ Step 7: con o1-reasoning attivato, mostra ragionamento step-by-step
- ✅ Step 8: chiedi di fermarsi se conflitto

### Gemini 2.0 (thinking-mode)
- ⚠️ Step 6: lista domande in testo
- ✅ Step 7: con thinking-mode attivato, mostra sintesi del ragionamento
- ✅ Step 8: chiedi di fermarsi se conflitto

---

## Métriche di Successo

Per validare che il prompt funziona bene:

- ✅ **Chiarezza del piano** — L'IA lista le 3 regole correttamente?
- ✅ **No allucinazioni** — Chiede prima, non inventa contesto?
- ✅ **Aderenza** — L'output segue le regole critiche?
- ✅ **Tempestività** — Quanto tempo aggiunto dal flusso Conversation→Plan→Alignment?

---

## Quando Usare Opus Anatomy

| Caso | Usa? | Perché |
|------|------|--------|
| Prompt semplice, generico | ❌ | Overhead eccessivo |
| Prompt critico (business, legale) | ✅ | Triple checkpoint before execution |
| Prompt complesso (design, strategia) | ✅ | Planning visibility |
| Output creative/exploratory | ⚠️ | Conversation OK, Plan/Alignment opzionali |

---

## Connessioni

- [[02_C-I-A-R-E_Pattern]] — framework di contenuto (complementare)
- [[03_Opus_vs_CIARE_Gaps]] — confronto e convergenza
- [[Prompting MOC]] — fondamenti
- [[Prompt Library]] — prompt pronti da aggiornare con questo pattern
