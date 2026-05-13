# Bibliò — Guida completa al tema WordPress

> Manuale operativo per gestire il sito Bibliò su WordPress + Infinity Free.
> Tema: `biblio-theme.zip` v0.1.0
> Data: 2026-05-13

---

## 📑 Indice

1. [Installazione tema](#1-installazione-tema)
2. [Configurazione iniziale](#2-configurazione-iniziale)
3. [Aggiungere e gestire libri](#3-aggiungere-e-gestire-libri)
4. [Generi (categorie libri)](#4-generi-categorie-libri)
5. [Pagine statiche da creare](#5-pagine-statiche-da-creare)
6. [Menu di navigazione](#6-menu-di-navigazione)
7. [Personalizzare colori, font, layout](#7-personalizzare-colori-font-layout)
8. [WooCommerce (PHASE 2)](#8-woocommerce-phase-2)
9. [Ottimizzazione per Infinity Free](#9-ottimizzazione-per-infinity-free)
10. [Struttura del tema (riferimento sviluppatore)](#10-struttura-del-tema-riferimento-sviluppatore)
11. [Troubleshooting](#11-troubleshooting)
12. [Roadmap & cose da fare dopo](#12-roadmap--cose-da-fare-dopo)

---

## 1. Installazione tema \[✔️ fatto]

### Prima installazione

1. Accedi al WordPress admin: `https://tuosito.web1337.net/wp-admin`
2. **Aspetto → Temi → Aggiungi nuovo → Carica tema**
3. Seleziona il file `biblio-theme.zip` (in `C:\Users\Chirone\Downloads\`)
4. Clicca **Installa ora** → poi **Attiva**

### Aggiornamenti futuri

Quando modifichi il tema:

- **Opzione A (consigliata)**: cancella `biblio-theme` dalla lista temi e ricarica il nuovo zip
- **Opzione B**: via FTP / File Manager Infinity Free, sostituisci la cartella `/wp-content/themes/biblio-theme/`

---

## 2. Configurazione iniziale \[✔️ fatto]

Da fare **una sola volta** dopo l'attivazione:

### 2.1 Permalink

**Impostazioni → Permalink** → seleziona **"Nome articolo"** → Salva.

⚠️ Importante: anche se è già selezionato, **clicca Salva comunque** la prima volta. Serve a rigenerare le rewrite rules per il Custom Post Type "Libri" (`/catalogo/` e `/libro/nome/`).

### 2.2 Lingua e fuso orario

**Impostazioni → Generali**:
- Lingua del sito: Italiano
- Fuso orario: Roma (UTC+1)
- Formato data: come preferisci

### 2.3 Lettura (homepage)

**Impostazioni → Lettura**:
- **La tua home page mostra**: lascia su "I tuoi articoli più recenti" — il tema userà automaticamente `front-page.php` (la home di design Bibliò)
- Se invece vuoi una pagina statica per la home, puoi farlo, ma perderai la home design del tema

### 2.4 Discussione (commenti)

**Impostazioni → Discussione** → disabilita "Permetti commenti su nuovi articoli" se non ti servono. Su Infinity Free, meno commenti = meno query DB = meno CPU.

---

## 3. Aggiungere e gestire libri

### 3.1 Aggiungere un libro

1. Sidebar admin → **Libri → Aggiungi libro**
2. **Titolo**: il titolo del libro (es. *"Il nome della rosa"*)
3. **Contenuto** (Block Editor): descrizione lunga del libro (opzionale, appare sotto il blurb nella pagina dettaglio)
4. **Riassunto** (sidebar destro, sezione "Riassunto"): testo corto per anteprima
5. **Immagine in evidenza** (sidebar destro): copertina libro
   - Se la imposti → viene usata come copertina
   - Se NON la imposti → il tema genera una copertina con gradiente colorato (vedi campo "Stile copertina" sotto)

### 3.2 Box "Dettagli libro"

Sotto l'editor trovi un meta box con questi campi:

| Campo | Tipo | Esempio | Note |
|---|---|---|---|
| **Autore** | testo | `Umberto Eco` | obbligatorio |
| **ISBN** | testo | `978-88-452-9999-9` | opzionale |
| **Prezzo (€)** | numero | `18.50` | usa il punto come separatore |
| **Prezzo noleggio 30gg (€)** | numero | `4.90` | solo se noleggiabile |
| **Noleggiabile** | Sì/No | `Sì` | controlla se mostrare il box noleggio |
| **Pagine** | numero | `512` | opzionale |
| **Anno** | numero | `1980` | opzionale |
| **Rating (0-5)** | numero | `4.7` | mostra ⭐ rating |
| **Badge** | scelta | `Novità` / `Bestseller` / `Plus` / nessuno | etichetta angolo card |
| **Stile copertina (0-5)** | scelta | `0`-`5` | gradiente usato se NO immagine evidenza |
| **Blurb** | testo lungo | *"Un viaggio nei misteri..."* | descrizione breve in pagina dettaglio |

### 3.3 Stili copertina disponibili

Quando NON imposti un'immagine in evidenza, il tema usa uno di 6 gradienti:

- **0** — Navy → blu profondo (testo crema)
- **1** — Coral → rosso scuro (testo crema)
- **2** — Marrone caldo (testo crema)
- **3** — Crema chiaro (testo scuro)
- **4** — Nero → grigio scuro (testo oro) — *Plus look*
- **5** — Verde → verde scuro (testo crema)

### 3.4 Genere

In sidebar destro → sezione **Generi** → assegna 1 o più generi (es. "Narrativa", "Classici").

I generi controllano:
- I filtri sidebar in `/catalogo/`
- Le card "Categorie" in homepage
- La pagina archivio per genere `/genere/narrativa/`

---

## 4. Generi (categorie libri)

### 4.1 Creare nuovi generi

**Libri → Generi** → riempi:
- **Nome**: es. `Narrativa`
- **Slug**: `narrativa` (URL friendly, minuscolo, senza spazi/accenti)
- **Descrizione**: opzionale

### 4.2 Generi suggeriti per Bibliò

Per avere icone giuste in homepage, usa questi slug (il tema ha mapping icona):

| Slug | Nome consigliato | Icona |
|---|---|---|
| `narrativa` | Narrativa | 📖 |
| `saggistica` | Saggistica | 🧠 |
| `poesia` | Poesia | 🪶 |
| `classici` | Classici | 🏛️ |
| `storia` | Storia | 📜 |
| `arte` | Arte | 🎨 |
| `filosofia` | Filosofia | 💭 |
| `ragazzi` | Ragazzi | 🧸 |
| `gialli` | Gialli | 🔍 |
| `fantasy` | Fantasy | 🐉 |
| `biografia` | Biografia | 👤 |
| `cucina` | Cucina | 🍝 |

Per altri slug, l'icona di default sarà 📚.

---

## 5. Pagine statiche da creare \[⚠️ work in progress]

Queste pagine sono **referenziate dal tema** (footer, banner Plus, chat FAB). Creane almeno 4 essenziali per evitare 404:

### 5.1 Pagine essenziali

| Titolo | Slug | Cosa metterci |
|---|---|---|
| **Plus** | `plus` | Spiegazione abbonamento (9,99€/mese, 2 noleggi inclusi, spedizione gratis) |
| **Noleggio vs Acquisto** | `noleggio-vs-acquisto` | Tabella comparativa noleggio/acquisto |
| **MyBibliò** | `mybiblio` | Form contatti o placeholder ("chat in arrivo") |
| **Contatti** | `contatti` | Email, indirizzo, form |

### 5.2 Pagine secondarie (per footer)

- `FAQ` → `/faq/`
- `Spedizioni` → `/spedizioni/`
- `Privacy` → `/privacy/`
- `Termini` → `/termini/`
- `Cookie` → `/cookie/`

### 5.3 Come si crea una pagina

1. **Pagine → Aggiungi nuova**
2. Scrivi il **titolo** (es. `Plus`)
3. Nel **Block Editor**:
   - Digita testo normale → diventa paragrafo
   - Per inserire blocchi speciali (titolo H2, immagine, pulsante, colonne): clicca `+` blu in alto a sinistra, oppure digita `/` nel contenuto
4. Sidebar destro → scheda **Pagina** → sezione **URL** → controlla lo **slug** (modificalo se serve, es. da `plus-2` a `plus`)
5. Clicca **Pubblica** (bottone blu in alto a destra)

### 5.4 Block Editor — blocchi utili

- **Paragrafo**: testo normale
- **Titolo**: H2/H3 (rispetta i font del tema)
- **Pulsante**: per CTA — il tema ha già stili `.btn .btn-primary` che puoi applicare con HTML personalizzato
- **Colonne**: layout multi-colonna
- **Immagine** / **Galleria**
- **HTML personalizzato**: se vuoi inserire HTML grezzo (es. pulsante con classi del tema)

### 5.5 Esempio HTML personalizzato per pulsante Plus

```html
<a class="btn btn-gold btn-lg" href="/plus/">Attiva Plus →</a>
```

---

## 6. Menu di navigazione \[⚠️ da non toccare al momento]

### 6.1 Creare il menu principale

1. **Aspetto → Menu**
2. Nome menu: `Principale`
3. Clicca **Crea menu**
4. Da sinistra, aggiungi:
   - Pagine: Plus, Noleggio vs Acquisto, Contatti
   - **Link personalizzato**: URL `/catalogo/`, Etichetta `Catalogo`
   - **Link personalizzato**: URL `/mybiblio/`, Etichetta `MyBibliò`
5. **Trascina** per riordinare (consigliato: Catalogo, Noleggio vs Acquisto, Plus, MyBibliò)
6. In fondo, sotto **Impostazioni menu** → spunta **"Menu principale"**
7. Clicca **Salva menu**

### 6.2 Menu footer (opzionale)

Crea un secondo menu chiamato `Footer` con le pagine legali e assegnalo a **"Menu footer"**.

> ⚠️ Il tema attualmente disegna il footer in modo statico (4 colonne hardcoded). Il menu footer è registrato ma non ancora renderizzato. Se vuoi attivarlo, va modificato `footer.php`.

---

## 7. Personalizzare colori, font, layout

### 7.1 Modificare colori

Tutti i colori sono in `style.css` come CSS variables (sezione `:root`). Per cambiare il coral primario:

```css
:root {
  --biblio-coral: #C75550;        /* ← cambia qui */
  --biblio-coral-dark: #A8423E;   /* ← e qui */
}
```

Modifica → salva → ricarica.

### 7.2 Modificare font

Sempre in `style.css`:

```css
@import url('https://fonts.googleapis.com/...');  /* qui aggiungi/cambi font */

:root {
  --font-display: 'Playfair Display', serif;
  --font-serif: 'Lora', serif;
  --font-sans: 'Inter', sans-serif;
}
```

### 7.3 Modificare layout home

File: `front-page.php`

Sezioni in ordine:
1. **Hero** (titolo + 3 copertine flottanti)
2. **Trust strip** (4 numeri: titoli, consegna, noleggio, rating)
3. **Selezione settimana** (6 libri)
4. **Categorie** (8 generi)
5. **Plus banner** (CTA scuro)
6. **Novità editoriali** (6 libri ordinati per data)

Per cambiare l'ordine, sposta i blocchi `<section>` nel file.

### 7.4 Editor visuale (alternativa)

**Aspetto → Editor file del tema** → puoi modificare `style.css` e altri file PHP direttamente da WordPress (sconsigliato in produzione — meglio FTP).

---

## 8. WooCommerce (PHASE 2)

Il tema **dichiara supporto WooCommerce** ma non è ancora pienamente integrato.

### 8.1 Stato attuale

✅ Header mostra carrello WooCommerce se WC è attivo
✅ Pagina account/carrello/checkout di WC funzionano con template default WC
❌ I "Libri" (CPT) NON sono prodotti WooCommerce vendibili
❌ Il pulsante "Aggiungi al carrello" su `single-book.php` non funziona se non c'è prodotto WC corrispondente

### 8.2 Opzioni per vendita reale

**Opzione A (sconsigliata su Infinity Free)** — Sostituire CPT Libri con Prodotti WooCommerce. Richiede di rifare tutto il design system come `single-product.php` di WC.

**Opzione B (consigliata)** — Per ogni Libro, crea un Prodotto WooCommerce con lo stesso ID/SKU. Il tema può poi collegare i due via meta. Richiede personalizzazione di `single-book.php`.

**Opzione C (più veloce)** — Mantieni Bibliò come "catalogo vetrina" + ordini via form/email. Niente WooCommerce.

### 8.3 Test WooCommerce su Infinity Free

⚠️ Da brief Perplexity: WooCommerce ha report di errori DB intermittenti su Infinity Free pre-2026. **Prima di andare live, testa per 72h** monitorando:
- Errori 500 sulle pagine prodotto
- Lentezza checkout
- Mail order che non partono

Se WC è instabile, considera Option B del brief: **headless** (WP backend + frontend statico Netlify).

---

## 9. Ottimizzazione per Infinity Free

### 9.1 Già incluso nel tema

- ✅ CSS unico (`style.css`) — 1 sola richiesta
- ✅ JS minimale (~15 righe)
- ✅ Heartbeat WP ridotto a 60s
- ✅ Emoji script disabilitati
- ✅ oEmbed/WLW/RSD links rimossi (CPU saving)
- ✅ Niente plugin ACF (meta box nativi)

### 9.2 Da fare a mano

#### Plugin essenziali (max 4 totali!)

1. **WP Super Cache** — caching pagine (riduce CPU drasticamente)
2. **Autoptimize** — minify CSS/JS automatico
3. *(opzionale)* **Contact Form 7** — per form contatti
4. *(opzionale)* **WooCommerce** — solo se vendita

#### Configurazione WP Super Cache

1. Plugin → installa "WP Super Cache" → attiva
2. **Impostazioni → WP Super Cache**:
   - **Caching On (Recommended)**: ON
   - **Cache Delivery Method**: "Expert" (htaccess) se possibile, altrimenti "Simple"
   - **Compress pages**: ON
   - **Cache HTTP headers**: ON
   - **Cache rebuild**: ON
3. **Advanced** → spunta:
   - "Don't cache pages for logged in users"
   - "Cache HTTP headers"

#### wp-config.php tweak

Aggiungi prima di `/* That's all, stop editing! */`:

```php
define('WP_MEMORY_LIMIT', '128M');
define('DISABLE_WP_CRON', true);  // solo se hai cron esterno
define('AUTOSAVE_INTERVAL', 300);
define('WP_POST_REVISIONS', 3);
define('EMPTY_TRASH_DAYS', 7);
```

#### Image optimization

Infinity Free dà 5 GB → usa **Cloudinary CDN** (free tier) per immagini libro pesanti. Oppure carica solo immagini < 200 KB già compresse (TinyPNG / Squoosh).

### 9.3 Monitoraggio

- **Disk**: cPanel Infinity Free → tieni < 4 GB
- **Inodes**: cPanel → tieni < 25.000
- **CPU**: se il sito va lento, controlla "Recent Visitors" e abilita caching più aggressivo

---

## 10. Struttura del tema (riferimento sviluppatore)

```
biblio-theme/
├── style.css              Tema header + tutti gli stili (design tokens + componenti)
├── functions.php          Setup, enqueue script, hooks performance
├── header.php             Top nav (logo + menu + icone azione)
├── footer.php             Footer 4 colonne + chat FAB
├── front-page.php         Home (hero + sezioni)
├── archive-book.php       Catalogo (sidebar filtri + griglia)
├── single-book.php        Pagina dettaglio libro
├── search.php             Risultati ricerca
├── page.php               Pagina statica generica
├── singular.php           Post singolo (blog, fallback)
├── index.php              Fallback ultimo livello
├── 404.php                Pagina errore
├── searchform.php         Form ricerca riutilizzabile
├── README.md
├── inc/
│   ├── post-types.php     Registra CPT 'book' + tassonomia 'book_genre'
│   ├── helpers.php        Funzioni: biblio_book_card(), biblio_book_cover(), biblio_meta()...
│   └── meta-boxes.php     Box "Dettagli libro" (autore, prezzo, ecc.)
└── assets/
    └── js/main.js         JS minimale (highlight nav attiva)
```

### 10.1 Funzioni PHP utili

Disponibili globalmente, puoi chiamarle in altri template o snippet:

| Funzione | Cosa fa |
|---|---|
| `biblio_meta($post_id, $key, $default)` | Recupera meta libro con fallback |
| `biblio_price($float)` | Formatta prezzo `"18,50€"` |
| `biblio_book_cover($post_id, $size)` | Renderizza copertina (`'sm'`/`'md'`/`'lg'`) |
| `biblio_book_card($post_id, $compact)` | Renderizza card libro completa |
| `biblio_books_query($args)` | Query CPT book con buoni default |
| `biblio_get_genres($limit)` | Lista generi |
| `biblio_genre_icon($slug)` | Emoji icona per genere |

### 10.2 Classi CSS chiave

| Classe | Cosa fa |
|---|---|
| `.btn .btn-primary` | Pulsante coral principale |
| `.btn .btn-secondary` | Pulsante outline scuro |
| `.btn .btn-rent` | Pulsante noleggio (marrone) |
| `.btn .btn-gold` | Pulsante Plus (oro) |
| `.btn .btn-lg` / `.btn-sm` / `.btn-block` | Modificatori |
| `.book-card` | Card libro |
| `.book-cover.cover-0..5` | Copertina con gradiente |
| `.eyebrow` | Label uppercase tracking-wide coral |
| `.lead` | Paragrafo intro grande |
| `.meta` | Testo piccolo grigio |
| `.section` + `.section-head` | Sezione standard con header |
| `.grid-4` / `.grid-6` / `.grid-cat` | Griglie responsive |
| `.plus-banner` | Banner Plus scuro |

---

## 11. Troubleshooting

### Errore: "L'archivio del tema non poteva essere installato"
→ Il file zip è corrotto o la cartella sbagliata. Ri-scarica `biblio-theme.zip` e ricarica.

### Catalogo `/catalogo/` mostra 404
→ Vai in **Impostazioni → Permalink → Salva** (rigenera rewrite rules).

### Pagina libro `/libro/nome/` mostra 404
→ Stesso problema sopra. Permalink → Salva.

### I libri non appaiono in homepage
→ Hai aggiunto almeno 1 libro pubblicato? Controlla **Libri → Tutti i libri** → status "Pubblicato".

### Copertina libro vuota / colore strano
→ Imposta lo "Stile copertina" (0-5) nel box Dettagli libro, OPPURE carica un'immagine in evidenza.

### Font non si caricano (testo system default)
→ Infinity Free a volte blocca Google Fonts. Soluzione: scarica i font WOFF2 e self-host nella cartella `/assets/fonts/`, poi cambia `style.css`.

### Sito lento / errori 500 intermittenti
→ Stai superando il CPU limit. Attiva WP Super Cache, riduci plugin a max 4, controlla che non ci siano loop infiniti nei template.

### Sidebar admin non mostra "Libri"
→ Il tema non è attivo, oppure c'è errore PHP. Vai in **Aspetto → Temi** e verifica che "Bibliò" sia attivo.

---

## 12. Roadmap & cose da fare dopo

### Subito (Week 1 post-deploy)
- [ ] Permalink → Salva
- [ ] Aggiungere 10-20 libri di test
- [ ] Creare i 4 generi base (Narrativa, Saggistica, Classici, Storia)
- [ ] Creare le 4 pagine essenziali (Plus, Noleggio vs Acquisto, MyBibliò, Contatti)
- [ ] Creare menu principale e assegnarlo
- [ ] Installare WP Super Cache + configurare

### Breve (Week 2-3)
- [ ] Caricare copertine reali (immagine in evidenza) per i libri principali
- [ ] Compilare contenuto pagine statiche (Plus, FAQ, Privacy, ecc.)
- [ ] Test responsive mobile su 3 dispositivi reali
- [ ] Monitoring Infinity Free: CPU, inode, disk usage per 7 giorni

### Medio (Week 4+)
- [ ] **Decisione WooCommerce**: test 72h su istanza separata
- [ ] Aggiungere pagina MyBibliò reale (chat o form intelligente)
- [ ] Self-host fonts se Google Fonts danno problemi
- [ ] Setup backup automatico (UpdraftPlus free)

### Lungo (PHASE 2)
- [ ] Integrazione checkout reale (WooCommerce o sistema custom)
- [ ] Gestione utenti registrati (wishlist, account)
- [ ] Sistema noleggio con scadenze
- [ ] Email transazionali (ordine, noleggio attivato/scaduto)
- [ ] Analytics (Plausible o GA4)

---

## 📞 Riferimenti rapidi

- **Tema zip**: `C:\Users\Chirone\Downloads\biblio-theme.zip`
- **Repo design originale**: https://github.com/Chirone23/biblio
- **Brief tecnico**: `C:\Users\Chirone\Downloads\BRIEF_Biblio_WordPress_InfinityFree.md`
- **Sito**: `https://biblio.web1337.net/`
- **Admin**: `https://biblio.web1337.net/wp-admin`

---

*Guida v1.0 — generata 2026-05-13. Aggiorna questo file quando aggiungi feature o cambi flusso operativo.*
