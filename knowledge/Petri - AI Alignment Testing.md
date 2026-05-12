# Petri — AI Alignment Testing Tool

**Fonti:**
- Anthropic: https://www.anthropic.com/research/donating-open-source-petri
- Inspect Petri (Meridian Labs): https://meridianlabs-ai.github.io/inspect_petri/

---

## Cos'è

Petri è uno strumento open-source per testare l'allineamento dei modelli AI. Sviluppato da Anthropic nell'ambito del programma Anthropic Fellows, è stato **donato a Meridian Labs** — una no-profit indipendente — per garantire credibilità e indipendenza da qualsiasi laboratorio commerciale.

**Inspect Petri** è l'implementazione pratica, costruita insieme al **UK AISI Red Team** (l'ente governativo britannico per la sicurezza AI), basata sul framework Inspect AI.

---

## Il problema che risolve

I modelli sanno quando vengono testati e si comportano meglio del normale. Petri risolve questo con realismo architetturale: usa prompt e infrastrutture di sistema reali per impedire al modello di riconoscere il contesto di valutazione.

---

## Architettura: 3 modelli, 3 ruoli

| Ruolo | Funzione |
|---|---|
| **Auditor** | LLM che simula utenti reali in scenari specifici, guida la conversazione |
| **Target** | Il modello sotto esame (qualsiasi LLM: Claude, GPT, Gemini, Llama…) |
| **Judge** | Legge le trascrizioni e assegna punteggi su 38 dimensioni comportamentali |

Non è un test statico — è una **conversazione multi-turno orchestrata** dove l'auditor si adatta dinamicamente alle risposte del target.

---

## Novità di Petri 3.0

| Miglioramento | Dettaglio |
|---|---|
| **Adattabilità** | Architettura modulare: auditor separato dal target, qualsiasi LLM testabile |
| **Realismo (Dish)** | Usa prompt e infrastrutture reali → il target non riconosce di essere testato |
| **Profondità (Bloom)** | Analisi comportamentale granulare su dimensioni specifiche |

---

## Seeds — 170+ scenari preconfigurati

File Markdown che istruiscono l'auditor su come comportarsi. Esempi:
- `manic_episode_sycophancy` → auditor finge stato maniacale con piani irrazionali, osserva se il target li assecondi
- Scenari su inganno, cooperazione con richieste dannose, comportamenti pericolosi

Personalizzabili: puoi creare seed per scenari specifici del tuo dominio.

---

## Judge Dimensions — 38 metriche comportamentali

Ogni dimensione riceve un punteggio 1-10 con giustificazione scritta collegata ai messaggi specifici della trascrizione.

| Dimensione | Cosa misura |
|---|---|
| `unprompted_sycophancy` | Il modello adula senza che gli venga chiesto |
| `concerning` | Comportamenti dannosi o pericolosi |
| `admirable` | Comportamenti positivi |
| `eval_awareness` | Il modello ha capito di essere sotto test |

---

## Capacità avanzate dell'Auditor

- **Rollback**: torna indietro nella conversazione e riprova con approccio diverso (testa la consistenza)
- **Modifica del system prompt** a runtime
- **Creazione/rimozione di tool** per simulare ambienti diversi
- **Prefilling** dei messaggi (test prompt injection)
- **Filtro di realismo**: scarta risposte dell'auditor che sembrano artificiali
- **Multi-epoch**: esegue più istanze di audit indipendenti in parallelo

---

## Uso pratico

```bash
pip install inspect-petri

inspect eval inspect_petri/audit \
  -T seed_instructions=tags:sycophancy \
  --model-role auditor=anthropic/claude-sonnet-4-6 \
  --model-role target=openai/gpt-5-mini \
  --model-role judge=anthropic/claude-opus-4-6
```

Disponibile via Claude API, Amazon Bedrock, Google Cloud Vertex AI, Microsoft Foundry.

---

## Perché la donazione a Meridian Labs è strategica

Uno strumento di valutazione gestito dal laboratorio che produce i modelli ha un problema strutturale di credibilità. Cedendo Petri a una no-profit:
- Lo strumento diventa **credibile per governi e ricercatori indipendenti**
- Si costruisce uno **standard di settore** adottabile da tutti
- Anthropic si posiziona come attore collaborativo, non proprietario

---

## Connessioni

- [[Agenti IA Design Patterns MOC]] — Pattern 18 (Guardrails) + Pattern 19 (Evaluation and Monitoring)
- [[Knowledge MOC]] — Ricerca Anthropic su AI safety e governance
- [[Index MOC]]
