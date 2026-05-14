# Bibliò — Context per nuova sessione

> Leggi questo file all'inizio di ogni sessione prima di fare qualsiasi modifica al tema.
> Ultimo aggiornamento: 2026-05-13

---

## 🎯 Cos'è il progetto

**Bibliò** è un e-commerce italiano di libri (acquisto + noleggio 30gg).
Design system originale: React SPA su https://github.com/Chirone23/biblio
Convertito in **WordPress theme** da caricare su Infinity Free hosting.

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
- **Versione attuale**: v0.2.0
- **Zip locale**: `C:\Users\Chirone\Downloads\biblio-theme.zip`
- **Sorgente locale**: `C:\tmp\biblio-theme\`

### Regola aggiornamenti (IMPORTANTE)

Il tema **NON si può più cancellare e ricaricare da WP admin**.
Ogni modifica va consegnata come **file singolo** da sostituire via File Manager Infinity Free.

Workflow standard per ogni modifica:
1. Leggi il file reale da `C:\tmp\biblio-theme\<file>`
2. Modifica solo quello che serve
3. Salva il file aggiornato in `C:\tmp\biblio-theme\<file>`
4. Di' all'utente: "sostituisci `<file>` in `wp-content/themes/biblio-theme/`"

Se l'utente ha scaricato i file reali dal server, si trovano in:
`C:\Users\Chirone\Documents\Secondo_Cervello\ITS\Alessandro D'Alessandro - Front-end\progetti\biblio\wp-site\`
→ Leggi da lì invece che da `C:\tmp\biblio-theme\` (quelli sono più aggiornati).

---

## 🏗️ Architettura del tema

### Struttura file

```
biblio-theme/
├── style.css              Tutti i CSS (design tokens + componenti)
├── functions.php          Setup + enqueue + require inc/
├── header.php             Nav sticky con logo + menu + icone WC
├── footer.php             Footer 4 colonne + chat FAB
├── front-page.php         Home (hero + griglia + categorie + Plus banner)
├── woocommerce.php        ← ROUTER UNICO per tutte le pagine WC
│                            is_singular('product') → dettaglio libro
│                            is_shop()/is_product_taxonomy() → catalogo
│                            else → woocommerce_content() fallback
├── archive-book.php       Catalogo CPT book (legacy, non più usato)
├── single-book.php        Dettaglio CPT book (legacy, non più usato)
├── search.php             Risultati ricerca
├── page.php               Pagina statica generica
├── singular.php / index.php / 404.php
├── searchform.php
├── inc/
│   ├── post-types.php     CPT 'book' + tax 'book_genre' (legacy)
│   ├── helpers.php        biblio_book_card(), biblio_book_cover(), biblio_meta(), biblio_price()...
│   ├── meta-boxes.php     Meta box "Dettagli libro" su CPT book
│   └── wc-integration.php Meta box "Dettagli Bibliò" su product WC + biblio_product_card() + biblio_product_cover()
└── assets/js/main.js      JS minimale
```

### File più importanti da conoscere

- **`woocommerce.php`** — tocca questo per qualsiasi modifica a shop, catalogo, dettaglio prodotto
- **`style.css`** — tocca questo per colori, font, layout, componenti CSS
- **`front-page.php`** — tocca questo per la home
- **`inc/wc-integration.php`** — tocca questo per meta box prodotto e card/cover WC
- **`header.php`** / **`footer.php`** — nav e footer

---

## 🎨 Design System Bibliò

### Palette colori (CSS variables in style.css)

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

### Classi CSS chiave

```
.btn .btn-primary    → coral, testo bianco
.btn .btn-secondary  → outline scuro
.btn .btn-rent       → marrone noleggio
.btn .btn-gold       → oro Plus
.btn-lg / .btn-sm / .btn-block

.book-card           → card libro con hover
.book-cover.cover-0..5 → copertina gradiente (6 stili)
.eyebrow             → label uppercase coral
.lead                → paragrafo intro grande
.meta                → testo piccolo grigio
.section + .section-head
.grid-4 / .grid-6 / .grid-cat
.plus-banner         → banner scuro Plus
.catalog-layout      → sidebar + griglia (catalogo)
.detail-grid         → 2 colonne (singolo prodotto)
```

### Stili copertina (0-5)

- **0** Navy → blu profondo
- **1** Coral → rosso scuro
- **2** Marrone caldo
- **3** Crema (testo scuro)
- **4** Nero → oro (Plus look)
- **5** Verde scuro

---

## 🛒 WooCommerce — stato attuale

### Prodotti presenti

10 prodotti attivi, SKU BOOK-1001...BOOK-1010. Categorie WC: Thriller, Fantasy, Romance, Drammatico, Psicologico, Giallo, Storico (e probabilmente altri).

Ogni prodotto ha:
- Prezzo regular + prezzo scontato (sale price) → mostrati barrato + prezzo finale
- **Nessuna immagine prodotto** ancora → il tema usa copertina gradiente CSS

### Meta box "Dettagli Bibliò" sui prodotti WC

Campi aggiuntivi (in `inc/wc-integration.php`) su ogni prodotto:

| Campo | Meta key | Note |
|---|---|---|
| Autore | `_biblio_author` | |
| Anno | `_biblio_year` | |
| Pagine | `_biblio_pages` | |
| Rating | `_biblio_rating` | default 4.5 |
| Prezzo noleggio | `_biblio_rent` | 0 = disattivo |
| Noleggiabile | `_biblio_rentable` | 0/1 |
| Badge | `_biblio_badge` | Novità/Bestseller/Plus |
| Stile copertina | `_biblio_cover_idx` | 0-5 |
| Blurb | `_biblio_blurb` | riassunto breve |

### Pagine WC attive

- `/shop/` — Catalogo (renderizzato da `woocommerce.php` con design Bibliò)
- `/categoria-prodotto/<slug>/` — Filtro categoria
- `/prodotto/<slug>/` — Singolo prodotto
- `/carrello/` — Cart WC standard
- `/pagamento/` — Checkout WC standard
- `/il-mio-account/` — Account WC standard

### Noleggio — stato

**Non funzionale** lato acquisto. Il box noleggio si mostra solo se `_biblio_rentable=1` e `_biblio_rent>0`. Il pulsante "Noleggia 30gg" è un placeholder `href="#"`. **PHASE 2.**

---

## ⚠️ Vincoli Infinity Free

| Vincolo | Limite | Regola |
|---|---|---|
| Disk | 5 GB | Niente immagini pesanti in locale → Cloudinary CDN |
| Inode | ~30k | Max 4 plugin, tema lean |
| CPU/Memory | ~40-128MB | WP Super Cache obbligatorio |
| PHP | 8.3 | OK |
| MySQL | 8.0 | Query semplici, no JOIN complessi |

### Plugin attivi (max 4 regola)

- WooCommerce ← obbligatorio
- _(WP Super Cache — da installare se non già fatto)_
- Slot liberi: 2-3

### Ottimizzazioni già incluse nel tema

- Emoji script rimossi
- oEmbed/WLW/RSD rimossi
- Heartbeat ridotto a 60s
- CSS unico, JS minimale (~15 righe)

---

## 📄 Pagine WordPress create

| Pagina | Slug | Stato |
|---|---|---|
| Plus | `/plus/` | ✅ Pubblicata |
| Shop | `/shop/` | ✅ WooCommerce |
| Carrello | `/carrello/` | ✅ WooCommerce |
| Il mio account | `/il-mio-account/` | ✅ WooCommerce |
| Pagamento | `/pagamento/` | ✅ WooCommerce |
| Noleggio vs Acquisto | `/noleggio-vs-acquisto/` | ⚠️ Da creare o WIP |
| MyBibliò | `/mybiblio/` | ⚠️ Placeholder |
| Contatti | `/contatti/` | ⚠️ Da creare |

---

## 🔧 CPT "Libri" — stato

Il Custom Post Type `book` è ancora registrato nel tema ma **non più usato**.
I contenuti editoriali sono tutti su **Prodotti WooCommerce**.
`archive-book.php` e `single-book.php` sono file legacy ancora nel tema ma non vengono toccati.

---

## 📋 Decisioni prese (non riaprire)

| Decisione | Scelta |
|---|---|
| Architettura | Option A: WP Theme ottimizzato (no headless, no static export) |
| Contenuti | Prodotti WooCommerce (no CPT book) |
| CSS framework | Vanilla CSS con design tokens (no Tailwind, no Bootstrap) |
| Meta campi | Meta box nativi PHP (no ACF, risparmio inode) |
| Plugin aggiuntivi | Max 4 totali, solo essenziali |
| Immagini CDN | Cloudinary per media pesanti (da configurare) |

---

## 🚧 In sospeso / PHASE 2

- [ ] Noleggio funzionale (pulsante placeholder → logica reale)
- [ ] Immagini copertina reali (ora solo gradient CSS)
- [ ] WP Super Cache configurato
- [ ] Pagine `/contatti/`, `/noleggio-vs-acquisto/` con contenuto reale
- [ ] Menu principale configurato in WP Admin
- [ ] MyBibliò chat (Claude API o form)
- [ ] Test WooCommerce checkout su Infinity Free (72h stress test)
- [ ] Self-host font se Google Fonts dà problemi su Infinity Free

---

## 📁 File locali importanti

| File | Path |
|---|---|
| Tema sorgente | `C:\tmp\biblio-theme\` |
| Tema zip | `C:\Users\Chirone\Downloads\biblio-theme.zip` |
| Guida completa MD | `...\biblio\BIBLIO_THEME_GUIDA_COMPLETA.md` |
| Guida completa DOCX | `...\biblio\BIBLIO_THEME_GUIDA_COMPLETA.docx` |
| Design originale React | https://github.com/Chirone23/biblio |
| Brief tecnico | `C:\Users\Chirone\Downloads\BRIEF_Biblio_WordPress_InfinityFree.md` |
| Convertitore MD→DOCX | `...\Melanie - Specifiche di progetto con AI\prog1\md2docx.js` |
| Launcher MD→DOCX | `C:\Users\Chirone\Documents\Secondo_Cervello\MD2DOCX.bat` |
| File tema server | `...\biblio\wp-site\` ← se scaricati da Infinity Free |

Prefisso path comune:
`C:\Users\Chirone\Documents\Secondo_Cervello\ITS\Alessandro D'Alessandro - Front-end\progetti\biblio\`

---

## 🗣️ Come lavorare in questa sessione

### Prima di modificare qualsiasi file

1. Controlla se `wp-site/` esiste e ha i file aggiornati dal server → usali come source of truth
2. Altrimenti usa `C:\tmp\biblio-theme\` come riferimento

### Per ogni modifica richiesta

- Genera **file singolo** aggiornato pronto da sostituire
- Di' esattamente: "sostituisci `inc/wc-integration.php` in `wp-content/themes/biblio-theme/inc/`"
- NON rigenerare lo zip completo (non serve più)

### Richieste tipo che funzionano bene

- "Modifica il colore del pulsante noleggio"
- "Aggiungi campo ISBN visibile nella card"
- "Fix: la sidebar del catalogo non si vede su mobile"
- "Aggiungi sezione recensioni nella pagina singolo prodotto"
- "Cambia il numero di colonne nella griglia home da 6 a 4"

---

*Context v1.0 — 2026-05-13. Aggiorna questo file dopo ogni sessione che cambia qualcosa di strutturale.*
