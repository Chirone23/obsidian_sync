# Fonti Verificate — Saggio AI

Bibliografia di supporto per [[Saggio AI - Bozza]] (tesi #3 da [[Angoli e Tesi - Saggio AI]], struttura in [[Struttura - Saggio AI]]).
Ricerca via Perplexity (framework [[01_C-I-A-R-E_Pattern|C.I.A.R.E.]] di [[Prompting MOC|Riccardo Raponi]]), **ogni link aperto e verificato uno per uno**.

> ⚠️ Il saggio è **senza citazioni nel testo** (vincolo di [[Struttura - Saggio AI]]). Queste fonti servono come fatti-osservazione e per reggere alla difesa orale d'esame. Bibliografia-fantasma = bocciatura: ricontrollare ogni ID arXiv al momento della trascrizione.

---

## ✅ TESI — incertezza epistemica senza calibrazione

| Autori | Titolo | Sede + Anno | Link | Cosa mostra |
|---|---|---|---|---|
| Jiang, Araki, Ding, Neubig | How Can We Know When Language Models Know? On the Calibration of LMs for QA | TACL 2021 | arxiv.org/abs/2012.00955 | La confidenza dei modelli non corrisponde alla correttezza; la selective prediction aiuta. **Fonte più forte sulla calibrazione.** |
| Guo, Pleiss, Sun, Weinberger | On Calibration of Modern Neural Networks | ICML 2017 | arxiv.org/abs/1706.04599 | Le reti moderne sono mal calibrate e troppo sicure. (Doppio taglio: vedi sotto, è anche counter.) |
| Manakul, Liusie, Gales | SelfCheckGPT: Zero-Resource Black-Box Hallucination Detection | EMNLP 2023 | arxiv.org/abs/2303.08896 | L'allucinazione è plausibilità che diverge dai fatti, non errore casuale. |
| Lin, Hilton, Evans | TruthfulQA: Measuring How Models Mimic Human Falsehoods | ACL 2022 | arxiv.org/abs/2109.07958 | I modelli producono risposte fluenti ma false che seguono i misconcetti comuni. |
| Varshney et al. | A Survey of Selective Prediction | arXiv 2021 | arxiv.org/abs/2107.05520 | Formalizza l'astensione: rispondere solo quando la confidenza è alta. |
| Chow | On Optimum Recognition Error and Reject Tradeoff | IEEE Trans. Info Theory 1970 | doi.org/10.1109/TIT.1970.1054434 | Teoria classica: scambiare copertura per affidabilità rifiutando i casi incerti. |
| Desai, Durrett | Calibration of Pre-trained Transformers | EMNLP 2020 | arxiv.org/abs/2003.07892 | I transformer pre-addestrati sono mal calibrati sotto distribution shift. |

## ✅ PERNO EMPIRICO — chi usa l'AI e per cosa (dato Anthropic, come osservazione)

| Org | Titolo | Anno | Link | Cosa mostra |
|---|---|---|---|---|
| Handa, Tamkin, McCain, Huang, Durmus … Ganguli (Anthropic) | Which Economic Tasks are Performed with AI? Evidence from Millions of Claude Conversations | 2025 | arxiv.org/abs/2503.04761 | **Fonte-perno.** Dati primari su larga scala: l'uso si concentra nel knowledge-work (scrittura, codice, analisi). |
| Anthropic | Anthropic Economic Index report: Cadences | 2026 | anthropic.com/research/economic-index-june-2026-report | Pattern d'uso per orario/occupazione; output più comuni = spiegazioni, documenti, guida, codice. |
| Anthropic | Anthropic Economic Index: New building blocks for understanding AI use | 2026 | anthropic.com/research/economic-index-primitives | "Primitive economiche": uso concentrato in un sottoinsieme di task e occupazioni. |

> **Nota difesa orale:** il dato mostra uso concentrato nel knowledge-work ma **non** "solo task banali". Inquadrare come *delega sensibile al rischio modellata dal segnale di incertezza*, non come "gli utenti fanno solo cose triviali". Coerente con sez. 2 e 6 della bozza.

## ✅ COUNTER / ANTITESI — l'incertezza si può rendere trasparente (è solo immaturità)

| Autori | Titolo | Sede + Anno | Link | Cosa mostra |
|---|---|---|---|---|
| Guo, Pleiss, Sun, Weinberger | On Calibration of Modern Neural Networks | ICML 2017 | arxiv.org/abs/1706.04599 | Temperature scaling: calibrazione corretta a basso costo, senza riaddestrare. **Perno tecnico dell'antitesi.** |
| Chen, Yoon, Ebrahimi, Arik, Pfister, Jha | Adaptation with Self-Evaluation to Improve Selective Prediction in LLMs | arXiv 2023 | arxiv.org/abs/2310.11689 | Self-evaluation + astensione migliora la selective prediction. |
| Ren, Zhao, Vu, Liu, Lakshminarayanan | Self-Evaluation Improves Selective Generation in LLMs | arXiv 2023 | arxiv.org/abs/2312.09300 | L'auto-valutazione produce punteggi correlati alla qualità dell'output. |
| Le, Miller, Singh, Sonenberg | Improving Model Understanding and Trust with Counterfactual Explanations of Model Confidence | arXiv 2022 | arxiv.org/abs/2206.02790 | Interfaccia: spiegare la confidence con controfattuali aumenta fiducia e comprensione (studio su umani). |
| Zhang, Liao, Bellamy | Effect of Confidence and Explanation on Accuracy and Trust Calibration in AI-Assisted Decision Making | arXiv 2020 | arxiv.org/abs/2001.02114 | Ponte tecnico↔interfaccia: mostrare la confidence calibra la fiducia umana. |
| Karny, Baez, Pataranutaporn | Multi-Turn Neural Transparency: Surfacing Neural Activations Improves User Calibration to LLM Behavioral Drift | arXiv 2026 | arxiv.org/abs/2605.15455 | Esporre i segnali interni migliora la calibrazione dell'utente via interfaccia. |
| Kadavath et al. | Language Models (Mostly) Know What They Know | arXiv 2022 | arxiv.org/abs/2207.05221 | I modelli si autovalutano parzialmente. Counter **debole** (in realtà "Both"): usare come sfumatura, non come avversario forte. |

> **Replica alla tesi (sez. 6 bozza):** il temperature scaling (Guo) calibra la confidence *aggregata*, ma il problema è il segnale *per-risposta* leggibile dall'utente al momento della delega — limite ancora aperto sul lato umano (Zhang, Le).

---

## 🔴 SCARTATE — bibliografia-fantasma (NON citare)

| Citazione fornita da Perplexity | Problema |
|---|---|
| White et al., "Reliable and Interpretable Model Confidence via CAV", ICML 2022, arXiv:2206.03048 | **Inventata.** Quell'ID è un paper di computer vision ("Layered Depth Refinement with Mask Guidance", CVPR 2022). Autore e titolo falsi. |
| "Schaefer and colleagues", "Surfacing Neural Activations…", arXiv:2605.15455 | Paper reale ma **autore e titolo sbagliati**. Corretto = Karny, Baez, Pataranutaporn (vedi tabella counter). |

---

## Metodo (per riuso futuro)
- Ricerca su **Perplexity**, prompt strutturato C.I.A.R.E., richiesta esplicita di autore/titolo/sede/anno/link verificabile.
- **Sessione pulita** per l'antitesi (evita l'anchoring: in stessa sessione Perplexity ha riproposto Kadavath invece di cercare un counter nuovo).
- **Verifica manuale di ogni link** (apertura arXiv/DOI): ha intercettato 1 fonte inventata + 1 con autore sbagliato su ~22 totali. Mai fidarsi in blocco.
