# Bibliò — WordPress Theme

Tema editoriale italiano per Bibliò. Pensato per Infinity Free hosting.

## Installazione

1. In WordPress: **Aspetto → Temi → Aggiungi nuovo → Carica tema**
2. Carica `biblio-theme.zip` e attivalo.
3. Vai su **Aspetto → Menu** e crea un menu in posizione "Menu principale" (es. Catalogo, Plus, Noleggio).
4. **Impostazioni → Lettura → La tua home page mostra**: seleziona "Una pagina statica" e scegli una pagina (oppure lascia "I tuoi articoli più recenti" per usare automaticamente `front-page.php`).
5. **Impostazioni → Permalink**: imposta su "Nome articolo" e salva (rigenera i rewrite rules per il CPT `book`).

## Aggiungere libri

- Sidebar admin → **Libri → Aggiungi libro**
- Inserisci titolo, contenuto (descrizione lunga), e nel box **Dettagli libro**: autore, prezzo, prezzo noleggio, ISBN, pagine, anno, rating, badge, stile copertina, blurb.
- Assegna un **Genere** dalla tassonomia (Libri → Generi).
- L'**immagine in evidenza** sostituisce la copertina generata; se assente, si usa lo "Stile copertina" (0-5) per il gradiente.

## Pagine consigliate da creare

- `Plus` (`/plus/`)
- `Noleggio vs Acquisto` (`/noleggio-vs-acquisto/`)
- `MyBibliò` (`/mybiblio/`) — chat / contatti
- `Contatti`, `FAQ`, `Privacy`, `Termini`, `Cookie`

## WooCommerce (opzionale)

- Installa WooCommerce. Il tema dichiara `add_theme_support('woocommerce')`.
- I libri sono CPT separati; se vuoi vendita reale, crea Prodotti WC corrispondenti, oppure aggiungi supporto WC al CPT `book` (richiede customizzazione).

## Ottimizzazioni per Infinity Free

- Stile e font caricati una sola volta; Google Fonts via `@import` (HTTP cache lato browser).
- `heartbeat` ridotto a 60s, emoji/oembed/wlwmanifest rimossi (CPU saving).
- Niente plugin obbligatori: meta box nativi, niente ACF.
- **Consigliato**: installare WP Super Cache; tenere ≤4 plugin totali; rispettare ~25k inode.

## Struttura tema (≈15 file)

```
biblio-theme/
├── style.css           Tokens + tutto il CSS
├── functions.php       Setup + enqueue + hooks
├── header.php          Nav
├── footer.php          Footer + chat FAB
├── front-page.php      Home
├── archive-book.php    Catalogo + filtri
├── single-book.php     Pagina libro
├── search.php          Risultati ricerca
├── page.php            Pagina statica
├── singular.php        Post singolo (fallback)
├── index.php           Fallback generico
├── 404.php
├── searchform.php
├── inc/
│   ├── post-types.php  CPT 'book' + tax 'book_genre'
│   ├── helpers.php     biblio_book_card(), biblio_book_cover(), ecc.
│   └── meta-boxes.php  Dettagli libro (autore, prezzo, ecc.)
└── assets/js/main.js
```

## Cosa NON è incluso (da fare a mano)

- Form di contatto / MyBibliò chat (pagina statica oppure plugin Contact Form 7)
- Integrazione checkout WooCommerce su CPT book (PHASE 2)
- Pagine `/plus/`, `/account/` ecc. (creale da WP-admin)
