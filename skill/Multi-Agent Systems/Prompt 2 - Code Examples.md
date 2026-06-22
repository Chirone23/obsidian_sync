---
name: Prompt 2 - Multi-Agent Code Examples
description: Prompt per Perplexity che richiede code production-ready per 5 gap operativi
type: skill
sessionId: multi-agent-communication
date: 2026-06-22
---

# Prompt 2: Code Examples Production-Ready

Dopo la ricerca base, usa questo prompt per ottenere **code examples concreti** che colmano i gap critici.

---

## Il Prompt

```markdown
[CONTEXT]
I'm building a production multi-agent system using either **LangGraph** or **CrewAI**. 
I've identified the main architectural patterns (sequential, hub-spoke, swarm, etc.) 
but I'm missing **operational/production concerns** that will break me in production:

1. **Agent Communication Protocol** — How do agents actually exchange data? Message format, 
   serialization, state passing between agents?
2. **Context Sharing** — How do I pass domain context (user history, API keys, embeddings, 
   retrieved documents) efficiently across agents without token waste?
3. **Observability & Debugging** — When a multi-agent flow fails at step 4 of 7, how do 
   I see which agent broke, what it received/produced, and why?
4. **Error Handling & Retry** — Circuit breakers, fallbacks, exponential backoff patterns 
   specific to multi-agent orchestration
5. **Event-Driven Patterns** — Real implementation (not abstract "Redis/NATS"). What library? 
   What's the boilerplate?

[INTENT]
Show me **code examples + architecture diagrams + link to real open-source projects** 
for each of these 5 gaps, focused on **LangGraph** and **CrewAI** since those are 
my finalists.

[AUDIENCE + OUTPUT]
I'm an engineer shipping to production in 2-3 weeks. I need:
1. **Agent Communication Pattern** — Code snippet (Python): how does Agent A send data to Agent B?
2. **Context Engineering** — Show 3 approaches (inline context, vector store lookup, state dict) 
   with trade-offs (token cost, latency, accuracy)
3. **Logging + Tracing** — How to instrument LangGraph/CrewAI for production debugging (e.g., 
   which agent ran, what it called, how long)
4. **Error Handling Cookbook** — 4 scenarios: agent timeout, API rate limit, LLM refusal, 
   wrong output format. How to recover?
5. **Event-Driven Real Example** — Link to an open-source multi-agent project using 
   [Temporal | Kafka | Celery | other]. What does it look like?

**Format:** Code blocks (Python), GitHub repo links, comparison table for context strategies, 
architecture diagrams if available

[RULES]
- Exclude: Abstract theory, toy examples. Only production-tested patterns.
- Include: Specific library names (e.g., "use Loguru for structured logging", "use Pydantic 
  for message validation")
- If code example is from a real repo, link to the exact file on GitHub
- For context strategies, show token cost (e.g., "inline: 500 tokens, vector lookup: 100 tokens")
- Flag which approach works better for LangGraph vs. CrewAI (they differ)

[SOURCES to check explicitly]
- **GitHub** — search "langgraph production" + "crewai observability" for real projects using them
- **Dev.to** — search "LangGraph logging" + "CrewAI error handling" for tutorials
- **Hugging Face** — search "multi-agent example" in Spaces for live demos with source code
- **Reddit** — r/MachineLearning, r/LocalLLM for "what broke in my multi-agent" war stories
- **Official Docs** — LangGraph docs on state/streaming/debugging, CrewAI docs on async/error handling

[EXAMPLES of what I'm looking for]
- "In LangGraph, use `stream()` instead of `invoke()` to see agent reasoning step-by-step"
- "CrewAI stores task results in memory.json — wrap it with structured logging to debug"
- "For context: embed user history at startup, pass vector ID to each agent instead of 
  full text (cheaper)"
- Real repo: "Check out GitHub org X/multi-agent-framework-benchmark — they instrument 
  every agent call with OpenTelemetry"

---

**TL;DR final output:**
- 5 code snippets (one per gap)
- 1 comparison table for context strategies
- Top 3 open-source projects using LangGraph/CrewAI in production (with link to their 
  error-handling / logging code)
- 1 decision table: "If you need X, use pattern Y from framework Z"
```

---

## Output atteso da Perplexity

Dovresti ricevere:
- ✅ Code snippet per state dict passing (LangGraph)
- ✅ Code snippet per task result chaining (CrewAI)
- ✅ 3 approcci per context con token cost
- ✅ Code per LangSmith + OpenTelemetry
- ✅ 4 error scenarios con recovery patterns
- ✅ Redis producer/consumer o Celery example
- ✅ 3 repo open-source validati

---

## Note operative

1. **Prerequisito:** Completa Prompt 1 prima di questo
2. **Copia il prompt**, incolla su Perplexity
3. **Aspetta 2-3 min** che cerchi repo e doc
4. **Se manca code specifico**, chiedi: *"Show me exact code for LangGraph state dict passing with TypedDict"*
5. **Salva tutto** come reference per implementazione
