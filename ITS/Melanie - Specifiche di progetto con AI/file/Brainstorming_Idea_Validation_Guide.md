# Brainstorming e Validazione dell'Idea — Guida Pratica

**Per:** Studenti del corso AI Projects Development, ITS ICT Academy Roma  
**Autrice:** Melanie Trucco  
**Data:** Aprile 2026  
**File collegato:** `Brainstorming_Idea_Validation_Template.md`

---

## Premessa

La scelta dell'idea non è una fase informale del progetto.  
È il primo passaggio di progettazione.

Prima di scrivere la specifica tecnica, dovete documentare come siete arrivati all'idea finale: quali alternative avete considerato, perché le avete scartate e perché l'idea scelta merita di diventare un MVP.

L'obiettivo non è dimostrare che la prima idea era giusta.  
L'obiettivo è dimostrare che avete ragionato, confrontato opzioni, identificato vincoli e scelto una direzione costruibile.

---

## Che cosa dovete consegnare

Il documento di brainstorming deve contenere:

1. **Brainstorming iniziale**  
   2-5 idee considerate nella fase iniziale.

2. **Confronto tra le idee**  
   motivazione sintetica, punti di forza, rischi e motivo per cui ogni idea è stata tenuta o scartata.

3. **Idea scelta**  
   descrizione chiara dell'idea che volete trasformare in progetto.

4. **Motivazione della scelta**  
   perché questa idea è più adatta al corso rispetto alle alternative.

5. **Validazione sulle 5 dimensioni**  
   fattibilità tecnica, sostenibilità economica, complessità, rischio e compliance, sostenibilità tecnologica.

6. **Decisione finale**  
   cosa verrà portato nella specifica tecnica e cosa resta fuori.

---

## Perché documentare anche le idee scartate

Le idee scartate sono parte del processo.  
Mostrano che non avete scelto a caso, ma avete confrontato possibilità diverse e valutato vincoli reali.

Una buona documentazione deve spiegare:

- quali idee sono state considerate
- quale problema cercavano di risolvere
- perché erano interessanti
- perché non sono state scelte
- che cosa avete imparato dal confronto

Scartare un'idea non significa che fosse sbagliata.  
Significa che, per questo corso, non era la più adatta per vincoli di tempo, costo, complessità o originalità.

---

## Il ruolo dell'AI nel brainstorming

L'AI non deve servire a confermare l'idea.  
Deve servire a metterla alla prova.

Usatela come strumento di analisi critica:

- per generare alternative
- per identificare rischi
- per evidenziare ambiguità
- per confrontare opzioni
- per ridurre il perimetro del progetto
- per trasformare un'idea generica in un MVP concreto

### Prompt utile

```text
Voglio costruire un sistema AI che faccia [X] per risolvere [Y].

Analizza questa idea come un consulente esperto.
Non cercare di confermarla: mettila alla prova.

Valutala su queste dimensioni:
1. fattibilità tecnica
2. sostenibilità economica
3. complessità
4. rischio e compliance
5. sostenibilità tecnologica

Per ogni dimensione indica:
- giudizio sintetico
- rischio principale
- domanda critica da risolvere
- suggerimento per ridurre lo scope
```

---

## Come scegliere l'idea finale

L'idea scelta deve rispettare quattro criteri di base:

1. **Problema reale**  
   deve risolvere un bisogno concreto, anche piccolo.

2. **Originalità**  
   non deve essere un progetto copiato online, già iniziato altrove o riciclato da altri corsi.

3. **Realizzabilità**  
   deve poter diventare un MVP entro il tempo disponibile.

4. **Costo quasi zero**  
   deve poter funzionare con strumenti gratuiti, free tier o costi API minimi.

Una buona idea di progetto non è necessariamente grande.  
È chiara, delimitata e costruibile.

---

## Le 5 dimensioni di validazione

### 1. Fattibilità tecnica

La domanda principale è: **si può costruire davvero con le competenze, gli strumenti e il tempo disponibili?**

Domande guida:
- L'AI aggiunge valore reale rispetto a una soluzione tradizionale?
- Il problema è definito con chiarezza?
- L'input e l'output del sistema sono chiari?
- Esistono librerie, API o strumenti adatti?
- Serve un'integrazione troppo complessa?

Esempio di risposta debole:

```text
Sì, è fattibile perché l'AI può farlo.
```

Esempio di risposta migliore:

```text
È fattibile se il MVP si limita a generare una sintesi da testo incollato dall'utente.
Non è fattibile, nella prima versione, includere login, salvataggio cloud e integrazione con Google Drive.
```

---

### 2. Sostenibilità economica

La domanda principale è: **il valore generato giustifica i costi di sviluppo e inference?**

Domande guida:
- Quante chiamate API servono per ogni utilizzo?
- Il progetto può funzionare con free tier o crediti minimi?
- Il costo cresce troppo se aumentano gli utenti?
- Si possono usare modelli leggeri per i task semplici?
- Il costo operativo è proporzionato al valore del progetto?

Esempio:

```text
Il costo è sostenibile perché il MVP usa una sola chiamata AI per ogni sintesi.
Il rischio economico aumenta se vengono aggiunti upload multipli, retry automatici o generazione di output molto lunghi.
```

---

### 3. Complessità

La domanda principale è: **lo sforzo richiesto è proporzionato al risultato che vogliamo ottenere?**

Domande guida:
- Quante componenti servono?
- Il progetto richiede frontend, backend, database e API esterne?
- Quale parte rischia di bloccare lo sviluppo?
- Il MVP può essere ridotto a un flusso più semplice?
- L'impatto giustifica lo sforzo?

Esempio:

```text
La versione completa è troppo complessa perché richiede login, database e dashboard.
Il MVP è gestibile se viene ridotto a una web app locale con input testuale e output strutturato.
```

---

### 4. Rischio e compliance

La domanda principale è: **il progetto introduce rischi tecnici, etici, legali o reputazionali?**

Domande guida:
- Il sistema tratta dati personali?
- Dove vengono inviati i dati?
- Serve considerare GDPR o AI Act?
- L'output può essere interpretato come consiglio medico, legale o finanziario?
- Ci sono dipendenze critiche da provider esterni?
- Serve supervisione umana prima dell'output finale?

Esempio:

```text
Il progetto può trattare dati personali se gli utenti inseriscono nomi o email negli appunti.
Per ridurre il rischio, il MVP non salva dati e avvisa l'utente di non inserire informazioni sensibili.
```

---

### 5. Sostenibilità tecnologica

La domanda principale è: **il sistema può sopravvivere a cambiamenti di modello, provider, prezzi o API?**

Domande guida:
- Il progetto dipende da un solo provider?
- È possibile cambiare modello senza riscrivere tutto?
- Le API usate sono stabili?
- Esistono alternative tecniche?
- Il sistema è progettato in modo abbastanza modulare?

Esempio:

```text
Il rischio principale è la dipendenza da un solo provider AI.
Per ridurlo, il codice deve isolare la chiamata al modello in un modulo separato, così da poter sostituire il provider in futuro.
```

---

## Come documentare il passaggio alla specifica tecnica

Alla fine della validazione, dovete produrre una decisione chiara:

```text
Procedo con questa idea perché [motivazione].
Il MVP includerà [funzionalità core].
Il MVP escluderà [fuori scope].
I rischi principali sono [rischi].
Queste decisioni verranno trasferite nella specifica tecnica.
```

Questa parte serve da ponte tra brainstorming e specifica tecnica.

La specifica tecnica non deve nascere da zero.  
Deve partire dalle decisioni prese durante la validazione dell'idea.

---

## Errori comuni

### 1. Scegliere l'idea più interessante, non quella più costruibile

Un'idea può essere interessante ma troppo grande per il corso.

### 2. Non documentare le alternative

Se non documentate le idee scartate, non si vede il processo decisionale.

### 3. Usare l'AI solo per confermare

Un brainstorming utile deve cercare criticità, non solo approvazione.

### 4. Confondere MVP e prodotto finale

Il MVP deve dimostrare il valore dell'idea, non includere tutte le funzionalità immaginabili.

### 5. Ignorare costi e dipendenze

Un progetto tecnicamente possibile può essere comunque fragile o insostenibile.

---

## Checklist finale

Prima di passare alla specifica tecnica, verificate:

- [ ] Ho documentato 2-5 idee iniziali
- [ ] Ho spiegato perché alcune idee sono state scartate
- [ ] Ho scelto una sola idea finale
- [ ] Ho motivato la scelta
- [ ] Ho analizzato l'idea sulle 5 dimensioni
- [ ] Ho identificato i rischi principali
- [ ] Ho definito cosa entra nel MVP
- [ ] Ho definito cosa resta fuori scope
- [ ] Ho individuato le decisioni da trasferire nella specifica tecnica

---

## In sintesi

Il brainstorming non serve a generare tante idee.  
Serve a scegliere consapevolmente quale idea vale la pena costruire.

La validazione non serve a dimostrare che l'idea è perfetta.  
Serve a capire se è abbastanza chiara, sostenibile e realizzabile da diventare un progetto.

---

*Documento creato per il corso AI Projects Development — ITS ICT Academy Roma*
