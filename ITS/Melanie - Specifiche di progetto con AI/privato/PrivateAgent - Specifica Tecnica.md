# PrivateAgent — Specifica Tecnica

**Progetto:** PrivateAgent
**Versione:** 1.0
**Data:** 2026-05-06
**Cartella fisica:** `C:\Users\Chirone\Desktop\Progetti\private-agent\`

> Questo documento è il riferimento vincolante per tutte le fasi di sviluppo.

---

## 1. Obiettivi e Perimetro

### Obiettivo principale
Agente AI locale che gestisce file e risorse di sistema, delega task complessi a Claude Code CLI, e sanitizza ogni output tramite privacy-filter prima di restituirlo.

### Il sistema FA:
- Riceve task in linguaggio naturale dall'utente
- Accede a file locali (lettura/scrittura) tramite tool
- Delega generazione di script/codice/estrazioni complesse a Claude Code CLI
- Passa ogni file generato attraverso `opf` (privacy-filter) prima di restituirlo
- Opera con un system prompt restrittivo che limita le azioni permesse

### Il sistema NON FA:
- Esegue codice arbitrario senza conferma utente
- Accede a internet direttamente
- Elimina file (solo lettura/scrittura)
- Bypassa il passaggio di sanitizzazione PII
- Mantiene memoria tra sessioni diverse

### Utenti target
Utente tecnico singolo, Windows 11, che usa il sistema per automatizzare task su file propri con garanzia di privacy.

### Valore prodotto
Riduce il tempo di gestione manuale di file + garantisce che nessun PII sopravviva negli output generati da AI.

---

## 2. Input e Output

### Input accettati

| Campo | Tipo | Formato | Vincoli |
|---|---|---|---|
| `task` | string | linguaggio naturale | max 2000 chars |
| `file_path` | string | path assoluto Windows | deve esistere se specificato |

**Edge case:**
- Task ambiguo → LLM locale chiede chiarimento prima di agire
- File non trovato → errore esplicito, nessuna azione
- Claude Code non disponibile → fallback con messaggio di errore chiaro

### Output attesi

| Campo | Tipo | Formato |
|---|---|---|
| `result` | string | testo plain o path file sanitizzato |
| `sanitized_file` | file | `.tmp/output_[timestamp]_sanitized.txt` |

---

## 3. Requisiti di Qualità

| Metrica | Valore minimo | Metodo |
|---|---|---|
| PII rimossa dall'output | 100% | opf su ogni file generato da Claude Code |
| Latenza task semplice | ≤ 5s | LLM locale risponde direttamente |
| Latenza task complesso | ≤ 60s | include round-trip Claude Code + opf |
| Tasso errori non gestiti | ≤ 5% | try/except su ogni tool call |

---

## 4. Architettura del Sistema

```
[Input utente - terminale]
        ↓
[agent.py — orchestratore Python]
        ↓
[Ollama API localhost:11434]
  modello: llama3.1 (o mistral-nemo)
  system prompt: restrittivo
  tools: read_file, write_file, call_claude_code
        ↓
  ├─ read_file(path) → legge file locale
  ├─ write_file(path, content) → scrive file in .tmp/
  └─ call_claude_code(prompt, output_file)
              ↓
        subprocess: claude -p "prompt"
              ↓
        output salvato in .tmp/raw_[timestamp].txt
              ↓
        subprocess: opf -f .tmp/raw_[timestamp].txt
              ↓
        .tmp/output_[timestamp]_sanitized.txt
              ↓
[Risultato restituito all'utente]
```

### Stack tecnologico

| Layer | Tecnologia | Motivazione |
|---|---|---|
| Linguaggio | Python 3.12 | già nel sistema a 3 livelli |
| LLM locale | Ollama + llama3.1 | tool use nativo, gratuito |
| LLM complesso | Claude Code CLI (`claude -p`) | già installato e pagato |
| Privacy | openai/privacy-filter (`opf`) | già installato |
| Output | `.tmp/` locale | file intermedi, mai committati |

### Dipendenze esterne

| Servizio | Utilizzo | Alternativa se down |
|---|---|---|
| Ollama (`localhost:11434`) | LLM locale orchestratore | errore esplicito all'utente |
| Claude Code CLI (`claude`) | generazione script complessi | task rifiutato con messaggio |
| `opf` CLI | sanitizzazione PII | blocca output, non restituisce file non sanitizzato |

---

## 5. System Prompt Restrittivo (LLM Locale)

```
Sei un agente AI locale con accesso controllato al filesystem.

PUOI fare:
- Leggere file specificati esplicitamente dall'utente
- Scrivere file SOLO nella cartella .tmp/
- Delegare task complessi a Claude Code tramite il tool call_claude_code
- Chiedere chiarimenti prima di agire su file

NON PUOI fare:
- Eseguire comandi di sistema arbitrari
- Eliminare o sovrascrivere file esistenti fuori da .tmp/
- Accedere a internet
- Ignorare il passaggio di sanitizzazione PII
- Agire senza conferma su task che modificano file importanti

Ogni file generato da Claude Code DEVE passare attraverso privacy-filter prima di essere restituito.
Quando non sei sicuro di un'azione, chiedi conferma all'utente.
```

---

## 6. Rischi e Assunzioni

### Assunzioni esplicite
1. Ollama installato e in esecuzione con modello che supporta tool use
2. `claude` disponibile nel PATH (Claude Code installato)
3. `opf` disponibile nel PATH (privacy-filter installato)
4. Python 3.12+ installato

### Rischi identificati

| Rischio | Probabilità | Impatto | Mitigazione |
|---|---|---|---|
| Modello Ollama non supporta tool use | Media | Alto | Testare con `llama3.1` al setup; documentare modelli compatibili |
| Claude Code in modalità interattiva (blocca subprocess) | Media | Alto | Usare flag `-p` / `--print` per modalità non interattiva |
| `opf` non disponibile nel PATH | Bassa | Critico | Check al startup; bloccare esecuzione se assente |
| Output Claude Code troppo grande per `opf` | Bassa | Medio | Chunking del file prima di passarlo a opf |
| LLM locale esegue tool call non autorizzati | Bassa | Alto | System prompt restrittivo + whitelist path nel codice Python |

### Vincoli privacy
- **PII:** nessun output lascia `.tmp/` senza essere passato per `opf`
- **Dati locali:** tutto resta su macchina locale; Claude Code non invia file, solo il prompt testuale

---

## Changelog

| Versione | Data | Modifica |
|---|---|---|
| 1.0 | 2026-05-06 | Prima versione |

---

## Connessioni

- [[Progettistica AI MOC]]
- [[PrivateAgent - Validazione Idea]]
