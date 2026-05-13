# 📋 BRIEF: Convertire Bibliò a WordPress su Infinity Free

**Versione:** 1.0  
**Data:** 2026-05-12  
**Status:** ✅ Ricerca completata (Perplexity)  
**Audience:** Technical Lead  

## Decisione Go/No-Go
- **Hosting:** InfinityFree — Free Tier
- **Stack:** WP 7.x · PHP 8.3 · MySQL 8.0
- **Architecture:** Option A (WordPress Theme Ottimizzato)

---

## 🎯 Obiettivo

Creare un WordPress theme basato sul design system Bibliò che funzioni affidabilmente su Infinity Free hosting (vincoli: 5GB disk, CPU/memory limitati, inode limits).

---

## ⚙️ Vincoli Tecnici Confermati (2026)

| Vincolo | Valore | Impatto su Bibliò | Soluzione |
|---------|--------|-------------------|-----------|
| **Disk Space** | 5 GB totale | Asset React (CSS, JS, SVG) devono essere minimalisti | Minify CSS/JS; external CDN per media |
| **PHP Version** | 8.3 | ✅ Full compatible con WP 7.x e custom PHP | Nessun problema |
| **Database** | MySQL 8.0 / MariaDB 11.4 | WP standard ok; no foreign keys | Use simple queries, avoid InnoDB advanced features |
| **CPU/Memory** | Undocumented limits (~40–128MB) | **CRITICO**: React-like heavy themes timeout | Aggressive caching (WP Super Cache); lazy-load components |
| **Inodes/Files** | ~30k limit | Plugin/theme file bloat blocks installs | Minimal plugins; prune unused files regularly |
| **Bandwidth** | Unlimited | No direct limit | Tie to CPU throttling via caching |
| **WooCommerce** | Not documented 2026 | **UNKNOWN**: pre-2026 reports show DB/CPU issues | Test heavily before deploy; light config only |

**Fonte Ricerca:** Perplexity (2026-05-12) con verifica multi-fonte su forum Infinity Free ufficiale, blog tecnici 2025-2026, YouTube tutorials.

---

## ⚠️ Top 3 Critical Risks

### 1️⃣ CPU/Memory Throttling (Impact 4/5)
- **Problema:** Heavy JavaScript + PHP rendering timeout su traffic spikes
- **Causa:** Infinity Free limita CPU/memory a ~40–128MB; React-like frontend + PHP backend supera limite
- **Soluzione:** Aumentare WP memory limit in wp-config.php; aggressive caching (WP Super Cache)
- **Alternativa:** Build static-optimized theme; lazy-load components; consider static export via Netlify

### 2️⃣ Inode Exhaustion (Impact 4/5)
- **Problema:** File-heavy theme + plugins hit ~30k limit, bloccano aggiornamenti e installazioni
- **Causa:** Bibliò design system ha molti asset (SVG, fonts, CSS vari); plugins aggravano
- **Soluzione:** Minimal plugin ecosystem; lean theme code; regular file audits via FTP

### 3️⃣ Uncertain WooCommerce Performance (Impact Unknown)
- **Problema:** No 2026 data ufficiale; pre-2026 reports mostrano intermittent DB errors
- **Causa:** WooCommerce è plugin-heavy; Infinity Free ha query limits indocumentati
- **Soluzione:** Test WooCommerce su istanza separata prima di deploy live

---

## 💡 Design Approach Recommendations

### ✅ Option A: Fully Optimized WordPress Theme (RECOMMENDED)
- Convertire Bibliò React components → WordPress PHP templates
- Usare design tokens da `colors_and_type.css`
- Minimal JavaScript: solo nav/footer statici + light interactivity
- Static HTML export option via plugin per pagine critiche (fallback)

**Pro:** Full WordPress ecosystem, easy CMS management, libreria Bibliò già completa  
**Con:** Require aggressive optimization per Infinity Free limits  
**Timeline:** 3-4 settimane

---

## 📋 Pre-Deploy Testing Checklist

**Prima di andare live su Infinity Free:**

- [ ] PHP 8.3 compatibility test (tema code locally)
- [ ] 5GB disk quota test (upload theme + sample media)
- [ ] MySQL query performance (WP schema import)
- [ ] CPU/memory limits (WP memory tweak; simulate load)
- [ ] Inode count post-install (target <25k files)
- [ ] WooCommerce test (if needed; separate DB instance)
- [ ] Caching strategy test (WP Super Cache active)
- [ ] Static export fallback test (Simply Static plugin)

---

## 📚 References & Sources

**Research Source:** Perplexity (2026-05-12)  
**Official Docs:**
- https://www.infinityfree.com (current specs)
- https://forum.infinityfree.com (community issues)

**Key Technical References:**
- CPU/Memory limits: https://space-node.net/blog/infinity-free-hosting-review-2026 (2026-04-02)
- Inode limits: https://forum.infinityfree.com/t/how-much-disk-space-is-free-account/80589
- WooCommerce reports: https://forum.infinityfree.com/t/facing-issues-when-i-am-using-woocommerce-plugin/85097
- Performance tuning: https://www.youtube.com/watch?v=AaZIzFG41gg

---

**NEXT STEP:** Architettura dettagliata → vedi [[ADR_WordPress_InfinityFree]]
