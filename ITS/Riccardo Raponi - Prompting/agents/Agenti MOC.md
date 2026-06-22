# Agenti — Indice #

**Tipo:** Indice / hub della cartella `agents`
**Materia:** [[Prompting MOC]]
**Tag:** #prompt #agent #moc
**Fonte:** articolo *"50 Prompt AI per Sviluppatori"* — [keepmyprompts.com](https://www.keepmyprompts.com/it/blog/prompt-ai-sviluppatori-debug-refactoring-deploy-veloci) → 8 aree riconvertite in system prompt singoli, attivabili su richiesta.

---

## Idea ##

Ogni area dell'articolo diventa un **agente specializzato** = un system prompt a sé, con ruolo, contesto da compilare, metodo e regole anti-allucinazione. Non in parallelo (costo token alto, vedi Prompt 02 in [[Prompt Library]]), ma attivabili uno alla volta come "infrastruttura riutilizzabile". Tutti in inglese per stabilità su output lunghi — [[Finding - Lingua vs Logica nel Drift]].

---

## Le 8 aree ##

| #   | Area                                    | Focus                                               | Stato |
| --- | --------------------------------------- | --------------------------------------------------- | ----- |
| 01  | [[Agent 01 - Debug & Error Resolution]] | Stack trace, memory leak, bug intermittenti, SQL    | ✅     |
| 02  | [[Agent 02 - Code Review & Quality]]    | OWASP, code smell, PR review, dipendenze            | ✅     |
| 03  | Agent 03 - Refactoring & Optimization   | SOLID, async/await, legacy, complessità ciclomatica | ⬜     |
| 04  | [[Agent 04 - Testing]]                  | Unit, integrazione, race condition, property-based  | ✅     |
| 05  | Agent 05 - Documentation                | JSDoc, README, ADR, changelog da commit             | ⬜     |
| 06  | [[Agent 06 - Architecture & Design]]    | Trade-off, schema DB, API contract, scalabilità     | ✅     |
| 07  | [[Agent 07 - Git & DevOps]]             | Commit message, CI/CD, Dockerfile, monitoraggio     | ✅     |
| 08  | Agent 08 - Learning & Exploration       | Spiegazioni con analogie, confronto tech, kata      | ⬜     |

---

## Struttura comune di ogni agente ##

Ogni file segue lo stesso schema (coerente col Prompt 02 della [[Prompt Library]]):

- **Header bracketed** + tipo/materia/tag/lingua
- **Dominio** + **Quando usarlo**
- **System Prompt** (blocco copiabile, EN): `ROLE & OBJECTIVE` → `CONTEXT` → `METHOD` → `ANTI-HALLUCINATION RULES` → `OUTPUT`
- **Note operative** (ground truth: compila il CONTEXT)
- **Connessioni** (backlink incrociati tra agenti + MOC)

---

## Connessioni ##

- [[Prompt Library]] — i prompt base, il framework C.I.A.R.E. e la Board multi-agente (Prompt 02)
- [[Finding - Lingua vs Logica nel Drift]] — system prompt in inglese
- [[Prompting MOC]]
