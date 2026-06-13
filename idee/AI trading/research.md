# Report tecnico-operativo: valutazione di un sistema di trading crypto autonomo Kronos + DeepSeek V3.1 + Kraken Futures

## TL;DR
- **Verdetto: il progetto è valido come esercizio di R&D ma il target dichiarato (25-40% annuo, Sharpe > 2.0) NON è realisticamente sostenibile per un retail; trattalo come laboratorio, non come macchina da soldi.** Il punto che salva o affonda tutto è la validazione statistica del backtest (Area 1): senza walk-forward purged/embargoed, costi realistici e centinaia di trade su più regimi, qualsiasi risultato profittevole di pochi giorni è rumore, non alpha.
- **Kronos ha edge predittivo reale ma modesto e validato su azioni cinesi, non su crypto-perpetual**: il paper riporta un Information Ratio di simulazione di 1.42-1.65 (su A-share CSI 300/800), ma accuratezza ≠ profittabilità una volta tolti commissioni, slippage e funding. DeepSeek ha chiuso secondo in Alpha Arena con +4,89% e Sharpe ~0,4: non è prova di redditività, è un singolo run di 2 settimane su 6 modelli.
- **Errore tecnico del progetto originale (libreria Anthropic come "proxy DeepSeek") da correggere**: DeepSeek ha un endpoint OpenAI-compatibile nativo; non serve passare da Claude. Sul piano regolamentare, finché operi con capitale proprio non sei un CASP MiCA, ma in Italia le plusvalenze crypto sono tassate al **33% dal 1/1/2026**.

## Key Findings

1. **Un singolo backtest o un run live di pochi giorni non distingue skill da fortuna.** Servono walk-forward analysis, purged k-fold cross-validation con embargo (Lopez de Prado), centinaia di trade su almeno 2 cicli di mercato e correzione per multiple testing (Deflated Sharpe Ratio).
2. **Sharpe > 2.0 netto è da hedge fund quant d'élite, non da retail.** In crypto, data la volatilità, uno Sharpe di 0,8-1,2 è già rispettabile; un backtest a Sharpe 2,0 tipicamente degrada a 1,0-1,4 in live.
3. **La leva 12-17x dei vincitori di Alpha Arena è pericolosa, non un modello da copiare.** GPT-5 e Gemini hanno chiuso a -62,66% e -56,71% per over-leverage. Best practice: frazione di Kelly (¼ o meno) + volatility targeting + leva effettiva bassa (≤2-3x).
4. **Kronos predice OHLCV/volatilità con edge statistico, ma è stato validato su A-share cinesi (CSI 300/800), non su crypto perpetual.** I TSFM spesso non battono modelli specializzati o il naive forecast su dati finanziari ad alta frequenza.
5. **L'architettura tecnica è fattibile**: DeepSeek API OpenAI-compatibile, Kraken Futures demo (demo-futures.kraken.com) attiva e identica alla produzione, python-kraken-sdk maturo. Ma i rischi operativi (disconnessioni, ordini falliti, slippage, funding) sono concreti.

## Details

### AREA 1 (PRIORITARIA) — Validazione statistica del backtest

**Il problema centrale.** Una strategia ML/LLM può sembrare profittevole per puro caso o per overfitting. Il rischio è massimo qui perché: (a) si testano molte configurazioni (data snooping), (b) le label sono forward-looking, (c) i mercati crypto sono non-stazionari. Il framework di riferimento è "Advances in Financial Machine Learning" di Marcos Lopez de Prado.

**Metodologia corretta.**
- **Walk-forward analysis**: simulazione storica in cui ogni decisione si basa solo su dati precedenti. È il "gold standard" introdotto da Pardo; garantisce out-of-sample se il purging è implementato correttamente.
- **Purged k-fold CV con embargo**: si eliminano (purge) i campioni di training le cui finestre temporali si sovrappongono al test, e si aggiunge un embargo (es. h ≈ 1% del numero di barre, cioè ~50 osservazioni ogni 1000 con embargo del 5%) dopo ogni fold per prevenire leakage da autocorrelazione/reazione di mercato. Lopez de Prado documenta che senza questo, Sharpe iniziali >2.0 crollano una volta imposto il purging temporale.
- **Combinatorial Purged CV (CPCV)**: genera molteplici percorsi di backtest (non uno solo) → una distribuzione di Sharpe invece di un punto, riducendo la probabilità di false scoperte (Probability of Backtest Overfitting, PBO).

**Quanti trade servono.** Minimo ~30 trade per analisi statistica di base (teorema del limite centrale), ma **200+ trade sono raccomandati** per significatività e per coprire diverse condizioni di mercato. Con 100 trade uno Sharpe di 1,0 può già essere statisticamente significativo al 95%. Esempio illustrativo: 13 vittorie su 20 (65% win rate) ha p-value >0,2 (rumore), mentre 130 su 200 (stesso 65%) ha p-value <0,01 (edge reale). Lopez de Prado fornisce la formula **MinTRL** (Minimum Track Record Length) e **MinBTL** (Minimum Backtest Length): strategie a basso Sharpe e bassa frequenza richiedono track record più lunghi. In letteratura esistono casi con 179 mesi out-of-sample ancora statisticamente insufficienti perché il MinTRL richiesto superava i 600 mesi.

**Costi realistici da includere (Kraken Futures).**
- **Commissioni**: struttura maker-taker. Le fee derivati Kraken partono da ~0,02% maker / 0,05% taker; il fee schedule ufficiale cita per il secondo livello "taker of 0.04% and maker of 0.015%", mentre il partner Bookmap riporta per Kraken Futures "7.5 bps taker fee and -2bps maker rebate". Usa il taker (~0,05%) come ipotesi conservativa nel backtest.
- **Funding rate dei perpetual**: pagato/ricevuto continuamente, realizzato ogni ora; cap a ±0,25%/ora (max 6% su 24h). È un costo strutturale per posizioni long mantenute.
- **Slippage e bid-ask spread**: vanno modellati esplicitamente; un market order paga taker + slippage in funzione della profondità dell'order book.
- **Liquidation fee** se vieni liquidato (metà del maintenance margin %, es. ~0,5% per BTC perp, cap 5%).

**Bias da evitare.** Look-ahead bias (usare dati futuri), survivorship bias (testare solo su asset sopravvissuti), data snooping/overfitting (provare troppe configurazioni e selezionare la migliore). Harvey et al. raccomandano una soglia t-stat di 3,0, non 2,0, dato il "factor zoo" di centinaia di fattori già testati.

**Metriche corrette.** Sharpe (return/volatilità totale), Sortino (solo downside), Calmar (return/max drawdown), max drawdown, e soprattutto **Deflated Sharpe Ratio** (Bailey & Lopez de Prado 2014, corregge per selection bias, multiple testing e non-normalità) e **Probabilistic Sharpe Ratio** (probabilità che lo Sharpe vero superi una soglia). Test di robustezza: Monte Carlo (reshuffling/bootstrap dei trade), parameter sensitivity analysis.

**Sharpe > 2.0 è realistico?** No, non in modo sostenibile per retail. Riferimenti: QuantStart nota che un retail con Sharpe > 2 "sta andando molto bene" e che gli hedge fund quant scartano strategie con Sharpe < 2-3 in fase di ricerca (cioè 2-3 è il livello d'élite, raggiunto con infrastruttura istituzionale). In crypto l'alta volatilità comprime lo Sharpe: 0,8-1,2 è già buono. Un backtest a Sharpe 2,0 degrada tipicamente a 1,0-1,4 una volta in produzione con costi reali. **Tratta il target Sharpe > 2.0 come un campanello d'allarme di overfitting, non come un obiettivo.**

### AREA 2 — Leva e position sizing

**Kelly e fractional Kelly.** Il Kelly criterion massimizza la crescita geometrica ma è "matematicamente ottimale e praticamente pericoloso": sovrastimare l'edge del 10% raddoppia la size raccomandata. Full Kelly può produrre, secondo le tabelle di QuantPedia, un drawdown del 50% con probabilità ~50%. **Raccomandazione standard: ¼ o ½ Kelly.** Half-Kelly riduce la volatilità ~25% sacrificando solo ~25% della crescita; in crypto molti pratici usano quarter-Kelly o meno. Ricalcola la frazione ogni ~20-50 trade perché win rate e payoff derivano nel tempo.

**Volatility targeting.** Dimensionare le posizioni per colpire una volatilità target costante: si riduce size in alta volatilità e si aumenta in bassa. Riduce i drawdown e stabilizza lo Sharpe; è complementare al Kelly frazionario.

**Leva e drawdown.** La matematica del recupero è impietosa: -50% richiede +100% per tornare in pari, -75% richiede +300%. Per questo la preservazione del capitale viene prima della massimizzazione.

**Alpha Arena (lettura critica).** Risultati finali ufficiali (18 ott–3 nov 2025, Hyperliquid, $10k ciascuno): **Qwen3 Max +22,3%, DeepSeek V3.1 +4,89%** (gli unici due in profitto); poi **Claude Sonnet 4.5 -30,81%, Grok 4 -45,3%, Gemini 2.5 Pro -56,71%, GPT-5 -62,66%**. Snapshot intermedio del 22/10/2025: DeepSeek a 12,9x di leva e Sharpe 0,42 (+48% momentaneo, poi sceso); Qwen ~16,7x e Sharpe 0,31. **Questi NON sono prova di redditività**: Nof1 stessa ammette "limited sample sizes / lack of statistical rigor, and shortness of evaluation period". Sample = 6 modelli, ~2 settimane, asset volatilissimo: è variance, non skill. Anche critici indipendenti notano che "il trader più sciocco può battere il mercato per anni per pura fortuna". La leva 12-17x non è "compatibile con un drawdown accettabile": è sopravvivenza fortunata in un campione minuscolo.

**Leva massima raccomandata.** Per un sistema retail con obiettivo di drawdown <20-25%: **leva effettiva ≤2-3x**, mai vicino ai 50x consentiti da Kraken. Kraken mostra il prezzo di liquidazione stimato; attenzione che la **leva effettiva aumenta automaticamente quando la posizione va in perdita** (meno collaterale → liquidazione più vicina), e il funding su posizioni cross-maturity può erodere il portafoglio fino a innescare la liquidazione.

### AREA 3 — Edge netto di Kronos

**Cos'è.** Kronos (arXiv 2508.02739, Yu Shi et al., accettato AAAI 2026) è il primo foundation model open-source per K-line, pre-addestrato con obiettivo autoregressivo "on a massive, multi-market corpus of over 12 billion K-line records from 45 global exchanges". Tre dimensioni: **Kronos-small (24,7M par.), Kronos-base (102,3M), Kronos-large (499,2M)**, context window 512 token. (Nota: "Kronos-mini" NON compare nel paper v1; esiste solo nel repo/HF — il 24,7M citato nel progetto è in realtà Kronos-small.)

**Performance documentate (dal paper, primaria).** La simulazione di investimento è su **azioni cinesi A-share (CSI 300/800) via Qlib, NON su crypto**, strategia long-only top-k con costo di transazione 0,15%. Metriche: Annualized Excess Return (AER) e Information Ratio (IR). Kronos-large: CSI300 AER 0,2193 (≈21,9%) IR 1,4177; media AER 0,2084, IR 1,6491. Miglior baseline (Moment-large): AER media 0,1681, IR 1,3677 → Kronos-large batte il miglior baseline di +0,04 AER e +0,28 IR. Per il forecasting di serie di prezzo, l'abstract dichiara "+93% RankIC over the leading TSFM and 87% over the best non-pre-trained baseline... a 9% lower MAE in volatility forecasting and a 22% improvement in generative fidelity". **Il paper NON riporta accuratezza direzionale**; il 58-65% talvolta citato proviene da un blog secondario (BrightCoding), da trattare con cautela.

**Limiti dei TSFM in finanza.** Lo stesso abstract di Kronos ammette che i TSFM "often underperform non-pre-trained architectures" su K-line. Studi indipendenti confermano: i TSFM (Chronos, TimesFM) underperformano ensemble come CatBoost/LightGBM in zero-shot su rendimenti azionari giornalieri; un confronto controllato su 918 esperimenti trova **accuratezza direzionale media del 50,08% (= lancio di moneta)** per architetture deep learning su dati finanziari orari, coerente con l'ipotesi di efficienza debole su orizzonti brevi. Un altro studio (Tiny Time Mixers) trova che i modelli specializzati eguagliano o superano i TSFM in 2 task su 3.

**Accuratezza ≠ profittabilità.** Anche un IC positivo non si traduce automaticamente in profitto: il repo stesso di Kronos avverte che i suoi segnali sono "raw predictions" da passare a un'ottimizzazione di portafoglio per isolare il "pure alpha". Sul netto, commissioni + slippage + funding possono azzerare un edge direzionale marginale. La traduzione previsione→profitto richiede che **edge per trade > costi per trade**, e questo va dimostrato nel backtest dell'Area 1 — non assunto. **Punto critico: Kronos è stato validato su un mercato (A-share daily) molto diverso dal tuo caso d'uso (crypto perpetual intraday); il transfer non è garantito.**

### AREA 4 — Architettura tecnica DeepSeek + Kraken

**Errore da correggere nel progetto.** Il codice originale usa la libreria `anthropic` con un modello Claude descritto come "proxy per DeepSeek". È concettualmente sbagliato: Claude (Anthropic) e DeepSeek sono modelli diversi, di aziende diverse — usare l'SDK Anthropic interroga Claude, non DeepSeek. **DeepSeek ha un endpoint nativo OpenAI-compatibile**: si usa l'SDK OpenAI cambiando solo `base_url="https://api.deepseek.com"` e l'API key. Non esiste alcun "proxy".

**Stato API DeepSeek (2026).** OpenAI/Anthropic-compatibile. Pricing storico V3.1 ~$0,15/1M token input e ~$0,75/1M output (tra i provider più economici sul mercato; cache hit a costo ridotto). **Attenzione alla deprecazione**: i nomi `deepseek-chat` e `deepseek-reasoner` saranno deprecati il **2026/07/24** e corrispondono alle modalità non-thinking/thinking di `deepseek-v4-flash` — il tuo codice dovrà aggiornare gli identificativi modello. Supporta streaming SSE, function/tool calling (utile per generare il JSON strutturato action/size/SL/TP/confidence). Latenza: la modalità reasoning ("thinking") è più lenta; per decisioni a bassa frequenza è accettabile, per HFT no. Considera anche dove finiscono i dati (provider in Cina vs routing UE/US via OpenRouter/Bedrock) se hai vincoli di residenza dati.

**Stato API Kraken Futures demo.** L'ambiente `demo-futures.kraken.com` è **attivo, pubblicamente accessibile, non richiede credenziali di account reale** (email disabilitate). Il codice WebSocket e REST è "identical to the live production code in terms of the feeds/endpoints and the response structure"; unica differenza il base URL (demo vs futures.kraken.com). Le API key di test si generano da demo-futures.kraken.com/settings/api. python-kraken-sdk (manutenuto, v3.x) supporta il sandbox via `sandbox=True` e fornisce client REST/WebSocket Futures (User, Trade, Market, Funding) più template per bot. Disponibili order types, websocket per dati real-time e funding rate storici. **Nota geografica importante: clienti US e giapponesi NON possono fare trading su Kraken Futures; in UE l'accesso è via Kraken Pro con wallet multi-collateral e leva massima soggetta a regole locali.**

**Rischi operativi.** Disconnessioni websocket (monitora status.kraken.com), errori "invalid nonce" se usi le stesse API key per più algoritmi (usa key dedicate), ordini falliti, slippage di esecuzione, rate limit/Cloudflare. Serve gestione errori robusta: retry con backoff esponenziale, idempotenza degli ordini, riconciliazione periodica delle posizioni aperte vs stato atteso, **kill-switch** su perdita massima giornaliera, e monitoraggio attivo del funding e del prezzo di liquidazione.

### AREA 5 — Regolamentazione (MiCA UE) e fiscalità italiana

**MiCA e trading per conto proprio.** MiCA regola i **CASP** (Crypto-Asset Service Providers): chi fornisce servizi crypto a terzi come occupazione professionale. **Un individuo che usa un bot autonomo sul proprio capitale, in self-custody, NON è tipicamente un CASP e non richiede licenza.** La distinzione chiave: se gestisci capitale di terzi (portfolio management), custodia, o esegui ordini per conto di clienti (attività di Classe 1), allora serve la licenza CASP (deadline piena 1 luglio 2026; capitale minimo €125.000-150.000; sanzioni fino a €5M o 12,5% del fatturato). ESMA (feb 2026) ha rilasciato un supervisory briefing che tratta esplicitamente il trading algoritmico e gli abusi di mercato, ma i bot software individuali "do not typically require a license if they are self-custodial tools used by an individual owner".

**Passaggio demo → reale.** Sul piano MiCA, passare da demo a denaro reale con **capitale proprio non cambia il tuo status** (resti non-CASP). Cambia tutto sul piano fiscale e di rischio. Se in futuro gestissi denaro di altri, scatterebbero gli obblighi CASP — da evitare.

**Fiscalità italiana (capitale proprio).**
- Plusvalenze crypto: tassate come "redditi diversi". Aliquota **26% fino al 31/12/2025; 33% sulle operazioni dal 1/1/2026**. L'aumento al 33% e l'abolizione della franchigia sono stabiliti dalla **Legge di Bilancio 2025 (L. n. 207/2024)**; la successiva Legge di Bilancio 2026 (L. 199/2025) ha aggiunto il carve-out al 26% per gli **EMT in euro MiCAR-compliant**.
- **Franchigia 2.000€ abolita dal 1/1/2025**: ogni plusvalenza è tassata.
- Monitoraggio: **Quadro RW/W obbligatorio sempre**, anche se non hai venduto o sei in perdita.
- **DAC8**: gli exchange UE comunicano automaticamente i dati all'Agenzia delle Entrate (raccolta dati dal 2026, primi scambi cross-border dal 2027). Non dichiarare è oggi molto più rischioso (sanzioni omessa RW 3-15% del valore; infedele dichiarazione fino al 240% dell'imposta).
- Metodo di calcolo plusvalenze: LIFO; minusvalenze riportabili fino a 4-5 anni. Possibilità di optare per regime amministrato presso intermediari italiani abilitati (interpello AdE n. 135/2025).
- **I derivati/perpetual** potrebbero avere un trattamento fiscale specifico (redditi diversi da contratti differenziali) diverso dalla mera compravendita spot: questo è un punto grigio — consulta un commercialista esperto crypto.

## Recommendations

**Fase 0 — Prima di scrivere altro codice (1-2 settimane).**
- Correggi l'errore architetturale: rimuovi la libreria Anthropic, usa l'SDK OpenAI con `base_url` DeepSeek; aggiorna gli identificativi modello in vista della deprecazione del 24/07/2026.
- Decidi che questo è un progetto R&D, non una fonte di reddito. Imposta un budget di perdita massima che puoi permetterti di perdere interamente.

**Fase 1 — Validazione statistica (il cancello che decide tutto, 1-3 mesi).**
- Costruisci un backtest con **walk-forward + purged k-fold CV con embargo**, su ≥2 cicli di mercato crypto (almeno 2-3 anni di dati orari/giornalieri BTC/ETH perpetual).
- Includi TUTTI i costi: taker ~0,05%, slippage modellato, funding rate orario, liquidation fee.
- Genera ≥200 trade. Calcola Sharpe, Sortino, Calmar, max drawdown, **Deflated Sharpe Ratio e Probabilistic Sharpe Ratio**, MinTRL.
- Esegui Monte Carlo (bootstrap dei trade) + parameter sensitivity analysis.
- **Soglia di GO**: il sistema deve battere (a) buy-and-hold BTC e (b) una baseline naive, con DSR significativo al 95% e Sharpe netto realistico (target onesto: **0,8-1,5, NON 2,0+**). Se non supera questo cancello, NON passare a denaro reale.

**Fase 2 — Paper trading su Kraken demo (2-3 mesi).**
- Gira il sistema completo su demo-futures.kraken.com. Verifica gestione errori, riconciliazione posizioni, latenza DeepSeek, comportamento del funding, kill-switch.
- Confronta i risultati live-demo con il backtest: se divergono di oltre ~30%, hai overfitting o bug.

**Fase 3 — Capitale reale minimo (solo se Fase 1 e 2 superate).**
- Inizia con capitale che puoi perdere. **Leva effettiva ≤2x.** Position sizing a ¼ Kelly o volatility targeting. Kill-switch su drawdown giornaliero (es. -5%) e cumulato (es. -20%).
- Tieni un registro fiscale dal giorno 1 (Quadro RW, plusvalenze al 33%, valuta il trattamento dei derivati). Considera un commercialista esperto crypto.

**Cosa NON fare.**
- NON usare leva 12-17x "perché DeepSeek/Qwen hanno vinto Alpha Arena".
- NON considerare un run profittevole di pochi giorni come prova di alpha.
- NON saltare la validazione statistica per "andare live prima".
- NON gestire capitale di terzi (faresti scattare obblighi CASP MiCA).

**Benchmark che cambiano le raccomandazioni.** Se in Fase 1 il DSR è significativo e lo Sharpe netto è >1,0 dopo costi su out-of-sample multi-regime → procedi con cautela alla Fase 2. Se lo Sharpe netto è <0,5 o il DSR non è significativo → abbandona o ripensa l'edge. Se in Fase 2 i risultati divergono dal backtest di oltre ~30% → fermati e indaga overfitting/bug prima di rischiare capitale reale.

## Caveats
- I risultati di Alpha Arena sono un singolo run su ~2 settimane con 6 modelli e sample minuscolo; Nof1 stessa li dichiara non statisticamente rigorosi. Da NON usare come prova di redditività di DeepSeek per il trading. I numeri intermedi (es. -75%/-67%) circolati nei media differiscono dai risultati finali (-62,66%/-56,71%).
- L'accuratezza direzionale 58-65% di Kronos viene da una fonte secondaria (blog), non dal paper, che invece NON riporta accuratezza direzionale e valida la simulazione di investimento su azioni cinesi (CSI 300/800), non su crypto.
- Pricing e nomi modello DeepSeek e fee Kraken cambiano nel tempo; verifica la documentazione ufficiale al momento dell'implementazione (deprecazione modelli DeepSeek 24/07/2026).
- La fiscalità crypto italiana è in rapida evoluzione (33% dal 2026, DAC8 dal 2026-27, trattamento incerto dei derivati); consulta un commercialista abilitato.
- Questo report è un'analisi tecnica indipendente, non consulenza finanziaria, legale o fiscale.