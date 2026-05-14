# Decisione LLM: Qwen Plus 0728 — Motivazione completa

**Data decisione:** 2026-05-14
**Status:** ✅ Confermato
**Parent:** [[MyBibliò AI Implementation MOC]]
**Ricerca a monte:** [[RESEARCH_LLM_Comparison]]

---

## TL;DR

Per il chatbot MyBibliò scegliamo **Qwen Plus 0728** (Alibaba, via OpenRouter o DashScope International). Costa ~**$1.40/mese** per il volume MVP (2.000 chiamate, 1.500 tok input + 400 tok output medi). È il sweet spot fra qualità linguistica, costo, latenza, stabilità geografica e prontezza produttiva. Decisione presa dopo 3 ricerche convergenti (Perplexity comparativa, Qwen deep research sul gap USA/Asia, lista ufficiale OpenRouter) e un test pratico sul free tier che ne ha confermato l'inutilizzabilità.

---

## 1. Motivazione economica

### 1.1 Costo reale sul nostro profilo d'uso
Profilo MVP stimato: **500–2.000 chiamate/mese**, ~1.500 token input medi, ~400 token output medi.

| Voce | Calcolo | Costo mensile |
|------|---------|--------------:|
| Input | 2.000 calls × 1.500 tok = 3M tok × $0.26/Mtok | $0.78 |
| Output | 2.000 calls × 400 tok = 0.8M tok × $0.78/Mtok | $0.62 |
| **Totale stimato** | | **$1.40** |
| Con prompt caching (system prompt ~1k tok ricorrente) | ~30% sconto effettivo | **~$1.00** |

### 1.2 Costo a scala
| Volume mensile | Stima costo Qwen Plus 0728 |
|---------------:|---------------------------:|
| 500 calls | $0.35 |
| 2.000 calls (MVP) | $1.40 |
| 10.000 calls (fase 2) | $7.00 |
| 50.000 calls (scale) | $35.00 |

Il modello regge la scalabilità senza ridiscussione fino a ~50k chiamate/mese.

### 1.3 Confronto con alternative
| Provider | Costo MVP | Costo 10k | Note |
|----------|----------:|----------:|------|
| **Qwen Plus 0728** | $1.40 | $7 | Scelto |
| Claude Haiku 4.5 | $7 | $35 | 5x più caro |
| GPT-4o-mini | $4 | $20 | 3x più caro |
| Gemini 2.0 Flash | $2 | $10 | Comparabile, ma vedi §3 |
| Qwen3.6 Plus | $2.54 | $12.70 | +€1/mese per qualità marginale superiore |
| Qwen3-Max | $5.46 | $27.30 | Overkill, costo Claude |

**Conclusione economica:** Qwen Plus 0728 è il **prezzo più basso fra i modelli production-grade**, escludendo quelli "Flash" tier che sacrificano qualità.

---

## 2. Motivazione di qualità

### 2.1 Posizionamento nel mercato
Dalle leaderboard LMSys Arena (marzo 2026):
- Qwen3-235B → Elo 1422–1449 (top 6 mondiale)
- Gemini 3 Pro → 1490 (leader)
- Claude Opus 4.5 → 1469
- GPT-5.1-high → 1457

**Gap USA vs Asia: 2.7%** (dato Stanford AI Index 2026).

Qwen Plus 0728 non è il flagship Qwen ma sta nella stessa famiglia architetturale (Qwen3 hybrid MoE). Per il task chat retrieval-augmented il differenziale del flagship vs. Plus è < 5% — irrilevante per consigliare libri.

### 2.2 Qualità italiano
- **Punto critico onesto**: nessun benchmark italiano specifico pubblico (MMLU-it, ItalianBench) misura Qwen Plus 0728 → **"DATO NON VERIFICATO"** formale
- Test empirici online (forum, Reddit, blog dev) indicano italiano "buono, fluido, non goffo" — categoria "lingue non-asiatiche multilingua"
- Per consigliare libri (lessico colto ma non specialistico legale/medico) la qualità è sufficiente
- Mitigation: piano test 30 query reali in italiano colto prima del go-live, switch a Qwen3.6 Plus se sotto soglia

### 2.3 Capabilities critiche per noi
| Capability | Stato Qwen Plus 0728 |
|------------|---------------------|
| Italiano fluido | ✅ Buono (non verificato benchmark) |
| Tool use / JSON Schema | ✅ Stabile (utile per fase 2 function calling) |
| Context window 1M | ✅ Sovradimensionato (ci basterebbero 8k) |
| Hybrid reasoning | ✅ Non-thinking di default = risposte veloci |
| Prompt caching | ✅ Riduce costo input ricorrente |
| Long-context stability | ✅ Multi-turn stabile |
| Structured output | ✅ Per estrazione profilo gusti utente |

---

## 3. Motivazione tecnica/integrazione

### 3.1 Compatibilità con stack Bibliò
- **PHP 8.3 + `wp_remote_post()`**: endpoint REST standard JSON, identico a OpenAI/Anthropic
- **No SDK obbligatorio**: chiamata diretta a `https://dashscope.aliyuncs.com/...` o `https://openrouter.ai/api/v1/chat/completions`
- **Provider-agnostic facile**: la nostra `mybiblio_llm_call()` traduce un payload standard chat → request Qwen
- **WordPress hosted on Infinity Free**: outbound HTTPS funziona (verificato in BRIEF_WordPress_InfinityFree)

### 3.2 Due endpoint disponibili
| Endpoint | Pro | Contro |
|----------|-----|--------|
| **Alibaba Cloud DashScope International** | ufficiale, supporto diretto, caching, fatturazione USD | richiede account Alibaba Cloud, KYC carta |
| **OpenRouter** | unified API (stesso formato OpenAI), switching banale, fatturazione consolidata | margine di ~5-10% sul prezzo, dipendenza da broker |

**Scelta consigliata in dev:** OpenRouter (più veloce iniziare).
**Scelta consigliata in prod:** DashScope diretto (costo più basso, latenza migliore).
Cambio fra i due = 1 file di config diverso.

### 3.3 Latenza
Stimata p50: **~400-600ms** per output da 400 token su endpoint EU/IT.
Test reali necessari ma da letteratura il modello è veloce (non è "thinking" di default).

### 3.4 Tool use stabile
Qwen3 family ha JSON Schema function calling completo (documentato ufficialmente). Ci serve in fase 2 quando l'LLM, invece di ricevere un blob di libri, chiama `search_books(filters)`.

---

## 4. Motivazione geografica/legale

### 4.1 Accessibilità dall'Italia
- ✅ Endpoint API raggiungibile da IP italiano senza VPN
- ✅ Pagamento con carta EU + PayPal su DashScope International
- ✅ Nessun KYC complesso, no entità cinese obbligatoria

### 4.2 GDPR — il punto di onestà
**Qwen NON ha un DPA GDPR-brandizzato come Anthropic/Google/OpenAI.**
- Alibaba Cloud International offre meccanismi di compliance EU
- I dati transitano potenzialmente su server EU (DashScope intl) ma non c'è garanzia ferrea
- Per un progetto ITS scolastico/MVP: rischio accettabile
- Per un sito con dati sensibili a volume: rischio da valutare

**Mitigation operative:**
- Non inviamo PII utente (email, indirizzi, dati pagamento) al prompt LLM
- Inviamo solo: messaggio utente + profilo gusti anonimo + lista prodotti
- Log lato server con possibilità di cancellazione (Art. 17 GDPR)
- Provider-agnostic comunque: se in futuro serve DPA stretto, switch a Claude in 1 giorno

### 4.3 Censura/policy
- Qwen filtra contenuti politici cinesi (Taiwan, HK, dissidenti)
- **Per consigliare libri italiani**: rischio bassissimo
- Edge case: se utente chiede "consigli sulla letteratura cinese dissidente" → possibile risposta evasiva, ma non blocchi
- Mitigation: il system prompt vincola l'output al catalogo Bibliò → la censura non emerge mai perché non si esce dal seminato

---

## 5. Motivazione strategica/futura

### 5.1 Reversibilità della scelta
Tutta l'architettura è provider-agnostic via `mybiblio_llm_call($messages, $tools, $provider = 'qwen')`. Switch a un altro provider = cambio una env var + il file `providers/<name>.php`. Costo del switch: 1 giornata di lavoro, zero impatto su retrieval/UI/profilo.

### 5.2 Hedging dei rischi
| Rischio | Scenario | Backup pronto |
|---------|----------|---------------|
| Qwen alza prezzi del 50% | $1.40 → $2.10 | accettabile, no switch |
| Cina stringe policy contenuti | filtri su libri | switch a Gemini 2.0 Flash |
| OpenRouter down/blocked | endpoint inaccessibile | switch a DashScope diretto |
| Qwen Plus 0728 dismesso | modello deprecato | switch a Qwen3.6 Plus (stesso provider) |
| Qualità italiano insufficiente | utenti lamentano risposte goffe | switch a Claude Haiku 4.5 (€7/mese, italiano top) |
| Compliance EU richiesta | scuola/ITS impone DPA | switch a Claude Haiku 4.5 + DPA Anthropic EU |

### 5.3 Posizionamento "moderno/economico"
Per un progetto ITS che valuta scelte tecniche, dimostrare:
- consapevolezza del gap USA/Asia chiuso nel 2026
- uso di modelli cinesi top-tier con criterio
- architettura disaccoppiata che non dipende dal vendor

…è un segnale tecnico più maturo di "uso OpenAI perché lo usano tutti".

---

## 6. Motivazione operativa (test reale)

### 6.1 Test free tier (eseguito 2026-05-14)
- Tentato: Qwen3 Next 80B A3B Instruct via OpenRouter free tier
- Risultato: **rate limit istantaneo** anche con singolo messaggio "ci sei?"
- Conclusione: free tier OpenRouter inutilizzabile per qualsiasi dev reale → si paga da subito

### 6.2 Onboarding stimato
| Task | Tempo |
|------|-------|
| Apertura account DashScope o OpenRouter | 1-2 ore (KYC carta) |
| Ricarica iniziale ($10 → 7 mesi MVP) | 5 min |
| Prima chiamata da WordPress via `wp_remote_post()` | 30 min |
| **Total time-to-first-response** | **~3 ore** |

---

## 7. Riepilogo numerico (decision sheet)

| Dimensione | Peso | Score 1-5 | Score pesato |
|------------|-----:|----------:|-------------:|
| Costo | 25% | 5 | 1.25 |
| Qualità italiano | 25% | 4 | 1.00 |
| Latenza | 15% | 4 | 0.60 |
| Stabilità geografica | 15% | 5 | 0.75 |
| Tool use | 10% | 5 | 0.50 |
| Compliance EU | 10% | 3 | 0.30 |
| **TOTALE** | | | **4.40 / 5** |

Per confronto:
- Claude Haiku 4.5 → 4.05 (costo penalizza)
- Qwen3.6 Plus → 4.30 (qualità marginale superiore, costo +50%)
- Gemini 2.0 Flash → 4.25 (compliance migliore, italiano comparabile, costo +40%)

---

## 8. Cosa decide chi e quando rivedere

### Trigger di revisione (rivedere la scelta se):
1. Costo mensile reale > $5 dopo 2 mesi di MVP → ridiscutere
2. Qualità italiano sotto soglia in 30 query test reali → switch backup
3. Latenza p95 > 3 secondi sostenuta → switch
4. Errori di geo-blocking dall'Italia in produzione → switch endpoint
5. Notizia di breach/policy change Alibaba Cloud → valutazione urgente

### Cadenza revisione
- **+1 mese dal go-live**: review costo reale + qualità con metriche
- **+3 mesi**: review strategica con dati utente
- **+6 mesi**: review benchmark mercato (nuovi modelli, prezzi)

---

## 9. Connessioni

- [[MyBibliò AI Implementation MOC]] — MOC padre
- [[RESEARCH_LLM_Comparison]] — ricerca a monte (Perplexity + Qwen + lista OpenRouter)
- [[BRIEF_WordPress_InfinityFree]] — vincoli hosting
- [[Bibliò MOC]] — progetto
- [[Prompting MOC]] — framework C.I.A.R.E. usato per le ricerche
