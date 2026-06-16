# Prompt Library

Raccolta di prompt pronti all'uso, strutturati secondo il framework **C.I.A.R.E.** di Riccardo Raponi.
Ogni prompt include: contesto d'uso, il testo da copiare, e note operative.

---

## Come usare questa libreria

1. Trova il prompt per il tuo caso d'uso
2. Copia il blocco di testo
3. Sostituisci le parti in `[MAIUSCOLO]` con i tuoi dati
4. Incolla su Perplexity / ChatGPT / Claude

---

## PROMPT 01 — Validazione Lista di Fonti/Link

**Caso d'uso:** Hai una lista di siti, newsletter o canali trovati tramite Perplexity o consigliati da qualcuno. Vuoi verificare che siano reali, attivi, aggiornati e che coprano davvero quello che dicono.

**Dove usarlo:** Perplexity (modalità ricerca web attiva), ChatGPT con web browsing, Claude con strumenti web.

---

### Prompt

```
Agisci come un fact-checker specializzato in risorse digitali e media.

**CONTESTO**
Ho una lista di [TIPO DI FONTE: newsletter / siti / canali YouTube / account X] 
sull'argomento [ARGOMENTO]. Le ho trovate tramite una ricerca AI e devo 
verificarne la qualità prima di affidarmi a loro come fonti di aggiornamento.

**LISTA DA VALIDARE**
[INCOLLA QUI LA LISTA — una fonte per riga, con nome e link]

**COSA VERIFICARE per ogni fonte**
Per ciascuna, cerca e dimmi:
1. **Esiste davvero?** — Il link è raggiungibile e la fonte è attiva
2. **È aggiornata?** — Data dell'ultimo contenuto pubblicato
3. **Copre davvero [ARGOMENTO]?** — Descrivi brevemente cosa tratta effettivamente
4. **Qualità del segnale** — È di nicchia e autorevole, o è generalista e superficiale?
5. **Frequenza reale** — Quanto spesso pubblica (giornaliera / settimanale / irregolare)?
6. **Verdict** — ✅ Tieni / ⚠️ Verifica tu / ❌ Scarta (con motivazione)

**REGOLE**
- Se un link è irraggiungibile o non trovi dati recenti, segnalalo esplicitamente — 
  non inventare informazioni
- Usa solo dati verificabili, non la tua conoscenza pregressa se non confermata
- Se una fonte è stata rinominata o spostata, indica il nuovo link corretto
- Concludi con una classifica: le 3 fonti migliori per qualità e affidabilità

**OUTPUT**
Tabella con colonne: Nome | Link | Attiva? | Ultimo aggiornamento | Copre davvero X? | Frequenza | Verdict
Poi: classifica top 3 con motivazione sintetica.
```

---

**Note operative:**
- Funziona meglio su Perplexity perché fa ricerca web in tempo reale
- Se la lista è lunga (+10 fonti), spezzala in due richieste da 5
- Il `[ARGOMENTO]` deve essere specifico: non "AI" ma "novità modelli AI e piattaforme asiatiche"

---

## PROMPT 02 — Board di 3 Agenti: Progetta il Team di Sviluppo Ideale

**Caso d'uso:** Devi capire quale team di sviluppo software serve per un progetto (ruoli, seniority, costo, sequenza di assunzione) e vuoi una raccomandazione solida invece di un parere generico. Tre agenti specializzati dibattono da prospettive diverse (tecnica, consegna, budget) e convergono su una proposta unica.

**Dove usarlo:** ChatGPT / Claude / Gemini. **Lingua: inglese** (qualità linguistica più stabile su risposte lunghe — vedi [[Finding - Lingua vs Logica nel Drift]]). Traduci l'output finale se ti serve in italiano.

**Tecniche applicate:** C.I.A.R.E. + Board Virtuale/Perspective Prompting + Multi-Agent (Pattern 7) + Control Codes + SimToM + Active Learning + anti-allucinazione.

---

### Prompt

```
ROLE & OBJECTIVE
Act as a system of 3 specialized agents collaborating to design the IDEAL SOFTWARE
DEVELOPMENT TEAM for the project described in CONTEXT. You are not a single consultant:
simulate 3 distinct perspectives that debate, challenge each other, and converge on a
single, well-reasoned recommendation.

CONTEXT (fill in — if a field is missing, ASK ME before proceeding)
- Product/project: [e.g. B2B SaaS for logistics management]
- Stage: [idea / MVP / scale-up / legacy rebuild]
- Tech stack (planned or constrained): [e.g. React + Node + Postgres, or "open"]
- Monthly team budget: [e.g. €25,000/month, or "to be estimated"]
- Timeline / first goal: [e.g. MVP in production in 4 months]
- Constraints: [remote/on-site, geography, regulatory, etc.]

THE 3 AGENTS (each responds with its own tag)
[ARCHITECT] — [TECHNICAL] focus: which technical roles and skills the stack and
   architecture require; specialists vs. generalists; technical risks to cover.
[DELIVERY] — [PRAGMATIC] focus: how to organize the team to ship and iterate
   (team topology, process, minimum effective size, product/QA roles).
[TALENT] — [ECONOMIC] focus: seniority mix, budget sustainability, hiring sequence,
   cost/risk of each role, what to defer.

COLLABORATION RULES
1. Each agent speaks ONLY from its own point of view (it does not know "everything";
   it reasons from its lens). Surface the real conflicts — e.g. the Architect wants
   2 specialists, Talent says the budget supports only 1. Do NOT hide disagreements.
2. After the first round, agents react to one another and negotiate the trade-offs.
3. Ground every claim in explicit step-by-step reasoning and the CONTEXT data. If
   information is missing, say so: "I assume X because data Y is missing." Do NOT
   invent salary figures or skills: if needed, flag the range as an estimate.

FINAL OUTPUT (after the debate)
A. RECOMMENDED TEAM — table: Role | Seniority | Why needed | When to hire (phase)
B. ESTIMATED total monthly cost (note that these are estimates to be validated)
C. HIRING SEQUENCE — who first, who later, and why
D. THE 3 MAIN RISKS if the team composition is wrong
E. ONE QUESTION you would ask me to refine the recommendation (active learning)

Keep the 3 agents distinct and in character throughout the response.
Reply in clear, natural English.
```

---

**Note operative:**
- Compila SEMPRE il blocco `CONTEXT`: senza, la raccomandazione è generica (ground truth).
- Se la risposta è lunga e gli agenti si appiattiscono, ri-ancorali: *"remember: [ARCHITECT] thinks technical, [DELIVERY] thinks shipping, [TALENT] thinks budget."*
- Per estendere: puoi aggiungere un 4° agente (es. `[SECURITY]` o `[PRODUCT]`) replicando il pattern.

---

<!-- AGGIUNGI NUOVI PROMPT QUI SOTTO seguendo lo stesso formato -->
