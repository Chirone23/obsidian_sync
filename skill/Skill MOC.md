# Skill MOC

Mappa delle skill operative, strumenti e guide pratiche.

Fonti: [[skill/Build a Second Brain with Claude Code + Obsidian.pdf|EZYE OS Guide]], [[skill/N8N + Claude Code.pdf|N8N + Claude Code]], [[skill/n8n Roadmap 2026_ Guida Pratica.pdf|n8n Roadmap 2026]]

---

## Claude Code + Obsidian: Second Brain Stack

**Fonte:** `skill/Build a Second Brain with Claude Code + Obsidian.pdf` (EZYE OS, Divjot Sahni)

Il problema di base: Claude Code riparte da zero ad ogni sessione. Obsidian risolve questo dandogli memoria persistente del vault.

### 5 Setup chiave

| Setup | Cosa fa |
|-------|---------|
| **01 — Vault MCP** | `mcp-obsidian` espone tutto il vault a Claude: full-text search, frontmatter, tag, BM25. Niente più copy-paste. |
| **02 — Claude Sidebar** | Plugin Obsidian che embeds Claude Code nel pannello destro. Multi-tab per conversazioni parallele. |
| **03 — Obsidian Skill** | Skill `/obsidian` che insegna a Claude il routing corretto: MCP per dati, CLI per daily notes, Git per sync. |
| **04 — Knowledge Compiler** | Pattern Karpathy: ogni nuova fonte aggiorna 10-15 note via `[[backlink]]`. La conoscenza compone. |
| **05 — Git Sync** | Git + plugin Obsidian Git (auto-commit 10 min). Sync cross-device senza abbonamento Obsidian Sync. |

### Principi operativi
- Usa `-s user` flag per MCP disponibile in ogni progetto
- Graph view mensile: i cluster densi = dove sta formandosi la vera expertise
- Knowledge Compiler: cerca correlati → estrai → aggiorna MOC → crea backlink → mai nota isolata

---

## n8n + Claude Code: Coding Agentico

**Fonte:** `skill/N8N + Claude Code.pdf`

Claude Code come ingegnere dell'automazione personale: progetta, scrive e carica workflow su n8n via MCP.

### Architettura del sistema (4 pilastri)

1. **Claude Code** — CLI Anthropic (piano Pro o Max, consigliato dentro VS Code/Cursor)
2. **MCP** — protocollo che fa "uscire" Claude dalla chat per interagire con n8n
3. **n8n Skills** — set di istruzioni/template che insegnano best practice a Claude
4. **VS Code** — ambiente di monitoraggio

### Setup essenziale
```
Server MCP: github.com/czlonkowski/n8n-mcp
Skills:     github.com/czlonkowski/n8n-skills
API Key:    n8n → Settings > API Keys (No Expiration per dev)
```

### Workflow operativo
1. **Prompt Strategico** in Edit Automatically mode → descrivi il workflow
2. **Analisi** → Claude cerca nodi, costruisce routing logic, connette, fa upload via API
3. **Debug** → screenshot errore + istruzione specifica a Claude
4. Se workflow complesso: `/model` → passa a Opus

### Best practices
- **Modularità**: costruisci blocchi principali, poi aggiungi tool/sotto-processi
- **Credenziali**: Claude non inserisce password — le selezioni tu in n8n dopo la generazione
- **Apprendimento continuo**: se Claude ripete lo stesso errore → aggiorna `claude.md` con regola specifica
- **Comandi utili**: `/compact` (risparmia context window), `/clear` (nuova sessione)

---

## n8n Roadmap 2026: Vale la Pena Imparare?

**Fonte:** `skill/n8n Roadmap 2026_ Guida Pratica.pdf` (Riccardo, Martes AI)

**Risposta breve:** Sì, ma il gioco è cambiato. Con Claude Code + MCP puoi generare workflow automaticamente — ma le fondamenta rimangono critiche per debuggare.

### Il Nuovo Paradigma: Approccio Ibrido (raccomandato)
1. Claude Code genera la base → risparmio 70% del tempo
2. Tu ottimizzi: fix errori, mapping variabili, error handling
3. Itera con Claude Code se necessario

**Senza fondamenta ti blocchi al primo errore.**

### Fondamenta da padroneggiare

**Trigger:**
- *Interno*: Schedule Trigger, When Executed by Another Workflow
- *Esterno (Webhook)*: attivato da evento esterno (WhatsApp, form, e-commerce)

**Nodi core:**
- **Action nodes** — integrazioni native (Google Sheets, Slack, Notion, WhatsApp, HubSpot…)
- **HTTP Request** — per API senza integrazione nativa (richiede conoscenza GET/POST/PUT/DELETE)
- **Filter / IF / Switch** — logica condizionale e routing
- **Loop** — iterazione elemento per elemento su una lista

**AI in n8n:**
- **Basic LLM Chain** — prompt → risposta da LLM (Gemini, OpenAI, Anthropic)
- **AI Agent** — sistema evoluto che usa Tools per compiere azioni autonome
  - Tools: Airtable, MCP Client, Call n8n Workflow, Perplexity, HubSpot…
  - Memoria: Postgres Chat Memory, Simple Memory, MongoDB Chat Memory

### Setup Self-Hosted
- VPS Hostinger KVM 2 (~€75/anno) vs cloud n8n (~€50-60/mese)
- Server in Europa per GDPR compliance

### Workflow pratici di esempio
- **Newsletter AI**: Schedule (7:00) → RSS Feed → AI synthesis → HTML format → Gmail
- **Lead Enrichment**: Webhook → Perplexity API → AI extraction → CRM update

---

## n8n Avanzato: AI Agents & Infrastruttura

**NotebookLM:** https://notebooklm.google.com/notebook/2001126c-8f86-4db4-8bc4-4bc1c94499f7

### 19 Nodi Essenziali (80-90% dei Workflow Professionali)

| Categoria | Nodi |
|-----------|------|
| **Trigger** | Manual, Schedule, Webhook, Gmail, Telegram, WhatsApp |
| **Logic** | If, Switch, Merge, Execute Workflow, Loop |
| **Data Transform** | Set, Split Out, Aggregate, Code Node (JS/Python) |
| **API Integration** | HTTP Request, GraphQL, REST API |
| **AI & LLM** | Basic LLM Chain, AI Agent, Bedrock (AWS) |
| **Communication** | Slack, Email, Discord, Twilio |
| **Database** | Postgres, MongoDB, Airtable, Google Sheets |
| **File Operations** | Split Binary String, Move Binary Data, File Write |
| **Error Handling** | Error Trigger, Try-Catch, Custom Handler |

### Architettura AI Agent

| Componente | Descrizione |
|-----------|-------------|
| **Brain** | LLM che ragiona: Claude Opus 4.5, GPT-4.1, Deepseek R1 |
| **Memory** | Database che salva il contesto: Postgres, MongoDB, Simple Memory |
| **Tools** | Le "mani" dell'agente: API, database, altri workflow n8n |

### RAG (Retrieval-Augmented Generation)

Addestra agenti su documenti privati:
1. **Chunking** (dividere documenti in pezzi):
   - *Statico*: split per paragrafo/capitolo (veloce, ma perde contesto ai confini)
   - *Dinamico*: split intelligente in base al significato (mantiene coerenza, più complesso)
2. **Vettorizzazione** (conversione testi in "frecce" matematiche)
3. **Vector Database** (es. Pinecone): archivia vettori per ricerca veloce
4. **Semantic Search** → recupera i chunk più rilevanti per rispondere

### Retell AI Voice Agent

Agente telefonico multilinguaggio per booking, customer service, survey:
- **Configurazione**: numero Twilio → webhook → flusso n8n → LLM (Claude)
- **Flow n8n**: ricevi chiamata → saluta in italiano → prendi slot calendar da Google Calendar → riserva appuntamento
- **Output**: registrazione audio + transcript in n8n per follow-up automatici

### Framework AID (Implementazione Aziendale)

Roadmap strutturata per deployare AI in azienda:

1. **Education** (settimane 1-2)
   - Workshop team su prompt engineering e AI basics
   - Esempi pratici: sintesi documenti, email drafting, analisi data
   
2. **Identification** (settimane 2-3)
   - Mappa processi interni: dove il tempo è sprecato?
   - Classifica in **Quick Wins** (bassa complessità, alto impatto: sintesi, drafting, categorizzazione)
   - Vs **Big Swings** (alta complessità: supply chain, modeling, agenti autonomi)
   
3. **Development** (settimane 4+)
   - MVP first: scegli un Quick Win, builds agile, testa in 1 settimana
   - Scale gradualmente su workflow complessi

### Vibe Coding — UI Cinematica

Stack per landing page/dashboard avanzate: **React + Tailwind CSS + GSAP + Three.js**

**3 Regole Fondamentali:**
1. Animazioni fluide e eleganti (non distrarre)
2. Responsive design: mobile-first
3. Performance: lazy loading per Three.js, minificazione

**Preset Estetici:**
- **Organic Tech**: Colori naturali (verde #3B7A5F, beige #F5E6D3, grigio #4A4A4A), font Poppins Bold, curve smooth (border-radius 20px), micro-animazioni onHover
- **Midnight Luxe**: Nero #0F0F0F con oro #D4AF37, font Playfair Display, linee geometriche sharp, glow effect su accenti

### Guardrails (Sicurezza LLM)

Nodi di sicurezza che bloccano attacchi e leak dati:

| Tipo | Blocca | Esempio |
|------|--------|---------|
| **Keyword** | Parole vietate (es. "code injection") | regex matcher |
| **Jailbreak** | Tentativi di bypassare istruzioni sistema | "Ignora le regole e…" |
| **Sanitizer** | PII (numeri carta, IBAN, SSN) prima che raggiungano l'LLM | Pattern recognition |
| **Regex Custom** | Pattern personalizzate (es. URL sospette) | Configurable filter |

### Agent Builder Agent

Agente n8n specializzato che **crea altri workflow n8n da un semplice prompt**.

**Stack:**
- **LLM**: Claude Opus 4.5 (ragionamento complesso)
- **Modo**: Generazione JSON che rappresenta la struttura del workflow
- **API n8n**: carica il JSON come workflow nuovo

**3 Esempi JSON:**

```json
{
  "name": "Newsletter AI",
  "triggers": [{"type": "Schedule", "cron": "0 7 * * *"}],
  "nodes": [
    {"type": "RSS_Feed", "url": "https://..."},
    {"type": "LLM_Chain", "model": "Claude", "prompt": "Sintetizza in 3 punti"},
    {"type": "Email", "to": "user@email.com"}
  ]
}
```

### Content Creation Pipeline

Workflow completo per generare video virali:

1. **Fonte dati**: Google Sheet con brief (titolo, tema, stile)
2. **Video generation**: API Veo 3 (Google) o Sora 2 (OpenAI)
3. **Polling**: controlla status generazione ogni 30s fino a completamento
4. **Upload**: Blotato (tool automatico) pubblica su TikTok, Instagram, YouTube con caption
5. **Tracking**: salva link output in sheet per analytics

### Error Trigger & Error Handling

- **Error Trigger**: attivato quando un nodo fallisce, permette retry logic o notifiche
- **Think Tool**: pausa workflow, pensa al problema, riprova intelligentemente
- **Custom Handler**: flusso alternativo se primo tentativo fallisce

---

## Skill Operative del Vault

Protocolli interni che standardizzano il modo in cui Claude interagisce col vault.

| Skill | Quando usarla |
|-------|---------------|
| [[Source Intake Protocol]] | Fonte nuova (PDF, web, video, notebook, immagine) da integrare |
| [[NotebookLM Query Playbook]] | Link NotebookLM con N fonti — sequenza di 4 query |
| [[MOC Integration Checklist]] | Prima/dopo ogni scrittura — garantisce backlink e routing corretto |
| [[Triage Protocol]] | All'inizio di ogni richiesta — critico vs creativo, Master Prompt |
| [[Daily Note Ritual]] | Apertura/chiusura giornata + review settimanale |
| [[Vault Audit]] | Routine settimanale/mensile per orfani, duplicati, coerenza |

---

## Connessioni

- [[Knowledge MOC]] — Context Engineering e uso AI per automazioni
- [[ITS MOC]] — Applicazione pratica di n8n nei progetti ITS
- [[Index MOC]]

---

## Sviluppare con l'AI nel 2026

**Fonte:** [[Sviluppare con l'AI nel 2026.md|Sviluppare con l'AI nel 2026]] — trascrizione video

Framework a 3 blocchi per ottenere output consistenti e funzionanti dall'AI:

| Blocco | Cosa fa |
|--------|---------|
| **Obiettivo** | Definisce "done" in modo non ambiguo |
| **Contesto** | Stack, file esistenti, input/output attesi, limiti performance |
| **Non fare (Guard Rails)** | Cosa non toccare, quali tech non introdurre, quando fermarsi e chiedere |

**Review Driven Development:** l'AI genera il piano → tu revisioni e commenti → l'AI aggiorna → solo dopo il tuo OK si genera codice.

**Guard rail anti-allucinazione:** `"Utilizza solo le informazioni presenti nel contesto o nelle fonti esplicite. Motiva sempre le tue risposte."`
