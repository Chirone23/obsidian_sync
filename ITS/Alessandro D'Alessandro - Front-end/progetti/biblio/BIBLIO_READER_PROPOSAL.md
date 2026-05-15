# Bibliò — Proposta Reader Libri Noleggiati (v3)

> Proposta tecnica per area di lettura libri noleggiati su WordPress + InfinityFree.
> v1: 2026-05-15 — proposta iniziale
> v2: 2026-05-15 — revisione post-validazione #1 (red flag IF integrati come prerequisiti hard)
> v3: 2026-05-15 — revisione post-validazione #2 (blocker reali identificati, 3 opzioni concrete)
> Collegato a: [[README]], [[CONTEXT_NUOVA_SESSIONE]], [[BIBLIO_AUDIT_2026-05-14]]

---

## ⚠️ Stato decisionale

**Decisione in sospeso prima di procedere.** La v2 conteneva 3 assunzioni che non reggono alla seconda validazione:

1. **CF free non supporta cache-key-per-utente** (servono Workers ~5€/mese o Enterprise). Sul free piano hai solo `Bypass Cache on Cookie` → cache utente-specifica impossibile senza budget.
2. **SHORTINIT non carica `pluggable.php`** → niente `wp_validate_auth_cookie`. Loader snello deve o ricaricare pluggable (≈ bootstrap pieno) o riscrivere auth a mano (rischio sicurezza in MVP). Guadagno reale ~3-4× non 10×.
3. **ToS InfinityFree vieta esplicitamente** file hosting, media streaming e uso commerciale. Un endpoint autenticato ad alto traffico è esattamente il pattern che bannano. Ban diventa modalità di failure probabile a 50+ utenti, non edge case.

Le 3 opzioni in fondo al doc chiudono questa proposta in una direzione concreta.

---

---

## Obiettivo

Permettere agli utenti che hanno noleggiato un libro di leggerlo dentro il sito, con testo reflowable che si adatta al display e caricamento incrementale (solo capitolo corrente, mai file intero).

---

## TL;DR cambiamenti v1 → v2

La v1 era scritta come se IF fosse hosting normale: in laboratorio funziona, in prod free salta al primo utente attivo. La v2 integra 5 fix come **prerequisiti hard**:

1. **Pre-estrazione capitoli in cache statica** (no `wp_kses_post` on-the-fly ad ogni richiesta)
2. **Cloudflare davanti obbligatorio** (no "valutare se serve")
3. **Tabella custom `wp_biblio_rentals`** (no order meta WC)
4. **Endpoint snello senza bootstrap WC** per `chapter/{n}` (SHORTINIT o loader minimale)
5. **Rate limit su `user_meta` con timestamp**, non transient (evita scritture su `wp_options`)

---

## Vincoli ambiente — letti correttamente stavolta

- **Hits/day ~50k** per account free IF (non "bandwidth non dichiarato")
- **CPU/EP limit aggressivo**: ogni hit REST = bootstrap WP completo (~30-60 MB RAM) → rischio 503 in burst
- **No object cache**: transient finiscono in `wp_options` (DB write per ogni uso)
- **Ban IF senza preavviso** per abuso CPU
- ~40-128 MB RAM, PHP 8.3, MySQL 8.0, `ZipArchive` builtin, cURL outbound bloccato

---

## Strategia: ePub + cache statica pre-estratta + CDN

### Principio rivisto

**Mai servire il file intero. E mai estrarre on-the-fly.**

1. All'upload del libro → job che estrae **tutti i capitoli**, sanitizza con `wp_kses_post` una volta sola, salva HTML statico in `books-protected/cache/{book_id}/{n}.html`
2. Endpoint `chapter/{n}` legge il file pre-sanitizzato dalla cache (solo `file_get_contents` + auth check) — niente `ZipArchive` runtime, niente sanitize runtime
3. Cloudflare cacha la risposta per utenti autenticati con cache key derivata dal cookie auth + book_id + chapter_idx (TTL 24h+)

Trasformiamo un problema CPU/RAM in un problema inode (che ne abbiamo): 100-300 libri × ~20 capitoli = 2-6k file dentro budget 30k.

---

## Architettura v2

```
UPLOAD libro (one-time, admin)
─────────────────────────────
admin upload .epub
  → inc/reader-ingest.php
    - ZipArchive open
    - per ogni capitolo dello spine:
        - estrai XHTML
        - wp_kses_post() (sanitize una volta)
        - opzionale: strip immagini non essenziali (riduce peso)
        - salva books-protected/cache/{book_id}/{n}.html
    - salva manifest (TOC) in cache/{book_id}/manifest.json
  → libro pronto, file ePub originale può anche essere rimosso


READING (runtime, ogni utente)
──────────────────────────────
Browser                           Server                       Cloudflare
─────────                         ───────                      ──────────
GET /leggi/<book-slug>/      →    page-reader.php              MISS → origin
                                  - check noleggio (1 query    cache HTML shell
                                    SELECT su wp_biblio_rentals)
                                  - render shell (no contenuto)

GET biblio/v1/read/{id}/manifest → loader snello              HIT 24h → CDN
                                  - skip WC bootstrap
                                  - file_get_contents(manifest.json)
                                  - auth: 1 SELECT su rentals
                             ←    JSON TOC

GET biblio/v1/read/{id}/chapter/{n} → loader snello          HIT 24h → CDN
                                    - auth: 1 SELECT su rentals
                                    - rate limit: check user_meta
                                      _biblio_last_chapter_ts
                                    - file_get_contents(cache/{id}/{n}.html)
                                    - update user_meta progress
                             ←    HTML statico pre-sanitizzato

Client: CSS columns paginazione + swipe (invariato vs v1)
```

---

## Componenti rivisti

```
biblio-theme/
├── inc/
│   ├── reader-ingest.php      ← NUOVO: admin upload + pre-estrazione cache
│   ├── reader-loader.php      ← NUOVO: bootstrap WP minimale per endpoint chapter
│   │                              define('SHORTINIT', true) o loader custom
│   │                              salta WC interamente
│   ├── reader-api.php         ← endpoints manifest + chapter (legge cache statica)
│   ├── rental.php             ← CRUD wp_biblio_rentals, check scadenza
│   └── reader-progress.php    ← save/load posizione in user_meta
├── page-reader.php            ← template shell reader
├── assets/
│   ├── js/reader.js           ← paginazione CSS columns + swipe + progress
│   └── css/reader.css         ← typo lettura, sepia/notte, responsive
└── books-protected/
    ├── .htaccess              ← Deny from all
    ├── originals/             ← .epub originali (post-ingest opzionali)
    └── cache/
        └── {book_id}/
            ├── manifest.json
            ├── 0.html
            ├── 1.html
            └── ...

DB:
wp_biblio_rentals
├── id, user_id (idx), book_id (idx), expires_at (idx)
├── created_at, source_order_id (nullable)
└── PRIMARY (id), UNIQUE (user_id, book_id)
```

---

## Tabella `wp_biblio_rentals` (decisione chiusa)

Niente meta su ordine WC. Tabella custom dedicata:

```sql
CREATE TABLE wp_biblio_rentals (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  book_id BIGINT UNSIGNED NOT NULL,
  expires_at DATETIME NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  source_order_id BIGINT UNSIGNED NULL,
  UNIQUE KEY user_book (user_id, book_id),
  KEY user_active (user_id, expires_at),
  KEY book_id (book_id)
) ENGINE=InnoDB;
```

Check noleggio = 1 query indicizzata:

```sql
SELECT 1 FROM wp_biblio_rentals
WHERE user_id = ? AND book_id = ? AND expires_at > NOW()
LIMIT 1;
```

Hook su `woocommerce_order_status_completed` per inserire la riga quando un ordine include un prodotto noleggiabile. Disaccoppiato dal resto: il reader non chiama mai codice WC.

---

## Bootstrap snello per endpoint capitolo

Endpoint `chapter/{n}` è il più caldo (1 fetch per cambio capitolo virtuale × N utenti). Bootstrap WP completo è insostenibile.

**Opzioni in ordine di preferenza:**

1. **File PHP dedicato fuori da REST WP** (`/wp-content/themes/biblio-theme/reader-chapter.php`) con `define('SHORTINIT', true)` prima di `wp-load.php` → carica solo DB + base, no plugin, no theme, no WC. Auth via validazione cookie WP a mano (`wp_validate_auth_cookie`).
2. Se SHORTINIT rompe `wp_validate_auth_cookie` su IF, fallback: bootstrap WP normale ma `remove_all_actions('init')` prima di servire (riduce caricamento plugin pesanti).
3. Ultimo: REST normale + Cloudflare cache aggressiva che assorbe il grosso (origin colpita 1× per capitolo per finestra di cache).

**Da testare empiricamente al primo prototipo.**

---

## Cloudflare come prerequisito hard

Non opzionale. Configurazione minima Fase 7:

- DNS Bibliò → proxied via Cloudflare (orange cloud)
- Page Rule su `/wp-json/biblio/v1/read/*/chapter/*`: Cache Everything, Edge TTL 24h, Browser TTL 1h
- Cache key include cookie auth user (Cloudflare Workers o cache key custom) → utenti diversi non si vedono il contenuto a vicenda
- WAF rule: blocca user agent script noti, rate limit 30 req/min per IP sull'endpoint chapter

Con CDN davanti, IF vede 1 richiesta ogni 24h per (utente × capitolo) invece di N. Hits/day giornaliero rientra anche con 50+ lettori attivi.

---

## Rate limit senza transient

Transient → `wp_options` write ogni hit = DB pressure. Sostituire con:

```php
$last_ts = (int) get_user_meta($user_id, '_biblio_last_chapter_ts', true);
if (time() - $last_ts < 3) {
    wp_send_json_error('rate_limited', 429);
}
update_user_meta($user_id, '_biblio_last_chapter_ts', time());
```

`user_meta` è già scritto comunque per progress → costo marginale.

---

## Paginazione client (invariata da v1)

```css
.reader-content {
  column-width: 100vw;
  column-gap: 0;
  height: calc(100vh - 80px);
  overflow: hidden;
}
@media (min-width: 900px) {
  .reader-content { column-width: 50vw; } /* 2 colonne */
}
```

JS: swipe + frecce + fine capitolo → fetch successivo. Font size / line-height / tema (light/sepia/notte) via CSS vars + localStorage.

---

## Pre-processing ePub all'ingest

Limite 5 MB della v1 era stretto. v2:

- Limite upload **15 MB**
- All'ingest: opzione "strip immagini non-cover" (toggle admin) per ridurre peso cache statica
- Cover libro: estratta separatamente e salvata come `cache/{book_id}/cover.jpg`

---

## Sicurezza e anti-leak

| Misura | Implementazione |
|---|---|
| ePub originali fuori da public | `books-protected/originals/` + `.htaccess Deny` |
| Cache HTML fuori da public | `books-protected/cache/` + `.htaccess Deny`; servita solo via PHP |
| Auth ogni capitolo | 1 SELECT su `wp_biblio_rentals` + validate cookie |
| Rate limit | `user_meta` timestamp, 1 capitolo / 3s |
| Sanitize HTML | `wp_kses_post` **all'ingest** (una volta), no runtime |
| Cloudflare WAF | rate limit IP + UA filtering |

Limite onesto: zero DRM. Friction per scraping casuale, non protezione anti-pirata professionale. Coerente con MVP/portfolio + libri pubblico dominio.

---

## Stima sforzo (revisionata)

| Pezzo | Effort |
|---|---|
| Migration `wp_biblio_rentals` + `inc/rental.php` (CRUD + hook WC order completed) | 2-3h |
| `inc/reader-ingest.php` (admin upload + ZipArchive + sanitize + cache write) | 3-4h |
| `inc/reader-loader.php` (bootstrap snello + test SHORTINIT su IF) | 2-4h ⚠️ rischio empirico |
| `inc/reader-api.php` (endpoints manifest + chapter, legge cache) | 1-2h |
| `page-reader.php` template | 1h |
| `assets/js/reader.js` (paginazione + swipe + progress) | 4-6h |
| `assets/css/reader.css` (typo, sepia/notte, responsive) | 2-3h |
| Setup Cloudflare + Page Rules + test cache | 1-2h |
| Test end-to-end con 1 ePub Pirandello | 2h |
| **Totale MVP v2** | **~18-27h** |

Aumento vs v1 (12-16h) giustificato da: ingest pipeline, loader snello, CDN setup, tabella custom.

---

## Decisioni chiuse in v2

| Decisione v1 | v2 |
|---|---|
| Formato fonti | **Solo ePub** (PDF reflowable scartato, complica troppo) |
| Modello noleggio | **Tabella custom `wp_biblio_rentals`** (no order meta) |
| Scadenza | **30gg fisso** dalla creazione riga rental (configurabile per libro = post-MVP) |
| Pagina "i miei libri" | **`/biblioteca/`** sezione dedicata (non dentro `/il-mio-account/`) |
| Demo libri | **Da scegliere**: 3 titoli pubblico dominio (proposta: Pirandello _Il fu Mattia Pascal_, Verga _I Malavoglia_, Svevo _La coscienza di Zeno_) |
| Sanitize | **All'ingest, una volta sola** (non runtime) |
| Cache | **Statica su disco + Cloudflare** (non transient runtime) |

---

## Rischi residui v2

| Rischio | Mitigazione |
|---|---|
| SHORTINIT incompatibile con auth cookie su IF | Test al primo sprint; fallback bootstrap normale + Cloudflare assorbe |
| Ban IF per abuso CPU comunque | Backup deploy strategy: Cloudflare Pages per asset statici + hosting pagato low-cost (~3€/mese) come piano B prima di prod |
| Inode esauriti se 300+ libri | Monitor `df -i` via plugin diagnostico; sotto 80% conservativo |
| Cache stantia se libro aggiornato | Versioning path: `cache/{book_id}/v{n}/...` + bump version all'ingest |
| Cloudflare cache leak tra utenti | Cache key include hash cookie auth; in dubbio, Bypass Cache su cookie presente + cache solo manifest (più piccolo, OK colpire origin per chapter) |

---

## Roadmap implementativa proposta

**Sprint 1 — Fondamenta (5-7h)**
- Tabella `wp_biblio_rentals` + `rental.php`
- `reader-ingest.php` con 1 ePub test
- Verifica cache scritta correttamente

**Sprint 2 — Endpoint + loader (4-6h)**
- `reader-loader.php` con test SHORTINIT su IF reale
- `reader-api.php` manifest + chapter
- Setup Cloudflare + page rule

**Sprint 3 — Frontend (6-9h)**
- `page-reader.php` + `reader.js` + `reader.css`
- Test paginazione mobile + desktop

**Sprint 4 — Hardening (3-5h)**
- Rate limit, progress save, pagina `/biblioteca/`
- Test carico simulato (10 utenti concorrenti)
- Documentazione deploy

---

## Blocker reali v3 (post-validazione #2)

### 🔴 Blocker

| # | Problema | Impatto |
|---|---|---|
| B1 | **CF free**: solo `Bypass on Cookie`, no cache-key-per-utente senza Workers (~5€/mo) | Fix #2 v2 non implementabile gratis. O bypass su auth → CF non aiuta → IF salta come v1. O cache pubblica chapter → leak fra utenti |
| B2 | **SHORTINIT** non carica `pluggable.php` → no `wp_validate_auth_cookie`, no `update_user_meta` | Loader snello v2 finisce ~10-15 MB RAM (3-4× guadagno), non 2-5 MB. Riscrittura auth a mano = rischio security in MVP |
| B3 | **ToS InfinityFree** vieta file hosting, media streaming, uso commerciale | Pattern reader autenticato ad alto traffico = ban probabile a 50+ utenti, non edge case |

### 🟡 Problemi minori

| # | Problema | Mitigazione |
|---|---|---|
| M1 | CF + dominio custom su IF: SSL Flexible = MITM su cookie auth, serve Full Strict + cert su origin | Validare setup SSL prima di Sprint 2, non durante |
| M2 | Inode reale IF ~30k cap, WP+WC+uploads già consumano 5-8k | Strip immagini non-cover diventa obbligatorio (non opzionale come in v2) |
| M3 | `upload_max_filesize` IF free = 5-10 MB (non alzabile da .htaccess) | Limite ePub safe a 8 MB, oppure ePub grandi via FTP + ingest da pulsante admin |
| M4 | Hook WC `order_status_completed` carica WC al checkout | Non specifico del reader; WC su IF in generale stressato |

---

## 🎯 Le 3 opzioni concrete

### Opzione 1 — MVP demo su IF, cache pubblica chapter
- Niente auth per-chapter sul contenuto (solo sulla pagina `/leggi/`)
- Leak fra utenti accettato perché libri = pubblico dominio
- Niente Workers, niente SHORTINIT custom, niente budget
- **Sprint 1-3 fattibili come v2**, salta loader snello e CF page rule complesse
- **Effort: ~14-18h** (vs 18-27h v2)
- ⚠️ Resta rischio ban IF a uso intenso, ma demo portfolio reggerà
- **Quando sceglierla**: questo è portfolio ITS / dimostrativo

### Opzione 2 — MVP serio su hosting low-cost (~3€/mese)
- Aruba / Netsons / Serverplan / VPS Hetzner CX11 (~4€)
- Architettura v2 quasi as-is, ma elimini fix #2 (CF hard) e #4 (bootstrap snello)
- Niente paura ban, niente CF Workers, SSL/cron standard
- **Effort: ~14-18h** (risparmi loader snello + CF setup complesso)
- **Quando sceglierla**: vuoi che giri davvero con utenti reali

### Opzione 3 — IF + CF Workers Paid (~5€/mese)
- Architettura v2 piena, cache-key-per-utente via Workers
- Accetti rischio ban IF residuo
- **Effort: ~18-27h** + complessità Workers
- **Worst ratio sforzo/risultato** — costo simile a opzione 2 con più complessità

---

## 📌 Raccomandazione

- **Portfolio ITS** → Opzione 1 (MVP demo su IF, leak accettato su pubblico dominio)
- **Produzione utenti reali** → Opzione 2 (hosting 3€/mo, architettura pulita)
- **Opzione 3 sconsigliata** (paghi quanto opzione 2 con più rischi)

---

## Cosa resta valido dalla v2

Indipendentemente dall'opzione scelta:

- ✅ Pre-estrazione + sanitize one-shot all'ingest
- ✅ Tabella `wp_biblio_rentals` custom (no order meta WC)
- ✅ `user_meta` per rate limit (no transient)
- ✅ Sprint plan: rentals+ingest prima del frontend
- ✅ CSS columns per paginazione client-side
- ✅ Strip immagini non-cover all'ingest (ora obbligatorio per M2)

Cosa cambia per opzione:
- **Opzione 1**: rimuovi auth per-chapter, CF Page Rule semplice "Cache Everything" pubblica
- **Opzione 2**: tutta v2 ma senza loader snello (bootstrap WP normale OK su VPS) e senza CF Workers
- **Opzione 3**: v2 piena + Workers per cache key

---

## Prossimo passo

**Decisione necessaria prima di scrivere codice**: opzione 1, 2 o 3?

Una volta scelta, riscrivo solo la sezione "Architettura" allineata all'opzione e si parte da Sprint 1 (rentals + ingest, comune a tutte e tre).

---

*Proposta v3 — 2026-05-15. Aperta in attesa di decisione opzione.*
