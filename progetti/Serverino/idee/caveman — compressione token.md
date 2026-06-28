# Caveman — compressione token (candidato per NOA)

**Data:** 2026-06-28
**Status:** 💡 Idea catturata — da valutare allo step 3 (memoria/retrieval)
**Fonte:** https://github.com/juliusbrussee/caveman (MIT, v1.9.0, maturo)

> Collega: [[progetti/Serverino/NOA — Gap e Roadmap (vs frontier)]] • [[progetti/Serverino/README]] • [[moc/Index MOC]]

---

## Cos'è

Skill/plugin per Claude Code e 30+ agenti: comprime lo **stile di output** dell'LLM per tagliare ~65% dei token in uscita mantenendo (dichiaratamente) la precisione tecnica. Livelli: `lite` → `full` → `ultra` → `wenyan`. Node ≥18, install automatica. Ecosistema correlato: `cavemem`, `caveman-code`, `cavekit`.

## Verdetto per NOA — NON copiarlo, rubarne un pezzo

**Da scartare: la compressione dello stile di output.** L'output di NOA lo legge l'utente su Telegram. Lo stile caveman (telegrafico, rotto) è pensato agente→agente o per codice, non per un assistente conversazionale (DEFINIZIONE §1: maggiordomo conversazionale). Degradare la UX per risparmiare token in uscita su `v4-flash` — già quasi gratis — è un cattivo affare. I token in uscita di NOA sono pochi (è il footer `{tokens_out}` dei messaggi).

**Da rubare: la compressione del CONTESTO IN INGRESSO.** Caveman ha `/caveman-compress` (~46% input token sui file di memoria). Questo colpisce il vero sink di NOA: oggi `_build_messages` infila per intero `system + padrone + memory + MOC` nel system prompt a **ogni** chiamata → Gap §2.3 ("legge file fissi e li mette interi nel prompt, niente retrieval"). Lì la compressione/densità ha senso economico reale.

## Come si aggancia al piano

- Pertinente allo **step 3 — memoria con retrieval** (embedding sul vault), non al tool-calling (step 1) né a Tavily (step 2).
- Non è un'alternativa al retrieval: è complementare. Retrieval = "porta solo i pezzi rilevanti"; compressione = "e portali densi". Prima il retrieval (taglio grosso), poi semmai la compressione (rifinitura).
- Da verificare se adottato: che la compressione non mangi i `[[backlink]]` del MOC (sono la struttura che NOA naviga).

## Azione

Nessuna ora. Riprendere allo step 3 come riferimento per la densità del contesto, non come dipendenza da installare.
