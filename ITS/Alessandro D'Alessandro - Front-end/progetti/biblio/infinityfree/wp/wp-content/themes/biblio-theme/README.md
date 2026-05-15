# Bibliò — WordPress Theme v0.3.0

Tema editoriale italiano per Bibliò. Pensato per InfinityFree hosting + WooCommerce.

## Struttura tema

```
biblio-theme/
├── style.css              Metadati WP (header obbligatorio) — stili in css/
├── css/
│   ├── tokens.css         CSS custom properties (palette, font, spacing, easing)
│   ├── base.css           Reset, tipografia, font import (Google Fonts)
│   ├── components.css     UI: nav, btn, card, cover, footer, chat-fab, ecc.
│   └── pages.css          Layout pagine (hero, catalog, detail) + responsive
├── functions.php          Setup, enqueue, performance hooks, includes
├── header.php             Nav
├── footer.php             Footer
├── front-page.php         Home (usa WooCommerce)
├── woocommerce.php        Router WC: single product, shop/catalog, taxonomy
├── archive-book.php       Archive CPT 'book' (legacy — CPT non più in prod)
├── single-book.php        Single CPT 'book' (legacy)
├── search.php             Risultati ricerca
├── page.php               Pagina statica
├── singular.php           Post singolo (fallback)
├── index.php              Fallback generico
├── 404.php
├── searchform.php
├── inc/
│   ├── post-types.php     CPT 'book' + taxonomy 'book_genre' (legacy, has_archive=false)
│   ├── helpers.php        biblio_meta(), biblio_price(), helper book-* (legacy)
│   ├── meta-boxes.php     Meta box 'Dettagli Bibliò' su prodotti WC
│   ├── wc-integration.php Helper WC: biblio_product_card(), biblio_product_cover(), biblio_products_query()
│   └── chatbot.php        MyBibliò AI: REST endpoint /biblio/v1/chat + footer UI
└── assets/js/main.js
```

## Installazione

1. Installa WooCommerce **prima** di attivare il tema.
2. **Aspetto → Temi → Aggiungi nuovo → Carica tema** → carica `biblio-theme.zip`.
3. **Aspetto → Menu** → crea menu in posizione "Menu principale".
4. **Impostazioni → Lettura** → home page statica oppure "Ultimi articoli" (usa `front-page.php` automaticamente).
5. **Impostazioni → Permalink** → "Nome articolo" → Salva (rigenera rewrite rules).

## Chiave API Groq (chatbot)

La chiave è hardcoded in `inc/chatbot.php` come fallback. Per sostituirla senza toccare il tema:

```php
// In wp-config.php, prima di "/* That's all, stop editing! */":
define('BIBLIO_GROQ_KEY', 'gsk_la_tua_chiave');
```

## Aggiungere libri

- **Prodotti WooCommerce** → Aggiungi prodotto
- In ogni prodotto compare il box **Dettagli Bibliò**: autore, anno, pagine, rating, prezzo noleggio, noleggiabile, badge, stile copertina (0-5), blurb.
- Se il prodotto ha un'immagine in evidenza la usa; altrimenti genera una copertina con gradiente CSS (stile 0-5).

## Pagine da creare in WP-admin

- `/plus/` — piano abbonamento
- `/mybiblio/` — pagina chat/account
- `/noleggio-vs-acquisto/`, `/contatti/`, `/faq/`, `/privacy/`, `/termini/`, `/cookie/`

## Ottimizzazioni InfinityFree

- CSS suddiviso in 4 file enqueued (cache granulare per browser).
- Heartbeat ridotto a 60s, emoji/oembed/wlwmanifest rimossi.
- Niente plugin obbligatori per i meta: meta box nativi.
- Consigliato: WP Super Cache; max 4 plugin; ≤25k inode.

## Bug fix v0.3.0

| Bug | Fix |
|---|---|
| `_visibility` meta_query deprecata WC 3.0+ | Sostituita con `tax_query` su `product_visibility` in `biblio_products_query()` |
| CPT `book` archive slug `catalogo` → conflitto URL WC | `has_archive => false` in `post-types.php` |
| Chatbot inline in `functions.php` (900 righe) | Spostato in `inc/chatbot.php` |
| CSS monolitico 305 righe | Splittato in 4 file in `css/` |
| `front-page.php` branch `$use_wc=false` dead code | Rimosso; solo WC path |
| Recensioni hardcoded "1.240" | Dinamico via `get_comments_number()` |
| Emoji feature icon (🛒 📦 ✨ ⭐ 🔍) | Rimossi/sostituiti con HTML entity o testo |
