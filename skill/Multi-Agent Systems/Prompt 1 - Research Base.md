---
name: Prompt 1 - Multi-Agent Research Base
description: Prompt per Perplexity che ricerca pattern, framework e best practices multi-agent
type: skill
sessionId: multi-agent-communication
date: 2026-06-22
---

# Prompt 1: Ricerca Base Multi-Agent Systems

Usa questo prompt su **Perplexity (Focus: Research Web)** per ottenere una mappa operativa di come far lavorare agenti che si coordinano.

---

## Il Prompt

```markdown
[CONTEXT]
Voglio capire come far lavorare insieme **più agenti AI** che comunicano e si coordinano 
tra loro, senza usare piattaforme "all-in-one" come Octogent. Mi interessa come 
costruire architetture dove agenti diversi:
- Si parlano / si passano informazioni tra loro
- Hanno ruoli specializzati e lavorano in sequenza o in parallelo
- Risolvono problemi complessi suddividendoli

[INTENTO]
Trovare: framework, pattern architetturali, tool/librerie, e best practices reali 
per orchestrare **multi-agent systems** in modo decentralizzato o modulare.

[AUDIENCE + OUTPUT]
Sono uno sviluppatore/progettista (non ricercatore puro). Voglio una mappa operativa 
che mi mostri:
1. Approcci principali (con nomi: ReAct, Swarm, CrewAI, langgraph, ecc.)
2. Pro/contro di ciascuno
3. Caso d'uso specifico per ogni pattern (quando usarlo)
4. Tool/librerie open-source più attive

**Formato:** Lista strutturata con tabella + link alle repo/doc ufficiali

[REGOLE]
- Escludi: Octogent, Make.com, Zapier, n8n (sono low-code/no-code, non programmazione)
- Focalizzati su: Python, TypeScript/Node, Go (linguaggi dove l'orchestrazione è in codice)
- Ricerca SOLO fonti aggiornate (ultimi 12 mesi): GitHub star count, commit activity, 
  issue aperti. Se una libreria è morta/abandonate, segnalalo.
- Se trovi paper accademici su multi-agent reinforcement learning (MARL) che siano 
  implementabili in pratica, menzioni brevemente (max 2-3)

[FONTI CONSIGLIATE da cercare esplicitamente]
Per una ricerca completa, consulta questi siti (verificati come leggibili):
- **Hugging Face (huggingface.co)** — cerca "multi-agent frameworks" e "Spaces" per demo live
- **Product Hunt (producthunt.com)** — sezione "AI Agents" per scoprire tool nuovi
- **DEV.to (dev.to)** — cerca tag #agents e #ai per tutorial su orchestrazione
- **Hacker News (news.ycombinator.com)** — ricerca "multi-agent systems" per discussioni tecniche
- **GitHub (github.com)** — baseline per repo code + star rating
- **Reddit (reddit.com/r/MachineLearning, /r/LocalLLM, /r/LanguageTechnology)** — community discussions

[ESEMPI di quello che cerco]
- "CrewAI aiuta a definire agenti con ruoli, task, strumenti condivisi" 
- "LangGraph usa una state machine per coordinare flussi multi-step"
- "Swarm pattern per agenti in handoff (uno completa, chiama il prossimo)"
- Implementazioni reali: chi ha costruito multi-agent systems per chatbot, data-pipeline, 
  customer support, automazione di ricerca?

---

**TL;DR finale:** 
Dammi una **tabella comparativa** (Framework | Linguaggio | Complessità setup | 
Tipo di comunicazione | Quando usarlo) e poi i **3 pattern più consigliati con 
un link a un example repository**.
```

---

## Output atteso da Perplexity

Dovresti ricevere:
- ✅ 7+ pattern architetturali nominati
- ✅ 7+ framework con star count e activity
- ✅ Pro/cons onesti
- ✅ Decision guide
- ✅ Link verificati a repo e doc

Se manca qualcosa di questi, chiedi un follow-up specifico.

---

## Note operative

1. **Copia il prompt** dalla sezione "Il Prompt" sopra
2. **Incolla su Perplexity** con Focus: Research Web attivo
3. **Attendi 1-2 min** che Perplexity cerchi le fonti
4. **Se risposta è generica**, chiedi: *"Show me code examples for LangGraph and CrewAI"*
5. **Salva il risultato** in una nuova nota Obsidian (o nel browser)
