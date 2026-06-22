---
name: Multi-Agent Systems Research & Implementation
description: Mappa per ricercare e implementare sistemi multi-agente con LangGraph/CrewAI
type: skill
originSessionId: current
---

# 🤖 Multi-Agent Systems MOC

Guida operativa per ricercare, progettare e implementare sistemi dove agenti AI si coordinano e comunicano.

**Framework:** C.I.A.R.E. (Riccardo Raponi) applicato al domain multi-agent

**Stack validato (2026):**
- **Frameworks:** LangGraph (state machine), CrewAI (role-based), AutoGen (multi-conversation)
- **Orchestration:** Python + Redis + Postgres
- **Infrastructure:** Checkpoint durability, rate limiting, cost tracking, PII masking

---

## 📋 Artifacts

### Fase 1: Ricerca Base
- [[Prompt 1 - Research Base]] — Ricerca su pattern architetturali, framework, best practices
  - Fonti consigliate: Hugging Face, Product Hunt, Dev.to, Hacker News, GitHub, Reddit
  - Output: Mappa operativa con decision guide

### Fase 2: Code Examples
- [[Prompt 2 - Code Examples]] — Code production-ready per 5 gap operativi
  1. Agent Communication Protocol (state dict, task result chaining)
  2. Context Sharing (3 approcci: inline, sliding window, vector lookup con token cost)
  3. Observability & Debugging (LangSmith, OpenTelemetry, Slack)
  4. Error Handling (timeout, LLM refusal, format validation, infinite loop)
  5. Event-Driven (Redis producer/consumer, Celery)

### Fase 3: Production Patterns
- [[Prompt 3 - Production Patterns]] — Infrastruttura production-grade
  1. Checkpoint Backends (Postgres vs Redis + migration strategy)
  2. Rate Limiting (asyncio.Semaphore, aiometer, Redis)
  3. Cost Tracking (per-agent spend, alerts 50%/80%/100%, hard-block)

### Fase 4: Testing & Security
- [[Prompt 4 - Testing & PII]] — Quality assurance
  1. Unit Testing (LangGraph + fake LLM + fixtures, CrewAI + mocks)
  2. PII Masking (regex patterns, logging handler, Slack integration, K8s sidecar)

### Blueprint Architetturale
- [[Blueprint - Architecture]] — Stack completo production-ready
  - Checkpoint (Postgres) + Context (Redis) + Cost (Redis) + Logging (Slack + masking)
  - Decision table per ogni component

---

## 🎯 Come usare questa cartella

1. **Ricerca:** Copia [[Prompt 1 - Research Base]] su Perplexity, aggiungi domande specifiche
2. **Implementazione:** Segui [[Prompt 2 - Code Examples]] per code patterns di base
3. **Production:** Integra [[Prompt 3 - Production Patterns]] per checkpoint/rate/cost
4. **QA:** Testa con [[Prompt 4 - Testing & PII]] prima di commit
5. **Riferimento:** Consulta [[Blueprint - Architecture]] per decisioni architetturali

---

## 📊 Stack Decision Table

| Componente | Scelta | Quando usarlo | Link |
|------------|--------|---------------|------|
| **Graph Orchestration** | LangGraph | Deterministic multi-step, state machine, debugging | [[Prompt 2 - Code Examples]] |
| **Agent Roles** | CrewAI | Role-based team (researcher, writer, analyst) | [[Prompt 2 - Code Examples]] |
| **Checkpoint** | Postgres | Production (ACID, durability) | [[Prompt 3 - Production Patterns]] |
| **Cache** | Redis | Hot cache, rate limiter, cost tracking | [[Prompt 3 - Production Patterns]] |
| **Rate Limit** | asyncio.Semaphore | In-process; aiometer per precisione 10/sec | [[Prompt 3 - Production Patterns]] |
| **Cost Gate** | Redis CostGate | Per-agent spend, alert 50%/80%/100% | [[Prompt 3 - Production Patterns]] |
| **Testing** | pytest + fake LLM | GenericFakeChatModel + InMemorySaver | [[Prompt 4 - Testing & PII]] |
| **PII Masking** | pii-sentry-py | Zero-dep; custom PIIMasker per pattern custom | [[Prompt 4 - Testing & PII]] |

---

## 🔗 Connessioni

- [[Prompting MOC]] (Riccardo Raponi) — Framework C.I.A.R.E., best practices prompting
- [[Agenti IA Design Patterns MOC]] — Pattern di orchestrazione
- [[Knowledge MOC]] — Fondamenti teorici AI
- n8n integration — Se usi n8n per flow, vedi [[skill/n8n Roadmap 2026_ Guida Pratica.pdf]]

---

## 📅 Status

- ✅ Ricerca base completata (2026-06-22)
- ✅ Code examples validati (2026-06-22)
- ✅ Production patterns testati (2026-06-22)
- ✅ Testing + PII implementati (2026-06-22)
- ⏳ Ready to integrate in projects

---

**Ultimo aggiornamento:** 2026-06-22
