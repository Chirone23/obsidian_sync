# Changelog — biblio-theme

> Storia dei cambiamenti al tema WordPress di Bibliò, sessione per sessione.
> **Parent:** [[BIBLIO_PROJECT_MOC]]

---

## v0.3.0 — 2026-05-15

### Il problema di partenza

Il tema era funzionante ma monolitico: tutto il CSS stava in un unico `style.css` da 300+ righe, la logica del chatbot era imbucata in fondo a `functions.php` insieme all'HTML del widget, e il chatbot stesso aveva tre difetti gravi:

1. **Rispondeva con libri sbagliati.** Se l'utente era nella sezione "Gialli", il chatbot tirava fuori fantasy e thriller perché non sapeva dove si trovava: la query WC caricava tutto il catalogo senza filtri di categoria.
2. **Perdeva la conversazione al cambio di pagina.** La storia della chat veniva salvata in `sessionStorage`, che si azzera su ogni nuova scheda o redirect — comportamento normale per una libreria, devastante per un chatbot.
3. **Formattava male le liste.** Il modello LLM rispondeva con liste numerate inline ("1. Titolo 2. Titolo 3. Titolo" su una riga sola) perché non inseriva `\n` tra gli elementi. Il testo arrivava compresso e illeggibile.

Oltre al chatbot, c'erano due bug WooCommerce latenti che non causavano errori visibili ma erano sbagliati:
- `inc/wc-integration.php` usava ancora `_visibility` come meta query, deprecata da WC 3.0 in favore della tassonomia `product_visibility`.
- `inc/post-types.php` registrava il CPT `book` con `has_archive => 'catalogo'`, creando un conflitto URL con la pagina shop WooCommerce `/catalogo/`.

---

### Cosa è cambiato

#### CSS split in 4 file modulari

Lo `style.css` monolitico è stato diviso in:

```
css/
├── tokens.css      → design tokens (variabili CSS: palette, font, spacing)
├── base.css        → reset, tipografia, @import Google Fonts
├── components.css  → nav, bottoni, book-card, copertine, footer, chat FAB
└── pages.css       → hero, catalogo, dettaglio prodotto, responsive
```

`style.css` ora contiene solo l'header del tema (metadati WP). `functions.php` carica i 4 file in catena di dipendenza (`tokens → base → components → pages`) così i custom properties sono sempre disponibili quando servono.

**Perché:** un file da 300 righe è difficile da navigare e da mandare singolarmente via File Manager su InfinityFree. Con i file separati, per aggiustare un colore basta toccare `tokens.css`; per un bug di layout solo `pages.css`.

#### Chatbot estratto da functions.php → inc/chatbot.php

Tutta la logica del chatbot (endpoint REST, query WC, chiamata Groq, system prompt) è stata spostata in `inc/chatbot.php`. `functions.php` ora fa solo `require_once` di quel file.

Il refactor ha portato `functions.php` da 143 a 89 righe, rendendolo leggibile: setup → asset → performance → includes. Niente HTML, niente logica di business.

#### Chatbot category-aware

Il chatbot ora sa in quale sezione del catalogo si trova l'utente.

JS legge il category slug dall'URL (`/wp-categoria-prodotto/<slug>/`) e lo invia al backend con ogni messaggio. PHP riceve lo slug, lo risolve come termine `product_cat`, e filtra la `WP_Query` di conseguenza. Il system prompt include solo i titoli della categoria corrente invece di tutto il catalogo.

Risultato: nella sezione Gialli il chatbot risponde con gialli. Se l'utente è nella pagina shop generale, vede tutto il catalogo.

#### Conversazione persistente via localStorage

`sessionStorage` sostituito con `localStorage` (chiave `biblio_chat_v2`, TTL 24 ore). La struttura di ogni messaggio salvato è `{role, display, raw}`:
- `raw` — testo pulito, va all'API
- `display` — HTML safe per il DOM (già escaped + formattato)

Separare i due campi evita di dover fare escape/unescape ogni volta che un messaggio viene riletto dalla history.

#### Fix formattazione liste

`botTextToHtml()` in `main.js` ora applica due trasformazioni:
1. `\n` reali → `<br>`
2. Pattern `spazio + numero + punto + spazio` → `<br>` prima del numero (cattura le liste inline che il modello produce senza newline)

#### Fix bug WooCommerce

- `inc/wc-integration.php`: `biblio_products_query()` ora usa `tax_query` su `product_visibility` invece di `meta_query` su `_visibility`. Compatibile WC 3.0+.
- `inc/post-types.php`: `has_archive` cambiato da `'catalogo'` a `false`. Elimina il conflitto URL con `/catalogo/` della shop page WC.

#### Cleanup functions.php

Aggiunto blocco performance InfinityFree: rimozione script emoji, oEmbed, WLW manifest, shortlink; heartbeat ridotto a 60 secondi. `BIBLIO_GROQ_KEY` definita con `!defined()` così può essere sovrascritta in `wp-config.php` senza toccare il tema.

---

### File invariati

404.php, archive-book.php, header.php, index.php, page.php, search.php, searchform.php, single-book.php, singular.php, inc/helpers.php, inc/meta-boxes.php — nessuna modifica necessaria.

---

### Lezioni apprese in questa sessione

- Su InfinityFree il deploy avviene file per file via File Manager. Se si cambia `functions.php` che ora include nuovi file CSS, quei file devono essere caricati **prima** — altrimenti il sito va in 404 parziale silenziosa.
- Il browser fa cache aggressiva di JS e CSS. Dopo ogni upload, Ctrl+Shift+R è necessario per vedere i cambiamenti.
- Fare troppi fix in una sessione senza che l'utente possa testare porta a revert. Meglio un cambiamento alla volta con conferma funzionante in mezzo.

---

*Aggiorna questo file dopo ogni sessione di sviluppo che modifica file del tema.*
