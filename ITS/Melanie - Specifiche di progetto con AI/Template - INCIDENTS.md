# INCIDENTS — Registro degli Incidenti

**Progetto:** `[Nome del progetto]`
**Aggiornato:** `YYYY-MM-DD`

> Documentare gli errori è parte integrante del metodo. Un incidente ben documentato diventa una risorsa di apprendimento. Minimo 2 voci richieste per la valutazione finale.

---

## Come Compilare

Un "incidente" è qualsiasi situazione in cui il sistema ha prodotto un output inatteso, errato, o ha fallito in modo non gestito. Include:
- Allucinazioni del modello
- Errori tecnici (eccezioni, crash)
- Output non conformi alla specifica
- Comportamenti inattesi in edge case

---

## Voci

---

### INC-001

**Data:** `YYYY-MM-DD`
**Componente:** `[es. validatore output]`
**Severità:** `Alta / Media / Bassa`
**Stato:** `Aperto / Risolto`

#### Descrizione del problema
*(Cosa è successo esattamente? Quale output hai ricevuto vs quale ti aspettavi?)*

#### Come è stato scoperto
*(Test manuale / test automatico / in produzione / peer review)*

#### Causa radice
*(Qual era la causa effettiva — prompt ambiguo, assunzione sbagliata, bug nel codice, limite del modello, ecc.)*

#### Impatto
*(Quali funzionalità erano compromesse? Quanti output erano affetti?)*

#### Soluzione adottata
*(Cosa hai cambiato per risolvere: modifica al prompt, aggiunta validatore, fix nel codice, ecc.)*

#### Come verificare che sia risolto
*(Test specifico che dimostra che il problema non si ripresenta)*

#### Prevenzione futura
*(Cosa aggiungi alla specifica, ai test o al processo per evitare che si ripeta?)*

---

### INC-002

**Data:** `YYYY-MM-DD`
**Componente:** `[componente]`
**Severità:** `Alta / Media / Bassa`
**Stato:** `Aperto / Risolto`

#### Descrizione del problema


#### Causa radice


#### Soluzione adottata


#### Prevenzione futura


---

*(aggiungere incidenti progressivamente durante lo sviluppo)*

---

## Riepilogo per la Presentazione

| ID | Componente | Problema | Causa | Risolto |
|---|---|---|---|---|
| INC-001 | | | | Sì/No |
| INC-002 | | | | Sì/No |

---

## Pattern di Errore Ricorrenti

*(Da compilare verso la fine del corso — quali tipi di errori si sono ripresentati?)*

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Template - PROMPT_LOG]]
- [[Template - Specifica Tecnica]]
