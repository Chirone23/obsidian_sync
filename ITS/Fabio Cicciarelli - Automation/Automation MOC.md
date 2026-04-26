# Automation MOC

Materia ITS — Automazione digitale e sistemi intelligenti. Piattaforma GoHighLevel (GHL).
**Docente:** Fabio Cicciarelli
**Fonte:** `ITS/Fabio Cicciarelli - Automation/`
**NotebookLM:** https://notebooklm.google.com/notebook/a0745347-8ae2-429a-ae17-6b68eb93b44d

---

## Teoria dell'Automazione Intelligente

### I 3 Livelli di Autonomia

| Livello | Nome | Come funziona |
|---------|------|---------------|
| **1** | **Workflow** (la ricetta fissa) | Sequenza predeterminata di passi, sempre nello stesso ordine, nessuna variazione. Avvio manuale, risultati identici a parità di input. |
| **2** | **Automation** (la ricetta che parte da sola) | Workflow attivato automaticamente da un **trigger**. Include logiche if/then fisse, programmate in anticipo. |
| **3** | **AI Agent** (il sistema che ragiona) | Comprende contesto, gestisce ambiguità, prende decisioni personalizzate caso per caso tramite NLP. |

### Concetti Fondamentali

**Trigger** — l'evento scatenante che avvia qualsiasi processo automatizzato:
- Basato sul **tempo** (schedule)
- Arrivo di **messaggi**
- Compilazione di **form**
- Ricezione di **webhook** esterno

**NLP (Natural Language Processing)** — permette all'agente di leggere linguaggio naturale, estrarne il significato reale e classificare le richieste (es. distinguere domanda tecnica da organizzativa).

---

## Infrastruttura Tecnica

### API — Come Funzionano

Le API sono il "cameriere digitale" che trasporta richieste e risposte tra applicazioni diverse. 3 elementi:

| Elemento | Descrizione |
|----------|-------------|
| **Endpoints** | URL specifici per funzioni dedicate (es. `/messages` per le email) |
| **Autenticazione** | **API Key** = stringa fissa (password permanente) · **OAuth** = token temporanei, più sicuro ("Accedi con Google") |
| **Parametri** | Dettagli della richiesta — filtri per ottenere dati precisi (limiti temporali, stati) |

### Webhooks vs Polling

| Metodo | Filosofia | Funzionamento | Efficienza |
|--------|-----------|---------------|------------|
| **Polling** | Pull | Il sistema controlla ripetutamente se è successo qualcosa | Spreca banda e CPU, ritardi inevitabili |
| **Webhook** | Push | Il sistema esterno notifica istantaneamente non appena accade un evento, verso un URL univoco | Reazione real-time, zero sprechi |

---

## GoHighLevel (GHL) — Piattaforma

CRM e automazione all-in-one. Struttura workflow: **Trigger → Filters → Actions → Branch (If/Else) → Wait** su canvas infinito.

### 8 Macro-Aree

#### 1. Settings & Infrastruttura Base
- **Custom Fields** — campi personalizzati per i contatti
- **Custom Objects** — entità nuove (es. "Veicoli", "Contratti")
- **Custom Values** — variabili globali (es. indirizzo ufficio, link di booking)
- **Tags standardizzati** — naming convention (es. `Action: Send PDF`) per evitare frammentazione DB
- *Esempio:* dropdown per segmentare lead palestra per obiettivo ("Dimagrimento" / "Massa")

#### 2. Cattura Contatti & Lead Generation
- **Form Builder + Surveys** con Conditional Logic v2 — mostra/nasconde campi, squalifica lead in real-time
- **Facebook Lead Ads** — integrazione nativa con mapping automatico dei campi nel CRM
- **Chat Widget** — sul sito, stimola interazione asincrona (SMS/Email) o sincrona (Live Chat)

#### 3. Comunicazione (Unified Inbox)
- **SMS (LC Phone)** — conformità A2P 10DLC (USA), gestione Opt-in/Opt-out esplicita
- **WhatsApp Business API** — messaggi proattivi oltre 24h richiedono Template pre-approvati da Meta
- **Voicemail Drop** — tecnologia "Ringless": deposita audio (64kbps) in segreteria senza far squillare il telefono
- Tutto centralizzato in un'unica Unified Inbox

#### 4. Automation — Workflow (Il Cuore)
- Canvas infinito con struttura Flow-Based
- **Math Operations** — calcola lead scoring, manipola date (es. +30 giorni a una scadenza)
- **Speed to Lead** — assegna lead di Facebook a un venditore e avvia chiamata automatica entro 5 minuti

#### 5. CRM & Gestione Contatti
- **Smart Lists** — viste database aggiornate in real-time tramite filtri AND/OR (es. "Lead entrati oggi" + "Senza appuntamento")
- **Companies** — relazioni B2B one-to-many (più contatti associati a un'azienda)

#### 6. Sales & Monetizzazione
- **Stripe / PayPal** — fatture singole (Invoices) o abbonamenti ricorrenti
- **Order Forms** nei funnel — Order Bump (aggiunte pre-acquisto) + 1-Click Upsell
- **LMS** — corsi online con Drip Content (rilascio lezioni a intervalli prestabiliti)

#### 7. Reputation & Review Management
- Invio automatico **Review Requests** via SMS/Email quando un'opportunità entra in stage "Vinto"
- Dashboard Sentiment per monitorare e rispondere a recensioni Google/Facebook

#### 8. Reporting & Analytics
- **Attribution Reports** (First Touch vs Last Touch) — da dove arriva il cliente (Google Ads vs Facebook Ads)
- **Phone Reports** — volume conversazioni e tempi di risposta dei venditori

### Costruzione AI Agent in GHL
Moduli nativi: **Conversation AI** (testo) + **Voice AI** (voce).
L'agente usa NLP per leggere il messaggio del lead, classificare l'intenzione e scegliere il ramo corretto del workflow.

---

## Esercizi Pratici

### Esercizio 1: Palestra "Energia Pura"
**Obiettivo:** trasformare visitatori in appuntamenti fissati.

Pipeline: `Lead Entrato → In Attesa di Prenotazione → Appuntamento Fissato → Perso`

**Workflow di Recupero:**
1. Lead non prenota entro 24h → WhatsApp di sollecito
2. Dopo altre 24h senza prenotazione → opportunità spostata su "Perso" + nota automatica

**Segmentazione contenuti (If/Else):**
- Obiettivo = Dimagrimento → invia "Guida Brucia Grassi"
- Obiettivo = Massa → invia "Guida Ipertrofia"

### Esercizio 2: Concessionario Auto
**Trigger:** invio form dal sito con campo "Interesse principale"

**Logica condizionale:**
- **Acquisto Auto** → email + WhatsApp per prenotare visita fisica in sede
- **Servizio Autista** → flusso verso call informativa con team dedicato

**Notifiche interne:** WhatsApp istantanea ai venditori con dettagli contatto per massimizzare velocità di risposta.

---

## Certificazione GHL Architect — 100 Domande

Test a risposta multipla, 7 moduli:

| Modulo | Area |
|--------|------|
| 1 | Governance & Account Hierarchy — sub-account e permessi staff |
| 2 | CRM & Data Management — Custom Fields, Smart Lists, tag |
| 3 | Conversations & Channels — WhatsApp, SMS, Email Builder |
| 4 | Sites, Funnels & Forms — interfacce di conversione, logiche multi-step |
| 5 | Calendars & Opportunities — appuntamenti e pipeline di vendita |
| 6 | Automation & Workflows — trigger, azioni condizionali, logiche Wait |
| 7 | Reporting, Settings & Scenarios — analisi attribuzione, scenari complessi |

---

## Progetti Pratici

*(da aggiungere quando disponibili)*

---

## Connessioni

- [[ITS MOC]]
- [[Skill MOC]] — n8n, Claude Code, automazioni complementari a GHL
- [[Imprenditoria MOC]] — CRM e sales pipeline nel contesto business
- [[Back-end MOC]] — API, webhooks, infrastruttura server
