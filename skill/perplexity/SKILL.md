---
name: ricerche-mirate
description: Suite di ricerca tech e imprenditoriale. Use when asked to analyze competitors, suggest a tech stack, validate a product idea, or summarize an article or URL. Attiva per analisi competitiva, scelta stack tecnico, validazione idea di prodotto, sintesi da fonte.
---

# Ricerche Mirate

Skill specializzata per ricerche tech e imprenditoriali. Copre quattro modalità operative che si attivano in base alla richiesta.

---

## Modalità 1 — Analisi Competitiva

**Trigger:** richieste tipo "analizza i competitor di X", "chi fa già questa cosa", "mappa il mercato di Y"

### Istruzioni
1. Identifica competitor diretti (stesso problema, stesso target) e indiretti (target sovrapposto)
2. Per ciascun competitor raccogli: nome, URL, proposta di valore, feature chiave, pricing, pro e contro
3. Costruisci una tabella comparativa
4. Concludi con gap di mercato identificati e opportunità di differenziazione
5. Cita fonti recenti (ultimi 12 mesi) con link

### Output atteso
- Tabella: Nome | Proposta di valore | Feature | Pricing | Pro | Contro
- Paragrafo finale: gap e opportunità di differenziazione
- Fonti numerate con link

---

## Modalità 2 — Stack Tecnico Consigliato

**Trigger:** richieste tipo "quale stack uso per X", "migliore libreria per Y", "come implemento Z"

### Istruzioni
1. Identifica il layer tecnico richiesto (frontend, backend, mobile, automazione, AI, database)
2. Proponi 2-3 opzioni per layer con: nome, link docs ufficiali, caso d'uso ideale, pro, contro, complessità (Basso/Medio/Alto)
3. Indica la combinazione consigliata con motivazione chiara
4. Linka tutorial o esempi pratici se disponibili
5. Segnala tecnologie in rapida evoluzione o a rischio deprecazione

### Output atteso
- Tabella per layer: Tool | Caso d'uso | Pro | Contro | Complessità
- Raccomandazione finale motivata
- Link a documentazione ufficiale e risorse pratiche

### Contesto utente
Stack preferito: Flutter/React Native (mobile), Supabase/Firebase (backend), n8n (automazione). Preferire open source o piano gratuito generoso.

---

## Modalità 3 — Validazione Idea di Prodotto

**Trigger:** richieste tipo "valida questa idea", "c'è mercato per X", "ha senso sviluppare Y"

### Istruzioni
1. Identifica il problema che l'idea risolve e il target di riferimento
2. Cerca dati di mercato: TAM/SAM/SOM, trend (Google Trends, report di settore), segnali di domanda reale (Reddit, forum, app store review)
3. Mappa 3-5 competitor esistenti (versione breve della Modalità 1)
4. Cerca casi studio di prodotti analoghi: successi e fallimenti
5. Identifica i principali rischi di esecuzione
6. Emetti verdetto: **Valida** / **Da pivottare** / **Mercato saturo**

### Output atteso
- Problema e Target
- Dati di mercato con fonti
- Competitor rapidi (3-5)
- Rischi principali
- Verdetto finale con motivazione

### Contesto utente
Progetti attivi: LevelUp IRL (app gamification vita reale, Battle Pass €4.99/mese), App OBD2 diagnostica auto. Usare solo dati concreti — se non esistono, dirlo esplicitamente.

---

## Modalità 4 — Sintesi da Fonte

**Trigger:** richieste tipo "riassumi questo articolo", "cosa dice questa pagina", URL fornito direttamente

### Istruzioni
1. Leggi e analizza l'intera fonte
2. Estrai:
   - Tesi principale (1-2 frasi)
   - Punti chiave (max 7 bullet)
   - Dati e numeri rilevanti con contesto
   - Citazioni dirette significative
   - Implicazioni pratiche per chi sviluppa prodotti tech
3. Segnala bias, limiti metodologici o informazioni non verificabili
4. Suggerisci 2-3 domande di approfondimento

### Output atteso
- **Tesi principale:** [1-2 frasi]
- **Punti chiave:** [bullet list]
- **Dati notevoli:** [lista con contesto]
- **Implicazioni pratiche:** [bullet list]
- **Limiti/Bias:** [se presenti]
- **Domande di approfondimento:** [2-3 domande]

### Note
- Sintesi = densità informativa massima, niente padding
- Output in italiano salvo diversa indicazione
- Se la fonte non è accessibile, segnalarlo subito senza inventare contenuto

---

## Regole Generali
- Rispondi sempre in italiano salvo richiesta diversa
- Cita sempre le fonti con link cliccabili
- Preferisci dati aggiornati (ultimi 12 mesi)
- Niente disclaimer eccessivi o risposte vaghe senza dati concreti


---

## Perplexity + Playwright MCP (Setup 2026-05-06)

**Status**: ✅ Pronto per testare  
**Tipo**: Browser automation MCP server  
**Auth**: Google OAuth (login manuale una volta)  

### Come usarlo

1. Segui [[SETUP_PLAYWRIGHT]] per l'installazione
2. Una volta configurato, avrai accesso al tool `perplexity_search` in Claude Code
3. Queries vanno direttamente a perplexity.ai tramite il tuo browser

### Vantaggi vs precedenti approcci

- ✅ Funziona con Google OAuth (non serve ricavare token scadenti)
- ✅ Nessun reverse engineering (usa l'interfaccia ufficiale)
- ✅ Gratis e senza limiti API
- ✅ Stabile e manutenibile

### File correlati

- `playwright-mcp-server.py` — lo script principale
- `SETUP_PLAYWRIGHT.md` — guida di installazione
