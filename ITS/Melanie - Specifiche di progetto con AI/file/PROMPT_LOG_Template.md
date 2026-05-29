# PROMPT_LOG.md — [Nome Progetto]

**Autrice:** Melanie Trucco — AI Projects Development, ITS ICT Academy Roma  
**Data:** Aprile 2026

*Questo file documenta l'evoluzione dei tuoi prompt di costruzione nel tempo. Ogni modifica deve essere registrata con motivazione. Un PROMPT_LOG compilato bene dimostra che hai iterato con metodo, non per tentativi casuali.*

---

## Come compilare questo file

Per ogni iterazione del prompt:
1. Copia la versione precedente del prompt
2. Descrivi cosa non funzionava nell'output
3. Scrivi cosa hai cambiato e perché
4. Descrivi il miglioramento osservato

Non cancellare le versioni precedenti, la storia delle iterazioni è parte del valore del documento.

---

## Esempio compilato — dal progetto AI Social Agents della docente Melanie Trucco
(In fondo trovate i template vuoti da compilare)

---

Content Generator per post LinkedIn

---

### Versione 1.0 — 2026-02-11

**Prompt:**
```
Sei un esperto di social media marketing. Scrivi un post LinkedIn 
professionale e coinvolgente per un'azienda italiana che si occupa 
di sostenibilità ambientale.
```

**Output osservato:**
Il modello ha prodotto un post che iniziava con "Nell'era in cui la sostenibilità è diventata una priorità..." — generico, lungo, con tono da comunicato stampa. Nessuna personalità, nessun hook.

**Problemi identificati:**
- Nessun formato definito → output imprevedibile in lunghezza e struttura
- Nessun vincolo sul tono → il modello ha scelto il tono "sicuro" (corporate)
- Nessun esempio di cosa si vuole → il modello ha usato i suoi pattern di default

---

### Versione 1.1 — 2026-02-11

**Cosa ho cambiato rispetto alla versione precedente:**
- Aggiunto formato strutturato (hook + corpo + chiusura)
- Aggiunto limite di parole (max 200)
- Aggiunto vincolo "NON iniziare con..." per i cliché più comuni

**Prompt aggiornato:**
```
Scrivi un post LinkedIn per un'azienda italiana di sostenibilità ambientale.

STRUTTURA:
- Hook (1 riga, max 12 parole): deve sorprendere o fare una domanda
- Corpo (3 paragrafi brevi): dato concreto + implicazione + call to action
- 3 hashtag pertinenti

VINCOLI:
- Max 200 parole
- Lingua: italiano, tono diretto e professionale
- NON iniziare con "Nell'era...", "Oggi più che mai...", "In un mondo..."
- NON inventare statistiche
```

**Output osservato:**
Netto miglioramento sul hook. Il dato era ancora inventato, nessuna fonte. Il corpo era ancora generico.

**Problemi residui:**
- Il modello inventa statistiche quando gli chiedi "dato concreto"
- Il corpo manca di specificità sul settore

---

### Versione 1.2 — 2026-02-12

**Cosa ho cambiato:**
- Aggiunto divieto esplicito di statistiche senza fonte
- Fornito contesto più specifico sull'azienda (nel brand profile)
- Spostato il "dato concreto" in input, non generato dall'AI

**Prompt aggiornato:**
```
[contesto azienda]: {brand_profile}
[dato reale da usare]: {dato_fornito}

Scrivi un post LinkedIn per questa azienda.

STRUTTURA:
- Hook (1 riga): usa il dato fornito per creare curiosità
- Corpo (2 paragrafi): cosa fa l'azienda + impatto concreto
- Chiusura: invito all'azione o domanda

VINCOLI:
- Max 180 parole
- NON aggiungere statistiche diverse da quella fornita
- NON usare "Nell'era...", "Oggi più che mai...", "Siamo orgogliosi..."
- 3 hashtag alla fine
```

**Output osservato:**
Output consistente e di qualità. Nessuna statistica inventata. Tono appropriato al brand. Hook efficace.

**Problemi residui:**
- Il modello chiude con una domanda retorica ogni volta → aggiungere rotazione dei tipi di chiusura

---

### Versione 1.3 — 2026-02-13 ✅

**Cosa ho cambiato:**
- Aggiunta variabile `{tipo_chiusura}` per alternare il pattern di conclusione

**Prompt finale in produzione:**
```
[vedi file prompts.py — funzione build_content_prompt()]
```

**Perché questa versione è stabile:**
Zero statistiche inventate, tono calibrato sul brand profile, struttura consistente, chiusure variate.

**Limiti noti:**
Fornire esempi diversi, risolvibile al prossimo round di post 

---

## Prompt: [Nome del componente — es. "Content Generator", "Validator", "Classifier"]

---

### Versione 1.0 — [Data]

**Prompt:**
```
[incolla qui il tuo primo prompt]
```

**Output osservato:**

**Problemi identificati:**
-xxxxxx e yyyyy
-xxxxxx e yyyyy

---

### Versione 1.1 — [Data]

**Cosa ho cambiato rispetto alla versione precedente:**
-xxxxxx e yyyyy
-xxxxxx e yyyyy

**Prompt aggiornato:**
```
[incolla qui il prompt modificato]
```

**Output osservato:**

**Problemi residui:**
-

---

### Versione 1.2 — [Data]

**Cosa ho cambiato:**
-

**Prompt aggiornato:**
```
[prompt]
```

**Output osservato:**

**Problemi residui:**

---

### Versione FINALE — [Data] ✅

**Prompt in produzione:**
```
[prompt finale]
```

**Perché questa versione è stabile:**

**Limiti noti:**

---


*Documento creato per il corso AI Projects Development — ITS ICT Academy Roma*
