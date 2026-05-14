# Ricerca LLM Comparativa — MyBibliò Chatbot

**Data:** 2026-05-14
**Fonte:** Perplexity Pro Search (master prompt C.I.A.R.E.)
**Status:** ✅ Completata · ⏳ Verdict da validare con test reali
**Parent MOC:** [[MyBibliò AI Implementation MOC]]

---

## 1. Tabella comparativa

| Modello | Provider | $/Mtok input | $/Mtok output | Context | Italiano | Tool use | Latenza p50 | Accesso IT | Pagamento | Fonte |
|---|---|---:|---:|---:|---:|---|---:|---|---|---|
| Qwen3‑Turbo | Alibaba Cloud DashScope Int. | $0.033 | $0.130 | 131k | 4 | ✅ | ~300‑400ms | ✅ no VPN | ✅ carta EU + PayPal | [pricepertoken](https://pricepertoken.com/pricing-page/model/qwen-qwen-turbo) |
| DeepSeek‑V3 | DeepSeek API | $0.14 | $0.28 | 164k | 4 | ✅ | ~400‑600ms | ❌ VPN richiesta | ✅ carta intl | [burnwise](https://burnwise.io/ai-pricing/deepseek) |
| GLM‑4.5‑Air | Zhipu AI / Z.ai | $0.20 | $1.10 | 128k | 3 | ✅ | ~400‑700ms | ✅ no VPN | ✅ carta intl | [Z.ai](https://docs.z.ai/guides/overview/pricing) |
| Yi‑Lightning | 01.AI | $0.14 | $0.14 | 128k | 3 (NON VERIF.) | ✅ | ~250‑400ms | ✅ no VPN | ✅ carta intl | [LLMDex](https://llmdex.pankajk.tech/learn/yi-lightning-for-chinese-llm) |
| Claude Haiku 4.5 | Anthropic | $1.00 | $5.00 | 200k | 5 | ✅ | ~600‑800ms | ✅ no VPN | ✅ carta EU | [Anthropic](https://pricepertoken.com/pricing-page/model/anthropic-claude-haiku-4.5) |
| Gemini 2.0 Flash | Google AI Studio | $0.10 | $0.40 | 100k | 4 | ✅ | ~300‑500ms | ✅ no VPN | ✅ carta EU | [Inworld](https://inworld.ai/models/google-ai-studio-gemini-2-0-flash) |

---

## 2. Deep dive sui 4 cinesi

### Qwen3‑Turbo
- **Italiano:** MMLU multi‑lingua buono. **DATO NON VERIFICATO** su ItalianBench/MMLU‑it
- **Accessibilità:** ✅ no VPN da IP EU/IT
- **Pagamento:** carta IT + PayPal, no conto cinese
- **Tool use:** ✅ JSON Schema robusto
- **Censura:** filtri solo su Cina/HK/Taiwan; libri italiani non a rischio
- **GDPR:** DPA EU disponibile ma non "specialista"

### DeepSeek‑V3
- **Italiano:** alto su EN/ZH. **DATO NON VERIFICATO** su IT
- **Accessibilità:** ⚠️ molte reti IT richiedono VPN
- **Pagamento:** carta intl con KYC opaco
- **Tool use:** ✅ ma meno documentato
- **Censura:** rischio "ombra" su temi politici percepiti
- **GDPR:** ❌ nessun DPA EU pubblico, server cinesi

### GLM‑4.5‑Air (Z.ai)
- **Italiano:** MMLU classico buono. **DATO NON VERIFICATO** su IT
- **Accessibilità:** ✅ no VPN
- **Pagamento:** carta intl + PayPal
- **Tool use:** ✅ ottimizzato per agentic workflows
- **Censura:** focus su politica cinese, docs opaca
- **GDPR:** nessun pacchetto compliance EU

### Yi‑Lightning (01.AI)
- **Italiano:** **DATO NON VERIFICATO** su IT
- **Accessibilità:** ✅ no VPN
- **Pagamento:** carta intl, KYC soft
- **Tool use:** ✅ ma esempi prod scarsi
- **Censura:** soggetto a regole cinesi
- **GDPR:** policy generiche, esposizione EU

---

## 3. Decision Matrix

Pesi: Costo 30% · Italiano 25% · Latenza 20% · Geo-stab 15% · Tool use 10%

| Modello | Costo | IT | Lat | Geo | Tool | **Somma pesata** |
|---|---:|---:|---:|---:|---:|---:|
| **Qwen3‑Turbo** | 5 | 4 | 4 | 5 | 5 | **4.50** 🥇 |
| Gemini 2.0 Flash | 4 | 4 | 4 | 5 | 5 | **4.25** 🥈 |
| Yi‑Lightning | 5 | 3 | 5 | 4 | 4 | **3.95** 🥉 |
| DeepSeek‑V3 | 5 | 4 | 4 | 2 | 5 | 3.80 |
| GLM‑4.5‑Air | 3 | 3 | 3 | 4 | 5 | 3.35 |
| Claude Haiku 4.5 | 1 | 5 | 3 | 5 | 5 | 3.30 |

---

## 4. Verdetto Perplexity

**Vincitore:** Qwen3‑Turbo
**Runner-up 1:** Gemini 2.0 Flash
**Runner-up 2:** Yi‑Lightning

**Score Confidence Perplexity:** 4/5

### Warning list
- Se Anthropic/Google rialzano prezzi +50% → riconsidera Qwen3 / Yi
- Se Cina stringe policy su contenuti letterari → switcha a Gemini / Claude
- Se latenza DeepSeek >1s costante → Qwen3 o Gemini

---

## 5. Note critiche post-ricerca

**Lettura onesta del verdetto:**

1. **Score "italiano 4" sui cinesi è ottimistico.** Tutti hanno "DATO NON VERIFICATO" su benchmark italiani specifici. Significa che il 4 è una stima multi-lingua, non IT-specifico. L'unico con qualità italiana **misurata** è Claude Haiku (5).

2. **Il costo di Claude Haiku va riletto per il volume reale.**
   - 2.000 calls/mese × 1.500 tok input × $1/Mtok = **$3.00**
   - 2.000 calls/mese × 400 tok output × $5/Mtok = **$4.00**
   - **Totale: ~$7/mese** → ben sotto il budget €15
   - Su Qwen3: ~$0.20/mese. Risparmio reale: €6/mese.

3. **Cui prodest 6€/mese di risparmio?** Per un MVP che deve **validare il prodotto**, non scalare costi, la qualità italiana verificata può valere il premium.

4. **Rischio geopolitico Qwen3**: bassa probabilità ma alto impatto. Se cambia la policy, riscrivere l'integrazione costa più di 6 mesi di Claude.

### Raccomandazione del Technical Lead (mia, non di Perplexity)

**Per partire MVP:** Claude Haiku 4.5
- Italiano top verificato
- API solida, SDK PHP non ufficiale ma `wp_remote_post` ok
- DPA EU compliant
- Costo reale <€10/mese fino a 5.000 chiamate
- Tool use stabile per fase 2 (function calling)

**Architettura provider-agnostic comunque obbligatoria** (`mybiblio_llm_call()` con switch): se in fase 2 il volume cresce a 10.000+ calls/mese e i numeri lo giustificano, **switch a Qwen3-Turbo in 1 giorno** senza toccare il resto del codice.

---

## 6. Connessioni

- [[MyBibliò AI Implementation MOC]] — sezione 7 "Punti aperti #1"
- [[Bibliò MOC]]
- [[Prompting MOC]] — framework C.I.A.R.E. usato per il master prompt
