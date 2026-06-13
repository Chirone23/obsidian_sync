---
tags: [weft, valutazione]
created: 2026-06-07
---

# Weft — Valutazione · README

**Domanda da rispondere:** Gli script Python in `execution/` possono girare dentro Weft? E se sì, Weft aggiunge valore reale per orchestrare l'AI che analizza infrastrutture di progetto?  
**Stato:** ⏳ Setup completato — test non ancora eseguiti  
**Gate critico:** T-001 e T-002 devono essere PASS per proseguire

---

## Documenti

| File | Scopo | Quando leggerlo |
|---|---|---|
| [[Piano di Test]] | 5 test definiti con criteri pass/fail, rischi, verdetto condizionale | Prima di iniziare qualsiasi test |
| [[SESSION_HANDOFF]] | Diario di sessione — cosa è stato fatto, cosa fare dopo | All'inizio e alla fine di ogni sessione |
| [[RISULTATI]] | Scorecard aggiornata dei test eseguiti | Per capire lo stato in 30 secondi |
| [[DECISIONI]] | Decisioni prese e motivazioni | Quando si cambia approccio |
| [[INCIDENTS]] | Problemi incontrati durante i test | Quando qualcosa si rompe |
| [[PROMPT_LOG]] | Evoluzione dei prompt nei nodi LLM Weft | Durante T-003 e T-005 |

---

## Come iniziare

1. Leggere [[Piano di Test]] §3 (Prerequisiti) — verificare che Weft sia installato
2. Scegliere `script_riferimento.py` da `execution/` — aggiornare [[DECISIONI]]
3. Eseguire T-001 → aggiornare [[RISULTATI]]
4. Se T-001 PASS → eseguire T-002
5. Aggiornare [[SESSION_HANDOFF]] prima di chiudere

---

## Connessioni

- [[Architettura 3 livelli]] — sistema su cui Weft verrebbe integrato
- [[Progettistica AI MOC]] — metodo di documentazione usato
