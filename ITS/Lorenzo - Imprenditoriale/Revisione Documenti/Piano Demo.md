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

| Fase                             | Cosa                                                                                 | Modello        | Effort | Think  | Tempo     |
| -------------------------------- | ------------------------------------------------------------------------------------ | -------------- | ------ | ------ | --------- |
| **0. Raccolta**                  | ~10 file campione stile + 1 parere-tipo grezzo da riscrivere                         | — (umano)      | —      | —      | 30-60 min |
| **1. Estrazione `voce.md`**      | Analisi dei 10 file → regole di stile (tono, lessico, struttura) in 1 file portabile | **Opus 4.8**   | high   | `ultrathink`   | 30-45 min |
| **2. Prompt/Skill Voce & Forma** | Direttiva: `voce.md` + task (riscrittura+forma+segnalazione) + hard rule citazioni   | **Sonnet 4.6** | medium | `think hard`   | ~20 min   |
| **3. Esecuzione riscrittura**    | Bozza grezza → output completo. Il prodotto in azione                                | **Sonnet 4.6** | low    | nessuno        | 5 min/run |
| **4. Validazione rubrica**       | Voto 3 assi su output + 1-2 iterazioni di fix prompt                                 | **Opus 4.8**   | medium | `think harder` | ~30 min   |

**Totale realistico: ~2,5-3,5 ore** di lavoro effettivo (escluso il tempo per procurare i file in Fase 0).

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
