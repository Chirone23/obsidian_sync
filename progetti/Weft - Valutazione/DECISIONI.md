---
tags: [weft, valutazione, decisioni]
created: 2026-06-07
---

# DECISIONI — Weft Valutazione

*Decisioni prese durante la valutazione. Solo dati consolidati — le ipotesi ancora aperte stanno in [[Piano di Test §9]].*

---

> Registra **fatti e decisioni**, non ipotesi in discussione. Ogni voce: decisione presa, motivazione, alternative scartate.

---

## 2026-06-07 — Approccio valutazione: test incrementale gate-based

**Decisione:** I test T-001 e T-002 sono gate obbligatori. Se uno dei due fallisce, i test successivi non vengono eseguiti e la valutazione si chiude con verdetto "non compatibile oggi".  
**Motivazione:** Inutile testare developer experience o durable execution se gli script Python esistenti non possono girare dentro Weft.  
**Alternative scartate:** Testare tutto in ordine sequenziale indipendentemente dai risultati.

---

## 2026-06-07 — Script di riferimento da scegliere

**Decisione:** Da definire — scegliere lo script più semplice in `execution/` che usa almeno una libreria esterna.  
**Stato:** ⏳ Aperta — da decidere prima di iniziare T-001.

---

## [Data] — [Titolo decisione]

**Decisione:**  
**Motivazione:**  
**Alternative scartate:**
