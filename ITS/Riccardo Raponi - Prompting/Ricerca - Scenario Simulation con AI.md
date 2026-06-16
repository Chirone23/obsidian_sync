# Ricerca — Simulazione Scenari con AI (Beyond il PDF)

**Data ricerca:** 2026-06-16
**Fonte:** Web search (arxiv, GitHub, learnprompting.org, forum)
**Collegato a:** [[Creazione di scenari con AI]] | [[Prompting MOC]]

---

## Il problema reale che il PDF non dice: Persona Drift

Il problema più studiato nelle simulazioni multi-persona è il **persona drift**: dopo qualche turno, i modelli abbandonano progressivamente il ruolo assegnato e convergono verso risposte generiche o contraddittorie.

Tre metriche per misurarlo (ricerca NeurIPS 2025):
- **Prompt-to-line consistency** — il personaggio risponde coerente col suo brief iniziale?
- **Line-to-line consistency** — il personaggio si contraddice tra un turno e l'altro?
- **Q&A consistency** — se interroghi il personaggio su fatti del suo ruolo, risponde correttamente?

**Soluzione pratica:** ri-ancorare il personaggio ogni 3-4 turni con un reminder esplicito nel prompt:
```
"Ricorda: sei Elena CFO, la tua priorità è il ROI a 3 anni, non l'equilibrio sociale."
```

> Fonte: [Consistently Simulating Human Personas with Multi-Turn RL — NeurIPS 2025](https://arxiv.org/abs/2511.00222)

---

## SimToM — La tecnica più efficace per la prospettiva

**SimToM (Simulated Theory of Mind)** è un framework a 2 step che batte Zero-Shot e Chain-of-Thought nelle simulazioni di prospettiva.

```
Stage 1: "Filtra cosa SA il personaggio X in questo momento"
Stage 2: "Rispondi SOLO dal punto di vista di X basandoti su quello che sa"
```

La differenza con il semplice role prompting: invece di dire *"sei il CFO"*, prima delimiti esplicitamente **l'informazione che il CFO possiede**, poi chiedi la risposta. Questo riduce il fenomeno per cui l'IA "bara" usando conoscenza che il personaggio non dovrebbe avere.

**Esempio applicato al caso TechLogistics:**
```
"Considera solo le informazioni che Elena (CFO) conosce:
budget aziendale, proiezione ROI, costi fissi attuali.
Elena NON sa ancora dell'incontro che Roberto (HR) ha avuto
con i sindacati ieri. Dando per scontato questo, come reagisce
Elena alla proposta di chiusura totale?"
```

> Fonte: [SimToM — learnprompting.org](https://learnprompting.org/docs/advanced/zero_shot/simtom) | [Paper arxiv](https://arxiv.org/abs/2311.10227)

---

## Character Card + Scene Contract — Lo stato dell'arte 2025

La tecnica **Rule-Based Role Prompting (RRP)** vincitrice al CPDC 2025 combina due blocchi distinti nel prompt:

- **Character Card** — descrive il personaggio: nome, ruolo, obiettivi, stile comunicativo, limiti di ciò che sa/può fare, frase-tipo
- **Scene Contract** — definisce il contesto: dove siamo, cosa è già successo, quali regole valgono per questa interazione

**Template:**
```
=== CHARACTER CARD ===
Nome: Elena Russo
Ruolo: CFO, Tech-Logistics S.p.A.
Obiettivo: Garantire ROI a 3 anni sulla ristrutturazione
Stile: [TECNICO] [SPECIFICO], usa dati numerici, non generalizzare
Limite: Non conosce i dettagli del contratto sindacale
Frase tipo: "I numeri dicono X, quindi la decisione razionale è Y"

=== SCENE CONTRACT ===
Contesto: Riunione di board straordinaria per decidere
centralizzazione magazzini. Sono già stati presentati i dati
finanziari base. Roberto ha appena sollevato il rischio sciopero.
Regola scena: Ogni partecipante risponde con il proprio tag.
Nessuno conosce ancora la posizione definitiva degli altri.
```

> Fonte: [Talk Less, Call Right — HuggingFace](https://huggingface.co/papers/2509.00482)

---

## Egocentric Context Projection (SPASM) — Contro la deriva

Il framework **SPASM** (2025) introduce l'**ECP — Egocentric Context Projection**: invece di passare all'IA tutta la cronologia della conversazione in modo neutro, la cronologia viene **riscritta dal punto di vista del singolo personaggio** prima di ogni turno.

In pratica: prima di chiedere la risposta di Roberto (HR), riscrivi brevemente gli ultimi scambi come li vedrebbe Roberto — filtrando le info che lui non ha, enfatizzando quelle che lo riguardano.

> Fonte: [SPASM arxiv](https://arxiv.org/html/2604.09212v1)

---

## Il limite fondamentale che la ricerca conferma

Un paper del 2026 titola: *"When simulations look right but causal effects go wrong"* — i modelli producono output che **sembrano** comportamentali e coerenti, ma le relazioni causali tra variabili sono spesso sbagliate. L'IA non modella davvero le conseguenze, genera testo plausibile.

**Implicazione pratica per il laboratorio d'esame:** lo scenario What-if non è una vera simulazione numerica. Se chiedi *"cosa succede al ROI se automatizziamo il 50%?"*, l'IA non calcola — interpola pattern. Per avere numeri affidabili, devi fornirli tu nel prompt e chiedere all'IA di ragionare su quelli.

Questo è il punto del **pappagallo stocastico** applicato agli scenari: l'output è plausibile, non predittivo.

> Fonte: [When simulations look right but causal effects go wrong](https://arxiv.org/pdf/2604.02458)

---

## Sintesi operativa — Miglioramenti rispetto al metodo del PDF

| Aspetto | Metodo PDF (base) | Metodo ricerca (avanzato) |
|---------|-------------------|--------------------------|
| Definizione personaggio | Tag + ruolo in una riga | Character Card strutturata |
| Contesto scena | Nel prompt principale | Scene Contract separato |
| Prospettiva | Role prompting generico | SimToM (filtra info per persona) |
| Deriva multi-turno | Non gestita | Re-anchor ogni 3-4 turni |
| Cronologia conversazione | Passata intera a tutti | ECP: riscritta per ogni agente |
| What-if numerico | L'IA inventa i numeri | Tu fornisci i dati, l'IA ragiona |

---

## Repository GitHub utili

| Repo | Cosa contiene |
|------|--------------|
| [NirDiamant/Prompt_Engineering](https://github.com/NirDiamant/Prompt_Engineering) | 22 tecniche con Jupyter Notebook, include CoT e scenario simulation |
| [promptslab/Awesome-Prompt-Engineering](https://github.com/promptslab/awesome-prompt-engineering) | Raccolta curata di risorse, include role-based e perspective prompting |
| [Advanced-Prompt-Engineering-LLMs](https://github.com/emmanuelrajapandian/Advanced-Prompt-Engineering-LLMs) | Notebook pratici su CoT e self-consistency |
| [LLM Agents — PromptingGuide.ai](https://www.promptingguide.ai/research/llm-agents) | Panoramica completa su agenti multi-ruolo |

---

## Fonti complete

- [SimToM Prompting — learnprompting.org](https://learnprompting.org/docs/advanced/zero_shot/simtom)
- [Think Twice: Perspective-Taking Improves LLMs' Theory-of-Mind — arxiv](https://arxiv.org/abs/2311.10227)
- [Consistently Simulating Human Personas with Multi-Turn RL — NeurIPS 2025](https://arxiv.org/abs/2511.00222)
- [Talk Less, Call Right: Rule-Based Role Prompting — HuggingFace](https://huggingface.co/papers/2509.00482)
- [SPASM: Stable Persona-driven Agent Simulation — arxiv](https://arxiv.org/html/2604.09212v1)
- [When simulations look right but causal effects go wrong — arxiv](https://arxiv.org/pdf/2604.02458)
- [LLM Agents — PromptingGuide.ai](https://www.promptingguide.ai/research/llm-agents)
- [NirDiamant/Prompt_Engineering — GitHub](https://github.com/NirDiamant/Prompt_Engineering)
- [Awesome-Prompt-Engineering — GitHub](https://github.com/promptslab/awesome-prompt-engineering)
