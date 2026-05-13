# Architecture Decision Record · 12 Maggio 2026
## Bibliò → WordPress su InfinityFree

Analisi completa della conversione del design system React Bibliò in un tema WordPress ottimizzato per i vincoli di InfinityFree hosting.

**Versione:** 1.0  
**Status:** ✅ Approvato — Option A (WordPress Theme Ottimizzato)  
**Stack:** WP 7.x | PHP 8.3 | MySQL 8.0 | Vanilla JS

---

## TL;DR — Raccomandazione in 30 secondi

### ★ Raccomandazione Primaria: Option A

Converti Bibliò in un tema WordPress nativo con PHP templates, design token CSS, e vanilla JS minimale. Usa WP Super Cache + fragment caching per restare sotto i limiti CPU di InfinityFree. Tieni WooCommerce fuori dalla fase 1: testalo su istanza separata prima di attivarlo. Stima: 3–4 settimane.

#### ✅ Cosa sopravvive intatto
- Palette coral / navy / gold / cream
- Tipografia Playfair + Lora + Inter
- BookCard hover + shadow
- MyBibliò chat (via WP AJAX + Claude API)
- Sezione Plus con banner premium
- Layout a grid del catalogo
- Token CSS (portati 1:1 da colors_and_type.css)

#### ⚠️ Cosa si semplifica
- SPA routing → navigazione WP multi-page
- Filtri catalogo: AJAX anziché React state
- Book covers: real img anziché CSS gradient
- Transizioni di pagina: CSS semplice
- WooCommerce: template override necessario
- Deploy pipeline: FTP/Git anziché npm build

---

## Confronto Architetture A / B / C

| Criterio | A: WP Ottimizzato | B: Headless | C: Static Export |
|----------|---|---|---|
| Brand Fidelity | ⬤⬤⬤⬤⬤ | ⬤⬤⬤⬤⬤ | ⬤⬤⬤⬚⬚ |
| Fit InfinityFree | ⬤⬤⬤⬤⬤ | ⬚⬚⬚⬚⬚ | ⬚⬚⬚⬚⬚ |
| CMS Usability | ⬤⬤⬚⬚⬚ | ⬚⬚⬚⬚⬚ | ⬚⬚⬚⬚⬚ |
| Complessità Dev | ⬚⬚⬚⬚⬚ | ⬚⬚⬚⬚⬚ | ⬚⬚⬚⬚⬚ |
| WooCommerce Safety | ⬚⬚⬚⬚⬚ | ⬚⬚⬚⬚⬚ | N/A |
| Timeline | 3–4 sett. | 4–5 sett. | 1–2 sett. |

---

## Component Mapping: React → WordPress PHP

| React | WP PHP | Rischio |
|-------|--------|---------|
| Shell.jsx / Nav | header.php | Basso |
| ChatFab | footer.php | Basso |
| Home.jsx | front-page.php | Basso |
| Catalog.jsx | archive-book.php | Medio |
| BookCard.jsx | template-parts/book-card.php | Basso |
| BookDetail.jsx | single-book.php | Basso |
| MyBiblioChat.jsx | template-parts/mybiblio-chat.php | **Medio** |
| Account.jsx | page-account.php | Medio |
| Checkout.jsx | custom form OR WC | **Alto** |
| Plus.jsx | page-plus.php | Basso |
| RentVsBuy.jsx | static page | Nullo |

---

## Tema Structure

```
biblio-theme/ (~65 file, 5% inode budget)
├── Root files (header, footer, templates)
├── template-parts/ (reusable components)
├── assets/ (CSS, JS, fonts)
├── inc/ (functions, CPT, AJAX, perf)
└── woocommerce/ (override FASE 2)
```

---

## CRITICAL CODE PATTERNS

### 1️⃣ Fragment Caching (CPU mitigation)
```php
function biblio_get_cached_book_grid($query_args, $cache_key, $ttl = 3600) {
    $cached = get_transient('biblio_grid_' . $cache_key);
    if ($cached !== false) return $cached;
    // ... render grid ...
    set_transient('biblio_grid_' . $cache_key, $output, $ttl);
    return $output;
}
```

### 2️⃣ wp-config.php Hardening
```php
define('WP_MEMORY_LIMIT', '128M');
define('WP_CACHE', true);
define('WP_POST_REVISIONS', 3);
define('DISALLOW_FILE_EDIT', true);
```

---

## Design Compromises + Mitigations

| Compromise | Impact | Mitigation |
|-----------|--------|-----------|
| SPA routing → multi-page | UX less "app-like" | CSS @view-transition + progress bar |
| BookCover: gradient → real image | Need actual images | CSS fallback gradient |
| AJAX filters: 150-300ms latency | Slightly slower | Skeleton loading |
| MyBibliò: 1-2s → 3-5s latency | Chat feels slow | "typing..." indicator |
| WooCommerce: default UI clash | Design breaks | /woocommerce/ override |

---

## Inode Budget

```
TOTAL: ~30,000 inodes

WP Core             ~1,100 files   ✅
Bibliò Theme        ~65 files      ✅
WP Super Cache      ~50 files      ✅
Yoast SEO Free      ~200 files     ✅
Media (Cloudinary)  ~0 files       ✅
─────────────────────────────────
SUBTOTAL            ~1,435 files   = 4.8% ✅

+ WooCommerce       ~2,400 files   = 13.1% total (test first)
```

---

## Timeline — Option A (3–4 settimane)

**Sett. 1:** Foundation (CPT, templates, cache)  
**Sett. 2:** Core Pages (home, catalog, detail)  
**Sett. 3:** Dynamic Features (chat, account, plus)  
**Sett. 4:** WooCommerce Test + Deploy

---

## NEXT

- Implementazione screens → vedi [[SCREEN_IMPLEMENTATION_GUIDE]]
- Referenza rapida → vedi [[SCHEMA_Biblio_WordPress_ConversationSummary]]
