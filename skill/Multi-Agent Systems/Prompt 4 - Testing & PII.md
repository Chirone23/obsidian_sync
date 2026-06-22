---
name: Prompt 4 - Testing & PII Masking
description: Prompt per Perplexity su unit testing multi-agent flows e masking PII in logs
type: skill
sessionId: multi-agent-communication
date: 2026-06-22
---

# Prompt 4: Testing & PII Masking

Prima di andare in produzione, devi testare il sistema e proteggere i dati sensibili nei log.

---

## Il Prompt

```markdown
[CONTEXT]
I'm finalizing a production multi-agent system (LangGraph or CrewAI) before deploying. 
I need:

1. **Unit Testing** — How do I test a multi-agent flow without making real LLM API calls? 
   How to mock agents, test partial flows, test routing logic?

2. **PII Masking** — When logs go to Slack/logging service, they contain user emails, 
   phone numbers, names, API keys. How do I redact them automatically before sending?

[INTENT]
Show me **production-ready testing patterns + PII masking code** that I can use immediately.

[AUDIENCE + OUTPUT]
I'm a developer shipping in 1 week. I need:
1. **Unit Test Pattern** — Code for pytest + fake LLM + fixtures; test isolated nodes; 
   test full flow; test error scenarios
2. **PII Masking** — Regex patterns for email/phone/SSN/CC/name/API keys; logging handler 
   that masks before emitting to Slack

**Format:** Python code (pytest, unittest.mock), masking patterns, decision table

[RULES]
- Exclude: Mocking best practices essays. Include: exact code I can copy-paste.
- Exclude: Theoretical PII patterns. Include: tested regex (false positive rate acceptable?)
- Include: How to test the masking itself (verify regex works on known PII)
- Include: Kubernetes/Docker integration if logs go to external service

[SOURCES to check]
- **LangGraph docs** — testing guide, fake LLM, InMemorySaver
- **CrewAI docs** — testing patterns
- **GitHub** — search "langgraph unit test" + "crewai mock" for real test suites
- **PII libraries** — pii-sentry-py, pii-redactor, Presidio (Microsoft)
- **Python logging** — custom handlers, formatters, Slack integration

[EXAMPLES of what I'm looking for]
- "Use GenericFakeChatModel to test without API calls"
- "Use InMemorySaver for checkpoint in tests (no Postgres needed)"
- "Test routing logic by mocking nodes and checking which was called"
- "Mask emails with regex [a-z]+@[a-z]+\.[a-z]+ → [REDACTED:email]"
- "Use custom logging handler to mask before Slack webhook"
- Real scenario: "Log contains 'Contact john@example.com', webhook receives 'Contact [REDACTED:email]'"

---

**TL;DR final output:**
- 6 unit test code examples (full flow, single node, partial, routing, patch, error)
- 1 pytest fixtures conftest.py (fake LLM, graph, state)
- 1 PII masker class (regex patterns, logging handler integration)
- 1 Slack webhook integration with masking
- 1 Kubernetes sidecar pattern (log interception)
- 1 test pattern for masking validation (staging environment)
```

---

## Output atteso da Perplexity

Dovresti ricevere:
- ✅ GenericFakeChatModel example
- ✅ InMemorySaver for test persistence
- ✅ 6 pytest test cases
- ✅ Fixture pattern con conftest.py
- ✅ PII patterns (email, phone, SSN, CC, name, API key, JWT)
- ✅ Custom logging handler con masking
- ✅ Slack webhook integration
- ✅ K8s sidecar definition

---

## Note operative

1. **Prerequisito:** Completa Prompt 3 prima di questo
2. **Copia il prompt**, incolla su Perplexity
3. **Focus:** Chiedi code pronto per pytest, non spiegazioni
4. **Se manca masking per tipo specifico**, chiedi: *"Add regex pattern for JWT tokens and credit cards"*
5. **Testa masking in staging** con dati reali prima di andare in produzione
