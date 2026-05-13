# 📊 SCHEMA CONVERSAZIONE — Bibliò WordPress su InfinityFree

**Sintesi Ottimizzata | Riferimento Rapido durante lo Sviluppo**

**Versione:** 1.0 | **Status:** ✅ Confermato — Option A

---

## 🎯 DECISIONI PRESE

```
PROJECT DECISION
├── Architecture: ✅ OPTION A (WordPress Theme Ottimizzato)
├── WooCommerce: ⏳ FASE 2 — test separato, non day-1
├── Hosting: InfinityFree (5GB, ~30k inode, CPU ~40-128MB)
├── Stack: WP 7.x | PHP 8.3 | MySQL 8.0 | Vanilla JS (no jQuery)
└── Timeline: 3-4 settimane
```

---

## 🔥 VINCOLI CRITICI (Non superarli)

| Vincolo | Limite | Azione |
|---------|--------|--------|
| **CPU/Memory** | ~40-128MB | WP Super Cache mandatory + fragment caching |
| **Disk** | 5 GB | Media su Cloudinary CDN (zero local images) |
| **Inodes** | ~30k | Max 4 plugin; tema <70 file; no page builder |
| **WooCommerce** | Unknown 2026 | TEST 72h su staging separato PRIMA di deploy live |
| **MyBibliò Latency** | <4s | Timeout PHP 30s; Haiku model (non Sonnet) |

---

## 📁 FILE CREATI

```
📁 01_Research/
├── BRIEF_WordPress_InfinityFree.md           ← spec richieste
└── ADR_WordPress_InfinityFree.md             ← architettura completa

📁 02_Development/
├── SCREEN_IMPLEMENTATION_GUIDE.md            ← per-screen PHP guide
└── SCHEMA_Biblio_WordPress_ConversationSummary.md (questo file)

📁 GitHub (original):
├── dev-biblio/colors_and_type.css           ← PORTARE 1:1 in biblio-tokens.css
├── dev-biblio/fonts/                        ← WOFF2 only
└── dev-biblio/ui_kits/biblio/               ← React → PHP mapping
```

---

## 🏗️ TEMA STRUCTURE (~65 file, 5% inode)

```
biblio-theme/
├── header.php                    ← Nav (Shell.jsx)
├── footer.php                    ← Footer + ChatFab
├── front-page.php                ← Home.jsx
├── archive-book.php              ← Catalog.jsx
├── single-book.php               ← BookDetail.jsx
│
├── template-parts/
│   ├── book-card.php             ← BookCard.jsx
│   ├── book-cover.php            ← BookCover
│   ├── mybiblio-chat.php         ← MyBibliò chat
│   └── [...altri...]
│
├── assets/
│   ├── css/biblio-tokens.css     ← FROM: colors_and_type.css (1:1)
│   ├── js/navigation.js          ← vanilla JS
│   └── fonts/                    ← WOFF2 only
│
└── inc/
    ├── post-types.php            ← CPT book
    ├── ajax-handlers.php         ← chat + filters
    └── performance.php           ← fragment caching
```

---

## ⚡ CRITICAL CODE PATTERNS

### Fragment Caching (CPU mitigation)
```php
function biblio_get_cached_book_grid($args, $key, $ttl=3600) {
    if ($c = get_transient("biblio_grid_$key")) return $c;
    // render...
    set_transient("biblio_grid_$key", $output, $ttl);
    return $output;
}
```

### wp-config.php
```php
define('WP_MEMORY_LIMIT', '128M');
define('WP_CACHE', true);
define('WP_POST_REVISIONS', 3);
define('DISALLOW_FILE_EDIT', true);
```

---

## 🚨 DESIGN COMPROMISES

| Compromise | Impact | Mitigation |
|-----------|--------|-----------|
| SPA routing → multi-page | UX less "app-like" | CSS @view-transition + progress bar |
| Gradients → real images | Need actual covers | CSS fallback gradient |
| AJAX latency 150-300ms | Slightly slower | Skeleton loading |
| Chat latency 3-5s | Feels slow | "typing..." indicator |
| WooCommerce visual clash | Design breaks | /woocommerce/ override |

---

## 📈 INODE BUDGET

```
LIMIT: ~30,000

WP Core + Theme + Plugins    ~1,435 files   ✅ 4.8%
+ WooCommerce (FASE 2)       ~2,400 files   ⚠️ 13.1% (test first)

DO NOT ADD:
❌ Elementor / Divi / page builders
❌ ACF Pro
❌ Large plugin suites
```

---

## 🧪 TESTING PROTOCOL

### Week 1-3: Local Dev
- PHP 8.3 compatibility
- Fragment cache invalidation
- PageSpeed: target >70 mobile
- No N+1 queries

### Week 4: InfinityFree Staging
- Full deploy
- WooCommerce test (72h monitoring) ⚠️
- Inode count <5k (without WC)
- Error log: zero 500 errors

---

## ⏱️ TIMELINE

```
WEEK 1: Foundation
├── WP setup + CPT + tassonomia
├── colors_and_type.css → biblio-tokens.css
└── header + footer templates

WEEK 2: Core Pages
├── front-page.php
├── archive-book.php
├── catalog-filters.js
└── single-book.php

WEEK 3: Dynamic Features
├── MyBibliò chat
├── page-account.php
└── page-plus.php

WEEK 4: WooCommerce Test + Deploy
├── Test WC 72h separate ⚠️
├── If stable: /woocommerce/ override
├── Deploy + 24h monitoring
└── Pre-deploy checklist
```

---

## 🚀 GO/NO-GO DECISION TREE

```
START: Option A (WP Theme)
│
├─→ WooCommerce Day-1 Required?
│   ├─→ YES  → SWITCH TO OPTION B (Headless)
│   ├─→ NO   → PROCEED OPTION A [3-4w]
│   └─→ TEST → PROCEED + test Week 4
│
├─→ Week 4: WC Test Results
│   ├─→ Stable        → DEPLOY WC ✅
│   ├─→ Unstable      → FALLBACK: custom form
│   └─→ CRITICAL      → ACTIVATE OPTION B
│
└─→ Deploy to Production
    ├─→ Monitor 24h (CPU, errors, inode)
    └─→ Simply Static backup ON STANDBY
```

---

## 📞 QUICK WINS (Start here se bloccato)

1. **Tema setup:** Copy ADR section Structure
2. **Colors:** Copy colors_and_type.css → biblio-tokens.css
3. **Header/Footer:** Copy Shell.jsx → header.php
4. **BookCard:** Copy template pattern
5. **Caching:** Copy fragment cache function
6. **wp-config.php:** Copy performance tweaks

---

## ✅ PRE-DEPLOY CHECKLIST

```
PHP & Compatibility:
[ ] PHP 8.3 zero warnings
[ ] WP 7.x health check passed

Performance:
[ ] PageSpeed >70 mobile
[ ] No N+1 queries
[ ] Fragment cache working

Inode & Disk:
[ ] <5k files (without WC)
[ ] CSS/JS minified
[ ] Fonts WOFF2 only

WooCommerce:
[ ] 72h test on staging ✅ OR fallback form ready

MyBibliò:
[ ] Timeout handler + fallback message

Security:
[ ] SSL 301 redirect
[ ] API key NOT in JS

Fallback:
[ ] Simply Static tested
[ ] Netlify standby ready
```

---

**UPDATED:** 2026-05-13  
**STATUS:** Option A confirmed | Claude Design in progress  
**NEXT:** Week 1 foundation → Request starter files ZIP

**Vedi anche:**
- [[BRIEF_WordPress_InfinityFree]] — requirements
- [[ADR_WordPress_InfinityFree]] — full architecture
- [[SCREEN_IMPLEMENTATION_GUIDE]] — per-screen specs
