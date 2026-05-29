# Prompt Engineering — Guida Pratica

**Autrice:** Melanie Trucco — AI Projects Development, ITS ICT Academy Roma  
**Data:** Aprile 2026

---

## Premessa

Un prompt non è un semplice testo: è una **specifica operativa**.  
Definisce il compito, il contesto, il formato atteso, i vincoli e i criteri di esclusione. Più questa specifica è chiara, più l'output diventa controllabile, verificabile e riutilizzabile.

Nel lavoro professionale, il prompt non va considerato come una domanda "più o meno ben scritta", ma come un componente del sistema. Per questo va progettato, testato, versionato e corretto come qualsiasi altro elemento del progetto.

---

## Il principio di base

Un prompt strutturato deve chiarire cinque elementi fondamentali:

1. **Ruolo** — da quale prospettiva il modello deve operare  
2. **Compito** — che cosa deve fare, in modo preciso  
3. **Formato** — in quale forma deve restituire l'output  
4. **Vincoli** — limiti di lunghezza, tono, lingua, dati ammessi, regole da rispettare  
5. **Esclusioni** — che cosa NON deve fare


### Struttura base

```text
You are a [ROLE] - Ruolo
Your task is to [TASK] - Compito
Output format: [FORMAT] - Formato
Constraints: [CONSTRAINTS] - Vincoli
Do not: [EXCLUSIONS] - Esclusioni
```

Questa struttura non garantisce da sola un buon risultato, ma riduce l'ambiguità e rende il comportamento del modello più stabile.

---

## ERRORI COMUNI

---

### Errore 1 — Pensare che un prompt lungo e dettagliato significhi automaticamente più qualità

L'intuizione più diffusa è: "se aggiungo più contesto, il modello capirà meglio". Nella realtà non funziona sempre così.

Quando un prompt diventa troppo lungo:
- aumenta il costo
- cresce il rischio di incoerenza
- alcune istruzioni perdono peso
- diventa più difficile capire quale parte stia guidando davvero il modello

**Regola pratica:** per task operativi, mantenere il prompt essenziale e leggibile. Se cresce troppo, spesso il problema non è di scrittura ma di architettura del contesto.

**Esempio debole**
```text
Sei un assistente esperto di marketing digitale con 15 anni di esperienza
nel settore B2B, specializzato in contenuti LinkedIn per aziende tecnologiche
di medie dimensioni con focus sull'innovazione, la leadership, la cultura
aziendale, il posizionamento...
```

**Esempio migliore**
```text
Sei un content strategist per aziende tech B2B italiane.
Compito: scrivi 1 post LinkedIn.
Tono: professionale, diretto, non accademico.
Formato: max 280 parole, in italiano.
Non usare statistiche non fornite nel contesto.
```

---

### Errore 2 — Dire cosa vuoi senza dire cosa NON vuoi

Molti output mediocri non dipendono da un'istruzione sbagliata, ma da un'istruzione incompleta.  
Se non è esplicitato cosa evitare, il modello tenderà a usare pattern comuni: aperture generiche, frasi stereotipate, conclusioni deboli, liste inutili, dati inventati.

**Regola pratica:** per ogni output importante, aggiungere sempre una sezione con esclusioni esplicite.

**Esempio**
```text
NON iniziare con frasi generiche come "Nel mondo di oggi" o "Nell'era digitale"
NON usare dati o percentuali se non sono presenti nel contesto
NON concludere con una domanda retorica
NON usare elenchi puntati
```

---

### Errore 3 — Fornire un’identità senza un’azione precisa

Dire "sei un esperto di..." non basta. Il modello capisce che deve assumere un tono esperto, ma non sa ancora quale operazione concreta deve svolgere.

**Confronto**
- `Sei un esperto di marketing` → il modello sa solo come presentarsi  
- `Sei un content strategist. Valuta se l'idea di questo post è originale, già vista o una variante. Rispondi con: ORIGINALE / GIÀ VISTO / VARIANTE + motivazione in una frase.` → il modello ora conosce il suo ruolo e il suo compito

**Regola pratica:** il ruolo è utile solo se è accompagnato da compito, formato e criterio di valutazione.


---

### Errore 4 — Non separare contenuto e formato

Stai chiedendo due cose diverse:
- **che cosa produrre**
- **come organizzarlo**

Quando queste due dimensioni restano confuse nello stesso blocco, il modello tende a privilegiare una parte e a trascurare l'altra.

**Esempio meno efficace**
```text
Scrivi un post LinkedIn su [tema] che sia coinvolgente, originale, in italiano,
max 250 parole, con un hook forte e 3 hashtag finali.
```

**Esempio migliore**
```text
COMPITO:
Scrivi un post LinkedIn su [tema].

VINCOLI DI CONTENUTO:
- Prospettiva personale o osservazione diretta
- Hook forte nella prima riga
- Nessuna statistica inventata

FORMATO OUTPUT:
- Italiano
- Max 250 parole
- 3 hashtag finali
- Nessun elenco puntato
```

Separare contenuto e formato non è una questione estetica: rende l'output più consistente e più facile da valutare.


---

### Errore 5 — Pensare che ripetere lo stesso prompt risolva il problema

Se un prompt fallisce e lo rilanciate identico, il risultato cambia spesso solo in superficie.  
Se il problema è strutturale, il modello tenderà a riprodurlo.

**Regola pratica:** prima di rifare, identificare il tipo di errore:
- errore di tono
- errore di formato
- errore di contenuto
- errore di ambiguità
- errore di vincoli mancanti

Solo dopo modificare il prompt e rilanciare.


---

### Errore 6 — Usare parametri senza collegarli al compito

Configurare i parametri tecnici senza una strategia precisa è un errore comune: Temperature, Top-p e Max Output Token non sono valori da regolare a caso.

**Punto importante:** le impostazioni consigliate cambiano da provider a provider e da modello a modello. Su modelli recenti, in alcuni casi il valore predefinito è già quello consigliato dal provider.

**Regola pratica:**
- per compiti strutturati, classificazione, estrazione e validazione, privilegiare stabilità e controllo
- per brainstorming o generazione creativa, si può tollerare maggiore variabilità
- non cambiare i parametri "per sentito dire": verificare sempre la documentazione aggiornata del modello che si utilizza

Temperature: più alta = output più variabile e meno prevedibile; più bassa = output più stabile e controllato.
Top-p: controlla quanto ampia è la selezione dei token possibili tra cui il modello può scegliere. Un valore più basso rende l’output più controllato; un valore più alto lascia più varietà.
Max output tokens: imposta la lunghezza massima della risposta generata.


---

### Errore 7 — Non usare esempi quando il formato è critico

Se volete un output in un formato preciso, spesso un esempio breve funziona meglio di una spiegazione lunga.

**Senza esempio** ZERO-SHOT
```text
Genera un hook LinkedIn originale su [tema].
```

**Con esempio** FEW-SHOT
```text
Genera un hook LinkedIn originale su [tema].

Esempi:
- "Il problema non era il tool. Era il processo."
- "Per tre mesi ho misurato la cosa sbagliata."
- "La parte più difficile dell'AI non è usarla, ma verificarla."

Formato:
- 1 frase
- massimo 15 parole
- deve creare curiosità o tensione
```

**Regola pratica:** usare 2-4 esempi, non di più. Devono essere pertinenti e diversificati.


---

### Errore 8 — Usare esempi che restringono troppo l'output

Gli esempi nel prompt sono utili, ma non sono neutri. Ogni esempio suggerisce al modello una direzione: tono, struttura, livello di dettaglio, stile e tipo di risposta attesa.

L'errore nasce quando gli esempi diventano un riferimento troppo dominante. In questo caso il modello tende a seguire il pattern mostrato, anche quando il compito richiederebbe maggiore adattamento.

Questo fenomeno si chiama **effetto di ancoraggio**: il modello attribuisce troppo peso agli esempi forniti e li usa come riferimento principale, invece di generalizzare correttamente il compito.

**Regola pratica:**
- usare esempi solo quando aiutano davvero
- chiarire che cosa devono mostrare: formato, tono, struttura o livello di dettaglio
- evitare esempi troppo vincolanti se l'obiettivo richiede varietà
- specificare che gli esempi non devono essere copiati o replicati

**Formula utile**
```text
Usa questi esempi solo come riferimento per tono, formato e livello di dettaglio.
Non copiarne la struttura, il contenuto o le formulazioni in modo automatico.
```

---

### Errore 9 — Credere che il modello "ricordi" davvero tutto il contesto

Nelle sessioni lunghe, il contesto può restare disponibile, ma non tutte le informazioni mantengono lo stesso peso.  
Le istruzioni iniziali possono diventare meno influenti, essere trascurate, compresse male o reinterpretate in base ai messaggi più recenti.

L'errore è pensare che una cosa detta una volta all'inizio della conversazione continui a guidare il modello con la stessa forza per tutta la sessione. In realtà, più il dialogo si allunga, più diventa necessario rendere espliciti i vincoli importanti.

**Regola pratica:** ripetere o fissare le istruzioni chiave quando servono davvero. Obiettivi, vincoli, formato dell'output e criteri di valutazione devono restare visibili e facili da recuperare, soprattutto nelle sessioni lunghe.

**Formula utile**
```text
Prima di continuare, usa questi vincoli come riferimento principale:
- Obiettivo: [OBIETTIVO]
- Formato output: [FORMATO]
- Vincoli: [VINCOLI]
- Cosa evitare: [ESCLUSIONE]
```

---

### Errore 10 — Inserire troppi compiti nello stesso prompt

Un prompt può contenere più istruzioni, ma non tutti i compiti hanno lo stesso peso.  
Quando chiedete troppe operazioni insieme, il modello tende a privilegiare la prima richiesta o quella più evidente, tralasciando le altre più superficiali o meno coerenti.

L'errore è trattare un flusso di lavoro come se fosse un'unica richiesta. Scrivere, sintetizzare, estrarre, correggere e formattare sono operazioni diverse: spesso funzionano meglio se vengono separate.

**Regola pratica:** dividere il lavoro in passaggi chiari. Se più task devono restare nello stesso prompt, numerateli e assegnate a ciascuno un output preciso.

**Formula utile**
```text
Esegui questi task in ordine:
1. Estrai i concetti principali dal testo.
2. Riorganizzali in 3 punti.
3. Riscrivi i punti in tono professionale.
4. Restituisci solo la versione finale.
```

---

### Errore 11 — Usare verbi troppo vaghi

Verbi come "spiega", "racconta", "migliora" o "scrivi qualcosa su" lasciano troppo spazio all'interpretazione.  
Il modello può produrre una risposta corretta in apparenza, ma troppo generica, ridondante o poco adatta all'uso reale.

L'errore è non trasformare l'intenzione in un'azione misurabile. Un buon prompt deve far capire non solo l'argomento, ma anche il tipo di operazione richiesta.

**Regola pratica:** sostituire i verbi vaghi con verbi operativi e collegare il compito a un risultato verificabile.

**Formula utile**
```text
Invece di: "spiegami questo testo"
Usa: "sintetizza questo testo in 5 punti, massimo 12 parole per punto"

Invece di: "rendilo più chiaro"
Usa: "riscrivi il testo per Manager con competenze tecniche di livello medio"
```

---

### Errore 12 — Non dichiarare esplicitamente il target, il contesto e il canale di comunicazione

**Esempio:** Lo stesso contenuto cambia molto in base a chi lo legge, dove viene pubblicato e perché viene scritto.  
Un post LinkedIn, una slide didattica, una mail interna e una specifica tecnica non richiedono lo stesso tono, lo stesso livello di dettaglio o la stessa struttura.

L'errore è chiedere un output senza definire le condizioni d'uso. Se il modello non conosce target, canale e contesto, tenderà a produrre una risposta generica.

**Regola pratica:** specificare sempre almeno pubblico, canale e obiettivo comunicativo.

**Formula utile**
```text
Target: [CHI LEGGE]
Canale: [DOVE SARÀ USATO IL TESTO]
Contesto: [PERCHÉ ESISTE QUESTO OUTPUT]
Obiettivo: [COSA DEVE OTTENERE]
```

**Esempio**
```text
Target: manager di aziende tecnologiche con età tra 35 e 50 anni, non tecnici ma abituati a strumenti digitali.  
Canale: post LinkedIn per decision maker.  
Contesto: discussione sul ruolo dell’innovazione nelle aziende moderne.  
Obiettivo: stimolare riflessione e interazione con un tono professionale ma diretto.
```

---

### Errore 13 — Non progettare il flusso di lavoro del prompt

Molti usano il modello come se dovesse risolvere tutto in un'unica risposta.  
In realtà, i risultati migliori arrivano spesso da una sequenza di passaggi: analisi, estrazione, sintesi, riscrittura, verifica e formattazione.

L'errore è confondere il prompt con l'intero processo. Un buon workflow divide il lavoro in fasi, usa l'output di un passaggio come input del successivo e rende ogni fase più controllabile.

**Regola pratica:** progettare una catena di prompt, non un unico prompt troppo carico. (RICORDA: LOST IN THE MIDDLE!)

**Formula utile**
```text
Fase 1: estrai i punti chiave principali dal testo.
Fase 2: raggruppali per tema.
Fase 3: riscrivili in forma narrativa.
Fase 4: controlla tono, lunghezza e coerenza.
Fase 5: genera la versione finale.
```

---

### Errore 14 — Non specificare il livello di affidabilità richiesto

In molti casi il modello prova comunque a completare il compito, anche quando i dati disponibili non sono sufficienti.  
Questo può portare a risposte plausibili ma non verificabili: nomi inventati, numeri non forniti, esempi non presenti nel testo, conclusioni troppo sicure.

L'errore è non dire esplicitamente come comportarsi davanti a informazioni mancanti o incerte. Nei contesti professionali, un output incompleto ma onesto è preferibile a un output completo ma inventato.

**Regola pratica:** inserire sempre una regola sull'uso dei dati e sulla gestione delle informazioni mancanti.

**Formula utile**
```text
Non inventare dati, nomi, numeri, fonti, clienti o risultati.
Usa solo le informazioni presenti nel testo fornito.
Se un'informazione manca, scrivi: "dato non disponibile".
Se una parte del task non è possibile, segnala il limite invece di colmarlo.
```

---

### Errore 15 — Non testare il prompt su varianti del compito

Un prompt può funzionare bene su un caso e fallire su un altro molto simile.  
Cambiano il canale, il pubblico, la lunghezza, il tono o il formato richiesto, e il comportamento del modello può diventare meno stabile.

L'errore è considerare definitivo un prompt dopo pochi tentativi. In realtà, un prompt solido va provato su casi diversi, inclusi esempi semplici, ambigui e limite.

**Regola pratica:** creare una base comune e poi adattarla in varianti nominate, mantenendo traccia di cosa cambia e perché.

**Esempio**
```text
prompt_linkedin_post_v1.md
prompt_linkedin_thread_v1.md
prompt_slide_summary_v1.md
prompt_quality_check_v1.md

Per ogni versione annota:
- cosa funziona
- cosa produce errori
- cosa tende a ripetersi
- quando va usata
```

---

### Errore 16 — Chiedere solo il risultato finale quando serve una valutazione

Per compiti di confronto, revisione o decisione, chiedere solo la risposta finale rende difficile capire se l'output sia affidabile.  
Il modello può dare un verdetto corretto, ma senza mostrare criteri, alternative o motivazioni sufficienti per valutarlo.

Si deve richiedere una spiegazione logica e verificabile. Non serve sempre una spiegazione lunga: spesso bastano criteri chiari e una motivazione sintetica.

**Regola pratica:** quando il task richiede valutazione, chiedere criteri, motivazione breve e verdetto finale.

**Formula utile**
```text
Valuta l'output secondo questi criteri:
1. chiarezza
2. coerenza con il target
3. rispetto dei vincoli

Per ogni criterio scrivi:
- giudizio sintetico
- motivazione in una frase

Concludi con un verdetto finale: APPROVATO / DA RIVEDERE.
```

---

### Errore 17 — Non controllare costo e quantità di contesto

Prompt molto lunghi, documenti interi, log completi o conversazioni estese non sono sempre necessari.  
Ogni informazione inserita nel prompt aumenta il numero di token, il costo e la complessità del contesto che il modello deve gestire.

L'errore è usare tutto il materiale disponibile invece di selezionare solo quello rilevante. Più contesto non significa automaticamente più qualità, come indicato nell'errore 1.

**Regola pratica:** prima ridurre il contesto, poi usare nel prompt operativo solo le informazioni davvero necessarie.

**Formula utile**
```text
Prima fase:
Estrai dal documento solo le informazioni rilevanti per [OBIETTIVO].

Seconda fase:
Usa solo i punti estratti per produrre [OUTPUT].
Non usare informazioni esterne e non recuperare dettagli esclusi dalla sintesi.
```

---

## Tecniche utili

### 1. Chiedere struttura di ragionamento (CHAIN OF THOUGHT)

Su task complessi, può essere utile chiedere al modello di esplicitare:
- criteri di valutazione
- passaggi logici
- opzioni alternative
- rischi e obiezioni

Questo non significa chiedere sempre "ragiona passo per passo" (STEP-BY-STEP) in modo meccanico.  
Con i modelli più recenti, spesso è più utile chiedere criteri, passaggi di verifica o un piano sintetico, invece di richiedere una lunga spiegazione passo per passo.

**Esempio**
```text
Valuta questa proposta usando 3 criteri:
1. fattibilità tecnica
2. sostenibilità economica
3. rischio operativo

Per ciascun criterio, indica:
- giudizio
- motivazione
- criticità principale
```

**Quando usarlo**
- valutazioni con criteri multipli
- confronto tra alternative
- decisioni con trade-off (cioè scelte che richiedono un compromesso tra vantaggi e svantaggi)
- quality check

**Quando evitarlo**
- output molto brevi
- task a formato rigidissimo
- casi in cui l'eccesso di parole rovina la chiarezza e aumenta i costi

---

### 2. Usare output strutturati e validazione a schema

Se l'output deve finire in un database, in una pipeline o in un'applicazione, il testo libero è spesso una cattiva idea. Quando possibile, è meglio usare output strutturati conformi a uno schema.

**Esempio di schema concettuale**
```json
{
  "hook": "string",
  "body": "string",
  "hashtags": ["string"],
  "narrative_mode": "personal_story | case_study | problem_discovery"
}
```
**Perché il campo `narrative_mode`?**
Nel mio sistema per LinkedIn, ho inserito questo campo per diversificare lo stile dei post. Obbligando l'AI a scegliere tra opzioni fisse (storia personale, caso studio o scoperta di un problema), evito che i post siano ripetitivi e automatizzo la strategia editoriale direttamente nel codice.

Questo approccio:
- riduce i problemi di parsing (facilita la lettura automatica dei dati)
- rende l'output più affidabile (il modello non "inventa" formati nuovi)
- facilita la validazione: permette di controllare automaticamente se l'AI ha inserito tutti i dati richiesti
- semplifica il debugging: se qualcosa si rompe, è immediato capire se l'errore è nel formato dei dati o nel codice
- semplifica l'integrazione con codice e database

**Regola pratica:** se l'output deve essere letto da una macchina, progettatelo come struttura, non come prosa.

---

### 3. Separare generazione e validazione

Il modello che genera l'output non dovrebbe essere l'unico responsabile della sua qualità finale.

Architettura minima consigliata:

```text
GENERATORE → output grezzo → VALIDATORE → output approvato
                               ↓
                          errore / retry
```

Il validatore può controllare:
- lunghezza
- formato
- presenza di dati non ammessi
- campi mancanti
- violazioni di vincoli
- segnali di allucinazione

Il validatore può essere:
- una funzione Python
- una regex (sequenze di caratteri speciali che definiscono un modello di ricerca/pattern per identificare, filtrare o manipolare stringhe di testo)
- uno schema JSON
- una seconda chiamata LLM con compito di verifica

**Regola pratica:** non chiedete al generatore di auto-certificarsi. Separate i ruoli.

---

## Prompt engineering e Context Engineering

Il prompt engineering da solo non basta più a spiegare come si costruisce un sistema affidabile. Sempre più spesso bisogna ragionare in termini di **context engineering**: non solo il prompt, ma l'intero stato che il modello riceve.

Questo include:
- prompt
- file allegati
- tool disponibili
- memoria o stato della sessione
- output precedenti
- istruzioni di sistema
- formato richiesto
- errori di retry

**Idea chiave:** se il modello sbaglia, il problema non è sempre nel prompt. Può essere nel contesto complessivo con cui lo state facendo lavorare.

---

## Checklist per un prompt professionale

- [ ] Il compito è definito in modo preciso?
- [ ] Il ruolo è utile e non solo decorativo?
- [ ] Il formato di output è esplicito?
- [ ] I vincoli sono chiari?
- [ ] È specificato anche che cosa non deve fare?
- [ ] Se uso esempi, sono pochi, pertinenti e diversificati?
- [ ] Il prompt è salvato e versionato?
- [ ] Ho testato il prompt su casi diversi, inclusi casi limite?
- [ ] L'output viene validato prima di essere usato?
- [ ] Il contesto è essenziale, leggibile e realmente necessario?

---

## In sintesi

Scrivere prompt efficaci non significa "parlare meglio con l'AI".  
Significa progettare interazioni che siano:
- chiare
- controllabili
- verificabili
- economiche
- integrabili in un sistema reale

Il prompt giusto non è quello che "suona bene".  
È quello che riduce l'ambiguità, migliora la qualità dell'output e rende il comportamento del modello più stabile nel tempo.

---

*Documento creato per il corso AI Projects Development — ITS ICT Academy Roma*
