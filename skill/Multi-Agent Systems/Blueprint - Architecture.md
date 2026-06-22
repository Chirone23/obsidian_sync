---
name: Blueprint - Complete Multi-Agent Architecture
description: Stack production-ready completo con tutti i componenti integrati
type: skill
sessionId: multi-agent-communication
date: 2026-06-22
---

# 🏗️ Blueprint: Complete Multi-Agent System Architecture

Stack production-ready con checkpoint durability, rate limiting, cost tracking, testing, PII masking.

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                         User Request                                 │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                    ┌────────▼──────────┐
                    │  FastAPI/Webhook  │
                    │  (HTTP Ingress)   │
                    └────────┬──────────┘
                             │
                    ┌────────▼──────────────────────┐
                    │   Rate Limiter (Semaphore)    │
                    │  Max 10 concurrent LLM calls  │
                    └────────┬──────────────────────┘
                             │
                    ┌────────▼──────────────────────┐
                    │   Cost Gate (Redis)           │
                    │  Check budget before call     │
                    └────────┬──────────────────────┘
                             │
        ┌────────────────────┼────────────────────┐
        │                    │                    │
   ┌────▼────┐         ┌────▼────┐         ┌────▼────┐
   │ Agent 1  │         │ Agent 2  │         │ Agent N  │
   │(LLM)     │         │(LLM)     │         │(LLM)     │
   └────┬────┘         └────┬────┘         └────┬────┘
        │                    │                    │
        └────────────────────┼────────────────────┘
                             │
        ┌────────────────────┴────────────────────┐
        │                                         │
   ┌────▼────────────────┐        ┌──────────────▼────┐
   │  LangGraph/CrewAI   │        │  Cost Tracking    │
   │  State Management   │        │  (Redis INCRBYFLOAT)
   └────┬────────────────┘        └──────────────┬────┘
        │                                        │
        │  ┌───────────────────────────────────┐ │
        │  │   Checkpoint (Postgres)           │ │
        │  │   Durability, ACID, Recovery      │ │
        │  └───────────────────────────────────┘ │
        │                                        │
   ┌────▼────────────────┐        ┌──────────────▼────┐
   │   Observability     │        │   Logging Handler │
   │   (LangSmith +      │        │   (PII Masking)   │
   │   OpenTelemetry)    │        └──────────────┬────┘
   └────┬────────────────┘                       │
        │                                        │
        └────────────────────┬───────────────────┘
                             │
                    ┌────────▼──────────┐
                    │  Slack Webhook    │
                    │  (Masked logs)    │
                    └───────────────────┘
```

---

## Stack Components

### 1. Orchestration Layer

| Componente | Scelta | Perché | Config |
|------------|--------|--------|--------|
| **Graph Engine** | LangGraph | State machine, deterministico, debugging facile | `graph.compile(checkpointer=PostgresSaver())` |
| **Agent Framework** | CrewAI (alternativa) | Role-based, semplice per team definiti | `Crew(agents=[...], tasks=[...])` |
| **State Schema** | TypedDict (LangGraph) | Type safety, validazione | `class State(TypedDict): messages: list` |

---

### 2. Infrastructure Layer

#### Checkpoint Backend (Durability)

```python
from langgraph.checkpoint.postgres import PostgresSaver
from langgraph.checkpoint.redis import RedisSaver

# Production: Postgres per durability
CHECKPOINT_DB = PostgresSaver.from_conn_string("postgresql://...")
graph = graph.compile(checkpointer=CHECKPOINT_DB)

# High-throughput: Redis per speed
CHECKPOINT_CACHE = RedisSaver("redis://localhost:6379")
```

**Trade-off Table:**
| Backend | Latency | Durability | Concurrent | Failover | Quando usare |
|---------|---------|------------|-----------|----------|--------------|
| Postgres | 10-50ms | ✅ ACID | 1000+ runs/day | Manual restore | Production, multi-day flows |
| Redis | <1ms | ⚠️ AOF/RDB | High | Backup to Postgres | Chat agents, read-heavy |
| SQLite | <5ms | ✅ Local | Single | None | Dev/testing |

#### Rate Limiting (Concurrency Control)

```python
import asyncio
from threading import Semaphore

# In-process: asyncio.Semaphore (simple)
LLM_SEMAPHORE = asyncio.Semaphore(10)

# In-process sync: threading.Semaphore
SYNC_SEMAPHORE = Semaphore(10)

# Distributed: Redis semaphore (multiple servers)
from self_limiters import RedisSemaphore
REDIS_SEMAPHORE = RedisSemaphore(
    redis_client=redis.Redis(),
    name="llm_calls",
    max_capacity=10
)
```

**Decision:**
- Single server? → asyncio.Semaphore
- Multiple workers? → Redis semaphore
- Precise 10/sec? → aiometer.run_on_each(max_per_second=10)

#### Cost Tracking (Budget Gate)

```python
from datetime import datetime
import redis

class CostGate:
    def __init__(self, redis_client: redis.Redis, daily_budget_usd: float):
        self.redis = redis_client
        self.budget = daily_budget_usd
    
    def record_cost(self, agent_id: str, tokens_in: int, tokens_out: int) -> float:
        key = f"llm_cost:{agent_id}:{datetime.utcnow().strftime('%Y-%m-%d')}"
        cost = (tokens_in / 1000) * 0.03 + (tokens_out / 1000) * 0.06  # GPT-4 rates
        
        # Atomic increment (thread-safe, distributed-safe)
        current_cost = self.redis.incrbyfloat(key, cost)
        
        # Alert escalation
        if current_cost >= self.budget:  # 100%
            raise BudgetExceededError(f"${current_cost:.2f} / ${self.budget}")
        elif current_cost >= self.budget * 0.8:  # 80%
            alert_slack(f"⚠️ {agent_id} at 80% budget")
        elif current_cost >= self.budget * 0.5:  # 50%
            log_milestone(f"📊 {agent_id} at 50% budget")
        
        return cost
```

---

### 3. Observability Layer

#### Logging + Tracing

```python
# LangGraph: LangSmith + OpenTelemetry
from langsmith import Client
os.environ["LANGSMITH_API_KEY"] = "your-key"

# CrewAI: Built-in tracing
crew = Crew(
    agents=[...],
    tasks=[...],
    tracing=True,  # Enable tracing
    verbose=True   # Step-by-step logs
)
```

#### PII Masking (Before Slack)

```python
import re
import logging

class PIIMasker:
    PATTERNS = {
        "email": r'[a-z]+@[a-z]+\.[a-z]+',
        "phone": r'\b\d{3}-\d{3}-\d{4}\b',
        "ssn": r'\b\d{3}-\d{2}-\d{4}\b',
        "cc": r'\b\d{4}[\d\s-]{12,16}\d\b',
    }
    
    def redact(self, text: str) -> str:
        masked = text
        for pii_type, pattern in self.PATTERNS.items():
            masked = re.sub(pattern, f"[REDACTED:{pii_type}]", masked)
        return masked

class SlackHandler(logging.Handler):
    def __init__(self, slack_url: str, masker: PIIMasker):
        super().__init__()
        self.slack_url = slack_url
        self.masker = masker
    
    def emit(self, record):
        masked_msg = self.masker.redact(record.getMessage())
        # Send to Slack
        requests.post(self.slack_url, json={"text": masked_msg})
```

---

## Deployment Architecture

### Development Stack
```
Local Postgres (checkpoint) → Local Redis (cache, cost) → Slack (logs)
pytest + fake LLM → no API calls → fast tests
```

### Production Stack
```
RDS Postgres (checkpoint) → ElastiCache Redis (cache, cost, rate limit) → Slack (logs)
LLM APIs (OpenAI, Anthropic) → Monitored by LangSmith → PII masked before logging
```

### Kubernetes Pod Spec

```yaml
apiVersion: v1
kind: Pod
metadata:
  name: multi-agent-worker
spec:
  containers:
  - name: agent
    image: multi-agent:v1.0
    env:
    - name: POSTGRES_URL
      valueFrom:
        secretKeyRef:
          name: db-secrets
          key: postgres-url
    - name: REDIS_URL
      value: redis://redis-service:6379
    - name: SLACK_WEBHOOK_URL
      valueFrom:
        secretKeyRef:
          name: slack-secrets
          key: webhook-url
    resources:
      limits:
        memory: "2Gi"
        cpu: "1"
  
  # Log masking sidecar
  - name: log-masker
    image: log-masker:1.0
    volumeMounts:
    - name: log-volume
      mountPath: /var/log
    env:
    - name: MASK_PII
      value: "true"
```

---

## Decision Tables

### Which Checkpoint Backend?

| Use Case | Backend | Config |
|----------|---------|--------|
| Multi-day workflows, human approval | Postgres | `PostgresSaver.from_conn_string(...)` |
| Chat agents, fast iteration | Redis | `RedisSaver("redis://...")` |
| Dev/test, no persistence | SQLite | `SqliteSaver("./checkpoints.db")` |
| Database fails, need recovery | Postgres + Redis backup | Restore from Redis, migrate to new Postgres |

### Which Rate Limiter?

| Scenario | Limiter | Code |
|----------|---------|------|
| Single server, simple | asyncio.Semaphore | `Semaphore(10)` |
| Exact 10/sec | aiometer | `aiometer.run_on_each(max_per_second=10)` |
| Multiple servers | Redis semaphore | `RedisSemaphore(...)` |

### Which PII Masking?

| Need | Library | Code |
|------|---------|------|
| Built-in patterns | pii-sentry-py | `redact(text)` |
| Custom org patterns | pii-redactor | `PIIRedactor(config)` |
| DIY + control | Custom regex | `PIIMasker` class above |

---

## Checklist: Before Shipping

- [ ] Tests run green: `pytest tests/ -v`
- [ ] Fake LLM works: no API calls in tests
- [ ] Checkpoint recovery tested: kill Postgres, resume from Redis backup
- [ ] Rate limiter tested: 10 concurrent calls, 11th waits
- [ ] Cost gate tested: budget alert at 50%, block at 100%
- [ ] PII masking tested: Slack logs contain no emails/phone
- [ ] Tracing enabled: LangSmith sees all agent calls
- [ ] Error handling: timeout, refusal, format validation, infinite loop all have recovery
- [ ] Monitoring: Slack alerts for critical failures
- [ ] Documentation: team knows how to debug, how to adjust budget, how to scale

---

## References

- [[Prompt 1 - Research Base]] — Framework discovery
- [[Prompt 2 - Code Examples]] — Agent communication, context, observability, error handling
- [[Prompt 3 - Production Patterns]] — Checkpoint, rate limiting, cost tracking
- [[Prompt 4 - Testing & PII]] — Unit testing, PII masking

---

**Status:** Production-Ready (2026-06-22)
