# Template — Specifica Tecnica

**Progetto:** `[Nome del progetto]`
**Versione:** 1.0
**Data:** `YYYY-MM-DD`
**Autore:** `[Nome]`

> Questo documento è il riferimento vincolante per tutte le fasi di sviluppo. Ogni modifica deve essere versionata.

---

## 1. Obiettivi e Perimetro

### Obiettivo principale
*(Una frase che descrive esattamente cosa fa il sistema)*

### Il sistema FA:
- 
- 

### Il sistema NON FA:
- 
- 

### Utenti target
*(Chi usa il sistema, con quale livello tecnico, in quale contesto)*

### Valore prodotto (misurabile)
*(Es: riduce il tempo di X del Y%, genera Z output al giorno con qualità W)*

---

## 2. Input e Output

### Input accettati

| Campo | Tipo | Formato | Vincoli | Obbligatorio |
|---|---|---|---|---|
| `campo_1` | string | testo libero | max 500 chars | Sì |
| `campo_2` | integer | numero intero | 1-100 | No |

**Edge case da gestire:**
- Input vuoto: `[comportamento atteso]`
- Input malformato: `[comportamento atteso]`
- Input oltre i limiti: `[comportamento atteso]`

### Output attesi

| Campo | Tipo | Formato | Range accettabile |
|---|---|---|---|
| `output_1` | string | JSON | max 1000 chars |
| `output_2` | float | numero | 0.0 – 1.0 |

---

## 3. Requisiti di Qualità

| Metrica | Valore minimo accettabile | Metodo di misurazione |
|---|---|---|
| Accuratezza output | ≥ 85% | Valutazione su test set di 50 casi |
| Latenza media | ≤ 3 secondi | Misurata end-to-end |
| Latenza P95 | ≤ 8 secondi | Percentile 95 su 100 chiamate |
| Tasso di errore | ≤ 5% | Errori / totale chiamate |
| Costo per output | ≤ 0,05€ | Costo API per singola generazione |

---

## 4. Architettura del Sistema

### Componenti principali

```
[Input utente]
      ↓
[Componente A - es. preprocessor]
      ↓
[LLM API - es. Claude claude-sonnet-4-6]
      ↓
[Componente B - es. validatore output]
      ↓
[Output / Storage]
```

### Stack tecnologico

| Layer | Tecnologia scelta | Motivazione |
|---|---|---|
| Linguaggio | Python 3.12 | |
| Framework web | FastAPI | |
| LLM | Claude claude-sonnet-4-6 | |
| Database | SQLite | |
| Deploy | Locale / Render | |

### Dipendenze esterne

| Servizio | Utilizzo | Alternativa se down |
|---|---|---|
| `[API esterna]` | `[scopo]` | `[fallback]` |

---

## 5. Rischi e Assunzioni

### Assunzioni esplicite
1. `[Assunzione 1]`
2. `[Assunzione 2]`

### Rischi identificati

| Rischio | Probabilità | Impatto | Strategia di mitigazione |
|---|---|---|---|
| Allucinazioni del modello | Media | Alto | Validatore automatico sull'output |
| Cambio pricing API | Bassa | Medio | Monitoraggio costi + soglia di alert |
| Deprecazione modello | Bassa | Alto | Architettura model-agnostic |
| Rate limiting API | Media | Medio | Retry con backoff esponenziale |
| Dati non disponibili | `[P]` | `[I]` | `[strategia]` |

### Vincoli normativi
- GDPR: `[dati personali trattati? Come?]`
- AI Act: `[categoria di rischio del sistema]`

---

## Changelog

| Versione | Data | Modifica |
|---|---|---|
| 1.0 | `YYYY-MM-DD` | Prima versione |

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Lezione 2 - Specifica Tecnica e Prompt Engineering]]
