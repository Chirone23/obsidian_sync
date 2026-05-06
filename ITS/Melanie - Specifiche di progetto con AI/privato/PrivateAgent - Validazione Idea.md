# PrivateAgent — Validazione Idea

**Corso:** AI Projects Development · [[Progettistica AI MOC]]
**Cartella:** `ITS/Melanie - Specifiche di progetto con AI/privato/`
**Data validazione:** 2026-05-06
**Stato:** Concept validato

---

## L'Idea

**Nome provvisorio:** PrivateAgent
**Problema:** Chi vuole usare un LLM locale per gestire file e risorse di sistema si trova davanti a due estremi — modelli piccoli che non sanno generare codice complesso, o modelli cloud che vedono dati potenzialmente sensibili. Non esiste un sistema che combina privacy locale + potenza cloud in modo controllato.
**Soluzione:** Un agente locale (Ollama) con system prompt restrittivo che ha accesso al filesystem. Quando il task è troppo complesso, delega a Claude Code CLI. L'output generato passa sempre attraverso `privacy-filter` prima di essere usato o restituito — garantendo che nessun dato personale sopravviva nel file finale.
**Target:** Utente tecnico singolo che gestisce dati propri o di clienti e vuole automazione AI senza esporre PII a servizi cloud.
**Angolo difendibile:** Nessun tool esistente combina routing intelligente locale/cloud + sanitizzazione automatica PII in un unico pipeline controllato dall'utente.

---

## Architettura ad alto livello

```
Utente
  ↓
LLM Locale (Ollama) — system prompt restrittivo
  ├─ task semplice → risponde direttamente
  └─ task complesso → claude -p "prompt" [subprocess]
                          ↓
                   file generato in .tmp/
                          ↓
                   opf -f file (privacy-filter)
                          ↓
                   file sanitizzato → restituito
```

---

## Validazione 5 Dimensioni

| Dimensione | Esito | Note |
|---|---|---|
| **Tecnica** | ✅ | Stack maturo: Ollama API + subprocess Python + opf CLI. Nessuna dipendenza cloud obbligatoria |
| **Economica** | ✅ | Costo zero per uso locale. Claude Code già pagato dall'utente. Nessun costo aggiuntivo |
| **Complessità** | ✅ | MVP fattibile in pochi giorni: un singolo script Python con tool use Ollama |
| **Rischio e Compliance** | ✅ | Privacy by design: PII rimossa prima di ogni output. Nessun dato sensibile lascia la macchina senza sanitizzazione |
| **Sostenibilità tecnologica** | ✅ | Ogni componente sostituibile: Ollama → LM Studio, Claude Code → altro CLI, opf → altro filtro |

**Esito complessivo: idea validata** — sistema semplice, componenti già installati, zero costi aggiuntivi.

---

## Perimetro MVP

**In scope:**
- ✅ LLM locale (Ollama) come orchestratore con tool use
- ✅ Tool `call_claude_code(prompt)` → spawna `claude -p` come subprocess
- ✅ Ogni output di Claude Code passa automaticamente per `opf`
- ✅ Tool `read_file(path)` e `write_file(path, content)`
- ✅ System prompt restrittivo (no esecuzione arbitraria di codice, no network, no delete)
- ✅ Output finale sempre in `.tmp/` con nome file esplicito

**Fuori scope MVP:**
- ❌ Interfaccia grafica / web UI
- ❌ Memoria persistente tra sessioni
- ❌ Multi-utente
- ❌ Tool per accesso a internet / browser
- ❌ Logging avanzato / dashboard

---

## Assunzioni da dichiarare

1. Ollama installato e funzionante con un modello che supporta tool use (es. `llama3.1`, `mistral-nemo`)
2. Claude Code installato e nel PATH (`claude` disponibile come comando)
3. `opf` installato e nel PATH
4. L'utente sa che i file in `.tmp/` possono contenere output intermedi non sanitizzati

---

## Connessioni

- [[Progettistica AI MOC]]
- [[PrivateAgent - Specifica Tecnica]]
