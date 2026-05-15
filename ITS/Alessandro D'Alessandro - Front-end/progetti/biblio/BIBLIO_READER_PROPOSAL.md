# Bibliò — Proposta Reader Libri Noleggiati

> Proposta tecnica per area di lettura libri noleggiati su WordPress + InfinityFree.
> Data: 2026-05-15 — da rivalutare prima di implementare.
> Collegato a: [[README]], [[CONTEXT_NUOVA_SESSIONE]], [[BIBLIO_AUDIT_2026-05-14]]

---

## Obiettivo

Permettere agli utenti che hanno noleggiato un libro di leggerlo dentro il sito, con testo che si adatta al display (reflowable) e caricamento incrementale (no file intero, solo il capitolo corrente).

---

## Vincoli ambiente (recap)

- Hosting **InfinityFree** gratuito: 5 GB disk, ~30k inode, ~40-128 MB RAM, kill processi pesanti
- PHP 8.3, MySQL 8.0, WordPress + WooCommerce attivi
- cURL outbound bloccato (irrelevante per il reader, tutto interno)
- No streaming reale, no `X-Sendfile`
- Tema standalone `biblio-theme` v0.3.0, deploy file singolo via File Manager

---

## Strategia: ePub + estrazione per capitolo

### Perché ePub e non PDF

- **PDF** = layout fisso, mobile inutilizzabile, non si adatta al display
- **ePub** = ZIP di XHTML per capitolo + CSS + immagini → reflowable nativo
- PHP ha `ZipArchive` builtin → estraibile 1 capitolo alla volta senza librerie esterne

### Principio chiave

**Mai servire il file intero.** Ogni richiesta = 1 capitolo (50-200 KB) estratto on-the-fly dallo ZIP, con check noleggio attivo + non scaduto.

---

## Architettura

```
Browser                            Server (PHP / WP REST)
─────────                          ───────────────────────
GET /leggi/<book-slug>/      →     page-reader.php
                                   - check utente loggato + noleggio valido
                                   - render shell reader (no contenuto libro)

GET biblio/v1/read/{id}/manifest → - ZipArchive open
                             ←     JSON: TOC (spine + titoli + idx ultimo letto)

GET biblio/v1/read/{id}/chapter/{n} → - check noleggio (scadenza order meta)
                                      - ZipArchive::getFromName(spine[n])
                                      - sanitize via wp_kses_post
                             ←     HTML pulito del solo capitolo

Render client-side:
- CSS columns per paginazione visiva ("2-4 pagine alla volta")
- Swipe / frecce per avanzare pagina virtuale
- Fine capitolo → fetch successivo
- Salva chapter_idx + scroll_pos in user_meta ogni X secondi
```

---

## Componenti da costruire

```
biblio-theme/
├── inc/
│   ├── rental.php          ← auth: check ordine WC attivo + scadenza 30gg
│   │                         meta order: _biblio_rental_expires_at
│   ├── reader-api.php      ← REST endpoints manifest + chapter
│   │                         ZipArchive + sanitize + rate limit
│   └── reader-progress.php ← save/load posizione lettura in user_meta
├── page-reader.php         ← template lettore (shell HTML)
├── assets/
│   ├── js/reader.js        ← paginazione CSS columns, swipe, save progress
│   └── css/reader.css      ← tipografia lettura, sepia/notte, responsive
└── books-protected/        ← FUORI da uploads, .htaccess deny all
    └── BOOK-1001.epub
```

---

## Come funziona la paginazione "2-4 pagine per volta"

Lato client, non server. Il capitolo arriva come blocco HTML; il container reader usa:

```css
.reader-content {
  column-width: 100vw;        /* 1 colonna mobile */
  column-gap: 0;
  height: calc(100vh - 80px); /* viewport pieno meno header */
  overflow: hidden;
}

@media (min-width: 900px) {
  .reader-content {
    column-width: 50vw;       /* 2 colonne tablet/desktop */
  }
}
```

Avanzamento pagina = `translateX(-100vw)` con animazione. JS gestisce swipe (touch) + frecce (keyboard) + fine capitolo → fetch successivo.

Controlli utente: font size, line-height, tema (light/sepia/notte) → CSS variables sul container, persistite in localStorage.

---

## Sicurezza e anti-leak

| Misura | Implementazione |
|---|---|
| File ePub fuori da public | `books-protected/` + `.htaccess` `Deny from all` |
| Auth ogni capitolo | check `is_user_logged_in()` + ordine WC + scadenza meta |
| Rate limit | max 1 capitolo / 3s per utente (transient) → scoraggia scraping |
| Sanitize HTML | `wp_kses_post()` su contenuto estratto (rimuove script/style malevoli) |
| Niente URL diretto al file | tutto via REST con nonce + cookie auth |

**Limite onesto:** chi vuole scrapare con script può farlo. Non è DRM, è friction. Per un MVP/portfolio con libri di pubblico dominio è proporzionato.

---

## Stima sforzo

| Pezzo | Effort |
|---|---|
| `inc/rental.php` (auth + scadenza order meta) | 1-2h |
| `inc/reader-api.php` (manifest + chapter con ZipArchive) | 2-3h |
| `page-reader.php` template | 1h |
| `assets/js/reader.js` (paginazione + swipe + save progress) | 4-6h |
| `assets/css/reader.css` (typo, sepia/notte, responsive) | 2-3h |
| Test con 1 ePub Pirandello / Verga | 1h |
| **Totale MVP** | **~12-16h** |

---

## Scope MVP vs futuro

**MVP (Fase 7 audit, da valutare):**
- 1-3 ePub demo di pubblico dominio (Pirandello, Verga, Pirandello)
- Auth via ordine WC simulato (no pagamento reale)
- Reader funzionante con paginazione + progress save
- Mobile + desktop

**Fuori MVP:**
- Libri sotto copyright (problema legale, non tecnico — hosting gratuito + libri commerciali = no)
- Annotazioni / highlight utente
- TTS (text-to-speech)
- Sincronizzazione progress multi-device avanzata
- DRM serio

---

## Rischi e mitigazioni

| Rischio | Mitigazione |
|---|---|
| `ZipArchive` lento con ePub grandi (>10 MB) | Limite upload 5 MB per ePub; cache capitolo in transient 1h |
| IF kill processo se richiesta troppo lunga | Capitoli piccoli (max 300 KB HTML); timeout PHP 30s sufficiente |
| Inode esaurito se molti libri | ePub = 1 file ciascuno; 100-300 libri ok dentro 30k inode totali |
| Bandwidth IF non dichiarato | Monitor primi giorni; se taglia, valutare CDN cache (Cloudflare free) |
| Copyright libri reali | Solo pubblico dominio nel MVP; per prod serve hosting pagato + licenze |

---

## Decisioni aperte (da chiudere prima di partire)

1. **Formato fonti**: solo ePub o anche PDF reflowable (PDF.js text layer)?
2. **Modello noleggio**: meta su ordine WC o tabella custom `wp_biblio_rentals`?
3. **Scadenza**: 30gg da acquisto fisso, o configurabile per libro?
4. **Pagina lista "i miei libri"**: dentro `/il-mio-account/` o sezione dedicata `/biblioteca/`?
5. **Demo libri**: quali 3 titoli di pubblico dominio?

---

## Prossimo passo se approvato

Partire da `inc/reader-api.php` (parsing ePub + endpoint chapter) perché decide il contratto di tutto il resto: se quello regge su IF, il reader frontend è solo CSS + JS senza sorprese.

---

*Proposta v1 — 2026-05-15. Rivalutare insieme a [[BIBLIO_AUDIT_2026-05-14]] per decidere se diventa Fase 7.*
