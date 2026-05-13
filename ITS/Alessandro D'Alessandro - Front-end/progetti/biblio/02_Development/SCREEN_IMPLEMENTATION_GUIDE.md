# Screen Implementation Guide — Bibliò WordPress

Per ogni schermata: file PHP, struttura query, pattern di caching, integrazione AJAX, CSS classes, e gotcha comuni.

**Versione:** 1.0  
**Audience:** PHP/WordPress developer con ADR knowledge  
**Week 1-4 Reference**

---

## 📑 Indice

1. **Homepage** (front-page.php) — featured grid + caching
2. **Catalogo** (archive-book.php) — AJAX filters
3. **Dettaglio Libro** (single-book.php) — meta data
4. **MyBibliò Chat** — Claude API integration
5. **Account** — user meta
6. **Plus** — static page
7. **Checkout** — PHASE 2 (WooCommerce)
8. **Onboarding** — form + redirect
9. **Noleggio vs Acquisto** — static
10. **Header/Nav** — menu setup
11. **Footer + ChatFab**
12. **BookCard Partial** — reusable

---

## 01 Homepage → front-page.php

**React Source:** Home.jsx  
**Rischio:** Basso  
**Queries:** 2 WP_Query (cached)

**Query Pattern:**
- Hero covers: `WP_Query _book_featured=1, posts_per_page=3, no_found_rows=true`
- Featured grid: `biblio_cached_book_grid()`, TTL 1h
- New arrivals: `biblio_cached_book_grid()`, TTL 1h

**Fragment Cache:**
```php
echo biblio_cached_book_grid(
    ['posts_per_page' => 6, 'meta_key' => '_book_featured'],
    'homepage_featured',
    HOUR_IN_SECONDS
);
```

**⚠️ Gotcha:** Se WP Super Cache non è attivo, 4 DB queries su ogni request = timeout su InfinityFree.

---

## 02 Catalogo → archive-book.php

**React Source:** Catalog.jsx  
**Rischio:** Medio  
**Queries:** AJAX filters

**AJAX Flow:**
1. User clicks filter → `catalog-filters.js`
2. POST to `admin-ajax.php?action=biblio_filter`
3. Handler runs WP_Query → returns JSON
4. JS replaces `#book-grid` innerHTML
5. Skeleton loading durante fetch

**⚠️ Gotcha:** `orderby=rand` è expensive; nonce must match; `meta_query` slows queries con >500 books.

---

## 03 Dettaglio Libro → single-book.php

**React Source:** BookDetail.jsx  
**Rischio:** Basso  
**Queries:** 2 (1 cached)

**Data Sources:**
- `the_post()` — title, excerpt
- `biblio_get_book_meta()` — custom fields
- Related: `biblio_cached_book_grid()`, TTL 2h

**⚠️ Gotcha:** Buy button è statico in Phase 1 (no WC); Add `data-book-id` per Phase 2.

---

## 04 MyBibliò Chat → template-parts/mybiblio-chat.php

**React Source:** MyBiblioChat.jsx  
**Rischio:** Medio  
**API:** Claude Haiku

**AJAX Flow:**
1. User types → POST `admin-ajax.php?action=biblio_chat`
2. Handler: `biblio_ajax_chat()` → calls Claude
3. Returns `{reply: string}`
4. JS appends bot bubble

**Code Pattern:**
```php
check_ajax_referer('biblio_chat', 'nonce');
$message = mb_substr(sanitize_textarea_field($_POST['message'] ?? ''), 0, 500);
set_time_limit(30);  // InfinityFree timeout
$reply = biblio_claude_complete($message, $system_prompt);
wp_send_json_success(['reply' => $reply]);
```

**⚠️ Gotcha:** Plugin dependency; API key in wp-config.php (never in JS); max 500 chars; timeout fallback.

---

## 05-12 Altre Schermate

- **Account** (page-account.php) — WP Users + user_meta
- **Plus** (page-plus.php) — 100% statica, Stripe redirect
- **Checkout** (PHASE 2) — WooCommerce test 72h separate
- **Header** (header.php) — wp_nav_menu(), custom logo
- **Footer** (footer.php) — include mybiblio-chat.php
- **BookCard** (template-parts/book-card.php) — reusable partial

**Vedi ADR Sezioni 3-12 per dettagli completi.**

---

**NEXT:** Setup starter files → vedi [[SCHEMA_Biblio_WordPress_ConversationSummary]]
