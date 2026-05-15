# Bibliò — Audit tema 2026-05-14

> Audit completo del tema `biblio-theme` deployato su `https://biblio.web1337.net/`.
> Fonte: `infinityfree/wp/wp-content/themes/biblio-theme/` (download server, source of truth).
> Eseguito con skill `/open-design` (subset Open Design — anti-AI-slop + craft rules).
> Decisione utente: solo audit + plan oggi, esecuzione nelle prossime sessioni.

---

## Stato reale del tema (più maturo del precedente MOC)

### Cosa è già solido
- **Design tokens completi** in `style.css :root` — palette, font, spacing, shadows, easing, durations
- **3 font** già caricati: Playfair Display (display), Lora (serif italic per titoli libro), Inter (sans body)
- **Helper WC puliti**: `biblio_product_card()`, `biblio_product_cover()`, `biblio_products_query()` in `inc/wc-integration.php`
- **Meta box "Dettagli Bibliò"** su prodotti WC: autore, anno, pagine, rating, prezzo noleggio, noleggiabile, badge, stile copertina (0-5), blurb
- **Router unico `woocommerce.php`**: gestisce shop, single product, taxonomy in un solo file
- **Front-page completa**: hero, trust strip, featured grid, categorie, Plus banner, novità
- **6 stili gradient cover** (navy/coral/marrone/crema/dark-gold/verde) con fallback automatico a immagine prodotto se presente
- **Performance hooks già in `functions.php`**: emoji rimossi, oembed/wlw/rsd rimossi, heartbeat → 60s
- **Italiano nativo** in tutto il copy
- **MyBibliò backend già esistente** in `inc/mybiblio/`:
  - `bootstrap.php` — loader
  - `admin.php` (8.8KB) — config pannello WP
  - `ajax.php` (3.5KB) — endpoint chat
  - `llm-client.php` (6KB) — client LLM (Claude API?)
  - `retrieval.php` (5KB) — retrieval SQL da custom tables
  - `validator.php` (2.4KB) — validazione input
  - `ui.php` (3.8KB) — render UI chat
  - `assets/` — CSS/JS chat

### Componenti CSS già pronti
`.btn` (.btn-primary/.btn-secondary/.btn-rent/.btn-ghost/.btn-plus/.btn-gold/.btn-lg/.btn-sm/.btn-block), `.book-card` + `.book-card.compact`, `.book-cover` + 6 stili gradient + size-sm/lg, `.cat-card`, `.plus-banner`, `.hero` + `.hero-stack`, `.trust`, `.section` + `.section-head`, `.grid-4/6/cat`, `.catalog-layout` + `.catalog-sidebar`, `.detail-grid` + `.buyrent-card`, `.chat-fab`, `.crumb`, `.entry-content`.

---

## 12 problemi identificati (in ordine di priorità)

### P0 — Anti-AI-slop violations (must-fix)

#### 1. Emoji come feature icons ovunque
Violazione regola #3 di `craft/anti-ai-slop.md` di Open Design.
**Occorrenze**: header (`🛒 👤 🔍`), footer (`❤`), FAB (`✨`), eyebrow detail page (`🛒 Acquista` / `📦 Noleggia`), wishlist/share buttons (`❤️ ↗`), rating star (`⭐`), book-card-rent (`📦`), Plus pill (`✨ Plus`), empty state catalog (`📚`), sidebar CTA (`✨`).
**Fix**: set di SVG monoline 1.6-1.8px stroke con `currentColor`. Set minimo: search, cart, user, heart, share, share-arrow, star, package, sparkles, book, chat. Inline SVG in helper PHP `biblio_icon($name, $size = 18)`.

#### 2. Metriche probabilmente inventate
`50.000+ titoli`, `1.240 recensioni` (hardcoded sulla detail page), `4,8/5`, `48 ore`, `30 giorni` — alcune sono claim brand (`30 giorni noleggio` è ok), altre sono fake numbers (regola anti-slop #6).
**Fix**: rendere dinamiche dove possibile (count prodotti reali, count recensioni WC reali), o sostituire con placeholder onesti tipo "Spedizione veloce in tutta Italia" senza numero.

#### 3. Copy hero/microcopy generico
"La tua biblioteca in un click." — funziona ma è SaaS-pitch, non editoriale. Per un brand che vende libri serve voce più calda.
**Fix**: testare alternative ("Ogni libro trova chi lo sta cercando.", "Più di un catalogo: una biblioteca che ti conosce.").

### P1 — Manutenibilità

#### 4. style.css monolitico (305 righe / 18KB)
Già a soglia gestibile, ma utente ha confermato pain point.
**Fix**: split in 4 file enqueueati:
- `css/tokens.css` (L14-87, `:root`)
- `css/base.css` (L89-130, reset + typography elementare)
- `css/components.css` (L132-274, btn/card/nav/footer/hero/etc)
- `css/pages.css` (catalog, detail, empty state, responsive @media)

#### 5. Inline styles diffusi nei file PHP
`style="margin-bottom:16px"`, `style="font-size:44px;font-style:italic"`, ecc. — almeno 30 occorrenze. Bloccano cache HTML.
**Fix**: estrarre in utility classes (`.mb-3`, `.mb-4`, `.title-detail`, ecc.) o in classi semantiche per componente. NO Tailwind — vanilla utility per coerenza con design tokens.

#### 6. CPT `book` legacy ancora caricato
File morti: `archive-book.php`, `single-book.php` (5KB ciascuno), helper `biblio_book_card()`, `biblio_books_query()`, `biblio_book_cover()`, `biblio_get_genres()`, `biblio_genre_icon()`. Inoltre `front-page.php` ha branch `if (!$use_wc)` mai eseguito in prod.
**Fix**: rimuovere `inc/post-types.php`, `inc/meta-boxes.php` (CPT meta), `archive-book.php`, `single-book.php`, helper book-* da `helpers.php`. Semplificare `front-page.php` rimuovendo branch legacy.

### P1 — UX gaps

#### 7. Mobile nav rotto
`@media (max-width:640px) .nav-links { display: none }` — nessun hamburger menu sostitutivo. Utenti mobile perdono completamente la navigazione.
**Fix**: aggiungere hamburger button + drawer/sheet con menu primary. Pattern Soft Warm: drawer da sinistra con sfondo `--biblio-cream-deep`, voci con font-serif.

#### 8. Manca icona MyBibliò in mobile bottom nav (richiesta esplicita utente)
Il `.chat-fab` c'è ed è sempre visibile, ma occupa molto spazio fisso bottom-right. In mobile dovrebbe stare in una bottom navigation bar tab insieme a: Home, Catalogo, MyBibliò, Account, (Carrello).
**Fix**: nuovo componente `.bottom-nav` solo mobile con 4-5 icone, rimpiazza il `chat-fab` floating sui breakpoint < 640px.

#### 9. Pagina `/mybiblio/` dedicata mancante
Backend MyBibliò esiste (vedi inc/mybiblio/) ma non c'è `page-mybiblio.php` o template dedicato. Il FAB rimanda a `/mybiblio/` che probabilmente fallback a `page.php`.
**Fix**: vedi sezione **Decisione design MyBibliò** sotto.

#### 10. Focus states non personalizzati
`:focus` solo browser default. Gap a11y, soprattutto per utenti tastiera.
**Fix**: aggiungere `:focus-visible` con outline coral 2px offset 2px su button/link/input.

### P2 — Performance InfinityFree

#### 11. Google Fonts via CDN esterno (`style.css` L12 `@import`)
Browser scarica direttamente (non server cURL), quindi non bloccato da InfinityFree, ma:
- `@import` dentro CSS blocca rendering (peggio di `<link>` in head)
- Latenza Google + DNS lookup extra
- No control su FOIT/FOUT

**Fix**: self-host woff2 in `assets/fonts/`, `@font-face` locale, rimuovere `@import`. Aggiungere `<link rel="preload" as="font" type="font/woff2" crossorigin>` per il display font (Playfair). Mantenere `font-display: swap`.

#### 12. WP Super Cache non configurato (da MOC originale)
Già nelle "in sospeso PHASE 2".
**Fix**: installare + config base file-based cache, escludere `wc-ajax`, `cart`, `checkout`, `my-account`.

---

## Plan di esecuzione — 6 fasi

Ogni fase consegna **file singoli da sostituire** via File Manager InfinityFree (workflow Biblio invariato).

### FASE 0 — Setup git locale + snapshot
- `git init` in `infinityfree/wp/wp-content/themes/biblio-theme/`
- Commit baseline `v0.2.0-audit`
- (opzionale) Push su `Chirone23/biblio-theme` come backup

### FASE 1 — Cleanup CPT book legacy
Rimuove dead code, riduce inode count (regola InfinityFree).
- Elimina `archive-book.php`, `single-book.php`
- Pulisci `inc/post-types.php` (rimuovi CPT registration + `book_genre` taxonomy)
- Pulisci `inc/helpers.php` rimuovendo helper `biblio_book_*`, `biblio_get_genres`, `biblio_genre_icon`
- Semplifica `front-page.php` rimuovendo branch `if (!$use_wc)`
- Aggiorna `functions.php` rimuovendo `require ... post-types.php` se inutilizzato dopo cleanup
- Bump version → `0.3.0`

### FASE 2 — CSS refactor in 4 file
- Spezza `style.css` in `css/tokens.css`, `css/base.css`, `css/components.css`, `css/pages.css`
- `style.css` resta come metadata-only + `@import` ai 4 file (oppure enqueue separato in `functions.php`)
- Validation: pixel-perfect match con la versione attuale (no regressioni visuali)

### FASE 3 — Icon set SVG + hamburger mobile + bottom nav MyBibliò
- Crea `inc/icons.php` con `biblio_icon($name, $size = 18, $stroke = 1.7)` — set monoline currentColor
- Sostituisci tutti gli emoji feature icon (header/footer/FAB/detail/cards/empty/sidebar)
- Aggiungi `.bottom-nav` mobile (5 voci: Home, Catalogo, MyBibliò centrale evidenziato, Account, Carrello)
- Aggiungi `.nav-hamburger` button + `.nav-drawer` mobile per menu primary
- Nascondi `.chat-fab` su mobile (sostituito da bottom-nav)

### FASE 4 — MyBibliò chat (drawer + pagina contestuale)
**Decisione design utente (questa sessione):**
- **Layout primario**: drawer laterale (sidebar) apribile da qualsiasi pagina + pagina `/mybiblio/` per cronologia conversazioni
- **Onboarding**: Quick picks editoriali curati + Recently viewed / your library (richiede dati utente loggato)

Da costruire (5 visual direction da definire in sessione FASE 4):
- `page-mybiblio.php` — template full conversation history + sidebar lista chat passate
- `inc/mybiblio/drawer.php` — markup drawer riusabile (incluso in `footer.php` se utente loggato)
- CSS: `.chat-drawer`, `.chat-message`, `.chat-input`, `.chat-suggestion-pill`, `.chat-history-item`
- JS: open/close drawer, submit message, render reply streaming (se LLM client supporta SSE)
- Quick picks fissi: "Consigliami un giallo italiano", "Cosa leggere dopo Calvino?", "Voglio un libro breve per il weekend", "Sorprendimi"
- Recently viewed: leggi cookie WC `woocommerce_recently_viewed` o user meta

### FASE 5 — Polish editoriale (anti-slop + microcopy IT + soul)
- Riscrivi copy hero (3 alternative testabili)
- Sostituisci metriche inventate con placeholder onesti o dinamici
- Aggiungi **una mossa tipografica forte** (80/20 craft rule): pull quote serif su home, oppure numeri trust in italic Lora con dimensione maggiore
- Microcopy button: "Aggiungi al carrello" → testare "Prendi una copia" o "Portalo a casa" (più editoriale)
- Microcopy stato "Spedito in 48h" → "Sul tuo scaffale entro 2 giorni"
- `:focus-visible` su tutto

### FASE 6 — Performance
- Self-host Playfair/Lora/Inter woff2 + `@font-face`
- Preload Playfair display
- Install + config WP Super Cache (4° plugin slot)
- Audit Lighthouse target: ≥90 Performance mobile su InfinityFree

---

## Decisioni utente prese in questa sessione

| Decisione | Scelta |
|---|---|
| Esecuzione | Solo audit oggi, codice nelle prossime sessioni |
| Layout MyBibliò chat | Sidebar drawer ovunque + pagina `/mybiblio/` per cronologia |
| Onboarding chat | Quick picks editoriali + Recently viewed (combinato) |
| Audience | Lettori italiani consumer |
| Pain points prioritari | CSS monolitico + audit completo del sistema + chat AI con icona mobile |

---

## File toccati / da toccare (mappa)

| File | Stato | Fase |
|---|---|---|
| `style.css` | Da spezzare | 2 |
| `functions.php` | Modifiche minori (enqueue 4 CSS, require cleanup) | 1, 2, 6 |
| `header.php` | Sostituire emoji + hamburger | 3 |
| `footer.php` | Sostituire emoji + bottom-nav mobile | 3 |
| `front-page.php` | Cleanup branch + microcopy | 1, 5 |
| `woocommerce.php` | Sostituire emoji + microcopy | 3, 5 |
| `inc/wc-integration.php` | Sostituire emoji in card | 3 |
| `inc/helpers.php` | Cleanup helper book-* | 1 |
| `inc/post-types.php` | Cancellare o svuotare | 1 |
| `inc/icons.php` | **NUOVO** — set SVG | 3 |
| `inc/mybiblio/drawer.php` | **NUOVO** — drawer chat | 4 |
| `page-mybiblio.php` | **NUOVO** — full chat page | 4 |
| `archive-book.php`, `single-book.php` | Cancellare | 1 |
| `css/tokens.css` `css/base.css` `css/components.css` `css/pages.css` | **NUOVI** | 2 |
| `assets/fonts/*.woff2` | **NUOVI** | 6 |

---

## Prossima sessione — come ripartire

1. Apri `BIBLIO_AUDIT_2026-05-14.md` (questo file) come contesto
2. Apri `CONTEXT_NUOVA_SESSIONE.md` per regole workflow
3. Decidi se partire da Fase 0 o saltare avanti
4. Per FASE 4 (chat): invoca `/open-design` per le 5 visual direction della pagina chat

Collega a: [[BIBLIO_PROJECT_MOC]], [[CONTEXT_NUOVA_SESSIONE]]

---

*Audit v1.0 — eseguito con `/open-design` Skill v1.0 (Claude Opus 4.7).*
