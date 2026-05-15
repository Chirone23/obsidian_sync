# Bibliò — Context per nuova sessione

> Leggi questo file all'inizio di ogni sessione prima di fare qualsiasi modifica al tema.
> Ultimo aggiornamento: 2026-05-15

---

## 🎯 Cos'è il progetto

**Bibliò** è un e-commerce italiano di libri (acquisto + noleggio 30gg).
Design system originale: React SPA su https://github.com/Chirone23/biblio
Convertito in **WordPress theme standalone** (`biblio-theme`, non child theme).

---

## 🌐 Ambiente live

| Cosa | Valore |
|---|---|
| Sito | `https://biblio.web1337.net/` |
| WP Admin | `https://biblio.web1337.net/wp-admin` |
| Hosting | Infinity Free (gratuito) |
| PHP | 8.3 |
| DB | MySQL 8.0 |
| WordPress | installato e attivo |
| WooCommerce | installato e attivo |

---

## 📦 Tema installato

- **Nome**: Bibliò (`biblio-theme`)
- **Versione attuale**: v0.3.0
- **Zip backup locale**: `C:\Users\Chirone\Documents\Secondo_Cervello\ITS\Alessandro D'Alessandro - Front-end\progetti\biblio\infinityfree\wp\wp-content\themes\biblio-theme_ultimate-v3.zip`
- **Sorgente locale**: `C:\Users\Chirone\Documents\Secondo_Cervello\ITS\Alessandro D'Alessandro - Front-end\progetti\biblio\infinityfree\wp\wp-content\themes\biblio-theme\`

### Regola aggiornamenti (IMPORTANTE)

Il tema **NON si può cancellare e ricaricare da WP admin** (inode).
Ogni modifica va consegnata come **file singolo** da sostituire via File Manager Infinity Free.

Workflow standard per ogni modifica:
1. Leggi il file reale dalla sorgente locale
2. Modifica solo quello che serve
3. Salva il file aggiornato
4. Di' all'utente: "sostituisci `<file>` in `wp-content/themes/biblio-theme/`"

---

## 🏗️ Architettura del tema (v0.3.0)

### Struttura file

```
biblio-theme/
├── style.css              Header tema (metadata only in v0.3.0)
├── css/
│   ├── tokens.css         Design tokens (tutte le CSS custom properties)
│   ├── base.css           Reset, typography, Google Fonts @import
│   ├── components.css     Nav, buttons, book-card, cover, footer, chat-fab
│   └── pages.css          Hero, catalog layout, detail page, responsive
├── functions.php          v0.3.0 — Setup + enqueue CSS + require inc/
├── header.php             Nav sticky con logo + menu + icone WC
├── footer.php             Footer 4 colonne + chat FAB
├── front-page.php         Home (hero + griglia + categorie + Plus banner)
├── woocommerce.php        ← ROUTER UNICO per tutte le pagine WC
│                            is_singular('product') → dettaglio libro
│                            is_shop()/is_product_taxonomy() → catalogo
│                            else → woocommerce_content() fallback
├── archive-book.php       Catalogo CPT book (legacy, non più usato)
├── single-book.php        Dettaglio CPT book (legacy, non più usato)
├── search.php / page.php / singular.php / index.php / 404.php
├── searchform.php
├── inc/
│   ├── chatbot.php        ← NUOVO in v0.3.0 — REST endpoint MyBibliò
│   ├── post-types.php     CPT 'book' + tax 'book_genre' (legacy)
│   ├── helpers.php        biblio_meta(), biblio_price(), biblio_book_card()...
│   ├── meta-boxes.php     Meta box "Dettagli libro" su CPT book
│   └── wc-integration.php Meta box "Dettagli Bibliò" su product WC
└── assets/js/main.js      Nav highlight + Chat UI con localStorage TTL 24h
```

### File più importanti da conoscere

- **`woocommerce.php`** — qualsiasi modifica a shop, catalogo, dettaglio prodotto
- **`css/tokens.css`** — colori, font, spacing (source of truth design)
- **`css/components.css`** — card, bottoni, nav, chat
- **`front-page.php`** — home
- **`inc/chatbot.php`** — REST API MyBibliò chatbot
- **`inc/wc-integration.php`** — meta box prodotto e card/cover WC

---

## 🤖 MyBibliò chatbot (implementato in v0.3.0)

### Stack

| Layer | Scelta |
|---|---|
| Backend | `inc/chatbot.php` — REST endpoint `biblio/v1/chat` |
| LLM provider | **Groq** (`llama-3.1-8b-instant`) via `wp_remote_post()` |
| API key | `BIBLIO_GROQ_KEY` in `functions.php` |
| Retrieval | `WP_Query` su prodotti WC + filtro per `product_cat` taxonomy |
| Storico multi-turn | Ultimi 6 messaggi passati a Groq |
| Frontend | Vanilla JS in `assets/js/main.js` |
| Persistenza | localStorage (key `biblio_chat_v2`, TTL 24h) |

### Come funziona

1. JS legge il category slug dall'URL (`/wp-categoria-prodotto/<slug>/`)
2. POST a `/wp-json/biblio/v1/chat` con `{message, category_slug, history[]}`
3. PHP filtra i prodotti WC per categoria (se slug presente)
4. Costruisce context con titoli/autori/prezzi del catalogo filtrato
5. Chiama Groq con system prompt + history multi-turn
6. JS renderizza risposta; converte `\n` → `<br>` e liste numerate inline

---

## 🎨 Design System Bibliò

### Palette colori (in `css/tokens.css`)

| Variable | Valore | Uso |
|---|---|---|
| `--biblio-coral` | `#C75550` | CTA principale, accenti, heading |
| `--biblio-coral-dark` | `#A8423E` | Hover coral |
| `--biblio-ink` | `#2A2A2A` | Testo body, sfondi scuri |
| `--biblio-cream` | `#F5F1E8` | Background principale ("carta") |
| `--biblio-cream-deep` | `#EDE6D3` | Superfici alternative, card back |
| `--biblio-navy` | `#3A4A5C` | Categorie, icone informative |
| `--biblio-gold` | `#C4A062` | Plus / Premium |
| `--biblio-rent` | `#8B6F4E` | Noleggio (marrone caldo) |
| `--biblio-gray` | `#E8E3DB` | Bordi, separatori |

### Font

| Variable | Font | Uso |
|---|---|---|
| `--font-display` | Playfair Display | H1, H2, logo, titoli |
| `--font-serif` | Lora | Titoli libro (italic), blockquote |
| `--font-sans` | Inter | Body, UI, meta |

---

## 🛒 WooCommerce — stato attuale

10 prodotti attivi, SKU BOOK-1001...BOOK-1010. Categorie: Thriller, Fantasy, Romance, Drammatico, Psicologico, Giallo, Storico.

Ogni prodotto ha meta `_biblio_*`:

| Campo | Meta key |
|---|---|
| Autore | `_biblio_author` |
| Anno | `_biblio_year` |
| Pagine | `_biblio_pages` |
| Rating | `_biblio_rating` |
| Prezzo noleggio | `_biblio_rent` |
| Noleggiabile | `_biblio_rentable` |
| Badge | `_biblio_badge` |
| Stile copertina | `_biblio_cover_idx` (0-5) |
| Blurb | `_biblio_blurb` |

**Nessuna immagine prodotto** ancora → copertina gradiente CSS.

### Pagine WC attive

- `/shop/` — Catalogo
- `/categoria-prodotto/<slug>/` — Filtro categoria (usato da chatbot per context)
- `/prodotto/<slug>/` — Singolo prodotto
- `/carrello/`, `/pagamento/`, `/il-mio-account/`

---

## ⚠️ Vincoli Infinity Free

| Vincolo | Limite |
|---|---|
| Disk | 5 GB |
| Inode | ~30k (max 4 plugin, tema lean) |
| CPU/Memory | ~40-128 MB |
| PHP | 8.3 |
| cURL outbound | ❌ bloccato — usare `wp_remote_post()` |

### Ottimizzazioni nel tema

- Emoji script rimossi
- oEmbed/WLW/RSD rimossi
- Heartbeat ridotto a 60s
- CSS split in 4 file (tokens/base/components/pages)

---

## 🚧 Phase 2 (non implementato)

- [ ] Noleggio funzionale (pulsante placeholder → logica reale)
- [ ] Immagini copertina reali (ora solo gradient CSS)
- [ ] WP Super Cache configurato
- [ ] Pagine `/contatti/`, `/noleggio-vs-acquisto/` con contenuto reale
- [ ] Menu principale configurato in WP Admin
- [ ] MyBibliò chatbot v2 (profilo gusti, rate limit, auth gating)

---

*Context v0.3.0 — 2026-05-15. Aggiorna questo file dopo ogni sessione che cambia qualcosa di strutturale.*
