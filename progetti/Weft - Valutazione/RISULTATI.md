---
tags: [weft, valutazione, risultati]
created: 2026-06-07
---

# RISULTATI — Weft Valutazione

*Tabella di riepilogo dei test eseguiti. Aggiorna dopo ogni sessione. Questo è il documento da leggere per capire lo stato della valutazione in 30 secondi.*

---

## Scorecard

| Test ID | Nome | Stato | Data | Esito | Note |
|---|---|---|---|---|---|
| T-001 | Compatibilità nodo Python | ⏳ Non eseguito | — | — | Gate critico |
| T-002 | Librerie custom nel nodo | ⏳ Non eseguito | — | — | Gate critico |
| T-003 | Developer experience | ⏳ Non eseguito | — | — | Dipende da T-001+T-002 |
| T-004 | Durable execution | ⏳ Non eseguito | — | — | Richiede Restate |
| T-005 | Parallelismo automatico | ⏳ Non eseguito | — | — | Opzionale |

**Legenda stato:** ⏳ Non eseguito / ✅ PASS / ❌ FAIL / ⚠️ PASS con riserve

---

## Verdetto attuale

*(aggiornare dopo T-001 e T-002)*

**Weft integrabile con `execution/`?** Non ancora valutato  
**Vale la pena continuare i test?** Non ancora valutato  
**Raccomandazione:** Eseguire T-001 e T-002 prima di qualsiasi altra valutazione

---

## Log delle sessioni di test

| Data | Test eseguiti | Risultato sintetico | Link sessione |
|---|---|---|---|
| 2026-06-07 | Nessuno (ricerca + setup) | Weft verificato reale; prereq §3 corretti; subject scelti; install rimandata a self-host locale | [[SESSION_HANDOFF#SESSION-02]] |

---

## Osservazioni qualitative

*(note libere raccolte durante i test — impressioni, frizioni, sorprese)*

- **Weft è reale** (verifica 2026-06-07): repo ~1.5k★, Rust/TS/Svelte, Show HN, docs su weavemind.ai. Beta dichiarata, ~2 mesi, breaking changes attesi.
- **Sorpresa 1 — install ≠ CLI standalone.** Niente `weft --version`/`restate --version`: si clona il monorepo e si lancia `./dev.sh server` + `./dev.sh dashboard`. Prereq §3 del piano erano sbagliati, corretti.
- **Sorpresa 2 — nodo Python è inline.** `ExecPython` accetta codice in un campo triple-backtick, non un path a `.py`. "Senza modifiche" = corpo incollato invariato, non import di file.
- **Frizione Windows.** `./dev.sh` è bash (+`brew install bash`): Mac/Linux-first → su Windows serve WSL2 + Docker Desktop.
- **Cloud esiste** (app.weavemind.ai, con playground e pricing). Buono per smoke test T-001, ma la sandbox cloud non rappresenta l'env locale → T-002 fedele solo in self-host.
- **Sandbox/import/requirements: non documentati** → T-002 resta un test empirico necessario.
