# Fonti Verificate — Saggio AI

Bibliografia di supporto per [[Saggio AI - Bozza]] (tesi #3 da [[Angoli e Tesi - Saggio AI]], struttura in [[Struttura - Saggio AI]]).
Ricerca via Perplexity (framework [[01_C-I-A-R-E_Pattern|C.I.A.R.E.]] di [[Prompting MOC|Riccardo Raponi]]), **ogni link aperto e verificato uno per uno**.

> ⚠️ Il saggio è **senza citazioni nel testo** (vincolo di [[Struttura - Saggio AI]]). Queste fonti servono come fatti-osservazione e per reggere alla difesa orale d'esame. Bibliografia-fantasma = bocciatura: ricontrollare ogni ID arXiv al momento della trascrizione.

> **Livelli di verifica:** ✅ = identità verificata (link → titolo + autori reali). ✅✅ = anche il *contenuto* verificato (abstract letto, il claim corrisponde a ciò che il paper sostiene). ✅✅✅ = verifica **indipendente** (fonte terza esterna conferma il claim/fenomeno, non solo la pagina del paper). Le fonti senza ✅✅ hanno claim coerente col titolo ma abstract non aperto: blindarle prima dell'esame se diventano portanti.

---

## ✅ TESI — incertezza epistemica senza calibrazione

| Autori | Titolo | Sede + Anno | Link | Cosa mostra |
|---|---|---|---|---|
| Jiang, Araki, Ding, Neubig | How Can We Know When Language Models Know? On the Calibration of LMs for QA | TACL 2021 | arxiv.org/abs/2012.00955 | ✅✅✅ La confidenza dei modelli non corrisponde alla correttezza (testuale: alla domanda "sono calibrati?" la risposta è "un enfatico no"); la selective prediction aiuta. **Fonte più forte sulla calibrazione.** |
| **CONFERMA INDIPENDENTE TESI** — review Nature 2026 | Evaluating large language models for accuracy incentivizes hallucinations | Nature 2026 | nature.com/articles/s41586-026-10549-w | ✅✅✅ Rivista top, terza parte. Survey indipendenti convergono: "poor calibration may result in hallucination"; "most models have calibration error rates above 70%"; l'incertezza spesso non rileva l'allucinazione perché il modello è sicuro anche sbagliando. **La fonte più autorevole dell'intero dossier.** |
| Guo, Pleiss, Sun, Weinberger | On Calibration of Modern Neural Networks | ICML 2017 | arxiv.org/abs/1706.04599 | Le reti moderne sono mal calibrate e troppo sicure. (Doppio taglio: vedi sotto, è anche counter.) |
| Manakul, Liusie, Gales | SelfCheckGPT: Zero-Resource Black-Box Hallucination Detection | EMNLP 2023 | arxiv.org/abs/2303.08896 | ✅✅ L'allucinazione è plausibilità che diverge dai fatti, non errore casuale (testuale: fatti veri → campioni consistenti, allucinati → "diverge and contradict"). |
| Lin, Hilton, Evans | TruthfulQA: Measuring How Models Mimic Human Falsehoods | ACL 2022 | arxiv.org/abs/2109.07958 | I modelli producono risposte fluenti ma false che seguono i misconcetti comuni. |
| Varshney et al. | A Survey of Selective Prediction | arXiv 2021 | arxiv.org/abs/2107.05520 | Formalizza l'astensione: rispondere solo quando la confidenza è alta. |
| Chow | On Optimum Recognition Error and Reject Tradeoff | IEEE Trans. Info Theory 1970 | doi.org/10.1109/TIT.1970.1054434 | Teoria classica: scambiare copertura per affidabilità rifiutando i casi incerti. |
| Desai, Durrett | Calibration of Pre-trained Transformers | EMNLP 2020 | arxiv.org/abs/2003.07892 | I transformer pre-addestrati sono mal calibrati sotto distribution shift. |

## ✅ PERNO EMPIRICO — chi usa l'AI e per cosa

> ⚠️ **Anthropic è parte in causa** (vende l'AI, misura i propri utenti). Per il fatto "l'adozione si concentra nel knowledge-work istruito" appoggiarsi alle **fonti terze indipendenti** qui sotto; usare Anthropic solo come *illustrazione dall'interno di un singolo prodotto*, non come prova unica.

### Fonti terze indipendenti (primarie per il perno)
| Org | Titolo | Anno | Link | Cosa mostra |
|---|---|---|---|---|
| NBER (National Bureau of Economic Research) | Workplace Adoption of Generative AI | 2024 | nber.org/digest/202412/workplace-adoption-generative-ai | ✅✅✅ **40% dei laureati** usa l'AI al lavoro vs **20% dei non-laureati**; adozione più alta in computer/matematica (49,6%) e management (49%). Conferma indipendente: l'uso si concentra nei knowledge worker istruiti. |
| Quarterly Journal of Economics (Oxford) | Generative AI at Work | 2025 | academic.oup.com/qje/article/140/2/889/7990658 | ✅✅✅ Top journal di economia. Evidenza empirica sull'uso dell'AI nel lavoro cognitivo. |
| Federal Reserve Bank of St. Louis | The State of Generative AI Adoption in 2025 | 2025 | stlouisfed.org/on-the-economy/2025/nov/state-generative-ai-adoption-2025 | ✅✅✅ Fonte istituzionale neutrale sull'adozione. |

### Anthropic Economic Index (illustrazione dall'interno, NON prova unica)
| Org | Titolo | Anno | Link | Cosa mostra |
|---|---|---|---|---|
| Handa, Tamkin … Ganguli (Anthropic) | Which Economic Tasks are Performed with AI? Evidence from Millions of Claude Conversations | 2025 | arxiv.org/abs/2503.04761 | ✅✅ Dati primari Claude: sviluppo software + scrittura ≈ metà dell'uso; **57% augmentation / 43% automation**. ⚠️ **Dato aggiornato: ~49% dei lavori ha ≥1/4 dei task fatti con Claude** (le iterazioni recenti dicono 49%, non 36% — usare 49% o l'intervallo). |
| Anthropic | Anthropic Economic Index report: Cadences | 2026 | anthropic.com/research/economic-index-june-2026-report | Output più comuni = spiegazioni, documenti, guida, codice. |

> **Nota difesa orale:** il dato mostra uso concentrato nel knowledge-work ma **non** "solo task banali" (57% augmentation). Inquadrare come *delega sensibile al rischio modellata dal segnale di incertezza*. Citare NBER/QJE per il fatto, Anthropic per il colore. Coerente con sez. 2 e 6 della bozza.

## ✅ COUNTER / ANTITESI — l'incertezza si può rendere trasparente (è solo immaturità)

| Autori | Titolo | Sede + Anno | Link | Cosa mostra |
|---|---|---|---|---|
| Guo, Pleiss, Sun, Weinberger | On Calibration of Modern Neural Networks | ICML 2017 | arxiv.org/abs/1706.04599 | ✅✅✅ Temperature scaling: calibrazione corretta a basso costo (testuale: reti moderne "poorly calibrated", temp. scaling "surprisingly effective"). **4.418 citazioni**, standard di campo (conferma indipendente: scispace, AWS, OpenReview). **Perno tecnico dell'antitesi.** |
| Chen, Yoon, Ebrahimi, Arik, Pfister, Jha | Adaptation with Self-Evaluation to Improve Selective Prediction in LLMs | arXiv 2023 | arxiv.org/abs/2310.11689 | ✅✅✅ Self-evaluation + astensione migliora la selective prediction (AUROC 74.6%→80.25%). Conferma indipendente: survey concordano — "abstention can avoid 50% hallucinations". |
| Ren, Zhao, Vu, Liu, Lakshminarayanan | Self-Evaluation Improves Selective Generation in LLMs | arXiv 2023 | arxiv.org/abs/2312.09300 | ✅✅✅ L'auto-valutazione produce punteggi correlati alla qualità ("correlate better with the overall quality"). Concetto confermato da survey indipendenti sulla selective prediction. |
| Le, Miller, Singh, Sonenberg | Improving Model Understanding and Trust with Counterfactual Explanations of Model Confidence | arXiv 2022 | arxiv.org/abs/2206.02790 | ✅✅ Interfaccia: spiegare la confidence con controfattuali aumenta fiducia e comprensione (studio su umani: "help users better understand and better trust"). |
| Zhang, Liao, Bellamy | Effect of Confidence and Explanation on Accuracy and Trust Calibration in AI-Assisted Decision Making | FAT* 2020 | arxiv.org/abs/2001.02114 | ✅✅✅ Mostrare la confidence calibra la fiducia umana — **MA** (testuale) "trust calibration alone is not sufficient". Conferma indipendente: pubblicato a FAT* 2020 (ACM Digital Library, ResearchGate). Vedi nota ribaltamento. |
| Karny, Baez, Pataranutaporn | Multi-Turn Neural Transparency: Surfacing Neural Activations Improves User Calibration to LLM Behavioral Drift | arXiv 2026 | arxiv.org/abs/2605.15455 | Esporre i segnali interni migliora la calibrazione dell'utente via interfaccia. |
| Kadavath et al. | Language Models (Mostly) Know What They Know | arXiv 2022 | arxiv.org/abs/2207.05221 | I modelli si autovalutano parzialmente. Counter **debole** (in realtà "Both"): usare come sfumatura, non come avversario forte. |

> **Replica alla tesi (sez. 6 bozza):** il temperature scaling (Guo) calibra la confidence *aggregata*, ma il problema è il segnale *per-risposta* leggibile dall'utente al momento della delega — limite ancora aperto sul lato umano (Zhang, Le).

> **🔑 Munizione per il ribaltamento antitesi→tesi (sez. 6-7):** la fonte più forte dell'antitesi smonta sé stessa. Zhang-Liao 2020 (testuale): *"trust calibration alone is not sufficient to improve AI-assisted decision making"* — mostrare la confidence calibra la fiducia ma **non basta** a migliorare la decisione, dipende dalla conoscenza che l'umano porta dove l'AI sbaglia. È la tesi del saggio detta dall'avversario: la trasparenza sull'incertezza è necessaria, ma il nodo della delega resta. Usare come perno della transizione: si smonta l'antitesi con la sua stessa fonte.

## ✅✅✅ ESEMPIO DI CRONACA — caso Mata v. Avianca (sez. 5 e 6)

Esempio concreto di allucinazione plausibile con danno reale: un avvocato di New York porta in tribunale sentenze inventate dall'AI. Usato circostanziale nel saggio (nessun nome nel testo).

> ⚠️ **Precisione richiesta:** è una **sanzione civile/disciplinare** (5.000 $ + caso respinto + lettere di scuse ai giudici), **non** una condanna penale per false prove. Dirlo accurato all'orale.

**Fatti verificati (verifica incrociata su domini diversi e in concorrenza):**
- 2023, U.S. District Court SDNY. Avvocato **Steven Schwartz** (studio Levidow, Levidow & Oberman). Causa Roberto Mata vs Avianca (lesione su volo).
- ChatGPT fabbrica **sei sentenze inesistenti** (Varghese v. China Southern Airlines, Martinez v. Delta, Shaboon v. EgyptAir, Petersen v. Iran Air, Miller v. United, Estate of Durden v. KLM), attribuite a giudici reali con citazioni inventate.
- Giudice **P. Kevin Castel**, decisione 22 giu 2023: atti respinti, **sanzione 5.000 $** per "subjective bad faith" (Rule 11).

| # | Fonte | Tipo di dominio | Link |
|---|---|---|---|
| 1 | Ordinanza Mata v. Avianca, 678 F.Supp.3d 443 (S.D.N.Y. 2023) | Fonte primaria giudiziaria | law.berkeley.edu (PDF) / law.justia.com |
| 2 | CNN Business, "Lawyer apologizes for fake court citations from ChatGPT" (27 mag 2023) | Giornalismo generalista | cnn.com/2023/05/27/business/chat-gpt-avianca-mata-lawyers |
| 3 | Association of Corporate Counsel / Seyfarth Shaw / Goldberg Segalla | Analisi legale professionale (concorrenti) | acc.com / seyfarth.com / goldbergsegalla.com |

> Altri domini giornalistici concorrenti che hanno riportato il caso (via Wikipedia): CNBC, Associated Press, ABC News. Convergenza totale sui fatti.

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
- **Tre livelli di verifica** (identità → contenuto → indipendente). Il terzo livello ha scoperto due cose: (a) la tesi è confermata da una review **Nature 2026**; (b) il perno non dipende più da Anthropic (fonte di parte) ma da **NBER / Quarterly Journal of Economics / Fed St. Louis**. Lezione: per fonti di parte (vendor) serve sempre una conferma terza indipendente.
