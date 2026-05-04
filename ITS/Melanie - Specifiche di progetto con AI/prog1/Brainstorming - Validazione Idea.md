# Brainstorming — Validazione Idea Progetto

**Corso:** AI Projects Development · [[Progettistica AI MOC]]
**Data:** 2026-05-04
**Scadenza consegna:** 2026-05-05

---

## Processo di Brainstorming

### Metodologia usata
Analisi critica con AI (Claude) sulle 5 dimensioni di validazione del corso + ricerca di mercato con Perplexity per verificare originalità, maturità del mercato e angolo difendibile.

### Vincoli applicati
- Realizzabile in ~1 mese
- Costi quasi a zero
- Risolvere un problema reale
- Idea originale (non derivata da altri corsi o progetti già avviati)
- Stack: Python + LLM API

---

## Idee Generate (7 totale)

| # | Idea | Tipo |
|---|---|---|
| 1 | AI Onboarding Assistant per PMI | B2B |
| 2 | AI Analizzatore di Contratti/Preventivi | B2B |
| 3 | AI Report Generator da dati grezzi | B2B |
| 4 | AI Lead Qualifier | B2B Sales |
| 5 | AI Competitive Intelligence Monitor | B2B / Agente |
| 6 | AI Preparatore Colloqui | B2C |
| 7 | AI Study Assistant per studenti | B2C |

---

## Idee Scartate

### Idea 3 — AI Report Generator da dati grezzi
**Descrizione:** Upload CSV/Excel → report narrativo con insight chiave generato automaticamente.
**Motivo scarto:** Mercato saturo (Power BI, Tableau, strumenti BI con AI integrata). Il problema è reale ma la soluzione è già commoditizzata. Difficile differenziarsi senza un verticale molto specifico. Scartata per mancanza di angolo originale difendibile.

### Idea 6 — AI Preparatore Colloqui
**Descrizione:** Dato un annuncio + CV, genera domande probabili, simula il colloquio e dà feedback.
**Motivo scarto:** Mercato B2C growing ma saturo di tool consumer (Big Interview, InterviewSim.ai, SmallTalk2Me). Differenziazione difficile senza un verticale stretto. Frequenza d'uso bassa (un colloquio ogni X mesi). Scartata per saturazione del segmento consumer.

### Idea 7 — AI Study Assistant per studenti
**Descrizione:** Upload dispense → flashcard, quiz, riassunto per capitolo.
**Motivo scarto:** Mercato saturo (Anki AI, NotebookLM, Quizlet AI). Problema reale ma soluzione già ampiamente coperta da tool gratuiti di qualità. Scartata per mancanza di spazio competitivo.

### Idea 4 — AI Lead Qualifier (scartata come prima scelta)
**Descrizione:** Data una lista di aziende, l'agente ricerca info pubbliche e produce schede lead con priority score.
**Motivo scarto come prima scelta:** Rischio tecnico troppo alto per un MVP da 1 mese. Scraping LinkedIn è fragile (rate limit, CAPTCHA, blocchi). Le API di enrichment affidabili hanno costi. Senza dati affidabili, lo scoring è arbitrario. Mantenuta come idea di riserva.

### Idea 5 — AI Competitive Intelligence Monitor (scartata come prima scelta)
**Descrizione:** Monitora competitor ogni settimana e produce digest delle novità rilevanti.
**Motivo scarto come prima scelta:** Change-detection noise difficile da gestire (non ogni cambio HTML è rilevante). Crawl reliability problematica su più domini in parallelo. Interessante ma rischio tecnico medio-alto per 1 mese. Mantenuta come idea di riserva.

### Idea 1 — AI Onboarding Assistant per PMI (scartata come prima scelta)
**Descrizione:** Ingerisce documenti aziendali e risponde alle domande dei nuovi dipendenti via chat (RAG).
**Motivo scarto come prima scelta:** Mercato early-stage e tecnicamente fattibile, ma meno interessante da costruire. Il problema reale c'è, ma l'angolo difendibile è meno netto rispetto al Contract Analyzer. Mantenuta come seconda opzione.

---

## Idea Scelta — AI Contract Analyzer for Non-Lawyers

### Perché questa idea
Tra tutte le idee generate, è quella con il miglior equilibrio tra:
- **Problema reale e non risolto** nel segmento SMB/freelance (i tool esistenti sono tutti enterprise/legal-heavy)
- **Angolo originale e difendibile** — nessun competitor occupa oggi il segmento non-lawyer italiano
- **Fattibilità tecnica in 1 mese** — stack chiaro, PDF digitali, output strutturato
- **Rischi gestibili** — AI Act gestibile con posizionamento corretto, costi inference bassi

### Motivazione sintetica
I freelance e le PMI italiane firmano contratti senza capirne le implicazioni. Gli strumenti esistenti (Spellbook, Harvey AI, Docusign IAM) sono pensati per avvocati: output in linguaggio giuridico, prezzi enterprise, workflow complessi. Il segmento non-lawyer è libero. Il sistema non dà "consulenza legale" ma evidenzia i punti critici in linguaggio plain — posizionamento che rispetta l'AI Act e risolve il problema reale.

---

## Analisi 5 Dimensioni

| Dimensione | Esito | Dettaglio |
|---|---|---|
| **Tecnica** | ✅ | Problema ben definito, LLM superiore alle alternative, stack chiaro (PyMuPDF + Claude API + layer deterministico) |
| **Economica** | ✅ | Costi inference pochi centesimi/contratto, freemium o pay-per-use, 2,7M freelance target in Italia |
| **Complessità** | ✅ | MVP fattibile in 1 mese con perimetro chiuso (solo PDF digitali, 5-7 categorie red flag) |
| **Rischio e Compliance** | ⚠️ | AI Act HIGH-RISK gestibile: posizionamento decision-support + disclaimer + human-in-the-loop |
| **Sostenibilità tecnologica** | ✅ | Provider-agnostic, PyMuPDF open source, logica separabile dall'LLM |

→ Dettaglio completo: [[Contract Analyzer - Validazione Idea]]

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Contract Analyzer - Validazione Idea]]
- [[Lezione 2 - Specifica Tecnica e Prompt Engineering]]
- [[Template - Specifica Tecnica]]
