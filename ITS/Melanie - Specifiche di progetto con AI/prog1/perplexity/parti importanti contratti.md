# Parti importanti dei contratti — ricerca Perplexity

> Knowledge di dominio per **SpecterAI**: *cosa* deve saper rilevare un analizzatore di contratti.
> Complementare a [[ricerche contratti]] (che invece cerca i **PDF di test**).

**Materia:** [[Progettistica AI MOC]] · **Progetto:** [[Specifica Tecnica v4 - SpecterAI]]
**Metodo prompt:** C.I.A.R.E. di [[01_C-I-A-R-E_Pattern|Riccardo Raponi]] ([[Prompting MOC]])
**Strumento:** Perplexity free (ricerca web + forum), output verificato manualmente.
**Data:** 2026-06-24

---

## Perché serve a SpecterAI

SpecterAI estrae 7 categorie di clausole (durata, recesso, penali, foro, riservatezza,
responsabilità, pagamenti). Questa ricerca **allarga e gerarchizza** quel set: dice quali
clausole contano davvero, le trappole comuni e i red flag — utile per definire prompt di
estrazione, scoring del rischio e messaggi all'utente nel sistema.

Le 7 categorie del progetto sono un sottoinsieme delle **clausole universali** qui sotto.

---

## 1. Checklist generale (tutti i tipi di contratto)

### Clausole universali

| Clausola | Perché conta | Trappola comune | Cosa controllare |
|---|---|---|---|
| Parti e firme | Chi è effettivamente vincolato | Entità sbagliata, firma mancante, firmi come persona invece che come azienda | Ragione sociale, indirizzi, P.IVA/CF, poteri di firma |
| Oggetto / scope | Cosa è incluso ed escluso | Formulazione vaga → extra non pagati o esclusioni a sorpresa | Deliverable, esclusioni, specifiche, quantità, criteri di accettazione |
| Prezzo e pagamenti | Quando e come paghi/sei pagato | Costi nascosti, "spese ragionevoli", pagamento legato ad approvazione poco chiara | Importi, IVA, acconto, milestone, tempi fattura, mora, rimborsi |
| Durata e rinnovo | Quanto sei vincolato | Rinnovo automatico, preavvisi lunghi facili da mancare | Date inizio/fine, rinnovo, modalità e termine di disdetta |
| Recesso / risoluzione | Come esci | Recesso unilaterale a favore dell'altra parte, penali d'uscita | Preavviso, cause di risoluzione immediata, cosa succede dopo |
| Responsabilità e penali | Ripartizione del rischio economico | Penali eccessive, esposizione senza tetto | Massimale, danni esclusi, proporzionalità delle penali |
| Foro e legge applicabile | Dove e come si risolvono i conflitti | Foro/arbitrato lontano o in paese sconosciuto | Legge applicabile, foro competente, arbitrato, lingua, spese |
| Riservatezza / IP | Cosa puoi dire e chi possiede cosa | Cessione occulta di tutti i diritti, NDA che nasconde non-concorrenza | Definizione info riservate, eccezioni, durata, proprietà deliverable |
| Change control | Evita modifiche a sorpresa | Richieste "piccole" verbali che diventano extra non pagati | Ogni modifica scritta, quotata e approvata prima di iniziare |
| Documenti richiamati | Tira dentro regole esterne | "Vedi policy/manuale/termini" ma i doc mancano o cambiano | Chiedere ogni documento richiamato + versione/data |

### Red flag ricorrenti (dai forum — opinione, non regola)
- Pressione a firmare subito ("leggi dopo"), urgenza.
- Rifiuto di mettere per iscritto scope, pagamenti, change request.
- Promesse vaghe tipo "lo sistemiamo strada facendo".
- Rischio sbilanciato: tu il downside, loro l'upside.
- Clausole piccole che cambiano tutto: IP, rinnovo, penali, foro.

### 5 domande prima di firmare
1. Cosa esattamente sto dando, pagando o promettendo?
2. Qual è la cosa più costosa che può andare storta?
3. Come esco, e con quanto preavviso?
4. C'è una clausola che cede diritti, soldi o flessibilità futura senza che me ne accorga?
5. Accetterei comunque se ogni promessa vaga sparisse e contasse solo il testo scritto?

### Copertura per tipo di contratto
| Tipo | Copertura checklist | Note |
|---|---|---|
| Lavoro dipendente | ✅ Buona | CCNL, prova, TFR, preavviso, non concorrenza |
| Fornitura / servizi B2B | ✅ Buona | SLA, penali, garanzie, clausole vessatorie |
| Freelance / P.IVA | ✅ Buona | IP, revisioni, acconti (vedi §2) |
| Locazione abitativa | ✅ Buona | Durata, caparra, manutenzioni |
| NDA | ✅ Buona | — |
| Locazione commerciale | ⚠️ Parziale | Regole diverse: 6+6, indennità avviamento (vedi §2) |
| Contratti B2C consumatore | ⚠️ Parziale | Manca diritto di recesso del Codice del Consumo |
| Compravendita immobiliare, mutuo, agenzia/franchising, assicurazione, patti parasociali, appalti pubblici | ❌ Scoperti | Regole specifiche non coperte |

---

## 2. Checklist freelancer (4 aree non coperte sopra)

### Cessione vs licenza IP

| Clausola | Perché conta | Trappola comune | Cosa controllare |
|---|---|---|---|
| Ambito cessione | Cliente diventa proprietario o solo utilizzatore? | "Tutti i diritti, ovunque, per sempre" quando serve solo l'uso | Trasferimento pieno o licenza limitata; copre solo il deliverable o anche tuoi template/codice/know-how? |
| Tempistica cessione | Quando perdi il controllo del lavoro | Diritti trasferiti alla firma, non al pagamento | La proprietà passa solo a pagamento saldato |
| Tipo di licenza | Tieni aperte le opzioni di riuso | Licenza esclusiva scritta così ampia da equivalere a vendita | Esclusiva/non esclusiva, territorio, durata, scopo, riuso dei metodi |
| Diritti morali / attribuzione | In UE rilevanti (paternità, portfolio) | Tenta di farti rinunciare a tutto, anche all'attribuzione | Puoi citare il lavoro, metterlo in portfolio, tenere il credit |
| Background IP | Protegge codice/tool/prompt/librerie preesistenti | Il cliente acquisisce per errore tutta la tua cassetta degli attrezzi | Il background IP resta tuo, con licenza d'uso limitata al progetto |
| Terze parti / open source | Evita violazioni o relicensing forzato | Deliverable con componenti non cedibili in toto | Elencare codice/asset di terzi e relative licenze |

### Retainer (incarico continuativo)

| Clausola | Perché conta | Trappola comune | Cosa controllare |
|---|---|---|---|
| Struttura retainer | Riservi tempo o vendi un blocco di lavoro? | "Retainer" usato a vanvera, in realtà servizio illimitato | Monte ore prepagato / fee di disponibilità mensile / pacchetto ricorrente |
| Scope e disponibilità | Evita che ti trattino come dipendente reperibile | Richieste illimitate, emergenze, meeting fuori dal retainer | Orari, tempi di risposta, cosa è incluso |
| Rollover / scadenza | Ore non usate non diventino passività occulta | Ore che si accumulano per sempre o spariscono senza preavviso | Le ore scadono, si cumulano o si convertono in fee? |
| Durata minima / recesso | Il retainer serve continuità | Cliente disdice subito dopo che hai bloccato il tempo | Preavviso, penale uscita anticipata, sorte dei prepagati |
| Pagamento / fatturazione | Rende il retainer utile per il cash flow | Pagamento a fine mese o legato ad approvazione soggettiva | Timing pagamento anticipato, data fattura, mora |
| Change control | Gli incarichi continuativi derivano senza controllo | Il retainer diventa discarica di task extra | Extra scritto, quotato e approvato prima |

### Termini delle piattaforme (Upwork/Fiverr/Toptal)

| Clausola | Perché conta | Trappola comune | Cosa controllare |
|---|---|---|---|
| Commissioni piattaforma | Determinano il netto | Fee nascoste, prelievo, perdite su cambio valuta | Stack completo: fee piattaforma + pagamento + spread FX + tasse |
| Escrow e rilascio fondi | Quando i soldi sono davvero al sicuro | "Pagato" non significa incassato: c'è finestra/approvazione | Regole rilascio milestone/settimanale, finestre di dispute, prove accettate |
| Non-circumvention | Può legarti alla piattaforma | Conosci un cliente lì e poi non puoi fatturargli diretto per molto tempo | Durata, fee di conversione, lavoro off-platform vietato. Upwork: pagamenti solo on-platform per 24 mesi |
| IP e uso commerciale di default | I termini possono decidere chi possiede l'output | Il compratore ottiene diritti di default, ma la licenza varia per piattaforma | Clausola IP della piattaforma + add-on ordine, soprattutto uso commerciale |
| Processo dispute | Dice quali prove vincono | La piattaforma ignora "qualità" non documentata | Conserva timestamp, messaggi di accettazione, storico deliverable, note scope |
| Controllo account / sanzioni | Perdere l'account costa la pipeline | Pagamenti off-platform, condivisione account, mismatch identità/località | Non violare anti-circumvention, contatto diretto, verifica account |

### Locazione commerciale (studio/ufficio)

| Clausola | Perché conta | Trappola comune | Cosa controllare |
|---|---|---|---|
| Tipo di locazione e uso | Il commerciale non è l'abitativo | Usare contratto residenziale per studio/ufficio | Deve consentire uso professionale/commerciale coerente con l'attività |
| Durata e rinnovo | In Italia struttura pensata per stabilità | Termini brevi che ignorano il modello 6+6 | Standard 6+6 anni per uffici/negozi, con rinnovo ex lege |
| Indennità di avviamento | Tutela il valore d'impresa su certi mancati rinnovi | Tenta di far rinunciare alle tutele di legge | Se l'attività rientra nell'*indennità di avviamento* (art. 34 L.392/78) |
| Canone, deposito, indicizzazione | Determina il costo totale di occupazione | Indicizzazione o spese accessorie nascoste/illimitate | Canone base, formula indice, deposito, utenze, spese condominiali, riparti |
| Riparazioni e fit-out | Studi/uffici spesso da modificare | Paghi riparazioni strutturali del proprietario o ripristini tutto all'uscita | Manutenzione ordinaria vs straordinaria, proprietà delle migliorie |
| Sublocazione / cessione | Conta se cresci o esci | Nessun diritto di subaffittare o cedere, anche con preavviso | Diritti di trasferimento, autorizzazioni, consenso per cambio attività |

---

## Verifica e affidabilità (mia revisione — 2026-06-24)

Output Perplexity **affidabile**: ricerca reale, fonti pertinenti (mimit.gov sulle clausole
vessatorie, Upwork legal, L.392/78 sulla locazione commerciale, forum etichettati come
opinione). Due sfumature italiane da correggere/tenere a mente:

- **Indennità di avviamento** (art. 34 L.392/78): spetta per attività con **contatto diretto
  col pubblico**. Per un ufficio/studio operativo **senza pubblico** spesso **non si applica**.
- **Diritti morali d'autore** (art. 22 L.633/41): **irrinunciabili e incedibili** in Italia.
  Una clausola che fa rinunciare alla paternità è in parte **inefficace**.

> ⚠️ Tutto questo è materiale informativo, **non consulenza legale**. Per contratti ad alto
> rischio (IP core, non concorrenza, penali, immobili) serve un avvocato o un'associazione di
> categoria. Implica anche il design anti-allucinazione di SpecterAI: il sistema deve
> segnalare il limite, non sostituirsi al legale.

---

## Connessioni

- [[Progettistica AI MOC]] — materia, framework di valutazione idea
- [[Specifica Tecnica v4 - SpecterAI]] — spec del progetto (le 7 categorie di clausole)
- [[ricerche contratti]] — ricerca gemella: i PDF di contratti reali per i test
- [[Prompt ricerca]] — prompt Perplexity del progetto
- [[01_C-I-A-R-E_Pattern|C.I.A.R.E.]] · [[Prompting MOC]] — metodo prompt usato
