# [NOME DEL PROGETTO] — Specifica Tecnica

**Versione:** 1.0  
**Data:** [GG/MM/AAAA]  
**Stato:** BOZZA / CONGELATA  
**Autore:** [Nome]  
**Repository:** [link GitHub]  

> Ogni versione del documento deve essere salvata come file distinto (v1, v2, etc.) e consegnata singolarmente per tracciare l'evoluzione del progetto.  
> La specifica finale deve essere completata PRIMA di iniziare il building.
> > Dopo il congelamento, ogni modifica importante deve essere documentata in una nuova versione, in `SPEC_ERRATA.md` o nel registro versioni (leggere la Guida).

---

## 0. Sintesi del progetto

Compilare questa sezione alla fine, ma tenerla all'inizio del documento.

**Nome del progetto:**  
[Nome breve e chiaro]

**Descrizione in una frase:**  
[Il sistema fa X per aiutare Y a ottenere Z]

**Tipo di sistema:**  
[es. chatbot, automazione, classificatore, generatore di contenuti, dashboard, pipeline dati, assistente operativo]

**Obiettivo del MVP:**  
[Che cosa deve dimostrare la prima versione funzionante]

**Output principale:**  
[Che cosa produce concretamente il sistema]

---

## 1. Obiettivo del sistema

### 1.1 Obiettivo principale

Descrivere in modo chiaro che cosa deve fare il sistema.

```text
Il sistema deve [azione principale] per [utente/contesto], producendo [output] in modo [criterio di qualità].
```

**Obiettivo:**  
[Scrivere qui]

### 1.2 Criterio di successo

Come capiamo che il sistema funziona?

| Criterio | Soglia minima | Come viene misurato |
|---------|---------------|---------------------|
| Accuratezza / qualità output | [es. almeno 80% output approvati] | [test, revisione manuale, checklist] |
| Tempo di esecuzione | [es. max 30 secondi] | [misura tecnica] |
| Costo operativo | [es. max 5 euro/mese] | [stima token/API] |
| Usabilità | [es. completabile in 3 passaggi] | [test utente] |

---

## 2. Problema che risolve

### 2.1 Problema reale

Quale problema concreto risolve il sistema?

**Problema:**  
[Descrizione del problema]

### 2.2 Perché è importante

Perché vale la pena risolverlo?

- [Motivo 1]
- [Motivo 2]
- [Motivo 3]

### 2.3 Soluzioni attuali

Come viene risolto oggi il problema?

| Soluzione attuale | Limite principale |
|------------------|------------------|
| [es. lavoro manuale] | [es. richiede troppo tempo] |
| [es. tool esistente] | [es. non personalizzato] |

### 2.4 Perché usare AI

Spiegare perché l'AI aggiunge valore rispetto a una soluzione tradizionale.

**L'AI è utile perché:**  
[Scrivere qui]

**L'AI non è necessaria se:**  
[Scrivere qui eventuali casi in cui una soluzione semplice basta]

---

## 3. Utenti target e contesto d'uso

### 3.1 Utente primario

| Campo | Descrizione |
|------|-------------|
| Utente primario | [Chi usa il sistema] |
| Livello tecnico | [Base / medio / avanzato] |
| Bisogno principale | [Che cosa vuole ottenere] |
| Contesto d'uso | [Dove/quando lo usa] |

### 3.2 Utenti secondari

| Utente | Ruolo nel sistema | Bisogno |
|--------|------------------|---------|
| [Utente 2] | [es. revisore] | [bisogno] |
| [Utente 3] | [es. amministratore] | [bisogno] |

### 3.3 Scenario d'uso principale

Descrivere un esempio realistico di utilizzo.

```text
Quando [situazione], l'utente [azione].
Il sistema riceve [input], elabora [processo] e restituisce [output].
L'utente poi [azione successiva].
```

---

## 4. Perimetro del MVP

### 4.1 Funzionalità core del MVP

Inserire solo ciò che deve essere costruito nella prima versione funzionante.

| ID | Funzionalità core | Priorità | Perché è necessaria |
|----|------------------|----------|---------------------|
| MVP-001 | [Funzionalità] | Alta | [Motivazione] |
| MVP-002 | [Funzionalità] | Alta | [Motivazione] |
| MVP-003 | [Funzionalità] | Media | [Motivazione] |

### 4.2 Funzionalità escluse / fuori scope

Tutto ciò che non è necessario per validare il MVP deve restare fuori.

| Fuori scope | Motivo dell'esclusione | Possibile versione futura |
|------------|------------------------|---------------------------|
| [Funzione esclusa] | [Troppo complessa / non essenziale] | v2 / mai |
| [Funzione esclusa] | [Motivo] | v2 / mai |

> Regola: se una funzionalità non è scritta nel MVP, non si costruisce nella prima versione.

---

## 5. Flusso operativo

Descrivere il percorso completo dal trigger iniziale all'output finale.

### 5.1 Flusso principale

```text
[Trigger iniziale]
    → [Input utente / input dati]
    → [Elaborazione 1]
    → [Elaborazione AI]
    → [Validazione]
    → [Eventuale intervento umano]
    → [Output finale]
```

### 5.2 Input richiesti

| Input | Formato | Obbligatorio? | Esempio |
|------|---------|---------------|---------|
| [Input 1] | [testo/file/API] | Sì/No | [esempio] |
| [Input 2] | [formato] | Sì/No | [esempio] |

### 5.3 Output atteso

| Output | Formato | Destinazione |
|--------|---------|--------------|
| [Output 1] | [testo/JSON/file/database] | [utente/app/database] |
| [Output 2] | [formato] | [destinazione] |

### 5.4 Punto di intervento umano

Indicare dove una persona deve controllare, approvare o correggere.

**Intervento umano previsto:**  
[Sì/No]

**Dove avviene:**  
[Prima dell'output finale / dopo la generazione / solo in caso di errore]

**Che cosa deve verificare:**  
[Scrivere qui]

---

## 6. Requisiti funzionali

Ogni requisito deve essere verificabile. Usare il formato `RF-XXX` (Requisito Funzionale).

| ID | Requisito | Priorità | Criterio di accettazione |
|----|-----------|----------|--------------------------|
| RF-001 | Il sistema deve... | Alta | Funziona se... |
| RF-002 | Il sistema deve... | Alta | Funziona se... |
| RF-003 | Il sistema deve... | Media | Funziona se... |
| RF-004 | Il sistema deve... | Bassa | Funziona se... |

### Esempio di requisito ben scritto

```text
RF-001 — Il sistema deve generare una sintesi di massimo 150 parole a partire da un testo caricato dall'utente.
Criterio di accettazione: la sintesi deve rispettare il limite di parole e mantenere almeno 3 concetti principali del testo originale.
```

---

## 7. Stack tecnologico e dipendenze

### 7.1 Scelte tecniche principali

| Area | Scelta | Motivazione |
|------|--------|-------------|
| Linguaggio | [es. Python 3.12] | [perché] |
| Framework | [es. FastAPI / Streamlit / Flask] | [perché] |
| Database | [es. SQLite / PostgreSQL / nessuno] | [perché] |
| Provider AI | [es. OpenAI / Anthropic / Gemini] | [perché] |
| Modello AI | [modello previsto] | [perché] |
| Hosting / deploy | [locale / Render / VPS / altro] | [perché] |

### 7.2 Dipendenze esterne

| Dipendenza | Uso | Rischio | Alternativa |
|-----------|-----|---------|-------------|
| [API esterna] | [a cosa serve] | [rate limit/costo/vendor lock-in] | [alternativa] |
| [Libreria] | [a cosa serve] | [rischio] | [alternativa] |

### 7.3 Stima costi

| Voce | Stima | Note |
|------|-------|------|
| API AI | [€/mese o $/mese] | [ipotesi di utilizzo] |
| Hosting | [€/mese] | [free tier / piano usato] |
| Database / storage | [€/mese] | [se applicabile] |
| Totale stimato | [€/mese] | [limite massimo accettabile] |

---

## 8. Architettura e flusso dati

### 8.1 Componenti principali

| Componente | Responsabilità | Input | Output |
|------------|----------------|-------|--------|
| [Frontend / UI] | [cosa fa] | [input] | [output] |
| [Backend] | [cosa fa] | [input] | [output] |
| [Modulo AI] | [cosa fa] | [input] | [output] |
| [Validatore] | [cosa fa] | [input] | [output] |
| [Database] | [cosa salva] | [input] | [output] |

### 8.2 Diagramma testuale del flusso dati

```text
[Utente]
   → [Interfaccia]
   → [Backend]
   → [Modulo AI]
   → [Validatore]
   → [Output finale]
```

### 8.3 Dati salvati

| Dato | Dove viene salvato | Per quanto tempo | Perché serve |
|------|-------------------|------------------|--------------|
| [Dato 1] | [file/db/log] | [durata] | [motivo] |
| [Dato 2] | [file/db/log] | [durata] | [motivo] |

---

## 9. Comportamento AI e prompt principali

Questa sezione descrive come viene usato il modello AI nel sistema.

### 9.1 Task affidati all'AI

| Task AI | Input | Output | Modello previsto | Rischio principale |
|--------|-------|--------|------------------|-------------------|
| [es. generazione sintesi] | [testo] | [sintesi] | [modello] | [allucinazioni / tono / formato] |
| [task 2] | [input] | [output] | [modello] | [rischio] |

### 9.2 Prompt principali

Inserire qui i prompt o il riferimento ai file `.md`.

| Nome prompt | File | Funzione |
|------------|------|----------|
| [prompt_generation] | `prompts/generation.md` | [genera output] |
| [prompt_validation] | `prompts/validation.md` | [verifica output] |

### 9.3 Regole di comportamento AI

- Non inventare dati non presenti nell'input
- Segnalare quando mancano informazioni
- Rispettare il formato richiesto
- Non produrre output finale se la validazione fallisce
- [Altra regola specifica]

---

## 10. Dati, privacy e vincoli normativi

### 10.1 Dati trattati

| Tipo di dato | È personale? | Fonte | Dove viene inviato? |
|-------------|--------------|-------|---------------------|
| [Dato] | Sì/No | [utente/API/file] | [locale/provider AI/API esterna] |
| [Dato] | Sì/No | [fonte] | [destinazione] |

### 10.2 GDPR

**Il sistema tratta dati personali?**  
[Sì/No/Non so]

Se sì:
- quali dati personali?
- perché sono necessari?
- dove vengono salvati?
- per quanto tempo?
- vengono inviati a provider esterni?

**Decisione GDPR:**  
[Descrivere come vengono ridotti, protetti o esclusi i dati personali]

### 10.3 AI Act

**Categoria di rischio stimata:**  
[Minimo / Trasparenza / Alto rischio / Non applicabile / Da verificare]

**Motivazione:**  
[Spiegare perché]

**Obblighi o attenzioni:**  
[Trasparenza, supervisione umana, logging, disclaimer, ecc.]

### 10.4 Sicurezza minima

- [ ] Le API key sono in `.env`
- [ ] `.env` è nel `.gitignore`
- [ ] Non vengono salvati dati sensibili inutili
- [ ] Gli errori non espongono informazioni private
- [ ] I log non contengono API key o dati personali non necessari

---

## 11. Validazione e quality control

### 11.1 Cosa deve essere validato

| Elemento | Metodo di validazione | Soglia / criterio |
|---------|----------------------|-------------------|
| Formato output | [schema / checklist / regex] | [criterio] |
| Qualità contenuto | [review umana / test set] | [criterio] |
| Allucinazioni | [controllo fonti / regole] | [criterio] |
| Lunghezza | [conteggio parole/token] | [criterio] |
| Errori tecnici | [test automatici] | [criterio] |

### 11.2 Validatori previsti

| Validatore | Tipo | Cosa controlla | Azione se fallisce |
|-----------|------|----------------|--------------------|
| [validator_format] | [schema/regex/Python] | [formato] | [retry / errore / blocco] |
| [validator_quality] | [LLM/umano/checklist] | [qualità] | [review / retry] |
| [validator_safety] | [regole/checklist] | [dati vietati] | [blocco] |

### 11.3 Test minimi prima del deploy

| Test | Input | Output atteso | Stato |
|------|-------|---------------|-------|
| Caso normale | [input] | [output] | Da fare |
| Caso limite | [input] | [output] | Da fare |
| Input incompleto | [input] | [errore controllato] | Da fare |
| Output non valido | [input] | [retry/blocco] | Da fare |

---

## 12. Gestione errori e fallback

| Scenario di errore | Comportamento atteso | Retry? | Messaggio / fallback |
|-------------------|----------------------|--------|----------------------|
| API AI non risponde | [cosa succede] | Sì/No | [messaggio] |
| Output non supera validazione | [cosa succede] | Sì/No | [messaggio] |
| Input mancante o non valido | [cosa succede] | Sì/No | [messaggio] |
| API esterna restituisce errore | [cosa succede] | Sì/No | [messaggio] |
| Costo/rate limit superato | [cosa succede] | Sì/No | [messaggio] |

### Errori da registrare in `INCIDENTS.md`

- errore tecnico ricorrente
- output AI non conforme
- costo anomalo
- allucinazione rilevante
- perdita di dati o comportamento inatteso
- decisione tecnica cambiata a causa di un problema

---

## 13. Deploy, manutenzione e aggiornamenti

### 13.1 Piano di deploy

| Elemento | Decisione |
|---------|-----------|
| Ambiente di sviluppo | [locale / altro] |
| Ambiente di produzione | [locale / cloud / VPS / altro] |
| Comando di avvio | [comando] |
| Variabili ambiente richieste | [lista] |
| Strategia di logging | [file/log console/servizio] |

### 13.2 Monitoring minimo

Cosa deve essere monitorato?

- errori di esecuzione
- costo API
- numero di richieste
- output falliti
- tempo medio di risposta
- feedback utente

### 13.3 Manutenzione

| Attività | Frequenza | Responsabile |
|---------|-----------|--------------|
| Controllo log | [giornaliero/settimanale] | [nome] |
| Revisione prompt | [frequenza] | [nome] |
| Aggiornamento dipendenze | [frequenza] | [nome] |
| Revisione costi | [frequenza] | [nome] |

---

## 14. Rischi, assunzioni e decisioni aperte

### 14.1 Assunzioni

Scrivere qui le ipotesi che date per vere ma che potrebbero rivelarsi sbagliate.

- [Assunzione 1]
- [Assunzione 2]
- [Assunzione 3]

### 14.2 Rischi

| Rischio | Probabilità | Impatto | Mitigazione |
|---------|-------------|---------|-------------|
| [Rischio] | Alta/Media/Bassa | Alto/Medio/Basso | [Mitigazione] |
| [Rischio] | Alta/Media/Bassa | Alto/Medio/Basso | [Mitigazione] |
| [Rischio] | Alta/Media/Bassa | Alto/Medio/Basso | [Mitigazione] |

### 14.3 Decisioni aperte

| Decisione da prendere | Opzioni | Deadline | Responsabile |
|----------------------|---------|----------|--------------|
| [Decisione] | [A/B/C] | [data] | [nome] |
| [Decisione] | [A/B/C] | [data] | [nome] |

---

## 15. Checklist pre-build

Prima di iniziare a costruire, verificare che la specifica sia completa.

### Problema e obiettivo

- [ ] Il problema è reale e ben definito
- [ ] L'obiettivo del sistema è chiaro
- [ ] Il successo è misurabile
- [ ] L'utente target è definito

### Scope e MVP

- [ ] Le funzionalità core del MVP sono esplicite
- [ ] Le funzionalità fuori scope sono esplicite
- [ ] Il progetto è realizzabile nel tempo disponibile
- [ ] Il progetto è sostenibile con costi minimi

### Architettura e implementazione

- [ ] Il flusso operativo è completo
- [ ] I requisiti funzionali sono verificabili
- [ ] Lo stack tecnologico è motivato
- [ ] Le dipendenze esterne sono note
- [ ] Le API key e i segreti sono gestiti correttamente

### AI, dati e validazione

- [ ] È chiaro dove viene usata l'AI
- [ ] I prompt principali sono definiti o referenziati
- [ ] I dati trattati sono mappati
- [ ] GDPR / AI Act sono stati considerati
- [ ] I criteri di validazione sono definiti
- [ ] Sono previsti fallback per gli errori principali

### Deploy e manutenzione

- [ ] Il piano di deploy è realistico
- [ ] È chiaro cosa monitorare
- [ ] È definito come documentare errori e modifiche
- [ ] La specifica è pronta per essere congelata

---

## 16. Registro versioni

| Versione | Data | Modifica | Autore |
|---------|------|----------|--------|
| 1.0 | [data] | Prima versione della specifica | [nome] |

---

*Documento creato per il corso AI Projects Development — ITS ICT Academy Roma*
