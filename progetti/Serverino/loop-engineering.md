# Serverino — Loop Engineering

**Data:** 2026-06-24
**Status:** 🟡 Design condiviso, pre-implementazione
**Scopo:** Definire come il Serverino esegue *loop closed* (genera → verifica → correggi → ripeti) avviati dall'utente, ricostruendo il concetto di "loop engineering" in Python puro fuori da Claude Code.

> Collega: [[progetti/Serverino/DEFINIZIONE_ASSISTENTE]] • [[progetti/Serverino/SPECS]] • [[progetti/Serverino/bot-architecture]] • [[moc/Index MOC]]

---

## 1. Inquadramento — perché combacia col 2.5

La "loop engineering" descrive loop **autonomi**. Il Serverino è livello **2.5** (DEFINIZIONE §5): niente goal-seeking, niente azioni a sorpresa. I due si incontrano sul **closed loop, singolo agente**, che la guida stessa indica come punto di partenza obbligato.

- **Open loop** (esplorazione libera) → ❌ viola §5, brucia token su CPU 6W.
- **Worktree / flotta di agenti** → ❌ roba da coding parallelo, non serve a un bot Telegram.
- **Claude come motore** → ❌ qui il "agente" di ogni giro è una chiamata a **DeepSeek-v4-flash**.
- **Closed loop avviato da comando** → ✅ è il design qui sotto.

Un "loop" qui = **un mini-ciclo Python `genera → verifica → correggi` attorno a chiamate DeepSeek**, avviato da un comando, con condizione di stop e budget fissati a monte.

---

## 2. Trigger — comando `/loop`

Il loop è **reattivo**, lo avvia l'utente. Non è una task schedulata silenziosa.

Due vie di attivazione:

```
A) Comando esplicito:   /loop [--gruppo <nome>] <obiettivo> [max-giri]
B) Linguaggio naturale: "usa il loop [con il team di programmazione] per X…"
```

Per B serve uno strato di **routing** prima dell'handler chat:
```
messaggio in →
  ├─ inizia con /loop                  → loop_runner(parse args)
  ├─ match frase-intento ("usa il loop")→ estrai gruppo + obiettivo → loop_runner
  └─ altrimenti                        → chat normale (DeepSeek)
```
Riconoscimento intento = **match di frase deterministico** (regex su `usa(re)?|utilizza il loop`, `fai un loop`), NON classificazione LLM su ogni messaggio (una chiamata in più per messaggio + rischio loop a sorpresa su CPU 6W). Se l'estrazione dell'obiettivo è ambigua → **conferma prima di avviare** ("Avvio un loop per «X»? sì/no").

```
Bot:  avvia loop_runner come coroutine in background
      → eventuale messaggio di avanzamento a ogni giro
      → consegna risultato finale  oppure  "fermato a N giri: <perché>"
```

Questo risolve da solo la tensione con §5: nessuna autonomia a sorpresa, parte **solo quando lo chiami**.

### Vincoli imposti dal `/loop`
- **Un loop alla volta.** Su CPU 6W: secondo `/loop` mentre uno gira → "occupato" o coda. Niente loop paralleli.
- **`/stop` obbligatorio.** Per uccidere un loop che cicla a vuoto e brucia token.
- **Il bot resta reattivo durante il loop.** Il `loop_runner` è una coroutine in background sullo **stesso event loop** asyncio (coerente con SPECS §8.1, single loop): il polling messaggi continua, puoi fare altre domande / usare altri comandi mentre il loop lavora. Il loop NON blocca la chat.

---

## 3. Le 5 fasi (dalla guida) → sul Serverino

```
loop_runner(obiettivo, max_giri, budget_token):
  for giro in range(max_giri):
      piano   = deepseek("plan", obiettivo, storia)   # PIANIFICA (LLM)
      out     = esegui_skill(piano)                    # ESEGUI (codice, deterministico)
      esito   = verifica(out)                          # VERIFICA
      if esito.ok:
          return out                                   # CONSEGNA → STOP
      storia += esito.errore                            # CORREGGI (rientra nel giro dopo)
  notifica_stop("non risolto", ultimo_errore)          # STOP pulito, niente loop infinito
```

Freni anti-deriva (conciliano col "niente retry-3" di §8.1): **max-giri** + **budget token per loop**. Il loop interno di una skill ≠ retry cieco: è limitato e verificato.

---

## 4. System prompt per fase — file separati

I system prompt **non stanno nel codice**, stanno in file di testo, uno per fase. Coerente con l'architettura 3-livelli (prompt = `directives/`, naturale; runner = `execution/`, deterministico) e col quinto elemento della guida ("skill" rilette a ogni giro).

```
directives/loop/
  plan.md       # system prompt fase "pianifica"
  fix.md        # system prompt fase "correggi"
  verify.md     # SOLO se la verifica richiede giudizio LLM (vedi §5)
```

```python
def load_prompt(fase: str) -> str:
    return (PROMPT_DIR / f"{fase}.md").read_text(encoding="utf-8")
```

### Tre regole sui prompt
1. **Creatore ≠ verificatore.** Se la verifica è LLM, `verify.md` ha tono *opposto*: "trova il difetto, presupponi sia sbagliato, output `PASS` solo se non trovi nulla". Prompt diversi = il trucco regge anche con un solo modello.
2. **Output strutturato, non prosa.** Ogni prompt chiude imponendo un formato parsabile (es. `verify.md` → "rispondi SOLO `PASS` o `FAIL: <motivo>`"). Su DeepSeek niente structured-output garantito → forzi via prompt, parsi con regex, fallback "non parsabile → FAIL".
3. **Versionabili.** Stanno nel git del vault, si modificano senza toccare Python, si testano a parte. ("Le direttive sono documenti vivi.")

**Anti-pattern da evitare:** prompt come stringhe Python multilinea o un mega-system-prompt con `if fase ==`. Perdi versionamento e separazione decisione/esecuzione.

---

## 5. Verifica — codice vs LLM

Caso preferito: **verifica deterministica in Python, niente chiamata DeepSeek**. Affidabile (o passa o no) e risparmia una chiamata per giro.

```
out    = esegui(piano)
esito  = verifica_codice(out)              # Python puro
   ├─ esito.ok == True  → STOP, consegna
   └─ esito.ok == False → storia += esito.errore ; giro+1
```

Cosa significa "verifica a livello di codice" dipende dal task:

| Tipo di task | `verifica_codice(out)` è… |
|---|---|
| Genera codice/script | esegue i test → `pytest`, exit code 0 = PASS |
| Produce file/JSON | parsing + schema check: valido? campi richiesti presenti? |
| Chiama un'API (meteo) | status 200 + valore plausibile? |
| Calcolo/dati | rispetta un'invariante? (somma = 100%, data nel futuro…) |

> **Regola che decide se il loop funziona o è teatro:** l'errore di `verifica_codice` deve **rientrare nel prompt del giro dopo** con il *perché*, non solo `True/False`.
> ✅ `FAIL: test_parsing riga 12, atteso int, ricevuto null` → DeepSeek corregge.
> ❌ solo `False` → il loop ripete lo stesso errore all'infinito.

Se la verifica richiede giudizio ("il testo è abbastanza buono?") → allora serve `verify.md` e una 2ª chiamata DeepSeek (creatore ≠ verificatore).

---

## 6. I 6 elementi della guida → stato sul Serverino

| Elemento guida | Sul Serverino | Stato |
|---|---|---|
| Automazioni (il battito) | comando `/loop` + (Phase 2) JobQueue | ✅ trigger definito |
| Memoria | `idee/bot-memory.md` + SQLite `logs` | ✅ già c'è |
| Skill | `skills/` dir, azione no-OAuth (meteo) | ✅ già deciso |
| Sotto-agenti (creatore ≠ verificatore) | 2ª chiamata DeepSeek con `verify.md` | ⚠️ solo se verifica LLM |
| Plugin/connettori | la skill stessa (API pubblica) | ✅ |
| Worktree | — non serve | ❌ skip |

---

## 7. Aperto — da decidere prima del codice

- [ ] **Primo task reale di `/loop`** — da qui discende la forma di `verifica_codice`. (es. "genera script Python che fa X" → verifica = test; "meteo formattato" → verifica = check JSON.)
- [ ] `/stop` e gestione "occupato" (un loop alla volta).
- [ ] Messaggi di avanzamento: ogni giro o solo inizio/fine?
- [ ] `max_giri` e `budget_token` di default.

---

[[progetti/Serverino/README]] • [[progetti/Serverino/REALITY_CHECK]] • [[progetti/Serverino/DECISIONE_ARCHITETTURA]]
