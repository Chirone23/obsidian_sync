# Knowledge MOC

Mappa della conoscenza generale: framework, metodologie e architetture.

**Fonti:** [[knowladge/Knowlage copy.pdf]], [[knowladge/StoryTell_Architettura_v2.1.docx.pdf]]

---

## Copywriting — Fondamenta e Sistemi

**Fonte:** `knowladge/Knowlage copy.pdf` (Andrei Pascu / Copywriting Mentorship)

### A.S.A. — Autonomous Service Activity

Framework per costruire un business di copywriting in autonomia. 5 step sequenziali:

1. **Skill** — padronanza tecnica del copy
2. **Reputation** — costruzione autorevolezza
3. **First client** — primo cliente pagante
4. **Raise prices** — scalare il valore percepito
5. *(Step 5 — desumibile dal contesto: sistematizzare / delegare)*

### APSOC — Framework di Scrittura

| Fase | Significato |
|------|-------------|
| **A** | Attenzione |
| **P** | Problema |
| **S** | Soluzione |
| **O** | Obiezioni |
| **C** | CTA (Call to Action) |

Sequenza persuasiva per copy strutturato: cattura attenzione → identifica il dolore → presenta soluzione → smonta resistenze → chiudi con CTA.

### CPB — Gestione Obiezioni

**Claim → Proof → Benefit**

Per ogni obiezione: fai una claim, dimostrala con prova, collega al beneficio del lettore.

### Pain Point vs Problema

- **Pain Point**: il sintomo percepito dal cliente (es. "sono sempre stanco")
- **Problema**: la causa reale (es. "dormo 5 ore")

Il copy efficace parla al pain point, risolve il problema.

### Leve emotive

| Tipo          | Approccio                                              |
| ------------- | ------------------------------------------------------ |
| **Implicite** | Evocate senza nominarle — "show don't tell"            |
| **Esplicite** | Nominate direttamente (paura, desiderio, appartenenza) |

**Regola d'oro:** mostra la trasformazione, non descrivere l'emozione.

### Metriche da conoscere

| Metrica | Cosa misura |
|---------|-------------|
| CR | Conversion Rate |
| CPA | Cost Per Acquisition |
| CPC | Cost Per Click |
| CPM | Costo per 1000 impression |
| ROAS | Return On Ad Spend |
| AOV | Average Order Value |
| LTV | Lifetime Value |
| Bounce Rate | % abbandono pagina |
| Engagement Rate | Interazioni / reach |
| Open Rate | % apertura email |
| Churn Rate | % abbandono servizio |

---

## Context Engineering

**Fonte:** `knowladge/Knowlage copy.pdf` (sezione avanzata)

Spostamento di paradigma: invece di correggere l'output AI, costruisci input migliori.

**Il principio**: 30 minuti per costruire un motore di contesto → 3 secondi per ogni uso successivo.

**Applicazione pratica:**
- Definisci il contesto prima di aprire una chat AI
- Template di prompt con variabili fisse (tono, audience, obiettivo)
- Reutilizza il contesto costruito — non riscrivere ogni volta

> Connessione diretta con [[Skill MOC]] — Setup 04 Knowledge Compiler e il pattern Karpathy.

---

## StoryTell 2.0 — Architettura Tecnica

**Fonte:** `knowladge/StoryTell_Architettura_v2.1.docx.pdf` (v2.1, Marzo 2026)

Libri personalizzati AR per bambini. Questa è la documentazione tecnica di architettura, non un concept — già in sviluppo.

### 3 Leve di Ottimizzazione (vs v2.0)

| Leva | Dettaglio |
|------|-----------|
| **1 — Video clips ridotti** | Da 4 clip a 2 + animatiche FFmpeg |
| **2 — Character Caching Layer** | AWS S3: genera asset personaggio una volta, riusa a €0.00 per libri successivi |
| **3 — TTS Routing Tier** | Google WaveNet (base) / ElevenLabs (premium) |

### COGS per Libro

| Scenario | Costo |
|----------|-------|
| Base — 1° libro | €0.360 |
| Base — ricorrente | €0.334 |
| Premium — 1° libro | €0.664 |
| Premium — ricorrente | €0.638 |

**Prezzo di vendita:** €65 → **Gross Margin ~60.8%** su tutti gli scenari

### TTS Routing

| Tier | Motore | Costo/libro |
|------|--------|-------------|
| Base | Google WaveNet | ~€0.096 |
| Premium | ElevenLabs | ~€0.40 |

### Sprint e Team

- **Timeline:** T1–T7, Q2–Q3 2026
- **Team:** Sofia (dev), Riccardo (business), Legale
- **Hard Rule:** nessun codice Ambassador prima di parere legale su architettura VPC delegata (D6)

---

## Connessioni

- [[Skill MOC]] — Context Engineering + AI tools per automazione
- [[ITS MOC]] — StoryTell 2.0 come progetto attivo
- [[Idee MOC]] — Idee a cui applicare copywriting e context engineering
- [[Index MOC]]


## EU AI Act (Reg. UE 2024/1689) e Governance IA

**Fonte:** NotebookLM notebook "EU AI Act e Compliance PA" — 29 fonti integrate (Reg. UE 2024/1689, DDL AI 132/2025, linee guida AgID, manuali verifica giornalistica, studi integrità scientifica)

### 5 Pilastri Tematici

#### 1. Quadro Regolatorio Europeo — Classificazione del Rischio

| Livello | Definizione | Esempi | Obblighi |
|---------|-------------|--------|---------|
| **Inaccettabile** | Sistemi vietati per minaccia ai diritti fondamentali | Social scoring, manipolazione subliminale, identificazione biometrica remota non autorizzata, sfruttamento minori | Divieto assoluto, sanzioni fino a €35M o 7% fatturato |
| **Alto Rischio** | Sistemi in settori critici | Infrastrutture, istruzione, selezione personale, servizi pubblici, giustizia | Conformità ex ante: governance dati, documentazione tecnica, logging, supervisione umana |
| **Limitato** | Sistemi che richiedono trasparenza | Chatbot, deepfake, IA generativa | Informare utenti; etichettare contenuti sintetici |
| **Minimo** | Applicazioni a impatto trascurabile | Filtri antispam, videogiochi | Libertà di utilizzo |

**Timeline di Entrata in Vigore:**
- **1 Agosto 2024**: Entrata in vigore formale
- **2 Febbraio 2025**: Divieti per rischio inaccettabile + AI Literacy obbligatoria
- **2 Agosto 2025**: Norme GPAI (modelli fondazionali) + sanzioni
- **2 Agosto 2026**: Piena applicazione sistemi ad alto rischio
- **2 Agosto 2027**: Conformità per IA integrata in prodotti regolati (es. dispositivi medici)

#### 2. Implementazione Italiana — DDL AI (Legge 132/2025)

**Autorità e Responsabilità:**
- **AgID** (Agenzia Italia Digitale): Innovazione nella PA, linee guida procurement, sviluppo IA nella PA
- **ACN** (Agenzia Cybersicurezza Nazionale): Vigilanza, cybersecurity, poteri ispettivi e sanzionatori

**Nuovi Reati (Sanzioni Penali):**
- Diffusione deepfake illeciti: reclusione 1-5 anni
- Aggravanti per reati commessi tramite IA

**Settori Specifici:**
- **Sanità**: IA come supporto diagnostico (mai sostitutiva del medico); piattaforma nazionale Agenas
- **Giustizia**: IA per ricerca documentale e analisi precedenti; decisione finale al giudice
- **Lavoro**: Obbligo informare lavoratori su sistemi IA (trasparenza algoritmica)

**Investimenti**: ~€1 miliardo per startup/PMI nel settore IA e calcolo quantistico

#### 3. Framework Operativi di Compliance

| Framework | Nome Completo | Scopo | Quando Applicare |
|-----------|---------------|-------|------------------|
| **FRIA** | Fundamental Rights Impact Assessment | Valutazione obbligatoria dell'impatto su diritti fondamentali | PA e deployer servizi essenziali (alto rischio) |
| **DPIA** | Data Protection Impact Assessment | Valutazione impatto protezione dati (GDPR) | Sistemi IA che processano dati personali |
| **GRADE** | Grading of Recommendations Assessment Development and Evaluation | Valutazione certezza prove e qualità evidenze scientifiche | Sintesi evidenze scientifiche e revisioni |
| **CRAAP Test** | Currency, Relevance, Authority, Accuracy, Purpose | Valutazione critica fonti + metacognizione | Information literacy e verifica fonti |
| **Modello Classico (Cooke)** | Cooke's Classical Model | Validazione giudizio esperti in assenza dati empirici | Decisioni con expertise limitata |

**Procedura FRIA — 5 Step:**
1. Descrizione processi: Come il sistema IA sarà integrato nei flussi di lavoro
2. Ambito temporale: Periodo e frequenza di utilizzo prevista
3. Categorie interessate: Elencare persone fisiche/gruppi influenzati
4. Valutazione rischi: Individuare potenziali danni ai diritti (dignità, non discriminazione, privacy)
5. Misure mitigazione: Supervisione umana effettiva + meccanismi ricorso cittadini

#### 4. Checklist di Conformità per Alto Rischio

- [ ] **Governance Dati**: Dataset rappresentativi e privi di bias
- [ ] **Documentazione Tecnica**: Fascicolo aggiornato prima della messa in servizio
- [ ] **Logging Automatico**: Tracciamento operazioni; conservazione log ≥ 6 mesi
- [ ] **Supervisione Umana**: Operatori con autorità di arresto/correzione (Pulsante di Stop)
- [ ] **Cybersecurity**: Robustezza contro attacchi avversariali
- [ ] **Registrazione UE**: Inserimento banca dati dell'Unione Europea

**Dichiarazione di Conformità UE** (Allegato V) — Elementi richiesti:
1. Nome e tipo del sistema
2. Indirizzo del fornitore
3. Attestazione responsabilità esclusiva
4. Conformità al regolamento
5. Conformità GDPR
6. Riferimento norme armonizzate
7. Dettagli organismo notificato
8. Luogo, data, firma

#### 5. Integrità Scientifica e Information Literacy

**Crisi nelle Ritrattazioni:**
- Aumento preoccupante in Life Sciences (falsificazione, manipolazione immagini)
- Pressioni per finanziamento + "Paper Mills"

**Metodologie di Validazione:**
- **GRADE**: Classificare qualità evidenze (HIGH ⊕⊕⊕⊕ → VERY LOW ⊕)
- **Letteratura Grigia vs Peer-Reviewed**: Non escludere dati critici in letteratura non indicizzata

**Protocollo Verifica UGC (4 Pilastri):**
1. **Provenienza**: Dove/come è stato creato il contenuto
2. **Fonte**: Chi ha creato/pubblicato
3. **Data**: Quando è stato creato
4. **Luogo**: Contesto geografico/fisico

**Strumenti di Verifica Digitale:**
- Ricerca inversa (Google Images)
- Analisi metadati (EXIF, geolocalizzazione via Google Earth/Wikimapia)
- Controllo meteorologico storico (Wolfram Alpha)

**Data Literacy — Interpretazione Grafici:**
1. Revisione titoli/assi/legende
2. Identificazione tendenze
3. Connessione a obiettivi decisionali
4. Proposta step successivi

### Connessioni

- [[ITS MOC]] — Compliance PA per progetti pubblici
- [[Skill MOC]] — Information literacy e verifica fonti
- [[Agenti IA Design Patterns MOC]] — Governance di sistemi agentici
- CV MOC — 
