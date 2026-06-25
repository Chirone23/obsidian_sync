# CHANGELOG BUILD — Sessione 04-06 · SpecterAI

**Progetto:** SpecterAI — AI Contract Analyzer per Non-Avvocati
**Periodo documentato:** 2026-06-04 (primo E2E reale via browser + fix critico + feature demo)
**Spec di riferimento:** [[Specifica Tecnica v4 - SpecterAI]]
**Fonti:** sessione Claude Code, log uvicorn, test curl cronometrati
**Collegati:** [[INCIDENTS]] INC-012 · [[SPEC_ERRATA]] (nota E2E 2026-06-04)

> Sessione in cui SpecterAI è stato **avviato e fatto girare end-to-end dalla web UI per la prima volta**. L'obiettivo era una demo presentabile con tempi e risultati visibili. Nel farlo è emerso (e stato corretto) un bug critico che rendeva l'upload web sempre fallato.

---

## 1 — Setup ambiente di esecuzione

Prima esecuzione su questa macchina: l'app non era mai stata lanciata via server (gli upload erano stati tentati aprendo `templates/index.html` come `file://`, che non ha backend → "Analisi in corso..." infinito).

| Passo | Comando / esito |
|---|---|
| Virtualenv | `python -m venv .venv` (Python 3.12.10) |
| Dipendenze | `pip install -r requirements.txt` — OK (nota: spacy 3.7.6 è una *yanked version*, installata comunque) |
| Modello privacy | `python -m spacy download it_core_news_sm` — OK (13MB, vedi [[feedback_spacy_model]]) |
| Avvio | `python -m uvicorn main:app --host 127.0.0.1 --port 8000` |
| Backend/modello | `LLM_BACKEND=cli` + `CLAUDE_MODEL=claude-haiku-4-5-20251001` impostati **come env var al lancio**, senza creare `.env` (scelta non persistente, repo invariato) |

**`.bat` sul Desktop** (`Avvia SpecterAI.bat`): creato per avviare con doppio clic (crea venv + installa + scarica modello + avvia + apre browser). Limite emerso: se manca `.env` lancia `setup.py` interattivo; e va aperto in finestra visibile. Per questa sessione il setup è stato fatto a mano (più affidabile).

> ⚠️ **Trappola UX ricorrente:** aprire `index.html` come file locale (`file://…/templates/index.html`) NON funziona — il form posta su `/analyze`, che esiste solo col server attivo. Usare sempre **`http://127.0.0.1:8000`**.

---

## 2 — Fix critico: INC-012 (`async for` su UploadFile)

Al primo upload reale (`ContrattoCOCOCO.pdf`): **500 in ~0,1s**, prima della chiamata LLM.

```
main.py:45  async for chunk in file:
TypeError: 'async for' requires an object with __aiter__ method, got UploadFile
```

`UploadFile` non è async-iterabile → ogni upload dalla web UI falliva. La pipeline `/analyze` **non aveva mai processato un upload con successo**; i test E2E precedenti (7/8 PDF, 2026-05-20) erano a monte dell'endpoint, non via HTTP multipart.

**Fix:** loop `while True: chunk = await file.read(1MB)` al posto di `async for`, limite 10MB invariato. Dettaglio completo + lezioni in [[INCIDENTS]] INC-012.

---

## 3 — Feature demo: tempo + modello nel report

Richiesta: mostrare in pagina **quanto** ci mette e **quale modello** gira, per la presentazione.

| File | Modifica |
|---|---|
| `main.py` | `import time`; cronometro `time.perf_counter()` attorno a `analyze()`; passati `elapsed_seconds` e `model` (= `config.MODEL`) al template |
| `templates/report.html` | nuovo blocco `.meta-badges` sotto il badge AI: `⏱️ Analisi in {N}s` + `🧠 Modello {model}` |

---

## 4 — Verifica E2E (backend cli + Haiku)

| Controllo | Esito |
|---|---|
| Status upload `ContrattoCOCOCO.pdf` | **200 OK** |
| Report | 3 top-rischi + **7/7** categorie, `language: italian` |
| Badge tempo | mostrato (es. `107.0s`) |
| Badge modello | `claude-haiku-4-5-20251001` |
| Tempi osservati | 76,8s / 81,0s / 107,2s — varianza intrinseca backend `cli` (log puliti, 1 tentativo, niente retry) |

**Nota tempi:** il backend `cli` avvia l'intero agente Claude Code ad ogni analisi → lento e variabile a prescindere dal modello (su `cli` non si applicano `max_tokens`/`temperature` della §6 — vedi [[SPEC_ERRATA]] ERR-08). Per garantire <60s servirebbe il backend `sdk` (API diretta, max_tokens=2048). Per questa demo i tempi ~1 minuto sono stati ritenuti accettabili.

> ⚠️ **SUPERATO il 2026-06-24** — questa conclusione si è rivelata sbagliata. La latenza NON era il backend `cli` in sé ma l'**extended thinking** (~1700 token nascosti). Disattivandolo (`MAX_THINKING_TOKENS=0`) le analisi scendono a **13s sul CLI a €0**, senza bisogno dell'`sdk`. Vedi [[INCIDENTS]] INC-013 e [[CHANGELOG_BUILD_24-06]].

---

## Connessioni

- [[INCIDENTS]] — INC-012 (questo fix)
- [[SPEC_ERRATA]] — ERR-08 (backend cli/sdk), nota E2E 2026-06-04
- [[Specifica Tecnica v4 - SpecterAI]] — spec corrente
- [[CHANGELOG_BUILD_20-05]] — sessioni building + hardening precedenti
- [[feedback_spacy_model]] — perché `it_core_news_sm` e non `lg`
