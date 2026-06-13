# Piano Demo — Assistente AI "Voce & Forma"

**Progetto:** Assistente AI per revisione documenti (PROGETTO LORENZO)
**Data:** 2026-06-13
**Scadenza demo:** lunedì 16/06/2026
**Documenti a monte:** [[Validazione Idea]] · [[Specifica Tecnica]]

> Stima e piano operativo per costruire la demo del flusso **Voce & Forma**: bozza grezza → riscrittura nello stile + controllo forma + segnalazione norme → gate umano.

---

## Principio guida

La demo ha **due nature diverse**, da non confondere:
- **Setup** (lo costruisci una volta → conta la qualità → modello forte: Opus)
- **Runtime** (è il prodotto vero che mostri → modello del prodotto: Sonnet 4.6)

**Regola d'oro:** mai mostrare a Lorenzo un output prodotto con Opus+thinking se poi lo Studio gira Sonnet+low. **La demo gira in condizioni reali.**

---

## Stima per fasi

| Fase                             | Cosa                                                                                 | Modello        | Effort | Think          | Tempo     |
| -------------------------------- | ------------------------------------------------------------------------------------ | -------------- | ------ | -------------- | --------- |
| **0. Raccolta**                  | ~10 file campione stile + 1 parere-tipo grezzo da riscrivere                         | — (umano)      | —      | —              | 30-60 min |
| **1. Estrazione `voce.md`**      | Analisi dei 10 file → regole di stile (tono, lessico, struttura) in 1 file portabile | **Opus 4.8**   | high   | `ultrathink`   | 30-45 min |
| **2. Prompt/Skill Voce & Forma** | Direttiva: `voce.md` + task (riscrittura+forma+segnalazione) + hard rule citazioni   | **Sonnet 4.6** | medium | `think hard`   | ~20 min   |
| **3. Esecuzione riscrittura**    | Bozza grezza → output completo. Il prodotto in azione                                | **Sonnet 4.6** | low    | nessuno        | 5 min/run |
| **4. Validazione rubrica**       | Voto 3 assi su output + 1-2 iterazioni di fix prompt                                 | **Opus 4.8**   | medium | `think harder` | ~30 min   |

**Totale realistico: ~2,5-3,5 ore** di lavoro effettivo (escluso il tempo per procurare i file in Fase 0).

---

## Dettaglio operativo delle fasi

### Fase 0 — Raccolta *(umano)*
**Cosa:** procurare il materiale di partenza. **Input:** nessuno. **Output:** ~10 testi campione di stile (✅ fatto: Studio Tarabella) **+ 1 bozza grezza** da riscrivere nella demo (appunti disordinati su un tema fiscale — ancora da scegliere). **Come:** ricerca web su un autore singolo e coerente. È il collo di bottiglia: senza la bozza grezza la Fase 3 non parte.

### Fase 1 — Estrazione `voce.md`
**Cosa:** leggere i 10 testi e distillarne le regole di stile in un unico file portabile. **Input:** corpus. **Output:** [[voce.md]] (✅ fatto). **Come:** analisi dei 3 assi (tecnico / empatico / tipografico) + struttura "sandwich" + hard rule citazioni. Modello forte perché è il task di giudizio fondante.

### Fase 2 — Direttiva/Skill Voce & Forma  ← *prossimo step*
**Cosa:** costruire **l'istruzione operativa** che fa lavorare il modello — il "programma" della demo. Non è ancora riscrivere niente: è preparare lo strumento. **Input:** `voce.md` + struttura output dalla [[Specifica Tecnica]]. **Output:** un file direttiva (es. `skill-voce-forma.md`) che contiene:
1. **Ruolo e compito** — "riscrivi questa bozza nello stile definito in voce.md".
2. **I 3 sotto-task in un solo passaggio** — riscrittura stile + controllo forma + segnalazione norme.
3. **Formato di output rigido** — le 4 sezioni della spec: `testo_riscritto` · `note_forma` · `norme_da_verificare` · `disclaimer_gate`.
4. **Hard rule citazioni** richiamata in modo vincolante.
**Come:** si assembla a tavolino (nessun dato cliente). È il pezzo che rende la demo *ripetibile*: una volta scritto, ogni bozza ci passa dentro uguale.

### Fase 3 — Esecuzione riscrittura
**Cosa:** far girare la direttiva su una bozza vera. È il prodotto in azione. **Input:** direttiva (Fase 2) + bozza grezza (Fase 0). **Output:** documento riscritto + note forma + norme segnalate. **Come:** Sonnet 4.6 in condizioni reali, senza thinking (vedi Regola d'oro). È ciò che mostri a Lorenzo.

### Fase 4 — Validazione rubrica
**Cosa:** giudicare l'output e migliorare la direttiva. **Input:** output Fase 3. **Output:** voto sui 3 assi + 1-2 fix alla direttiva. **Come:** rubrica della spec (tono / tecnico / umano, ≥4/5) + verifica hard gate (0 citazioni inventate). Occhio severo, non auto-conferma.

---

## Consiglio sintetico

- **Modello:** Opus per costruire e criticare (fasi 1 e 4); **Sonnet 4.6 per il runtime** (fase 3) — è il modello che lo Studio userà davvero. Haiku **fuori dalla demo**: la fedeltà di stile è tutto il valore.
- **Effort/Think:** alto + thinking solo dove c'è *giudizio* (estrarre/validare la voce). Basso + no-think dove c'è *esecuzione* (la riscrittura) — lì vuoi vedere il comportamento "nudo" del prodotto.

---

## Riferimento — keyword di thinking nel prompt

Il livello di ragionamento si attiva scrivendo una keyword **dentro il prompt** (non è un'impostazione separata). Più profondo = più qualità su task di giudizio, ma più token/tempo.

| Keyword | Livello |
|---|---|
| `think` | Base |
| `think hard` | Più profondo |
| `think harder` | Ancora più profondo |
| `ultrathink` | Massimo |

Esempio d'uso: `ultrathink and refactor this function`

**Come è mappato sulle fasi (colonna Think):**
- **Fase 1** `ultrathink` — estrarre la voce dal corpus è il task di giudizio più delicato e si fa una volta sola: massimo ragionamento giustificato.
- **Fase 2** `think hard` — composizione della direttiva, profondità media.
- **Fase 3** `nessuno` — è il prodotto in azione: deve girare "nudo", in condizioni reali (vedi Regola d'oro).
- **Fase 4** `think harder` — la validazione critica richiede occhio severo, non auto-conferma.

---

## Rischio principale

**La Fase 0 è il collo di bottiglia.** Senza i ~10 file campione e il parere-tipo grezzo, tutte le fasi successive slittano. È l'unica cosa che serve *prima* di tutto.

---

## Checklist demo

- [x] Fase 0 — Raccolti ~10 file campione di stile
- [ ] Fase 0 — Scelto 1 parere-tipo grezzo da riscrivere
- [ ] Fase 1 — Creato `voce.md` (regole di stile portabili)
- [ ] Fase 2 — Costruita direttiva/skill Voce & Forma
- [ ] Fase 3 — Eseguita riscrittura su Sonnet 4.6 (condizioni reali)
- [ ] Fase 4 — Validato output con rubrica 3 assi + iterato

---

## Connessioni

- [[Validazione Idea]]
- [[Specifica Tecnica]]
- [[idea nel piatto]]
