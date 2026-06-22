---
name: Prompt 3 - Production Patterns
description: Prompt per Perplexity su checkpoint, rate limiting, cost tracking per production
type: skill
sessionId: multi-agent-communication
date: 2026-06-22
---

# Prompt 3: Production Patterns (Checkpoint, Rate Limiting, Cost Tracking)

Questo prompt colma i gap che rimangono quando passi da POC a produzione.

---

## Il Prompt

```markdown
[CONTEXT]
I'm deploying a production multi-agent system (LangGraph or CrewAI). The basic patterns 
work, but I'm missing 3 critical operational concerns:

1. **Checkpoint Recovery** — If the server crashes during a 7-step workflow at step 4, 
   how do I resume from step 5? Postgres vs Redis trade-offs? How to migrate if DB dies?

2. **Rate Limiting** — If 10 users request simultaneously with 5 agents each (50 concurrent 
   LLM calls), I'll hit OpenAI rate limits (429 errors) or burn quota. How to queue/throttle 
   to max 10 LLM calls/sec?

3. **Cost Control** — LLMs are expensive. How do I track spend per agent per day? Alert 
   when approaching budget? Hard-block when budget exceeded?

[INTENT]
Show me **production-ready code + architecture** for all 3 with real failure scenarios.

[AUDIENCE + OUTPUT]
I'm deploying in 1-2 weeks. I need:
1. **Checkpoint Strategy** — Code for Postgres vs Redis; migration strategy if primary DB dies
2. **Rate Limiter** — Semaphore implementation with exact limits (max 10/sec)
3. **Cost Gate** — Per-agent spend tracking, alerts (50%, 80%, 100%), hard-block at limit

**Format:** Python code, decision table, architecture diagram if available

[RULES]
- Exclude: Vague advice ("use Redis"). Include: exact class names, import statements, config.
- Exclude: Toy code. Include: production patterns with atomic operations, race condition prevention.
- Include: How to test each pattern (what if checkpoint DB crashes? What if cost hits 100%?)
- Include: Monitoring/alerting (Slack, PagerDuty, Datadog hooks)

[SOURCES to check]
- **GitHub** — search "langgraph checkpoint" + "crewai redis" for real implementations
- **LangGraph docs** — PostgresSaver, RedisSaver, checkpoint recovery
- **CrewAI docs** — memory persistence, async handling
- **Stack Overflow** — "langgraph resume after crash", "rate limit concurrent llm calls"
- **Production blogs** — patterns from Anthropic, OpenAI, LangChain teams

[EXAMPLES of what I'm looking for]
- "PostgresSaver auto-creates schema, survives 1000 runs/day without breaking"
- "Use asyncio.Semaphore(10) to limit concurrent LLM calls"
- "Redis INCRBYFLOAT is atomic, prevents race condition when 5 workers increment cost simultaneously"
- "If cost hits 100%, raise BudgetExceededError before making LLM call (fail fast)"
- Real scenario: "Server crashed at run 847, checkpoint persisted, resumed seamlessly on restart"

---

**TL;DR final output:**
- 3 code snippets (Postgres checkpoint, rate limiter, cost gate)
- 1 migration strategy if checkpoint DB dies
- 1 architecture diagram (checkpoint → state → cost → monitoring)
- 1 decision table: "Postgres vs Redis", "asyncio vs aiometer vs Redis semaphore"
- 1 test scenario: "Crash at step 4/7, resume, verify cost recorded correctly"
```

---

## Output atteso da Perplexity

Dovresti ricevere:
- ✅ PostgresSaver + RedisSaver con esempi
- ✅ Migration strategy (backup → restore)
- ✅ asyncio.Semaphore e aiometer examples
- ✅ Redis CostGate con alerts
- ✅ Decision table per scegliere backend
- ✅ Test pattern per recovery

---

## Note operative

1. **Prerequisito:** Completa Prompt 2 prima di questo
2. **Copia il prompt**, incolla su Perplexity
3. **Focus:** Chiedi code concreto, non theory
4. **Se manca migration strategy**, chiedi: *"Show me exact steps to backup Postgres to Redis, then restore to new Postgres instance"*
5. **Testa ogni pattern** prima di commit
