# PROMPT_LOG — SpecterAI

**Progetto:** SpecterAI (AI Contract Analyzer per Non-Avvocati in Italiano)  
**Data inizio:** 2026-05-04  
**Versione prompt attuale:** v1  

---

## Tabella Iterazioni

| Versione | Data | Stato | Descrizione |
|---|---|---|---|
| v1 | 2026-05-04 | Operativo | Prompt iniziale basato su C.I.A.R.E., temperature=0, few-shot examples (2 casi) |

---

## v1 — 2026-05-04 — Iterazione Iniziale

**Problema:** Definire il prompt di sistema che guida Claude nell'analisi contrattuale strutturata per utenti non-legali.

**Soluzione:** Struttura C.I.A.R.E.:
- **Contesto:** L'utente è un imprenditore/manager italiano senza formazione legale
- **Intento:** Analizzare contratti e evidenziare 7 categorie di risk (payment_terms, auto_renewal, penalties, liability_limitation, termination, governing_law, intellectual_property)
- **Audience:** Non-avvocati; linguaggio plain-language, no legal jargon
- **Regole:** 
  - Output JSON strutturato (present, severity, explanation, questions_to_ask)
  - Temperature=0 (deterministico)
  - Max tokens=2048
  - Esclusione: no legal advice, no recommendations, decision-support only
- **Esempi:** 2 few-shot examples (clausola rischiosa + clausola neutrale)

**Implementazione:**

```
Sei un assistente legale IA specializzato in contract risk analysis per imprenditori italiani senza formazione legale.
Analizza il contratto fornito e restituisci un JSON con 7 categorie di rischio...
```

**Parametri Claude:**
- Model: claude-3-5-sonnet-20241022
- Temperature: 0
- Max tokens: 2048

**Few-shot Examples:**
1. Clausola auto_renewal presente (rischiosa) — Spiega perché, pone domande
2. Clausola auto_renewal assente (neutra) — Conferma assenza, pone domande per validazione

**Risultati:** 
- ✅ Output JSON ben strutturato come atteso
- ✅ Linguaggio plain-language verificato
- ⏳ Full testing con dataset eterogeneo: pending durante building
- ⏳ Performance optimization: pending

---

## Prossime Iterazioni (Template)

### v2 — [Data] — [Problema] → [Soluzione]

**Problema:** [Descrizione del problema riscontrato durante testing]

**Soluzione:** [Modifiche al prompt, parametri, few-shot examples]

**Risultati:** [Outcome della modifica]

---

## Note Tecniche

- **Linguistic choice:** Italiano semplice, niente gergo legale
- **Determinism:** Temperature=0 per output coerente e riproducibile
- **Structure:** JSON validation built-in, parsing facile lato client
- **Context window:** 2048 token output = abbastanza per analisi completa + spiegazioni
