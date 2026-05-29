# La Specifica Tecnica — Guida Pratica

**Per:** Studenti del corso AI Projects Development, ITS ICT Academy Roma  
**Autrice:** Melanie Trucco  
**Data:** Aprile 2026  
**File collegato:** `TECHNICAL_SPEC_Template.md`

---

> Ogni versione del documento deve essere salvata come file distinto (v1, v2, etc.) e consegnata singolarmente per tracciare l'evoluzione del progetto.  
> La specifica finale deve essere completata PRIMA di iniziare il building.

---

## Premessa

La specifica tecnica non è una formalità burocratica.  
È il documento che trasforma un'idea in un progetto realizzabile, verificabile e controllabile.

Nel contesto di questo corso, la specifica ha un ruolo centrale: prima si definisce con precisione che cosa il sistema deve fare, poi si costruisce. Se la specifica è vaga, anche il codice generato dall'AI sarà vago. Se la specifica è incompleta, il progetto tenderà a cambiare direzione durante lo sviluppo.

Una buona specifica non deve essere lunga per forza. Deve essere chiara, stabile e verificabile.

---

## Perché la specifica è la base del progetto

In un progetto AI, la specifica svolge quattro funzioni fondamentali:

1. **Chiarisce il problema**  
   obbliga a definire quale problema reale si vuole risolvere, per chi e con quale valore.

2. **Delimita il perimetro del MVP**  
   distingue ciò che va costruito subito da ciò che resta fuori scope.

3. **Guida l'AI builder**  
   Cursor, un agent AI o uno sviluppatore umano possono lavorare meglio se hanno istruzioni operative chiare.

4. **Definisce i criteri di validazione**  
   permette di capire se il sistema funziona davvero, invece di basarsi su impressioni soggettive.

La specifica è quindi il ponte tra idea, decisione progettuale e implementazione.

---

## La regola del congelamento

Una specifica si scrive, si rivede, si migliora e poi si congela. Non cancellare le varie versioni! Servono tutte per comprendere i passaggi eseguiti tra una e l'altra, le modifiche apportate e per quale motivo. Nella fase di consegna del materiale si dovrà essere in grado di spiegare i passaggi avvenuti. 

**Congelare la specifica** significa stabilire che quella versione diventa il riferimento per il building. Da quel momento, il progetto non cambia direzione senza documentare la modifica. Nei progetti individuali e di piccola entità si può modificare la specifica documentando i cambiamenti nel CHANGELOG (Aggiornalo indicando che la logica è stata cambiata a causa di un errore nelle specifiche originali) ma è bene abituarsi A NON FARLO. In contesti Enterprise o in settori regolamentati (come il medicale, l'automotive o il fintech), se si modifica la 'Specifica Approvata' senza un'errata corrige o una revisione formale, la specifica perde il suo valore legale e contrattuale.

Questo non vuol dire che la specifica debba essere perfetta. Vuol dire che deve essere abbastanza stabile da permettere la costruzione.

### Dopo il congelamento

Ogni modifica importante deve essere registrata in uno di questi modi:

- nuova versione della specifica
- nota in `SPEC_ERRATA.md`
- decisione documentata nel repository (CHANGELOG etc)
- aggiornamento del registro versioni

### Perché è importante

Se la specifica cambia continuamente mentre costruite, diventa impossibile capire:
- se un errore dipende dal codice o dalla definizione del sistema
- quale versione delle decisioni sta seguendo l'AI builder
- perché una funzionalità è stata aggiunta, rimossa o modificata

---

## Come usare il template

Il file `TECHNICAL_SPEC_Template.md` è diviso in sezioni operative.  
Non tutte richiederanno la stessa quantità di testo, ma tutte devono essere considerate.

Per un progetto didattico, la specifica non deve diventare un documento enorme. Deve però contenere decisioni sufficientemente chiare da permettere a un'altra persona, o a un AI builder, di capire cosa costruire senza fare domande fondamentali.

### Lunghezza indicativa

Per un MVP didattico:
- **minimo accettabile:** 5-7 pagine ben compilate
- **buono:** 8-12 pagine
- **eccellente:** 13-20 pagine

Questi valori sono puramente indicativi e dipendono dalla tipologia del progetto. 
La qualità della specifica non si misura dal numero di righe, ma dalla chiarezza delle decisioni.

---

## Struttura della specifica

La specifica è organizzata in queste aree:

1. Sintesi del progetto  
2. Obiettivo del sistema  
3. Problema che risolve  
4. Utenti target e contesto d'uso  
5. Perimetro del MVP  
6. Flusso operativo  
7. Requisiti funzionali  
8. Stack tecnologico e dipendenze  
9. Architettura e flusso dati  
10. Comportamento AI e prompt principali  
11. Dati, privacy e vincoli normativi  
12. Validazione e quality control  
13. Gestione errori e fallback  
14. Deploy, manutenzione e aggiornamenti  
15. Rischi, assunzioni e decisioni aperte  
16. Checklist pre-build  
17. Registro versioni  

Le sezioni successive spiegano come compilarle.

---

## 0. Sintesi del progetto

Questa sezione si trova all'inizio del documento, ma conviene compilarla alla fine.

Serve a dare una visione immediata del progetto: nome, scopo, tipo di sistema, output principale e obiettivo del MVP.

### Cosa deve contenere

- nome del progetto
- descrizione in una frase
- tipo di sistema
- obiettivo del MVP
- output principale

### Formula utile

```text
Il sistema fa [X] per aiutare [Y] a ottenere [Z].
```

### Esempio

```text
Il sistema analizza appunti di lezione e genera una sintesi strutturata per aiutare studenti ITS a ripassare più velocemente.
```

### Errore comune

Scrivere una descrizione troppo generica, per esempio:

```text
Il progetto usa l'AI per aiutare gli studenti.
```

Questa frase non dice che cosa fa il sistema, per chi, né quale output produce.

---

## 1. Obiettivo del sistema

L'obiettivo deve spiegare in modo chiaro che cosa il sistema deve fare.

Non deve essere un'aspirazione generica, ma una descrizione operativa.

### Domande guida

- Che cosa deve fare il sistema?
- Per quale utente o contesto?
- Quale output produce?
- Come capiamo che l'output è accettabile?

### Esempio debole

```text
Il sistema deve aiutare a gestire meglio lo studio.
```

### Esempio forte

```text
Il sistema deve trasformare appunti grezzi di lezione in una sintesi di massimo 500 parole, organizzata in concetti chiave, definizioni e domande di ripasso.
```

La differenza è che il secondo esempio è costruibile e verificabile.

---

## 2. Problema che risolve

Questa sezione deve dimostrare che il progetto nasce da un problema reale, non solo da un'idea interessante.

### Domande guida

- Quale problema concreto esiste oggi?
- Chi lo incontra?
- In quale situazione?
- Come viene risolto oggi?
- Perché le soluzioni attuali non bastano?
- Perché l'AI aggiunge valore?

### Esempio

```text
Gli studenti raccolgono appunti disordinati durante le lezioni e spesso non riescono a trasformarli in materiale di studio riutilizzabile. Oggi lo fanno manualmente, perdendo tempo e ottenendo risultati poco uniformi.
```

### Nota importante

Non basta dire che l'AI rende il processo "più veloce". Bisogna spiegare dove aggiunge valore:
- sintesi
- classificazione
- generazione
- confronto
- estrazione
- controllo qualità
- automazione di un passaggio ripetitivo

---

## 3. Utenti target e contesto d'uso

Un sistema non esiste in astratto. Cambia a seconda di chi lo usa, dove lo usa e con quale obiettivo.

### Cosa definire

- utente primario
- eventuali utenti secondari
- livello tecnico
- contesto d'uso
- bisogno principale
- scenario d'uso realistico

### Esempio

```text
Utente primario: studente ITS con competenze digitali di base.
Contesto d'uso: dopo la lezione, da laptop personale.
Bisogno principale: trasformare appunti non strutturati in materiale utile per il ripasso.
```

### Errore comune

Scrivere "utenti generici" o "chiunque".  
Un progetto per "chiunque" è quasi sempre troppo vago.

---

## 4. Perimetro del MVP

Il MVP è la versione minima del sistema che dimostra il valore dell'idea.

Non è una demo incompleta. È una versione ridotta, ma funzionante.

### Funzionalità core

Inserire solo ciò che deve esistere nella prima versione.

Esempio:

```text
MVP-001 — L'utente può caricare un testo di appunti.
MVP-002 — Il sistema genera una sintesi strutturata.
MVP-003 — Il sistema restituisce 5 domande di ripasso.
```

### Fuori scope

Questa parte è fondamentale. Scrivere cosa non verrà costruito protegge il progetto da ambizioni eccessive.

Esempio:

```text
Fuori scope:
- login utenti
- dashboard analytics
- app mobile
- salvataggio cronologia cloud
- integrazione con Google Drive
```

### Regola pratica

Se una funzionalità non è necessaria per validare il MVP, resta fuori dalla prima versione.

---

## 5. Flusso operativo

Il flusso operativo descrive il percorso dal trigger iniziale all'output finale.

Serve a capire come il sistema funziona nella pratica.

### Domande guida

- Da cosa parte il sistema?
- Quale input riceve?
- Quali passaggi esegue?
- Dove interviene l'AI?
- Dove interviene l'essere umano?
- Qual è l'output finale?

### Esempio

```text
[Utente incolla appunti]
    → [Sistema pulisce il testo]
    → [AI genera sintesi]
    → [Validatore controlla lunghezza e formato]
    → [Utente visualizza e modifica]
    → [Output finale: sintesi + domande]
```

### Perché è utile

Un flusso scritto bene fa emergere subito le lacune:
- cosa succede se l'input è vuoto?
- cosa succede se l'output AI non rispetta il formato?
- chi approva il risultato?
- dove vengono salvati i dati?

---

## 6. Requisiti funzionali

I requisiti funzionali descrivono ciò che il sistema deve fare.

Devono essere numerati e verificabili.

### Formato

```text
RF-001 — Il sistema deve [azione verificabile].
Criterio di accettazione: funziona se [condizione verificabile].
```

**RF** significa **Requisito Funzionale**.  
Il numero serve per tracciare funzioni, test, bug e modifiche.

### Esempio debole

```text
Il sistema deve fare una buona sintesi.
```

### Esempio forte

```text
RF-001 — Il sistema deve generare una sintesi di massimo 500 parole a partire dagli appunti forniti dall'utente.
Criterio di accettazione: la sintesi contiene almeno 3 concetti principali e non supera il limite di parole.
```

### Regola pratica

Se non potete testare un requisito, non è scritto abbastanza bene.

---

## 7. Stack tecnologico e dipendenze

Questa sezione spiega con quali strumenti verrà costruito il progetto.

Non basta elencare tool: bisogna motivare le scelte.

### Cosa indicare

- linguaggio
- framework
- database
- provider AI
- modello AI
- librerie principali
- API esterne
- hosting o ambiente di esecuzione
- costo stimato

### Esempio

```text
Linguaggio: Python 3.12
Framework: Streamlit
Database: nessuno nella versione MVP
Provider AI: OpenAI / Anthropic / Gemini
Motivazione: setup rapido, costo basso, compatibilità con il livello del progetto.
```

### Domande guida

- Perché questo stack è adatto al MVP?
- Esistono alternative più semplici?
- Quali dipendenze esterne possono creare rischio?
- Il progetto resta utilizzabile se un provider cambia prezzo o API o se viene deprecato o se è momentaneamente irraggiungibile?

---

## 8. Architettura e flusso dati

L'architettura descrive i componenti principali e come comunicano tra loro.

Non serve un diagramma complesso. Serve chiarezza.

### Cosa indicare

- componenti del sistema
- responsabilità di ogni componente
- input e output
- dati salvati
- percorso dei dati

### Esempio

```text
[Interfaccia utente]
    → riceve gli appunti

[Modulo AI]
    → genera sintesi e domande

[Validatore]
    → controlla lunghezza, formato e dati inventati

[Output finale]
    → mostra risultato all'utente
```

### Errore comune

Confondere architettura con codice.  
La specifica deve spiegare come è organizzato il sistema, non scrivere già l'implementazione riga per riga.

---

## 9. Comportamento AI e prompt principali

Questa sezione è specifica per i progetti AI-powered.

Serve a chiarire dove viene usata l'AI, per quale compito e con quali regole.

### Cosa indicare

- task affidati all'AI
- input ricevuto dal modello
- output richiesto
- modello previsto
- prompt principali
- rischi del comportamento AI
- regole di esclusione

### Esempio

```text
Task AI: generazione sintesi
Input: appunti grezzi dell'utente
Output: sintesi strutturata in 3 sezioni
Rischio principale: inventare concetti non presenti negli appunti
Regola: usare solo informazioni presenti nel testo fornito
```

### Prompt principali

I prompt possono essere inseriti nella spec o referenziati come file esterni:

```text
prompts/generation.md
prompts/validation.md
prompts/rewrite.md
```

### Regola pratica

Se l'AI ha un compito importante nel sistema, quel compito deve essere descritto nella specifica.

---

## 10. Dati, privacy e vincoli normativi

Questa sezione non va lasciata alla fine come promemoria.  
I dati influenzano architettura, provider, logging, deploy e sicurezza.

### Cosa indicare

- quali dati vengono trattati
- se sono dati personali
- dove vengono salvati
- dove vengono inviati
- per quanto tempo restano disponibili
- se vengono inviati a provider esterni

### GDPR

Chiedersi sempre:

- il sistema tratta dati personali?
- questi dati sono necessari?
- possono essere ridotti o esclusi?
- vengono salvati?
- vengono inviati a terze parti?

### AI Act

Per un progetto didattico è sufficiente una valutazione preliminare:

```text
Categoria stimata: [Minimo / Trasparenza / Alto rischio / Non applicabile / Da verificare].
Motivazione: [spiegazione breve]
```

### Sicurezza minima

- API key in `.env`
- `.env` in `.gitignore`
- nessuna API key nei log
- nessun dato personale non necessario nei log
- errori gestiti senza esporre informazioni sensibili

---

## 11. Validazione e quality control

Questa è una delle sezioni più importanti.

L'output dell'AI non è automaticamente corretto. Va controllato.

### Cosa validare

- formato
- lunghezza
- coerenza
- presenza di dati inventati
- rispetto dei vincoli
- qualità dell'output
- errori tecnici

### Tipi di validatore

- checklist manuale
- regex
- schema JSON
- funzione Python
- secondo prompt di verifica
- revisione umana

### Esempio

```text
Validatore formato:
controlla che l'output abbia titolo, sintesi e domande.

Validatore lunghezza:
controlla che la sintesi non superi 500 parole.

Validatore contenuto:
controlla che il testo non contenga informazioni non presenti negli appunti.
```

### Regola pratica

Non chiedete al generatore di auto-certificarsi.  
Generazione e validazione devono essere due passaggi separati.

---

## 12. Gestione errori e fallback

Un sistema serio deve sapere cosa fare quando qualcosa va storto.

### Domande guida

- cosa succede se l'API AI non risponde?
- cosa succede se l'input è vuoto?
- cosa succede se l'output non supera la validazione?
- cosa succede se viene superato un limite di costo o rate limit?
- l'utente riceve un messaggio comprensibile?

### Esempio

```text
Errore: output non supera la validazione.
Comportamento: il sistema tenta una nuova generazione con messaggio di errore nel prompt.
Limite: massimo 2 retry.
Fallback: mostra messaggio all'utente e registra errore in INCIDENTS.md.
```

### Regola pratica

Gli errori non vanno solo corretti. Vanno documentati.

---

## 13. Deploy, manutenzione e aggiornamenti

Anche un MVP deve avere una strategia minima di esecuzione e manutenzione.

### Cosa indicare

- dove gira il progetto
- come si avvia
- quali variabili ambiente servono
- come si leggono gli errori
- cosa va monitorato
- chi aggiorna prompt, dipendenze e costi

### Esempio

```text
Ambiente di sviluppo: locale
Ambiente di produzione: non previsto nel MVP
Comando di avvio: streamlit run app.py
Variabili ambiente: OPENAI_API_KEY
Monitoring: errori console, costo API, output falliti
```

### Regola pratica

Se non sapete come avviare, monitorare o correggere il sistema, il progetto non è ancora pronto.

---

## 14. Rischi, assunzioni e decisioni aperte

Ogni progetto si basa su ipotesi.

La specifica deve renderle visibili.

### Assunzioni

Esempi:

```text
- Gli utenti forniranno appunti abbastanza lunghi da generare una sintesi utile.
- Il modello scelto sarà sufficiente per gestire testi in italiano.
- Il costo API resterà entro il budget previsto.
```

### Rischi

Esempio:

| Rischio | Probabilità | Impatto | Mitigazione |
|---------|-------------|---------|-------------|
| Output troppo generico | Media | Medio | migliorare prompt e validazione |
| Costo API superiore al previsto | Bassa | Medio | limitare lunghezza input/output |
| Dati personali negli appunti | Media | Alto | avviso utente e filtro dati |

### Decisioni aperte

Le decisioni non ancora prese vanno segnate, non lasciate implicite.

---

## 15. Checklist pre-build

Prima di iniziare a costruire, usare la checklist del template.

La checklist serve a evitare di partire con decisioni mancanti.

### La specifica è pronta quando

- il problema è chiaro
- l'obiettivo è misurabile
- il MVP è delimitato
- i requisiti sono verificabili
- lo stack è motivato
- il flusso operativo è completo
- i dati sono mappati
- i criteri di validazione sono definiti
- gli errori principali hanno un fallback
- la specifica può essere congelata

---

## 16. Registro versioni

Il registro versioni serve a tracciare l'evoluzione della specifica.

Ogni modifica importante deve indicare:

- versione
- data
- cosa è cambiato
- autore

### Esempio

| Versione | Data | Modifica | Autore |
|---------|------|----------|--------|
| 1.0 | 10/04/2026 | Prima versione completa | Nome |
| 1.1 | 12/04/2026 | Aggiornata sezione validazione | Nome |

---

## Errori comuni nella specifica

### 1. Scrivere troppo poco

Una specifica troppo breve lascia troppe decisioni implicite.  
L'AI builder dovrà indovinare.

### 2. Scrivere troppo senza decidere

Una specifica troppo lunga non è automaticamente buona o migliore di una più breve.  
Se contiene descrizioni generiche ma poche decisioni verificabili, non guida davvero il progetto.

### 3. Confondere obiettivo e funzionalità

L'obiettivo spiega il valore del sistema.  
Le funzionalità spiegano cosa il sistema fa.

### 4. Non definire il fuori scope

Senza fuori scope, il progetto tende a crescere senza controllo.

### 5. Non scrivere criteri di accettazione

Ogni requisito importante deve avere una risposta alla domanda:  
"Come verifico che funziona?"

### 6. Trattare la validazione come dettaglio finale

Nei sistemi AI, la validazione è parte dell'architettura.

---

## Quanto deve essere dettagliata la specifica?

Non esiste una lunghezza universale.

Per questo corso, la specifica deve essere:

- abbastanza dettagliata da guidare il building
- abbastanza chiara da essere letta da un'altra persona
- abbastanza sintetica da non diventare un documento ingestibile
- abbastanza verificabile da permettere test e revisione

### Regola pratica

La specifica è pronta quando una persona che non conosce il progetto può leggerla e capire:

- che cosa deve essere costruito
- perché serve
- per chi serve
- cosa è dentro e fuori dal MVP
- come funziona il flusso
- quali errori sono previsti
- come si valuta se il sistema funziona

---

## In sintesi

Una buona specifica tecnica non serve a bloccare la creatività.  
Serve a trasformarla in decisioni realizzabili.

Nel corso, la specifica è il documento che permette di passare da:

```text
idea interessante
```

a:

```text
MVP costruibile, validabile e documentato
```

**Il valore non sta nella lunghezza della specifica, ma nella qualità delle decisioni che contiene.**

---

*Documento creato per il corso AI Projects Development — ITS ICT Academy Roma*
