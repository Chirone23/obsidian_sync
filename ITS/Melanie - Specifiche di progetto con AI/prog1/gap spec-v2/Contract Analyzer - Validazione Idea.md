# Contract Analyzer — Validazione Idea

**Corso:** AI Projects Development · [[Progettistica AI MOC]]
**Cartella:** `ITS/Melanie - Specifiche di progetto con AI/prog1/`
**Data validazione:** 2026-05-04

---

## L'Idea

**Nome provvisorio:** AI Contract Analyzer for Non-Lawyers
**Problema:** Freelance e PMI ricevono contratti che non capiscono appieno e firmano senza sapere cosa rischiano. I tool esistenti sono pensati per avvocati — output troppo tecnico, linguaggio giuridico, overkill per chi non ha un legal team.
**Soluzione:** Upload PDF contratto → estrazione automatica di clausole critiche → output in linguaggio plain con lista "cose da verificare prima di firmare".
**Target:** Freelance, autonomi, piccoli imprenditori italiani (stima: 2,7M soggetti)
**Angolo difendibile:** *Decision-support*, non legal advice. Nessun tool dedicato occupa questo segmento oggi.

---

## Ricerca di Mercato (Perplexity)

### Competitor esistenti
- **Docusign IAM**, **Spellbook**, **Legora**, **Harvey AI** — tutti enterprise/legal team, non SMB
- **CompareX** — ha un free analyzer ma è demo enterprise, non dedicato a freelance
- Nessun tool dedicato al segmento non-lawyer SMB italiano

### Maturità del mercato
- **Saturo** nel segmento enterprise/legal
- **Early-stage / vuoto** nel segmento non-lawyer SMB

### Failure mode dei competitor
- Output troppo legale e verboso per chi non è avvocato
- Assumono contratti strutturati da grandi aziende — i contratti SMB sono spesso disordinati
- Falsa confidence: risk score senza uncertainty label

---

## Controvalutazione (Perplexity stress-test)

| Obiezione | Dato trovato | Verdetto |
|---|---|---|
| "Usano già ChatGPT gratis" | Nessun dato su non-lawyer che usano ChatGPT per contratti. Il 58% dei *lawyer* sì, non i freelance | Falso problema |
| Frequenza d'uso bassa | Nessun dato su contratti/anno per freelance italiani — assunzione necessaria | Rischio gestibile (dichiarare assunzione) |
| AI Act HIGH-RISK | Confermato: tool di analisi legale = alto rischio EU AI Act | Rischio reale, gestibile con posizionamento |
| WTP italiano basso | Nessun dato specifico. PMI italiane lente nell'adozione SaaS | Irrilevante per MVP da corso |
| Concorrenza free | Nessun tool free dedicato SMB esiste oggi | Falso problema |
| Mercato troppo piccolo | 2,7M freelance/autonomi in Italia (Statista 2019) | Verde |

### Assunzione da dichiarare nella specifica
> Il freelance medio gestisce 10-20 contratti/anno (clienti, fornitori, subappalti). Nessun dato pubblico disponibile — assunzione conservativa da verificare con user research.

---

## Validazione 5 Dimensioni (framework corso)

| Dimensione                    | Esito | Note                                                                                               |
| ----------------------------- | ----- | -------------------------------------------------------------------------------------------------- |
| **Tecnica**                   | ✅     | Problema ben definito, stack chiaro (PyMuPDF + Claude API), layer deterministico per date/importi  |
| **Economica**                 | ✅     | Costi inference bassissimi (pochi centesimi/contratto), freemium o pay-per-use                     |
| **Complessità**               | ✅     | MVP fattibile in 1 mese SE perimetro chiuso (solo PDF digitali, 5-7 categorie red flag, no OCR)    |
| **Rischio e Compliance**      | ⚠️    | AI Act HIGH-RISK gestibile: posizionamento come decision-support + disclaimer + human-in-the-loop  |
| **Sostenibilità tecnologica** | ✅     | Provider-agnostic: logica separabile dall'LLM, PyMuPDF open source, switch su GPT-4o se necessario |

**Esito complessivo: idea validata** — un rischio gestibile, un'assunzione da dichiarare.

---

## Perimetro MVP (fuori scope)

- ❌ Scansioni PDF / OCR
- ❌ Confronto tra versioni del contratto
- ❌ Multilingua (solo italiano per ora)
- ❌ Dashboard / storico analisi
- ❌ Integrazione CRM o firma digitale

**In scope:**
- ✅ Upload PDF digitale
- ✅ Estrazione 5-7 categorie di red flag (scadenze, penali, rinnovo automatico, limitazione responsabilità, foro competente, termini di pagamento, disdetta)
- ✅ Output plain language + lista "domande da fare prima di firmare"
- ✅ Disclaimer AI Act visibile nell'UI

---

## Rischio AI Act — Gestione

**Problema:** tool di analisi legale = sistema alto rischio EU AI Act
**Soluzione:** posizionamento corretto fin dalla specifica tecnica
- Non dire mai "questo contratto è ok/non ok"
- Dire sempre "questi sono i punti da verificare con un professionista"
- Disclaimer esplicito in ogni output
- Human-in-the-loop visibile (l'utente decide, non il sistema)

**Riferimento:** nessun tool esistente è stato sanzionato in EU per questo — tutti usano questo posizionamento con successo.

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Lezione 1 - Case Study e Setup]]
- [[Lezione 2 - Specifica Tecnica e Prompt Engineering]]
- [[Template - Specifica Tecnica]]
