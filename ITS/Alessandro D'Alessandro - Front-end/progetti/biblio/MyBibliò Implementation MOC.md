# MyBibliò Implementation MOC

Mappa operativa per implementare il chatbot **MyBibliò** dentro il sito WordPress di Bibliò ospitato su Infinity Free.

**Parent:** [[Bibliò MOC]]
**Materia:** [[Front-end MOC]] · [[Progettistica AI MOC]]
**Prompting framework:** [[Prompting MOC]] (C.I.A.R.E. di Riccardo Raponi)
**Patterns AI:** [[Agenti IA Design Patterns MOC]]

---

## 1. Obiettivo

Costruire un chatbot assistente libreria che:
1. risponde **solo** sul catalogo Bibliò (no allucinazioni)
2. consiglia libri dal profilo gusti dell'utente (ibrido implicito + esplicito)
3. aiuta a scegliere fra noleggio e acquisto
4. funziona dentro i limiti di Infinity Free (CPU 40–128 MB, inode 30k, no WP-Cron affidabile)

**Stato:** decisioni MVP chiuse il 2026-05-14 — vedi [[project_mybiblio_chatbot]] in memoria.

---

## 2. Stack tecnico

| Layer | Scelta | Note |
|-------|--------|------|
| CMS | WordPress 7.x | esistente |
| E-commerce | WooCommerce | esistente, fonte dati prodotti |
| Linguaggio backend | PHP 8.3 | `wp_remote_post()` per chiamate API |
| Linguaggio frontend | Vanilla JS | no React, no framework |
| Retrieval | `WP_Query` su prodotti WC + meta `_biblio_*` | zero tabelle custom in MVP |
| LLM provider | TBD (ricerca Perplexity in corso) | provider-agnostic via `mybiblio_llm_call()` |
| Memoria conversazione | `$_SESSION` PHP (ultime N risposte) | no DB |
| Profilo gusti | `user_meta._mybiblio_profile` (JSON) | implicito + esplicito + GDPR |
| Hosting | Infinity Free Free Tier | vedi [[BRIEF_WordPress_InfinityFree]] |

---

## 3. Struttura file (dentro `biblio-theme/inc/mybiblio/`)

```
inc/mybiblio/
├── bootstrap.php              # require di tutti i moduli
├── llm/
│   ├── client.php             # mybiblio_llm_call() provider-agnostic
│   ├── providers/
│   │   ├── claude.php
│   │   ├── openai.php
│   │   ├── gemini.php
│   │   ├── qwen.php
│   │   ├── deepseek.php
│   │   └── glm.php
│   └── system-prompt.php      # prompt MyBibliò in italiano (C.I.A.R.E.)
├── retrieval/
│   ├── search.php             # mybiblio_search_books($filters)
│   ├── filters.php            # parser intent → WP_Query args
│   └── validator.php          # post-LLM: verifica titoli citati esistano
├── profile/
│   ├── store.php              # get/set user_meta._mybiblio_profile
│   ├── implicit.php           # raccoglie ordini WC + prodotti visti
│   ├── explicit.php           # estrae info dalla chat
│   └── gdpr.php               # export + delete
├── chat/
│   ├── session.php            # rolling buffer ultime N
│   ├── rate-limit.php         # 15-20 msg/giorno/utente
│   ├── guardrails.php         # whitelist topic + anti-injection
│   └── log.php                # audit log per admin
├── ajax/
│   ├── send-message.php       # endpoint POST principale
│   ├── stream-sse.php         # SSE con timeout-aware fallback
│   └── admin-takeover.php     # live takeover (fase 2)
├── ui/
│   ├── page-mybiblio.php      # template /mybiblio/ full-page
│   ├── widget-fab.php         # drawer del FAB
│   └── assets/
│       ├── chat.css
│       └── chat.js            # fake-streaming + SSE client
└── admin/
    ├── dashboard.php          # log chat + interventi
    └── settings.php           # API key + provider selection
```

---

## 4. Roadmap implementativa (4 fasi)

### Fase 1 — Scaffold provider-agnostic (1 settimana)
- [ ] Cartella `inc/mybiblio/` con tutti i file vuoti
- [ ] `mybiblio_llm_call($messages, $tools)` con switch provider
- [ ] System prompt italiano colto in `system-prompt.php`
- [ ] Endpoint AJAX `send-message.php` con mock response
- [ ] Settings page admin per API key

### Fase 2 — Retrieval + anti-allucinazione (1 settimana)
- [ ] `mybiblio_search_books()` con filtri prezzo/genere/autore/pagine
- [ ] Validator post-output: verifica titoli citati esistano in catalogo
- [ ] Fallback "non ce l'ho" con suggerimento filtri
- [ ] Test con 20+ query reali

### Fase 3 — UI + UX (1-2 settimane)
- [ ] Pagina `/mybiblio/` full-page con storico sessione
- [ ] Widget FAB con drawer aperto/chiuso
- [ ] Streaming SSE con fallback fake-streaming
- [ ] Auth gating: 3 msg demo → login
- [ ] Rate limit 15-20 msg/giorno

### Fase 4 — Profilo + admin (1-2 settimane)
- [ ] Raccolta implicita: ordini WC + prodotti visti
- [ ] Estrazione esplicita da chat ("ho letto X" → profile.libri_letti)
- [ ] Comandi utente: "dimentica", "mostrami cosa sai di me"
- [ ] GDPR: export/delete profilo in `/account/`
- [ ] Admin log + intervento (lettura prima, takeover dopo)

---

## 5. Documenti di riferimento

### Spec funzionali e tecniche
- [[ITS/Alessandro D'Alessandro - Front-end/progetti/biblio/biblio_specs_funzionale_mvp]] — §13 dedicata al chatbot
- [[ITS/Alessandro D'Alessandro - Front-end/progetti/biblio/biblio_spec_tecnica_mvp_v0_1]]
- [[ITS/Alessandro D'Alessandro - Front-end/progetti/biblio/01_Research/BRIEF_WordPress_InfinityFree]]
- [[ITS/Alessandro D'Alessandro - Front-end/progetti/biblio/01_Research/ADR_WordPress_InfinityFree]]

### Da creare durante l'implementazione
- `01_Research/SPEC_MyBiblio_Chatbot.md` — spec tecnica esecutiva da approvare prima del codice
- `01_Research/RESEARCH_LLM_Comparison.md` — output dei 3 prompt Perplexity (US vs cinesi)
- `02_Development/system-prompt-v1.md` — system prompt versionato

---

## 6. Vincoli e regole non negoziabili

1. **Anti-allucinazione 3 livelli**: filtro pre-LLM via WP_Query + system prompt rigido + validatore post-output
2. **Solo utenti loggati** (3 messaggi demo per ospiti) — spec §13.2
3. **No vector DB** — spec §2.2.6
4. **No memoria lunga tra sessioni** — spec §13.9.5 (eccetto profilo gusti esplicito in user_meta)
5. **Niente plugin esterni** — vincolo inode Infinity Free (max 30k file)
6. **API key sempre lato server** — mai esposta a JS

---

## 7. Punti aperti

1. **Scelta LLM** — in attesa di ricerca Perplexity (3 prompt forniti in conversazione)
2. **SSE su Infinity Free** — da testare, fallback fake-streaming pronto
3. **Live takeover admin** — valutare ridimensionamento a "lettura + messaggio scriptato" per MVP
4. **Outbound HTTPS** — verificare che Infinity Free non blocchi `api.anthropic.com` / `dashscope.aliyuncs.com`
5. **Max execution time PHP** — verificare che le chiamate API non vadano in timeout

---

## 8. Connessioni

- [[Bibliò MOC]] — progetto padre
- [[Front-end MOC]] — materia
- [[Progettistica AI MOC]] — design di sistemi AI
- [[Prompting MOC]] — framework C.I.A.R.E. per system prompt
- [[Agenti IA Design Patterns MOC]] — pattern di riferimento per chatbot RAG
- [[Knowledge MOC]] — Context Engineering
