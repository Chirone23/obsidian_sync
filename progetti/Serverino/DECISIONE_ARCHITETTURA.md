# Serverino Bot — Decisione Architettura

**Data:** 2026-06-15
**Status:** ✅ Decisa
**Esito:** Variante custom ispirata a Nanobot ("Serverino core") — NON Nanobot as-is, NON wrapper piatto.

> Segue al [[progetti/Serverino/REALITY_CHECK]]. Risolve i gradini #1, #2, #4, #5 della scala di impatto.

---

## Decisioni prese

| # | Tema | Decisione |
|---|---|---|
| 1 | **Architettura** | Variante custom ispirata a Nanobot. Rubiamo le idee, scriviamo ogni riga. |
| 2 | **Modello DeepSeek** | `deepseek-v4-flash` (NON `deepseek-chat` — ritirato 24/07/2026 15:59 UTC, [fonte](https://api-docs.deepseek.com/updates)). |
| 4 | **Sorgente contesto** | Leggi i `.md` dal clone git locale del vault. NIENTE MCP Obsidian (non gira headless). |
| 5 | **Git clone da messaggio** | ❌ Tagliato dall'MVP. Bot legge solo il vault principale. Eventuale Phase 2 con whitelist. |

---

## Perché questa strada (non le altre)

- **Nanobot as-is:** stabile se pinnato, ma ti dà 10 canali + 20 provider che non usi, al prezzo di RAM extra su 4GB e debug cieco in codice altrui. Per single-user è zavorra.
- **Wrapper piatto ~100 righe:** ottimo ma senza struttura per crescere.
- **Variante custom (scelta):** architettura pulita ed estendibile + possiedi ogni riga. ~150-200 righe.

Principio guida: *Agents read, humans write* — leggi Nanobot per i pattern, scrivi il tuo.

---

## Cosa rubare da Nanobot

| Idea | Presa? | Note |
|---|---|---|
| Core loop (parse → context → LLM → respond) | ✅ | Cuore dell'agente |
| Seam Provider (astrazione LLM) | ✅ sottile | 1 funzione `call_llm()`, non 20 provider |
| Seam Channel | ⚠️ minimo | Telegram concreto ma isolato, non multi-canale |
| Skill/plugin auto-discovery | ✅ mini | Cartella `skills/`, capability senza toccare il core |
| Memory file-based | ✅ | `.md` del vault + SQLite log |
| Multi-canale / multi-provider / WebUI / scheduling | ❌ | Zavorra single-user |

---

## Architettura "Serverino core"

```
serverino/
├── main.py              # core loop (~40 righe): orchestratore
├── core/
│   ├── channel.py       # interfaccia + impl Telegram (polling)
│   ├── provider.py      # call_llm() → DeepSeek (client OpenAI-compatible)
│   ├── context.py       # legge .md dal clone git del vault
│   └── memory.py        # SQLite log + last-N messaggi
├── skills/              # plugin auto-caricati (l'idea rubata)
│   └── __init__.py
├── config.py
├── .env                 # secret, mai committare
└── systemd/serverino.service
```

**3 principi di design (da Nanobot):**
1. **Core sottile, periferia sostituibile** — il loop chiama interfacce, non conosce DeepSeek/Telegram.
2. **Capability via skill, non via if** — nuova funzione = nuovo file in `skills/`.
3. **Config-driven** — comportamento in `.env` + `.md` del vault, non hardcoded.

---

## ⚠️ Unico rischio: scope creep

Ispirarsi a Nanobot può farti ricostruire Nanobot. **Regola:** scrivi un pezzo solo quando serve. Il seam Channel è 1 funzione, la skill system è ~20 righe. Se stai scrivendo "infrastruttura per il futuro", fermati.

---

## Note tecniche da non dimenticare

- DeepSeek **non ha SDK ufficiale** → usa client OpenAI puntato a `https://api.deepseek.com`.
- `deepseek==0.1.0` in requirements.txt è fantasma → rimuovere.
- `.env` chmod 600, sanitize log (no API key in journalctl).
- DeepSeek free tier: 5M token gratis / 30gg al signup.

---

## Prossimi step (quando si scrive codice)

Un file alla volta, intento a parole prima di ogni file. Ordine suggerito:
1. `config.py` + `core/provider.py` (pezzo più isolato/testabile)
2. `core/context.py`
3. `core/memory.py`
4. `core/channel.py` + `main.py`
5. `systemd/serverino.service`

---

[[progetti/Serverino/REALITY_CHECK]] • [[progetti/Serverino/README]] • [[progetti/Serverino/SPECS]] • [[progetti/Serverino/bot-architecture]] • [[progetti/Serverino/NANOBOT_SETUP]]
