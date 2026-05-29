# INCIDENTS.md — [Nome Progetto]

**Autrice:** Melanie Trucco — AI Projects Development, ITS ICT Academy Roma  
**Data:** Aprile 2026

*Ogni problema rilevante che incontri durante il building va documentato qui: è la tua memoria tecnica. Solo dati sintetici, l'agente in Cursor può fornirti i dati se ti occorre*

---

## Come compilare una voce

Ogni incident deve avere:
- **Data e ora** (approssimativa va bene)
- **Severity** (critico / medio / basso)
- **Descrizione**: cosa è successo
- **Root cause**: perché è successo (spesso diverso da cosa è successo)
- **Fix applicato**: cosa hai fatto concretamente per risolverlo
- **Learnings**: cosa non farai mai più, o farai diversamente
- **Stato**: risolto / in monitoraggio / workaround attivo / aperto

---


## Esempi compilati — dal progetto AI Social Agents della docente Melanie Trucco
(In fondo trovate i template vuoti da compilare)

---

### INC-001 — Il post citava statistiche inventate: 87% e 3.2x
**Data:** 2026-02-10
**Severity:** Critico
**Componente:** Content Generator — prompt principale

**Cosa è successo:**
Durante la review manuale di un batch di 5 post, ho notato che due contenevano statistiche specifiche ("il 87% dei professionisti..." e "aumenta il ROI di 3.2x"). Non ho mai fornito questi dati al sistema. Erano stati generati dall'LLM.

**Root cause:**
Il prompt chiedeva "includi dati concreti per dare credibilità al contenuto". Il modello, non avendo dati reali, ha inventato percentuali plausibili. Il problema era già nella specifica del prompt, non nella validazione.

**Fix applicato:**
1. Aggiunto constraint esplicito nel prompt: "NON aggiungere mai statistiche, percentuali o dati numerici a meno che non siano forniti esplicitamente in input"
2. Costruito validatore regex che cerca pattern come `\d+[%]`, `\d+x`, `\d+ volte su \d+`
3. Aggiunto test automatico con 10 post campione

**Learnings:**
- Quando chiedi "dati concreti" a un LLM senza fornirglieli, inventa. Sempre.
- Il vincolo "NON fare X" è più efficace del vincolo "fai Y in modo responsabile"
- I validatori regex sono grossolani ma efficaci come prima linea di difesa

**Stato:** Risolto

---

### INC-002 — Output in inglese invece di italiano su ~20% dei post
**Data:** 2026-02-14
**Severity:** Medio
**Componente:** Content Generator — variabile lingua

**Cosa è successo:**
Circa 1 post su 5 veniva generato in inglese nonostante il brand profile specificasse italiano.

**Root cause:**
Il brand profile del cliente era scritto in inglese. L'LLM, vedendo un contesto prevalentemente in inglese, tendeva a rispondere in inglese anche quando la lingua target era specificata altrove nel prompt.

**Fix applicato:**
Spostato il vincolo lingua all'inizio del prompt e reso esplicito: "LINGUA OBBLIGATORIA: italiano. Anche se il contesto fornito è in un'altra lingua, il post DEVE essere in italiano."

**Learnings:**
- La lingua del contesto influenza la lingua dell'output più di quanto pensassi
- L'ordine delle istruzioni nel prompt conta: le istruzioni critiche vanno in cima

**Stato:** Risolto

---

### INC-003 — Validatore approvava post ripetitivi: stessa struttura 7 volte
**Data:** 2026-02-17
**Severity:** Basso
**Componente:** Validator — thematic_repetition check

**Cosa è successo:**
7 post consecutivi iniziavano con una domanda retorica e finivano con "Cosa ne pensi?". Tutti approvati dal validatore.

**Root cause:**
Il validatore controllava ogni post singolarmente (stateless). Non aveva memoria dei post precedenti.

**Fix applicato:**
Aggiunto check di diversità tematica che confronta ogni nuovo post con gli ultimi 7 approvati tramite similarità.

**Learnings:**
- La validazione va pensata sia a livello di singolo output che a livello di serie temporale
- "Statefulness" non è solo un problema di architettura è un problema di qualità

**Stato:** Risolto

---

TEMPLATE VUOTI DA UTILIZZARE

---

## Incident Log

### INC-001 — [Titolo]
**Data:**
**Severity:**
**Componente:**

**Cosa è successo:**

**Root cause:**

**Fix applicato:**

**Learnings:**
-

**Stato:**

---

### INC-002 — [Titolo]
**Data:**
**Severity:**
**Componente:**

**Cosa è successo:**

**Root cause:**

**Fix applicato:**

**Learnings:**
-

**Stato:**

---

*Documento creato per il corso AI Projects Development — ITS ICT Academy Roma*
