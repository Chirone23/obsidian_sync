# Creazione e Simulazione di Scenari con AI — MOC

**Fonte:** `Test-AI-Prompting-Architecture-Il caso TechLogistics.pdf`
**Docente:** Riccardo Raponi
**Tema:** Prompt Architecture per il Decision Making Strategico

---

## Il Cambio di Paradigma

L'obiettivo non è usare l'IA come generatore di testo, ma come **simulatore di scenari decisionali complessi**.

| Modalità | Descrizione |
|----------|-------------|
| **Generazione Lineare** | Prompting transazionale: scambio richiesta → risposta (A → B). L'IA produce testo in modo bidimensionale. |
| **Simulazione Multidimensionale** | Ambiente protetto per testare decisioni, anticipare crisi, risolvere conflitti strategici. L'IA approssima conversazioni coerenti basate su modelli predittivi. |

> **Principio chiave:** passare dall'IA come generatore di testo all'IA come simulatore di scenari decisionali complessi.

---

## Il Ciclo dell'Architettura (4 Nodi)

Un prompt efficace si progetta in modo **iterativo** basandosi su chiarezza, specificità e creatività.

```
[Node 01] Definizione dell'obiettivo
       ↓
[Node 02] Selezione del Framework
       ↓
[Node 03] Aggiunta di Dettagli (dati settore, KPI, vincoli)
       ↓
[Node 04] Valutazione e Feedback (coerenza, raffinamento)
       ↑___________________________________________↑
```

---

## Il Toolkit

### Board Virtuali — Perspective Prompting

Istruire l'IA affinché simuli **prospettive multiple** per identificare conflitti di interesse tra dipartimenti.

- Ogni agente ha un ruolo specifico con priorità dichiarate
- Si usano **Control Codes** (tag) per vincolare tono e focus: `[TECNICO]`, `[FORMALE]`, `[SPECIFICO]`
- Ogni membro del board risponde con il proprio tag (es. `[CEO]`, `[CFO]`, `[HR]`, `[CMO]`)

### Analisi Condizionale — What-If

**Sollecitazione Condizionale:** valutare le conseguenze di una scelta o l'impatto di variabili esterne attraverso ipotesi logiche a cascata.

Esempio: *"Se il costo delle materie prime aumenta del 10%..."* genera:
- Scenario A: Assorbimento Costi
- Scenario B: Traslazione su Prezzo Finale
- Scenario C: Rinegoziazione Fornitori

### Active Learning — Two-Way Interaction

Progettare prompt in cui l'IA **richiede attivamente chiarimenti** all'utente prima di fornire una soluzione definitiva.

```
AI → Richiesta di Chiarimenti → Utente
Utente → Fornitura Dati/Feedback → AI
```

---

## Caso Studio: TechLogistics S.p.A.

**Decisione da prendere:** centralizzare la distribuzione eliminando i magazzini regionali.

| Elemento | Dettaglio |
|----------|-----------|
| **Vantaggio** | Risparmio di €2M/anno in costi fissi |
| **Rischio** | Possibile sciopero di 150 persone e blocco totale |
| **Vincolo Finanziario** | Ritorno sull'investimento (ROI) a 3 anni |
| **Vincolo di Mercato** | I clienti pretendono la consegna in 24 ore |

### Board Virtuale — 4 Persona

| Ruolo | Nome | Focus |
|-------|------|-------|
| CEO `[CEO]` | Marco Valenti | Scalabilità |
| CFO `[CFO]` | Elena Russo | ROI a 3 anni |
| HR Manager `[HR]` | Roberto Sarti | Rischio sciopero 150 persone |
| CMO `[CMO]` | Giulia Bianchi | Consegne in 24h |

---

## I 3 Step del Laboratorio d'Esame

### Step 1 — Configurazione (Priming)

Accendere il Board Virtuale: non chiedere un parere generico, ma **istruire l'IA a interpretare ruoli specifici**.

**Prompt base:**
```
"Agisci come il Board di Direzione di Tech-Logistics.
Configura 4 Persona: Marco (CEO), focalizzato sulla scalabilità;
Elena (CFO), focalizzata sul ROI a 3 anni; Roberto (HR), che teme
scioperi di 150 persone; Giulia (CMO), che vuole consegne in 24h.
Usate i tag [CEO], [CFO], [HR] e [CMO] per rispondere.
Qual è la vostra reazione iniziale alla chiusura dei magazzini?"
```

**Prompt avanzato con Control Codes:**
```
"Simula una discussione tra il CFO e l'HR Manager.
Il CFO deve usare un tono [TECNICO] e [SPECIFICO] sui costi.
L'HR deve rispondere evidenziando il rischio reputazionale.
Analizzate se il risparmio di 2M€ giustifica il rischio di blocco totale."
```

### Step 2 — Scenario What-If

Introdurre una variabile per vedere come cambia l'equilibrio del Board.

**Prompt:**
```
"Cosa succede se decidiamo di automatizzare solo il 50% dei
magazzini regionali invece di chiuderli?
Chiedi a Elena (CFO) di ricalcolare il ROI e a Roberto (HR)
di valutare se questo riduce la probabilità di sciopero."
```

> **Insight:** ogni scelta ha un "prezzo". Meno automazione = meno risparmio, ma forse più pace sociale.

### Step 3 — Mediazione Finale (La Task d'Esame)

Ruolo: non più osservatore, ma **Consulente Esterno** incaricato di scrivere la decisione finale.

**Prompt finale:**
```
"Agisci come un consulente esterno. Basandoti sul dibattito precedente,
proponi un piano in 4 fasi. Usa il ragionamento 'Step-by-Step'.
Il piano deve:
1. Garantire il risparmio (CFO);
2. Mantenere le 24h (CMO);
3. Includere una fase di Active Learning per ascoltare i 150
   dipendenti prima di agire (HR)."
```

**I 3 output da consegnare:**
1. **Il Verbale** — trascrizione del conflitto iniziale tra i 4 manager (ottenuta col Priming)
2. **L'Analisi del Rischio** — cosa succede nello scenario What-if (50% automazione)
3. **Il Progetto Finale** — piano in 4 fasi firmato come "Consulente Mediatore"

---

## Sintesi: Tecnica → Output → Impatto Business

| Output | Tecnica IA | Impatto Business |
|--------|-----------|-----------------|
| Il Verbale | Perspective Prompting + Control Codes | Mappatura oggettiva dei conflitti interdipartimentali |
| L'Analisi del Rischio | Conditional Prompting (What-If) | Stress-test finanziario e quantificazione trade-off (Automazione vs Risparmio) |
| Il Progetto Finale | Step-by-Step Chain of Thought + Active Learning | Creazione piano di transizione operativo e mitigazione rischio sindacale |

> **L'obiettivo finale non è un prompt perfetto, ma una decisione strategica inattaccabile.**

---

## Connessioni

- [[Prompting MOC]] — fondamenti, framework C.I.A.R.E., tecniche avanzate
- [[Prompt Library]] — template pronti all'uso
- [[ITS MOC]]
