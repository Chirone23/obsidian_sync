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

