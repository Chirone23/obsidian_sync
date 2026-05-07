# Documentazione SpecterAI — Indice

**Progetto:** SpecterAI (AI Contract Analyzer per Non-Avvocati in Italiano)
**Fase:** Building (Lezione 3) — review pre-consegna in corso
**Specifica corrente:** [[Specifica Tecnica v3 - SpecterAI]]
**Ultimo aggiornamento indice:** 2026-05-07

---

## 1. Validazione idea (fase iniziale)

| File | Scopo |
|------|-------|
| [[Brainstorming - Validazione Idea]] | Brainstorming iniziale delle 5 idee candidate (2026-05-04) |
| [[Contract Analyzer - Validazione Idea]] | Validazione approfondita dell'idea scelta |
| [[perplexity_ricerche]] | Ricerca di mercato Perplexity sulle 5 idee — competitor, gap, dimensioni |
| `perplexity/What is the current official pricing for Anthropic.md` | 4 query di verifica fattuale pre-consegna (pricing Sonnet, Anthropic ToS, AI Act classification, provider alternativi) → integrate in v3 |
| [[Verifica PC - personale]] | Snapshot ambiente di sviluppo (HW/SW) |

---

## 2. Specifica tecnica

| File | Stato |
|------|-------|
| [[Specifica Tecnica v1 - SpecterAI]] | Prima bozza (2026-05-04) — superata |
| [[Specifica Tecnica v2 - SpecterAI]] | Seconda iterazione (2026-05-04) — superata |
| [[Specifica Tecnica v3 - SpecterAI]] | **Versione corrente** (2026-05-07) — integra tutti i 13 fix da Review + Meta-Review |

---

## 3. Diario di building

I 3 file storici che tracciano il processo di iterazione:

### [[PROMPT_LOG]]
Diario delle iterazioni del prompt di sistema: versioni v1/v2/v3, problema → soluzione, parametri Claude (temperature, max_tokens), few-shot examples, risultati per iterazione.
**Aggiornare:** dopo ogni test significativo del prompt.

### [[INCIDENTS]]
Registro strutturato di errori, bug e comportamenti inattesi: ID univoco (INC-001…), severity, root cause, soluzione, lezioni apprese, eventuali ricadute sulla Specifica v2.
**Aggiornare:** ogni volta che si verifica un errore inatteso durante il building.

### [[SESSION_HANDOFF]]
Stato del progetto tra sessioni: ✅ completato · 🔄 in progress · ⏳ prossimi step · blocchi/domande aperte · file modificati.
**Aggiornare:** all'inizio e alla fine di ogni sessione.

---

## 4. Review e meta-review pre-consegna

| File | Scopo |
|------|-------|
| [[Review Spec v2 - Gap e Roadmap Pre-Consegna]] | Gap analysis sulla Spec v2: cosa manca, roadmap di fix mirati (~4-6h) prima della consegna |
| [[Meta-Review Multi-Agent - Validazione della Review]] | Validazione della review tramite 3 agenti OpenCode in parallelo (modelli free OpenRouter), verdetto convergente |
| [[ChristianG_File2_Valutazione_Studente]] | **Feedback della prof** — valutazione intermedia Lezione 2 → 3 (95/100) |
| [[ChristianG_File3_Stack_Tecnico]] | **Feedback della prof** — analisi dello stack tecnico scelto e percorso consigliato |

---

## 5. Altri file in cartella

- `package.json` — dipendenze Node del prototipo
- `.claude/` — config locale Claude Code per il progetto

---

## Relazione con la Specifica v2

I file di **Diario di building** (sezione 3) fanno parte della **valutazione del corso**: documentare il processo di revisione (v1, v2, v3…) ha lo stesso peso del prodotto finale.
Vedi: [[Specifica Tecnica v2 - SpecterAI#12. Documentazione di Progetto]]

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Lezione 3 - Building e Incident Management]]
