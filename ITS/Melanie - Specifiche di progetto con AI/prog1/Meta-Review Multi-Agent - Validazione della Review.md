# Meta-Review Multi-Agent — Validazione della Review #

**Oggetto:** validazione di [[Review Spec v2 - Gap e Roadmap Pre-Consegna]]
**Data:** 2026-05-07
**Metodo:** 3 agenti OpenCode in parallelo (modelli free OpenRouter), prospettive complementari
**Progetto:** [[Specifica Tecnica v2 - SpecterAI]] · [[Progettistica AI MOC]]

---

## Verdetto convergente ##

La review **è solida ma DA INTEGRARE** (non da rifare).

| Agente | Modello | Verdetto |
|---|---|---|
| Devil's Advocate | NVIDIA Nemotron 3 Super 120B | **7/10 — DA INTEGRARE** |
| Docente ITS (Melanie sim.) | OpenAI GPT-OSS 120B | **PARZIALE** |
| Tech Lead NLP | Z.ai GLM 4.5 Air | **3 errori tecnici critici** |

---

## Punti su cui i 3 agenti concordano ##

- ✅ Struttura e azionabilità della roadmap sono ottime
- ⚠️ Mancano **fonti/evidenze** a sostegno di affermazioni critiche (specialmente sui costi)
- ⚠️ Manca **valutazione di impatto/priorità** oltre la timeline temporale

---

## Gap nuovi emersi (che la review aveva mancato) ##

### Dal Devil's Advocate ###
- **Incoerenza interna:** "zero user research" ma score validazione di mercato = 4/5 → incongruente, dovrebbe essere 3/5
- I rischi presentati come assoluti, ignorando mitigazioni già presenti (es. few-shot)
- Nessuna fonte esterna a supporto (AI Act, benchmark costi API)

### Dalla Docente ITS — domande che Melanie farà ma NON anticipate ###
1. **GDPR** sui PDF caricati (critico per AI su documenti legali)
2. **Continuità operativa** se Anthropic API down (single point of failure)
3. **Metriche di qualità** (precisione/recall, non solo test pass/fail)
4. **CI/CD e deployment strategy** (container, monitoraggio)
5. **Modello di business / go-to-market** (anche per MVP accademico)
6. **Gestione PDF corrotti** o formati non supportati

### Dal Tech Lead NLP — errori tecnici nella review ###

| Affermazione review | Verdetto tecnico | Nota |
|---|---|---|
| `assert raw_excerpt in contract_text` | **IMPRECISA** | Serve normalizzazione whitespace + fuzzy matching, altrimenti falsi negativi |
| Pydantic non valida semantica | CORRETTA | — |
| `langdetect` su contratti legali | **IMPRECISA** | Fallisce su testi misti/giuridici, servono profili custom |
| `temperature=0` per estrazione | CORRETTA | Riduce casualità, ma può penalizzare ambiguità |
| Troncamento 40k char | **IMPRECISA** | Dipende dal modello (sicuro su Opus 200k) |
| Costo 0,02 €/analisi | **❌ SBAGLIATA** | Plausibile solo per Claude Haiku, NON per Sonnet/Opus |

**Top 3 errori tecnici critici:**
1. Sottovalutazione costi API (0,02€ non realistico per modelli mid-range)
2. Mancanza di normalizzazione testo nell'`assert raw_excerpt`
3. Assenza di strategie di chunking avanzato per contratti lunghi

---

## Voto stimato del progetto se applichi TUTTI i fix ##

**28/30** (stima Docente GPT-OSS).
- 2 punti detratti per: dipendenza single-vendor LLM + workflow produttivi mancanti.

---

## Raccomandazione finale — Roadmap Estesa ##

La roadmap originale (4-6h) copre **~60%** di quanto serve. Per arrivare a 30/30 aggiungere:

| # | Fix aggiuntivo | Tempo | Priorità |
|---|---|---|---|
| 8 | Sezione **GDPR + privacy** sui PDF | 30 min | Alta (legal AI) |
| 9 | **Ricalcolo costi** per modello Anthropic specifico (Haiku vs Sonnet) | 20 min | Alta (errore tecnico in review) |
| 10 | **Metriche di qualità** misurabili (precision/recall su 5 contratti) | 45 min | Media |
| 11 | Abbassare score validazione mercato a **3/5** (coerenza interna) | 5 min | Alta (correzione) |
| 12 | Aggiungere **normalizzazione** + fuzzy match in `assert raw_excerpt` | 15 min | Alta (errore tecnico) |
| 13 | Sezione **fallback API down** (degraded mode / coda offline) | 20 min | Bassa |

**Totale aggiuntivo: ~2h 15min** → consegna pronta in **6-8h totali** anziché 4-6h.

---

## Limiti di questa meta-review ##

- I 3 agenti hanno valutato un **estratto sintetico** della review, non il file completo
- Nessuno ha letto i file di progetto sottostanti (Specifica Tecnica, Validazione Idea) → giudizi sulla review, non sul progetto
- Le "domande di Melanie" del Docente sim. sono inferite, non da rubric reale
- I costi citati dal Tech Lead sono ordine di grandezza, non listini ufficiali Anthropic verificati

---

## Connessioni ##

- [[Review Spec v2 - Gap e Roadmap Pre-Consegna]] — review originale
- [[Specifica Tecnica v2 - SpecterAI]] — documento sotto review
- [[Progettistica AI MOC]] — corso e contesto didattico
- [[OpenCode Delegation Protocol]] — metodo usato per generare la meta-review
