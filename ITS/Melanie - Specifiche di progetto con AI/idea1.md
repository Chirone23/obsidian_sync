# Idea 1 — Product Intelligence Analyzer

**Corso:** AI Projects Development · [[Progettistica AI MOC]]
**Stato:** Concept validato
**Tag:** #idea #melanie #ai-project

---

## Descrizione sintetica

Sistema AI che analizza recensioni e feedback testuali di un prodotto/servizio ed estrae segnali strutturati e actionable per il team di prodotto. Non è un semplice sentiment analyzer: filtra il rumore generico ed estrae problemi specifici, categorizzandoli per tipo e frequenza.

---

## Il problema che risolve

Le aziende ricevono centinaia di recensioni ma non hanno tempo di leggerle tutte. Il risultato è che bug ricorrenti, richieste di feature e segnali di churn vengono ignorati o scoperti tardi. Questo sistema trasforma testo non strutturato in dati d'azione.

---

## Input / Output

**Input:** testo libero (recensioni, commenti, ticket di supporto)

**Output strutturato:**

```json
{
  "bugs": [
    {"issue": "lag", "context": "Samsung S23", "frequency": 12}
  ],
  "missing_features": [
    {"feature": "dark mode", "frequency": 8}
  ],
  "unmet_expectations": [
    {"expected": "funzione X", "got": "funzione Y", "frequency": 5}
  ],
  "competitor_mentions": [
    {"competitor": "AppX", "reason": "migliore performance", "frequency": 3}
  ],
  "unintended_use_cases": [
    {"use_case": "usato per gestire team remoti", "frequency": 4}
  ],
  "ux_friction": [
    {"issue": "difficile trovare impostazioni notifiche", "frequency": 6}
  ],
  "price_perception": [
    {"sentiment": "negative", "note": "troppo caro per quello che offre", "frequency": 9}
  ],
  "churn_signals": [
    {"signal": "valutando il passaggio a competitor Y", "frequency": 2, "priority": "urgent"}
  ]
}
```

**Filtro esplicito:** feedback generici senza contesto ("non mi piace", "pessimo") vengono scartati. Viene estratto solo ciò che contiene un contesto specifico + un problema concreto.

---

## Le 8 categorie di segnale

| Categoria | Esempio | Valore per l'azienda |
|---|---|---|
| **Bug / problema tecnico** | "lagga su Samsung S23" | Fix tecnico specifico |
| **Feature mancante** | "manca il dark mode" | Backlog prodotto |
| **Aspettativa delusa** | "pensavo avesse X, invece ha Y" | Gap comunicazione/design |
| **Menzione competitor** | "preferisco AppX perché fa Y meglio" | Intelligence competitiva |
| **Use case non previsto** | "lo uso per fare X" | Nuovo mercato/segmento |
| **Friction UX/onboarding** | "un'ora per trovare le impostazioni" | Fix UI/documentazione |
| **Percezione del prezzo** | "troppo caro per quello che offre" | Pricing strategy |
| **Segnale di churn** | "sto passando a Y" | Retention urgente |

---

## Stack tecnologico

| Layer | Tecnologia | Motivazione |
|---|---|---|
| Linguaggio | Python 3.12 | Standard corso |
| Framework web | FastAPI | Standard corso |
| LLM | Claude claude-sonnet-4-6 (Anthropic) | Ottimo per structured output |
| Database | SQLite | Semplicità, no infrastruttura |
| Deploy | Locale / Render | MVP in un mese |

---

## Complessità stimata

**Media.** Il nodo critico è il prompt engineering: il modello deve distinguere in modo affidabile le 8 categorie e scartare il rumore. Richiede un prompt ben strutturato e un validatore sull'output JSON.

Fasi principali:
1. Prompt design + test su recensioni reali
2. Validatore output (schema JSON obbligatorio)
3. Aggregazione per frequenza
4. API FastAPI con endpoint `/analyze`
5. Report finale (JSON o HTML semplice)

---

## Conformità normativa

| Aspetto | Stato |
|---|---|
| AI Act — categoria | Rischio Limitato (testo scritto, no biometria) |
| AI Act — uso HR/educativo | Fuori perimetro (solo feedback su prodotti) |
| GDPR — dati personali nel testo | Anonimizzare prima dell'invio all'API |
| GDPR — profiling individuale | Output aggregato, non per-persona |
| DPA con Anthropic | Verificare ToS se si processano dati personali |

**Obbligo AI Act applicabile:** trasparenza — l'utente deve sapere che il sistema usa AI.

---

## Valore misurabile (per la specifica tecnica)

> "Il sistema estrae almeno l'85% delle segnalazioni specifiche su 50 recensioni reali, riducendo il tempo di categorizzazione manuale da 2 ore a 5 minuti."

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Lezione 1 - Case Study e Setup]]
- [[Template - Specifica Tecnica]]
