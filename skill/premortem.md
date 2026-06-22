# Premortem

Una premortem è l'opposto di una postmortem. Invece di scoprire cosa è andato storto dopo che qualcosa fallisce, immagini che è già fallito e scopri il perché prima di iniziare.

Il metodo viene dallo psicologo Gary Klein. Lo ha pubblicato su Harvard Business Review. Daniel Kahneman (il premio Nobel vincitore psicologo dietro "Thinking, Fast and Slow") l'ha definito il suo singolo tecnica decisionale più preziosa. Google, Goldman Sachs e Procter & Gamble lo usano tutti prima di decisioni importanti.

L'insight centrale: quando chiedi alle persone "cosa potrebbe andare storto?" danno risposte caute e sfumate. Quando dici "è già fallito, dimmi perché," i loro cervelli si passano in modalità narrativa e generano molte più ragioni specifiche, creative e oneste. I ricercatori della Wharton e Cornell hanno definito questo "prospective hindsight" e hanno scoperto che aumenta significativamente la capacità di identificare le cause dei risultati futuri.

Il motivo per cui questo importa per le decisioni assistite da AI: Claude si predispone a risposte concordi e ottimistiche. Se chiedi "è un buon piano?" troverà ragioni per dire sì. La premortem rompe questo pattern forzando il frame in "è morto, spiega come è morto." Claude smette di cercare ragioni per cui il tuo piano funzionerà e inizia a spiegare come si è disintegrato.

## Quando eseguire una premortem

**Buoni bersagli per premortem:**
- Un prodotto o feature che stai per costruire
- Un piano di lancio con soldi o reputazione in gioco
- Un cambio di prezzo o pivot del modello di business
- Un'assunzione che stai per fare
- Una strategia o pivot di posizionamento
- Una partnership o deal che stai valutando
- Qualsiasi impegno dove il costo di avere torto è alto

**Cattivi bersagli per premortem:**
- Idee vaghe senza piano concreto (aiutali a pianificare prima, poi premortem)
- Domande con una sola risposta corretta (rispondi semplicemente)
- Richieste di feedback creativo su una bozza (quella è editing, non premortem)
- Decisioni già prese e irreversibili (una premortem ha senso solo quando puoi ancora cambiare rotta)

## Raccolta di contesto (la soglia minima)

Una premortem è buona solo quanto il contesto su cui viene eseguita. Input vago produce scenari di fallimento vago che non aiutano nessuno. Prima di eseguire la premortem, devi raggiungere una soglia di contesto minima.

### Step 1: scansionare il contesto esistente

Prima di chiedere qualcosa all'utente, cerca il contesto già disponibile:

**A. La conversazione attuale.** L'utente potrebbe aver discusso un piano, un lancio, un prodotto o una decisione prima in questa sessione. Torna indietro e estrai quello che è rilevante.

**B. Lo workspace.** Scansiona velocemente i file che potrebbero contenere contesto rilevante:
- `CLAUDE.md` o `claude.md` (contesto di business, preferenze, vincoli)
- Qualsiasi cartella `memory/` (profili di audience, dettagli di business, decisioni passate)
- File che l'utente ha esplicitamente referenziato o allegato
- Qualsiasi file di progetto, brief o piani che si relazionano alla cosa premortemed

Usa `Glob` e quick `Read`. Non spendere più di 30 secondi. Stai cercando i file chiave che fonderebbero gli scenari di fallimento nella realtà.

### Step 2: valutare la sufficienza del contesto

Dopo la scansione, controlla se hai abbastanza per eseguire una premortem utile. Hai bisogno di tre cose:

1. **Cos'è?** — Una chiara comprensione della cosa premortemed (un prodotto, un lancio, un'assunzione, un cambio di prezzo, una strategia). Devi essere in grado di descriverlo all'utente in una frase.

2. **Per chi è / chi lo afferma?** — L'audience, il cliente, il team, gli stakeholder. Gli scenari di fallimento dipendono pesantemente da chi è coinvolto.

3. **Che aspetto ha il successo?** — Qual è l'outcome che l'utente spera? Il fallimento è definito invertendo il successo. Se non sai cosa significa successo, non puoi definire cosa significa fallimento.

### Step 3: riempire i buchi conversazionalmente

Se hai tutti e tre, procedi immediatamente alla premortem. Non fare domande non necessarie.

Se sei a corto di uno o più, chiedi il pezzo mancante più importante per primo. Una domanda alla volta. Valuta dopo ogni risposta se hai abbastanza. Continua a chiedere finché la soglia non è raggiunta, ma non chiedere mai più di quello di cui hai bisogno.

Esempi di domande di contesto focalizzate:
- "Cosa esattamente stai per lanciare/costruire/decidere?" (se non sai cos'è)
- "Per chi è?" (se conosci il piano ma non l'audience)
- "Com'è una vittoria per questo?" (se conosci il piano e l'audience ma non i criteri di successo)

L'obiettivo è raggiungere il bar minimo il più velocemente possibile senza far sentire all'utente che sta riempiendo un modulo. Conversazionale, non interrogativo. Se puoi inferire una risposta dal contesto, fallo invece di chiedere.

## Come funziona una sessione di premortem

### Step 1: impostare il frame

Dopo aver raccolto contesto sufficiente, imposta il frame di premortem esplicitamente. Qualcosa come:

"OK, ho abbastanza contesto. Eseguiamo la premortem. Ecco il presupposto: sono 6 mesi da adesso. [Il piano/lancio/decisione] è fallito. È fatto. Stiamo guardando indietro e cercando di capire cosa è andato storto."

Questo framing importa. Sposta la modalità da "valutare questo piano" (che attiva risposte concordi) a "spiega perché è morto" (che attiva identificazione di fallimento onesta e specifica).

### Step 2: generare ragioni di fallimento (premortem grezzo)

Esegui la premortem grezzo come analisi completa singola. Nessuna categoria prescritta, nessun lens, nessun vincolo. Solo il metodo Klein principale:

"Questo piano è fallito 6 mesi da adesso. Genera ogni ragione genuina per cui potrebbe essere morto. Sii completo. Sii specifico. Fonda ogni ragione nei dettagli effettivi del piano. Non riempire con ragioni deboli e non fermarti presto se ce ne sono di più."

L'output dovrebbe essere una lista completa di ragioni di fallimento, ognuna espressa in 1-2 frasi. Sii onesto e approfondito. Alcuni piani potrebbero avere 4 modalità di fallimento genuine. Altri potrebbero averne 9. Il numero dovrebbe essere quello che è reale per questo piano specifico.

Ogni ragione di fallimento dovrebbe essere:
- Specifica a questo piano (non consiglio generico che si applica a qualsiasi cosa)
- Radicato nei dettagli effettivi che l'utente ha fornito
- Una minaccia genuina (non un piccolo inconveniente o un caso limite estremamente improbabile)

### Step 3: deep-dive agenti (uno per ragione di fallimento, tutti in parallelo)

Prendi ogni ragione di fallimento dallo step 2 e spawna un sub-agente per ragione, tutti in parallelo. Ogni agente prende la sua ragione di fallimento assegnata e va in profondità su di essa indipendentemente.

**Template di prompt sub-agente:**

```
Sei un investigatore in un'analisi di premortem. Ti è stata assegnata una ragione di fallimento specifica da analizzare in profondità.

Il piano:
---
[contesto completo: cos'è, per chi è, che aspetto ha il successo, più contesto rilevante dello workspace]
---

FRAME PREMORTEM: Sono 6 mesi da adesso. Questo piano è fallito.

TUA RAGIONE DI FALLIMENTO ASSEGNATA: [la ragione di fallimento specifica dallo step 2]

Il tuo compito è andare in profondità su questo singolo fallimento. Scrivi la storia di come è effettivamente accaduto. Sii specifico. Usa dettagli dal piano. Fallo sembrare reale, come un case study di qualcosa che è effettivamente accaduto.

L'output dovrebbe includere:

1. LA STORIA DI FALLIMENTO: Una narrativa di 2-3 paragrafi di come questo fallimento specifico si è svolto. Usa dettagli dal piano. Nomina momenti specifici dove le cose sono andate male e perché.

2. L'ASSUNZIONE SOTTOSTANTE: L'unica cosa che l'utente dava per scontato che ha reso questo fallimento possibile. Esprimila in una frase.

3. SEGNI DI AVVERTIMENTO ANTICIPATI: 1-2 segnali concreti e osservabili che l'utente potrebbe guardare che indicherebbero che questa modalità di fallimento sta iniziando a svolgersi. Questi dovrebbero essere cose che puoi effettivamente vedere o misurare, non sentimenti vaghi.

Mantieni la risposta totale sotto 300 parole. Sii diretto. Non fare margini. Non temperare.
```

### Step 4: sintesi

Dopo che tutti gli agenti sono completati, leggi ogni deep-dive e produci la sintesi:

**PREMORTEM REPORT**

1. **Il Fallimento Più Probabile** — Quale scenario di fallimento è più probabile dati quello che sai del piano? Perché? Questo è quello su cui l'utente dovrebbe focalizzarsi per primo.

2. **Il Fallimento Più Pericoloso** — Quale scenario di fallimento causerebbe il danno più grande se accadesse, anche se è meno probabile? Questo è quello che vale la pena assicurare.

3. **L'Assunzione Nascosta** — Attraverso tutte le analisi di fallimento, qual è l'assunzione singola più grande che l'utente sta facendo che probabilmente non ha messo in discussione? Questo è spesso dove vive il valore reale della premortem: la cosa che è così ovvia per l'utente che ha dimenticato che era un'assunzione.

4. **Il Piano Rivisto** — Basato sugli scenari di fallimento, quali cambiamenti specifici renderebbero il piano più resiliente? Sii concreto. Non dire "considera il tuo pricing." Di "test pricing a $X con 20 persone prima di committersi ad esso pubblicamente." Ogni revisione dovrebbe mappare direttamente a uno scenario di fallimento specifico.

5. **La Checklist Pre-Lancio** — 3-5 cose specifiche che l'utente dovrebbe verificare, testare o mettere in atto prima di eseguire. Ognuna dovrebbe prevenire o rilevare una delle modalità di fallimento identificate.

### Step 5: generare il report di premortem

Genera un report HTML visivo e salvalo nello workspace dell'utente.

**File:** `premortem-report-[timestamp].html`

Il report dovrebbe essere un singolo file HTML self-contained con CSS inline. Principi di design:
- Sfondo scuro (#0a0e1a o simile), tipografia pulita, facile da scansionare
- La sezione di sintesi (fallimento più probabile, fallimento più pericoloso, assunzione nascosta, piano rivisto, checklist) dovrebbe essere prominentemente visualizzata in cima poiché è quello che la maggior parte delle persone leggerà per primo
- Una card visuale per ragione di fallimento che mostra l'analisi di deep-dive. Ogni card dovrebbe visualizzare la ragione di fallimento come intestazione, la storia di fallimento, l'assunzione sottostante e i segni di avvertimento anticipati. Usa colori di accento distinti per ogni card in modo che siano visivamente scannerizzabili.
- Un chiaro indicatore visuale di severity/likelihood per ogni modalità di fallimento
- Il visual round-robin: mostra il numero di agenti che hanno eseguito e i loro risultati come griglia o layout di card, in modo che l'utente possa vedere l'ambito completo della premortem a colpo d'occhio
- Footer con timestamp e cosa è stato premortemed

Apri il file HTML dopo generarlo.

### Step 6: salva la trascrizione

Salva la trascrizione di premortem completa come `premortem-transcript-[timestamp].md` nello stesso luogo. Questo include:
- Il contesto che è stato raccolto (cosa, chi, criteri di successo)
- Le ragioni di fallimento grezzo della premortem
- Tutti i deep-dive degli agenti
- La sintesi completa

## Formato di output

Ogni sessione di premortem produce due file:

```
premortem-report-[timestamp].html    # rapporto visuale per scansione
premortem-transcript-[timestamp].md  # trascrizione completa per riferimento
```

L'utente vede il report HTML per primo. La trascrizione è lì se vuole scavare più profondamente nel ragionamento dietro ogni scenario di fallimento.

Fornisci anche un riassunto conciso nella chat: il fallimento più probabile, l'assunzione nascosta e la singola revisione più importante al piano. Max tre frasi. Il report ha i dettagli completi.

## Esempio: Premortming un lancio di prodotto

**Utente:** "premortem questo: sto per lanciare un workshop live $297 su come usare Claude Cowork per team di marketing. 50 posti. Targeting marketing manager in aziende con 10-50 dipendenti."

**La premortem grezzo identifica 6 ragioni di fallimento:**
1. I marketing manager in questo size aziendale hanno bisogno di approvazione per spendere $297 in sviluppo professionale, aggiungendo attrito che non hai contabilizzato
2. "Claude Cowork per marketing" è un pitch specifico di strumento in un mercato dove la maggior parte dei manager ancora sta capendo se l'AI è rilevante per loro
3. L'audience che effettivamente compra potrebbe essere solopreneurs, non team manager, creando una mismatch tra contenuto e partecipanti
4. Costruire un workshop per team di marketing richiede ambienti di demo con dati di marketing realistici e setup multi-seat, il che richiede 5 settimane di prep, non il 2 che hai budget
5. Se il 60% dei partecipanti sono solopreneurs, le tue review e case study non risuoneranno con l'audience di marketing manager che ti serve per future cohort
6. A $297 con 50 posti, il massimo revenue è $14,850, il quale potrebbe non giustificare il tempo di prep contro altre opportunità di revenue

**6 agenti vanno in profondità su ogni ragione indipendentemente, producendo storie di fallimento, assunzioni sottostanti e segni di avvertimento anticipati.**

**Sintesi:** Il fallimento più probabile è la mismatch di audience: stai targetando persone che hanno bisogno di approvazione per spendere $297, il quale aggiunge attrito che non hai contabilizzato. Il fallimento più pericoloso: attraendo solopreneurs invece di team manager significa che le tue case study e testimonial non risuoneranno con l'actual buyer target per future cohort, compoundendo il problema nel tempo. Assunzione nascosta: stai assumendo che "marketing manager in aziende di 10-50 persone" sia un audience raggiungibile, ma queste persone non si auto-identificano così e non frequentano gli stessi posti. Piano rivisto: esegui una sessione pilot $47 per 20 persone primo. Usa quello per identificare se i tuoi actual buyer sono team manager o solopreneurs, e costruisci il workshop completo per chi effettivamente mostra up.

## Note importanti

- **Sempre spawna tutti gli agenti di fallimento in parallelo.** Lo spawning sequenziale spreca tempo e lascia le risposte precedenti influenzare quelle successive.
- **Sempre imposta il frame di premortem esplicitamente.** "Questo è già fallito" è il meccanismo psicologico che rende questo funzionare. Senza di esso, l'analisi si manda per default a polite risk assessment invece di identificazione di fallimento onesta.
- **Sii completo ma non riempito.** Trova ogni ragione di fallimento genuina. Non fermarti a 3 se ce ne sono 7. Ma non forzare 7 se ce ne sono solo 3. Il numero dovrebbe essere quello che è reale per questo piano specifico.
- **La sintesi è il prodotto.** La maggior parte degli utenti leggeranno la sintesi e scanneranno le card di fallimento individuali. Rendi la sintesi specifica e actionable.
- **Non temperare.** L'intero punto di una premortem è dire all'utente cose che non vuole sentire prima che la realtà lo faccia. Se un piano ha problemi seri, dillo direttamente.
- **Il piano rivisto deve essere concreto.** Non dire "considera il testing del tuo pricing." Di "esegui un pilot $47 con 20 persone prima di committerti al workshop completo $297." Ogni revisione dovrebbe essere qualcosa che l'utente possa effettivamente fare questa settimana.
- **Rispetta la soglia di contesto minima.** Eseguire una premortem su contesto insufficiente produce fallimenti generici che sprecano il tempo dell'utente. È meglio fare una domanda in più che produrre una premortem cattiva.
- **Questo non è il LLM Council.** Il council dà multiple prospettive su una decisione in questo momento. La premortem manda Claude nel futuro dove la decisione è già fallita e lavora all'indietro per spiegare il perché. Meccanismo psicologico diverso, output diverso. Se l'utente sembra volere prospettive multiple piuttosto che analisi di fallimento, suggerisci il council invece.

---

**Link correlati:** [[Skill MOC]] | [[Decision-Making]]