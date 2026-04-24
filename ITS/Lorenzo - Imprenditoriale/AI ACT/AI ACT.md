# EU AI Act (Regolamento UE 2024/1689) — MOC Approfondito

**Data estrazione:** 2026-04-24  
**Fonte:** NotebookLM notebook "EU AI Act e Compliance PA" — 29 fonti integrate  
**Versione documento:** 1.0

---

## 📋 Indice Contenuti

1. [Panoramica Strategica](#panoramica-strategica)
2. [Quadro Normativo UE](#quadro-normativo-ue)
3. [Implementazione Italiana](#implementazione-italiana)
4. [Governance e Compliance](#governance-e-compliance)
5. [Framework Operativi](#framework-operativi)
6. [Integrità Scientifica](#integrità-scientifica)
7. [Information Literacy](#information-literacy)
8. [Definizioni Tecniche](#definizioni-tecniche)
9. [Dati Numerici Critici](#dati-numerici-critici)

---

## Panoramica Strategica

L'**EU AI Act** è il **primo regolamento organico al mondo** sull'intelligenza artificiale, adottato dall'UE nel 2024 e basato su un **approccio al rischio proporzionato**.

**Obiettivo principale:** Proteggere i diritti fondamentali garantendo che l'IA sia sicura e antropocentrica.

**Caratteristiche distintive:**
- Approccio basato sul rischio (4 livelli: inaccettabile, alto, limitato, minimo)
- Applicazione scaglionata (2024-2027)
- Obblighi proporzionati al livello di rischio
- Sanzioni significative (fino a €35M o 7% del fatturato globale)

---

## Quadro Normativo UE

### Definizione di IA (EU AI Act)

**Sistema basato su macchine** con capacità di:
- Inferenza (apprendimento, ragionamento, modellizzazione)
- Generare output che **influenzano ambienti reali o virtuali**
- Distinguersi dal software deterministico tradizionale

### Classificazione del Rischio — 4 Livelli

#### 1. Rischio INACCETTABILE — Sistemi Vietati

| Elemento | Dettaglio |
|----------|-----------|
| **Divieto** | Assoluto e immediato |
| **Conseguenze** | Nullità atti, confisca, sanzioni massime |
| **Sanzione** | Fino a €35 milioni o 7% del fatturato globale |
| **Esempi** | Social scoring, manipolazione subliminale, identificazione biometrica remota non mirata, sfruttamento vulnerabilità (minori/disabili), riconoscimento facciale tramite scraping non autorizzato |
| **Entrata vigore** | 2 Febbraio 2025 |

**Cosa è vietato in dettaglio:**
- Social scoring: assegnare score sociale ai cittadini basato su IA
- Manipolazione subliminale: influenzare comportamenti senza consapevolezza
- Sfruttamento vulnerabilità: colpire deliberatamente minori, persone disabili, anziani
- Identificazione biometrica remota non autorizzata: riconoscimento facciale su scala massiccia senza consenso

---

#### 2. Rischio ALTO — Sistemi Regolamentati

| Elemento | Dettaglio |
|----------|-----------|
| **Settori** | Infrastrutture critiche, istruzione, lavoro, giustizia, migrazione, servizi pubblici essenziali |
| **Obblighi** | Conformità ex ante (prima della messa in servizio) |
| **Entrata vigore** | 2 Agosto 2026 (piena applicazione) |
| **Supervisione** | Obbligatoria supervisione umana |

**Requisiti di Conformità (Ex Ante):**
- Gestione del rischio strutturata
- Governance dei dati (qualità, rappresentatività, assenza bias)
- Documentazione tecnica completa
- Logging automatico (tracciamento operazioni)
- Supervisione umana effettiva (con autorità di arresto/correzione)
- Cybersecurity robusta
- Registrazione nella banca dati UE

**Settori Specifici e Obblighi:**

| Settore | Obblighi Specifici |
|---------|-------------------|
| **Infrastrutture Critiche** | Protezione da attacchi, failsafe mechanism, monitoraggio continuo |
| **Istruzione** | Trasparenza nel supporto didattico, no sostituimento insegnante |
| **Lavoro** | Informare lavoratori di utilizzo IA, trasparenza algoritmica |
| **Giustizia** | IA come supporto ricerca documentale, decisione finale al giudice |
| **Migrazione** | Conformità diritti umani, non discriminazione |
| **Servizi Pubblici** | FRIA obbligatoria, DPIA, coinvolgimento cittadini |

---

#### 3. Rischio LIMITATO — Trasparenza

| Elemento | Dettaglio |
|----------|-----------|
| **Sistemi** | Chatbot, assistenti virtuali, deepfake, IA generativa |
| **Obbligo** | Informare gli utenti dell'interazione con IA |
| **Etichettatura** | Contenuti sintetici devono essere marcati come tali |
| **Entrata vigore** | 2 Febbraio 2025 |

---

#### 4. Rischio MINIMO — Libertà

| Elemento | Dettaglio |
|----------|-----------|
| **Sistemi** | Filtri antispam, videogiochi basati su IA |
| **Obblighi** | Nessuno obbligatorio |
| **Opzione** | Adesione volontaria a codici di condotta |

---

### Timeline di Attuazione (2024-2027)

| Data | Traguardo | Dettagli |
|------|-----------|----------|
| **1 Agosto 2024** | Entrata in vigore | Inizio periodo transitorio |
| **2 Febbraio 2025** | Divieti + Literacy | Divieti rischio inaccettabile; obbligo AI Literacy; divieto per sistemi vietati |
| **2 Agosto 2025** | Modelli GPAI | Entrano in vigore norme per IA per finalità generali (LLM) |
| **2 Agosto 2026** | Piena Applicazione | Scadenza per sistemi ad alto rischio; operatività autorità nazionali |
| **2 Agosto 2027** | Prodotti Regolamentati | Conformità per IA integrata in prodotti già soggetti a norme UE (es. dispositivi medici) |

---

### Modelli GPAI (General Purpose AI)

**Definizione:** Modelli fondazionali (es. Large Language Model) con capacità generiche di generare vari output.

**Obblighi Rafforzati:**
- Documentazione tecnica su training data
- Valutazione di capacità e rischi
- Cybersecurity per quelli a rischio sistemico
- Conformità a norme di trasparenza

**Soglia Rischio Sistemico:** Modelli addestrati con **cumulative compute > 10²⁵ FLOPs**
- Nota: anche modelli con > 10²³ FLOPs capaci di generare linguaggio richiedono monitoraggio

---

## Implementazione Italiana

### DDL AI — Legge 132/2025

L'Italia integra l'EU AI Act con normativa nazionale che pone **l'essere umano al centro** (principio antropocentrico).

### Autorità Designate

#### AgID — Agenzia per l'Italia Digitale

**Ruolo:** Autorità per l'Innovazione  
**Responsabilità:**
- Promozione dell'innovazione IA nella PA
- Definizione linee guida per procurement pubblico
- Supporto sviluppo IA nella Pubblica Amministrazione
- Formazione e advisory per enti pubblici

#### ACN — Agenzia per la Cybersicurezza Nazionale

**Ruolo:** Autorità per Vigilanza e Sicurezza  
**Responsabilità:**
- Vigilanza sulla cybersecurity dei sistemi IA
- Monitoraggio della resilienza digitale del Paese
- Poteri ispettivi e sanzionatori
- Coordinamento con autorità europee

---

### Nuovi Reati e Sanzioni Penali

#### Deepfake Illeciti
- **Sanzione:** Reclusione da **1 a 5 anni**
- **Applicazione:** Diffusione di contenuti falsificati (video, audio) a danno di terzi

#### Aggravanti per Reati tramite IA
- Qualsiasi reato commesso con ausilio IA riceve aggravante di pena
- Raddoppio della pena in caso di violazione grave

---

### Applicazioni Settoriali in Italia

#### 1. Sanità

**Principio:** IA come **supporto** diagnostico e preventivo, **mai sostitutiva** del medico

**Implementazioni:**
- Piattaforma nazionale **Agenas** per coordinamento
- Algoritmi diagnostici: validazione medica obbligatoria
- Responsabilità: medico rimane soggetto decisionale
- Vincoli: protezione dati paziente + consenso informato

#### 2. Giustizia

**Principio:** IA per **ricerca documentale** e **analisi precedenti**, decisione finale al giudice

**Ambiti:**
- Ricerca case law automatizzata
- Analisi precedenti giurisprudenziali
- Supporto drafting atti processuali
- No sostituimento magistrato in decisioni

---

#### 3. Lavoro

**Obbligo:** Informare i lavoratori su utilizzo sistemi IA (trasparenza algoritmica)

**Scopi:**
- Selezione candidati (recruiting)
- Valutazione performance
- Scheduling turni
- Monitoraggio produttività

**Vincoli:** Diritto lavoratore di comprendere come IA lo valuta

---

### Investimenti Pubblici

**Stanziamento:** Circa **€1 miliardo** per 2025-2027

**Destinazione:**
- Startup e PMI nel settore IA
- Ricerca su calcolo quantistico
- Infrastrutture per IA pubblica
- Formazione e upskilling

---

## Governance e Compliance

### FRIA — Fundamental Rights Impact Assessment

**Obbligatorietà:** Richiesta per PA e deployer di servizi essenziali (sistemi ad alto rischio)

**Procedure FRIA — 5 Step:**

| Step | Descrizione | Output |
|------|-------------|--------|
| **1. Descrizione Processi** | Dettagliare come il sistema IA sarà integrato nei flussi di lavoro dell'ente | Documento processi |
| **2. Ambito Temporale** | Definire periodo e frequenza di utilizzo prevista | Timeline utilizzo |
| **3. Categorie Interessate** | Elencare persone fisiche e gruppi influenzati dall'output IA | Lista stakeholder |
| **4. Valutazione Rischi** | Individuare potenziali danni ai diritti fondamentali (dignità, non discriminazione, privacy) | Risk matrix |
| **5. Misure Mitigazione** | Descrivere supervisione umana effettiva e meccanismi di ricorso per cittadini | Mitigazione plan |

**Output finale:** Relazione FRIA con analisi impatto e misure di protezione

---

### DPIA — Data Protection Impact Assessment

**Norma:** GDPR Articolo 35

**Relazione con FRIA:** Complementare; spesso eseguite insieme

**Focus:** Protezione dati personali (GDPR) vs. diritti fondamentali (FRIA)

---

### Checklist di Conformità — Sistemi Alto Rischio

**Provider e Deployer devono verificare:**

- [ ] **Governance Dati**
  - Dataset rappresentativi
  - Assenza bias sistematici
  - Documentazione qualità dato
  - Verifica distorsioni

- [ ] **Documentazione Tecnica (Allegato IV)**
  - Descrizione generale (finalità, varianti, interazioni)
  - Descrizione elementi (metodi sviluppo, algoritmi, requisiti dati)
  - Procedure monitoraggio (accuratezza, supervisione)
  - Sistema gestione rischi strutturato

- [ ] **Logging Automatico**
  - Capacità tracciamento operazioni
  - Conservazione log ≥ **6 mesi**
  - Audit trail immutabile
  - Accesso autorizzato a log

- [ ] **Supervisione Umana**
  - Personale con **autorità di arresto/correzione**
  - Training per operatori
  - "Pulsante di stop" tecnico sempre disponibile
  - Procedures per override sistema

- [ ] **Cybersecurity**
  - Robustezza contro attacchi avversariali
  - Protezione da injection attacks
  - Isolamento risorse critiche
  - Test di sicurezza regolari

- [ ] **Registrazione Banca Dati UE**
  - Inserimento provider
  - Registrazione deployer pubblici (alto rischio)
  - Aggiornamento metadata periodico

---

### Dichiarazione di Conformità UE (Allegato V)

**Elementi richiesti:**

1. Nome e tipo del sistema IA
2. Indirizzo del fornitore
3. Attestazione di responsabilità esclusiva del fornitore
4. Dichiarazione di conformità al Regolamento
5. Conformità al GDPR (se applicabile)
6. Riferimento a norme armonizzate applicate
7. Dettagli organismo notificato (se rilevante)
8. Luogo, data, firma e timbro

---

## Framework Operativi

### GRADE — Valutazione Qualità Evidenze

**Uso:** Validazione evidenze scientifiche e revisioni

**Classificazione Qualità:**

| Livello | Simbolo | Significato | Declassare se |
|---------|---------|-------------|---------------|
| **ALTA** | ⊕⊕⊕⊕ | Forte fiducia nella stima | — |
| **MODERATA** | ⊕⊕⊕ | Fiducia moderata | Bias, inconsistenza |
| **BASSA** | ⊕⊕ | Fiducia limitata | Variabilità risultati |
| **MOLTO BASSA** | ⊕ | Fiducia molto limitata | Piccoli studi, bias alto |

---

### CRAAP Test — Valutazione Fonti

**Acronimo:** Currency, Relevance, Authority, Accuracy, Purpose

| Criterio | Domande | Applicazione |
|----------|---------|--------------|
| **C — Currency** | È attuale? Data aggiornamento? Pertinente oggi? | Tecnologie, normative, statistiche |
| **R — Relevance** | È rilevante al mio argomento? Livello dettaglio appropriato? | Audience, scopo, contesto |
| **A — Authority** | Chi è l'autore? Quali credenziali? Editore affidabile? | Esperienza, affiliazioni, pubblicazioni |
| **Ac — Accuracy** | È verifiable? Cited references? Errori evidenti? | Fact-check, peer review, consensus |
| **P — Purpose** | Perché è pubblicato? Intento commerciale/politico? | Bias dichiarato/nascosto |

---

### Modello Classico (Cooke)

**Uso:** Validare giudizio esperti quando dati empirici diretti mancano

**Metodologia:**
1. Selezionare panel di esperti (5-20)
2. Formulare domande target (valori incogniti a rischio)
3. Formulare domande di calibrazione (valori noti per pesare expertise)
4. Calcolo pesi statistici basato su performance calibrazione
5. Combinazione aggregata di giudizi

---

## Integrità Scientifica

### Crisi Globale nelle Ritrattazioni

**Trend:** Aumento preoccupante in Life Sciences (ultimi 20 anni)

**Cause Principali:**
- Falsificazione dati (9%)
- Manipolazione immagini (7%)
- Plagio (16%)
- Errori onesti (14%)
- Altre (54%)

**Fattori Sistemici:**
- Pressioni per finanziamento ("publish or perish")
- Paper Mills (fabbriche di articoli falsati)
- Incentivi disallineati (numero pubblicazioni > qualità)
- Peer review insufficiente

---

### Letteratura Grigia vs Peer-Reviewed

**Studio Case:** Ricerca sui canidi

**Scoperta:** Dipendenza esclusiva da riviste indicizzate ometteva dati gestionali critici presenti in:
- Rapporti tecnici
- Tesi
- Documenti grigi non indicizzati
- White papers

**Implicazione:** Analisi completa richiede triangolazione fonti (non solo peer-reviewed)

---

## Information Literacy

### Protocollo 4 Pilastri — Verifica UGC (User Generated Content)

Utilizzato per verificare contenuti generati dagli utenti durante **crisi e emergenze** (giornalismo, disaster response).

| Pilastro | Domande | Fonti |
|----------|---------|-------|
| **Provenienza** | Dove/come è stato creato? Chi lo ha trovato? Contesto originale? | Backtracking URL, Wayback Machine, WHOIS |
| **Fonte** | Chi ha creato il contenuto? Affidabilità? Expertise? Bias noto? | Social media profilo, storia pubblicazioni, affiliazioni |
| **Data** | Quando è stato creato/pubblicato? Timeline plausibile? | Metadati EXIF, timestamp post, archive.org, Google Images |
| **Luogo** | Dove fisicamente il contenuto è stato creato? Coerente? | Geolocalizzazione Google Earth, Wikimapia, landmarks |

**Strumenti Digitali di Verifica:**
- **Ricerca Inversa:** Google Images, Reverse.com, TinEye
- **Metadati:** EXIF data (fotocamera, GPS, data/ora)
- **Geolocalizzazione:** Google Earth, Wikimapia, confronto landmarks
- **Meteorologia:** Wolfram Alpha (condizioni meteo storiche per verificare coerenza)

---

### Data Literacy — Interpretazione Grafici

**Procedura 4 Step:**

1. **Revisione Titolo/Assi/Legende**
   - Il titolo risponde alla domanda?
   - Assi sono etichettati con unità?
   - Legenda è chiara?

2. **Identificazione Tendenze**
   - Aumenta, diminuisce, stabile nel tempo?
   - Picchi/valli? Cause visibili?

3. **Connessione a Obiettivi Decisionali**
   - Come questo dato supporta la decisione?
   - Quali altri dati servono?

4. **Proposta Step Successivi**
   - Approfondimento statistico?
   - Confronto benchmark?
   - Analisi sottogruppi?

---

## Definizioni Tecniche

| Termine | Definizione | Contesto |
|---------|-------------|----------|
| **IA** | Sistema basato su macchine con capacità di inferenza (apprendimento, ragionamento, modellizzazione) per generare output che influenzano ambienti reali/virtuali | EU AI Act Art. 3 |
| **Rischio Inaccettabile** | Sistemi che minacciano i diritti fondamentali e sono vietati | EU AI Act Art. 5 |
| **Alto Rischio** | Sistemi in settori critici (infrastrutture, istruzione, lavoro, giustizia) con obblighi conformità ex ante | EU AI Act Art. 6 |
| **GPAI** | General Purpose AI — modelli fondazionali (LLM) con capacità generiche di generare vari output | EU AI Act Art. 3 |
| **Rischio Sistemico (GPAI)** | GPAI con cumulative compute > 10²⁵ FLOPs per training | EU AI Act Art. 59 |
| **Supervisione Umana** | Personale con autorità di arresto o correzione del sistema | EU AI Act Art. 14 |
| **Logging** | Tracciamento automatico di tutte le operazioni con conservazione ≥ 6 mesi | EU AI Act Art. 12 |
| **Bias** | Distorsione sistematica nei dati di training che causa discriminazione | EU AI Act Considerando 37 |
| **Deepfake** | Contenuto sintetico falso (video, audio, immagine) generato o manipolato tramite IA | DDL AI Art. 18 |
| **FRIA** | Valutazione impatto sui diritti fondamentali (Fundamental Rights Impact Assessment) | EU AI Act Art. 27 |
| **DPIA** | Valutazione impatto protezione dati personali secondo GDPR | GDPR Art. 35 |

---

## Dati Numerici Critici

### Sanzioni Finanziarie

| Violazione | Sanzione Massima | Percentuale Fatturato | Euro Massimi |
|------------|------------------|----------------------|--------------|
| **Sistemi Vietati** | 7% del fatturato globale | 7% | €35.000.000 |
| **Violazione Obblighi Alto Rischio** | 3% del fatturato globale | 3% | €15.000.000 |
| **Informazioni False ad Autorità** | 1% del fatturato globale | 1% | €7.500.000 |

---

### Sanzioni Penali (DDL AI)

| Reato | Sanzione |
|-------|----------|
| **Diffusione Deepfake Illeciti** | Reclusione 1-5 anni |
| **Reati Aggravati tramite IA** | Raddoppio pena |

---

### Timeline Normativa (Date Esatte)

| Data | Evento | Dettagli |
|------|--------|----------|
| **1 Agosto 2024** | Entrata in vigore | EU AI Act ufficialmente vigente |
| **2 Febbraio 2025** | Divieti + Literacy | Sistemi inaccettabili vietati; AI Literacy obbligatoria |
| **2 Agosto 2025** | GPAI Regulations | Modelli fondazionali soggetti a norme |
| **2 Agosto 2026** | Piena Applicazione | Sistemi alto rischio in piena conformità |
| **2 Agosto 2027** | Prodotti Regolati | IA in prodotti già regolati (es. dispositivi medici) |

---

### Soglie Tecniche

| Parametro | Valore | Applicazione |
|-----------|--------|--------------|
| **FLOPs Rischio Sistemico (GPAI)** | > 10²⁵ | Modelli presunti rischio sistemico |
| **FLOPs Monitoraggio (GPAI)** | > 10²³ | Modelli generative language |
| **Conservazione Log** | ≥ 6 mesi | Sistemi alto rischio |
| **Tempo Notifica Incidente** | Entro 2 giorni | Deployer a autorità |
| **Investimento Pubblico Italia** | ~€1 miliardo | 2025-2027 (startup/PMI/ricerca) |

---

## Connessioni e Backlink

> **Nota:** Questo MOC è collegato a:
> - [[moc/Knowledge MOC]] — Sezione EU AI Act (versione sintetizzata)
> - [[moc/Agenti IA Design Patterns MOC]] — Governance sistemi agentici
> - [[moc/ITS MOC]] — Progetti che implicano compliance PA

---

**Versione:** 1.0 (2026-04-24)  
**Stato:** Completato da Query 1-3 NotebookLM  
**Prossimi step:** Query 4 (dati numerici approfonditi) quando NotebookLM disponibile