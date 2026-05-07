# Imprenditoria MOC

Materia ITS — Business, imprenditoria, regolamentazione AI e modelli di revenue.
**Docente:** Lorenzo
**Fonte:** `ITS/Lorenzo - Imprenditoriale/`
**NotebookLM:** https://notebooklm.google.com/notebook/4c3b6ee3-040a-45e0-b333-f22124f56e4a

---

## EU AI Act — Regolamento UE 2024/1689

Primo quadro giuridico organico al mondo sull'intelligenza artificiale. Direttamente applicabile in tutti gli Stati membri.

**Principio antropocentrico:** l'IA è strumento di supporto — mai sostituisce la decisione umana.

### Framework di Classificazione del Rischio

| Livello | Esempi | Obblighi |
|---------|--------|----------|
| **Rischio Inaccettabile (Vietato)** | Social scoring, manipolazione subliminale, sfruttamento vulnerabilità minori/disabili, scraping facciale per database riconoscimento | Proibiti |
| **Alto Rischio** | Infrastrutture critiche (acqua, gas, elettricità), istruzione (valutazione esami), occupazione (selezione personale), servizi pubblici (borse, alloggi), amministrazione giustizia | Gestione rischio, governance dati, logging, supervisione umana, cybersecurity, trasparenza |
| **Rischio Limitato** | Chatbot, deepfake | Obbligo di trasparenza — utente deve sapere che interagisce con AI |
| **Rischio Minimo** | Filtri antispam, videogiochi basati su IA | Codici di condotta volontari |

### Timeline di Attuazione

| Data | Evento |
|------|--------|
| **2 Febbraio 2025** | Divieti assoluti in vigore (Art. 5) + obbligo AI literacy per personale fornitori/deployer |
| **2 Agosto 2025** | Norme GPAI (General Purpose AI, LLM grandi dimensioni) + Ufficio Europeo per l'IA operativo + sanzioni applicabili |
| **2 Agosto 2026** | Piena applicazione sistemi alto rischio (Allegato III) + FRIA obbligatoria per enti pubblici e operatori servizi essenziali |
| **2 Agosto 2027** | Completamento: sistemi IA integrati in prodotti regolamentati (dispositivi medici, automotive) + modelli GPAI già su mercato pre-agosto 2025 |

### DDL AI Italia (Legge 132/2025)

Integra l'AI Act con specificità per settori italiani. Approvata 23 settembre 2025.

**Obiettivi:**
- Rafforzare principio antropocentrico (IA a supporto dell'uomo)
- Promuovere innovazione nazionale con fondi > 1 miliardo €
- Introdurre norme penali specifiche (es. deepfake illeciti)

**Agenzia per l'Italia Digitale (AgID):**
- Promuove sviluppo IA nel settore pubblico
- Definisce linee guida operative per adozione/procurement PA
- Supporta amministrazioni in conformità tecnica e notifica organismi valutazione

**Agenzia per la Cybersicurezza Nazionale (ACN):**
- Autorità competente per vigilanza mercato e controllo conformità
- Poteri ispettivi e sanzionatori
- Vigila su resilienza digitale e rischi sistemi IA

### Obblighi per Sistemi ad Alto Rischio

**Requisiti Tecnici e Organizzativi:**

1. **Sistema di Gestione del Rischio** — Processo iterativo per identificare e mitigare rischi durante intero ciclo di vita
2. **Governance dei Dati** — Dataset pertinenti, rappresentativi, completi, privi di bias
3. **Documentazione Tecnica** — Redatta prima messa in servizio per dimostrare conformità
4. **Logging (Tracciabilità)** — Registrazione automatica eventi per controllo ex-post decisioni
5. **Trasparenza** — Istruzioni per uso chiare per interpretare correttamente output
6. **Supervisione Umana** — Design che permette a persone fisiche qualificate di intervenire/correggere/arrestare
7. **Robustezza e Cybersecurity** — Livelli adeguati di accuratezza e protezione da attacchi avversariali
8. **Registrazione** — Sistema registrato in banca dati UE

---

## FRIA (Fundamental Rights Impact Assessment)

**Articolo 27 AI Act** — Obbligatorio per deployer (utilizzatori professionali) di:
- Servizi pubblici
- Enti creditizi/assicurativi
- Prima messa in servizio sistemi ad alto rischio

### 5 Step Procedurali di Implementazione

| Step | Azione |
|------|--------|
| **1. Descrizione Processi** | Dettagliare processi interni dell'ente dove IA sarà utilizzata in linea con finalità prevista |
| **2. Ambito Temporale** | Definire periodo di tempo e frequenza di utilizzo del sistema |
| **3. Identificazione Categorie** | Specificare categorie di persone fisiche e gruppi potenzialmente influenzati dall'IA |
| **4. Valutazione Rischi** | Identificare rischi specifici sui diritti fondamentali delle categorie mappate (con istruzioni fornitore) |
| **5. Misure Mitigazione** | Descrivere: supervisione umana, governance interna, procedure reclamo in caso di rischi |

**Output:** Notificare risultati all'autorità di vigilanza (AgID/ACN Italia) con template Ufficio Europeo per l'IA.

---

## Template Checklist: Conformità Sistemi Alto Rischio

Verifica per aziende e PA:

- [ ] Sistema di Gestione del Rischio attivo e documentato
- [ ] Governance dei Dati: dataset pertinenti, rappresentativi, senza errori/bias
- [ ] Documentazione Tecnica redatta pre-deployment
- [ ] Logging automatico eventi per tracciabilità
- [ ] Trasparenza: istruzioni uso chiare fornite
- [ ] Supervisione Umana: personale qualificato può intervenire/arrestare
- [ ] Robustezza e Cybersecurity: accuratezza e protezione adeguate
- [ ] Registrazione: sistema in banca dati UE

---

## Obblighi Settoriali Specifici (Legge 132/2025)

### Sanità
- IA affianca medici in prevenzione/diagnosi
- Giudizio clinico rimane esclusiva del medico
- Agenas gestisce piattaforma nazionale dedicata

### Giustizia
- IA velocizza ricerca documentale e analizza precedenti
- Decisione finale e valutazione critica: competenza esclusiva del giudice
- IA come supporto, non sostituzione

### Risorse Umane (Lavoro)
- Selezione e valutazione: uso trasparente e sicuro obbligatorio
- Datore di lavoro: obbligo informare lavoratori su uso IA (evitare opacità algoritmica)
- Garanzie su non-discriminazione

### Sicurezza
- Esclusione attività difesa e intelligence
- Agenzia Cybersicurezza Nazionale (ACN): vigilanza su resilienza digitale
- Controllo su rischi sistemi IA utilizzati

---

## Dettagli Compliance e Scadenze

### Obblighi Già Operativi

**AI Literacy (Dal 2 Febbraio 2025):**
- Fornitori e deployer devono garantire personale abbia alfabetizzazione IA sufficiente

**GPAI — General Purpose AI (Entro Agosto 2025):**
- Fornitori LLM grandi dimensioni: obblighi trasparenza, copyright, documentazione tecnica
- Uso responsabile di modelli foundation

### Procedure Critiche

**Gap Analysis:**
- PA deve mappare non solo software evidenti
- Identificare anche funzionalità IA integrate in gestionali e piattaforme terze acquistate

**Incident Reporting:**
- Segnalazione incidenti gravi ad AgID/ACN entro **15 giorni** (2 giorni in casi critici)
- Procedure tempestive obbligatorie

### Proposte in Discussione

**Digital Omnibus (Novembre 2025):**
- Proposta Commissione UE di posticipare applicazione sistemi alto rischio di 16 mesi
- Non ancora approvata né recepita in Italia

---

## Information Literacy e Verifica delle Fonti

### Test CRAAP + Pensiero Critico

**CRAAP**: Attualità · Rilevanza · Autorità · Accuratezza · Scopo

Il test va integrato con **pensiero critico e metacognizione** per contrastare i bias personali — da solo non è sufficiente.

### Verifica UGC (User Generated Content) in Emergenze

4 pilastri per verificare contenuti generati da utenti:
1. **Provenienza** — da dove viene il contenuto?
2. **Fonte** — chi l'ha prodotto?
3. **Data** — quando?
4. **Luogo** — dove?

### Integrità dei Dati Scientifici

- Rischi della falsificazione dei dati nelle scienze della vita
- Misconduct scientifico compromette progresso e fiducia pubblica
- **Letteratura grigia vs Peer-reviewed**: dipendere da una sola fonte porta a decisioni parziali
- Modelli classici per validare il giudizio degli esperti quando mancano dati empirici

### Analisi dei Dati in Contesti Consulenziali

Guide per l'interpretazione efficace di grafici e tabelle a supporto di decisioni data-driven.

---

## Progetti Pratici

- [[AI ACT]] — Materiali AI ACT specifici

---

## Connessioni

- [[ITS MOC]]
- [[Knowledge MOC]] — Copywriting, pitch, modelli di revenue
- [[Idee MOC]] — Da idea a business regolamentato
- [[Progettistica AI MOC]] — Specifiche tecniche conformi all'AI Act
- [[Agenti IA Design Patterns MOC]] — Implementazione tecnica sistemi AI conformi compliance


---

## Materiali Aggiuntivi

- [[Ecosistema AI Moderno - Panoramica]] — Agentic Design Patterns (Gulli), GPT-5, MCP/A2A, impatto AI sul lavoro (NotebookLM)
- [[Metodo FAST MOC]] — framework bootstrap di Antonio Romano (Focus/Action/Shift/Traction + 6 casi studio italiani)
