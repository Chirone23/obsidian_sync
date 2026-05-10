# Documentazione SpecterAI — Indice

**Progetto:** SpecterAI (AI Contract Analyzer per Non-Avvocati in Italiano)
**Fase:** Building Lez. 3 — da iniziare (prossimo step: G1 test prompt su Claude Code CLI)
**Specifica corrente:** [[Specifica Tecnica v3 - SpecterAI]] (v3.1, changelog 18 righe)
**Ultimo aggiornamento indice:** 2026-05-10

---

## 1. Validazione idea (fase iniziale)

| File | Scopo |
|------|-------|
| [[Brainstorming - Validazione Idea]] | Brainstorming iniziale delle 7 idee candidate (2026-05-04) |
| `gap spec-v2/Contract Analyzer - Validazione Idea.md` | Validazione approfondita dell'idea scelta |
| `perplexity/` | Ricerche Perplexity: market research iniziale + 4 query di verifica fattuale pre-consegna (pricing Sonnet, Anthropic ToS, AI Act, provider) integrate in v3 |
| [[Verifica PC - personale]] | Snapshot ambiente di sviluppo (HW/SW, 8 dipendenze, API key) |

---

## 2. Specifica tecnica

| File | Stato |
|------|-------|
| `spec precedenti/Specifica Tecnica v1 - SpecterAI.md` | Prima bozza (2026-04-30) — superata |
| `spec precedenti/Specifica Tecnica v2 - SpecterAI.md` | Seconda iterazione (2026-05-04) — superata |
| [[Specifica Tecnica v3 - SpecterAI]] | **Versione corrente v3.1** (2026-05-10) — 18 modifiche tracciate nel changelog §13 |

**Storia v3 (changelog completo in §13 della spec):**
- v3 (2026-05-07): 13 fix da Review v2 + Meta-Review multi-agent + 4 query Perplexity (AI Act riclassificato limited-risk, verifica raw_excerpt, test plan eseguibile, scenari costo Sonnet, gate lingua IT/EN, ecc.)
- v3.1 (2026-05-10): 3 patch da feedback prof File3 (Stretch Goals §2.bis, dev token strategy §7, Build Roadmap §12.bis) + 2 fix da review Perplexity validation (retention Anthropic 7gg, DPA esplicito) + 3 fix coerenza interna da audit Opus (soglia 0.92, kappa N=35, langdetect)

---

## 3. Diario di building

I 3 file storici che tracciano il processo di iterazione:

### [[PROMPT_LOG]]
Diario delle iterazioni del prompt di sistema e della spec: versioni v1→v3.1, problema → soluzione, parametri Claude (temperature, max_tokens), few-shot examples, lezioni metodologiche per ogni iterazione.
**Aggiornare:** dopo ogni test significativo del prompt o iterazione spec.

### [[INCIDENTS]]
Registro strutturato di errori e correzioni: 6 incident risolti (INC-000a/b/c di setup, INC-000d/e/f metodologici scoperti via verifica fattuale: AI Act misclassification, sottostima costi Sonnet, drift retention Anthropic), 3 forecast per Lez. 3-4 (INC-001/002/003).
**Aggiornare:** ogni volta che si verifica un errore inatteso durante il building.

### [[SESSION_HANDOFF]]
Stato del progetto tra sessioni: ✅ completato · 🔄 in progress · ⏳ prossimi step · blocchi/domande aperte · file modificati. Sezione "Stato attuale" in cima con sintesi post-sessione 2026-05-10.
**Aggiornare:** all'inizio e alla fine di ogni sessione.

---

## 4. Review, meta-review e audit pre-consegna

| File | Scopo |
|------|-------|
| `gap spec-v2/Review Spec v2 - Gap e Roadmap Pre-Consegna.md` | Gap analysis sulla Spec v2: cosa manca, roadmap fix mirati prima della consegna |
| `gap spec-v2/Meta-Review Multi-Agent - Validazione della Review.md` | Validazione della review via 3 agenti OpenCode in parallelo (modelli free OpenRouter), verdetto convergente |

---

## 5. Feedback prof (Melanie)

| Cartella / File | Data | Contenuto |
|---|---|---|
| `valutazione x (prima valutazione)/ChristianG_File2_Valutazione_Studente.md` | 2026-05-06 | Valutazione intermedia Lez. 2→3 (95/100) — letto, integrato in v3.1 |
| `valutazione x (prima valutazione)/ChristianG_File3_Stack_Tecnico.md` | 2026-05-06 | Analisi stack tecnico + percorso consigliato — letto, 3 patch integrate in v3.1 |
| **`valutazione 10-5/ChristianG_Valutazione_Aggiornata.md`** | 2026-05-10 | **Valutazione aggiornata della prof — DA LEGGERE** |
| **`valutazione 10-5/ChristianG_Promemoria_Lezione4.md`** | 2026-05-10 | **Promemoria Lezione 4 — DA LEGGERE** |

> ⚠️ I 2 file in `valutazione 10-5/` sono arrivati il 2026-05-10 e devono essere letti per verificare se contengono nuove richieste che potrebbero richiedere ulteriori patch alla v3.1 prima della Lez. 3.

---

## 6. Altri file in cartella

- `package.json` — dipendenze Node del prototipo (eventuale frontend)
- `perplexity/` — sotto-cartella con dump delle ricerche Perplexity

---

## Relazione tra spec e diari

I file di **Diario di building** (sezione 3) fanno parte della **valutazione del corso**: documentare il processo di revisione (v1, v2, v3, v3.1…) ha lo stesso peso del prodotto finale.
Vedi: [[Specifica Tecnica v3 - SpecterAI]] §12 Documentazione di Progetto + §13 Provenance & Versioning.

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Lezione 3 - Building e Incident Management]]
