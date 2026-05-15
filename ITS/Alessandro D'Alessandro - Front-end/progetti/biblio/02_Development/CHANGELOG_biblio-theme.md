# Storia di Bibliò — dal concept al tema WordPress

> Racconto cronologico del progetto: dalle prime idee fino allo stato attuale del tema.
> **Parent:** [[BIBLIO_PROJECT_MOC]]
> **Collegato a:** [[BIBLIO_AUDIT_2026-05-14]] · [[MyBibliò AI Implementation MOC]] · [[CONTEXT_NUOVA_SESSIONE]]

---

## Atto I — Il concept (prima della spec)

Bibliò nasce come React SPA. L'idea era costruire una piattaforma libraria che facesse tre cose che nessuna libreria online italiana faceva insieme: vendere cartacei, vendere e-book in modo definitivo, e noleggiarli per 30 giorni — con tutto accessibile da un unico catalogo. A completare il quadro, un assistente AI integrato, MyBibliò, che non fosse un chatbot generico ma un consulente che conosceva solo e soltanto i libri sul sito, capace di aiutare l'utente a scegliere senza inventarsi nulla.

Il design system originale era già sviluppato in React: palette coral/navy/gold/cream, tre font (Playfair Display per i titoli, Lora per i libri, Inter per l'interfaccia), sei stili di copertina gradiente per i libri sprovvisti di immagine. Era qualcosa di curato, con una propria voce editoriale.

---

## Atto II — La spec funzionale (definire il prodotto)

Prima di toccare codice, il progetto è passato attraverso una fase di specifica. La `biblio_specs_funzionale_mvp.md` ha definito i confini del prodotto: cosa c'è nell'MVP e cosa rimane fuori.

**Dentro l'MVP:**
- Catalogo libri consultabile pubblicamente
- Tre modalità per libro: cartaceo, e-book acquisto, e-book noleggio
- Area personale con libreria digitale e storico ordini
- Chatbot MyBibliò per soli utenti loggati
- Backoffice admin con import da Excel

**Fuori dall'MVP (decisione esplicita):**
- Abbonamento Bibliò Plus
- Vector database per il chatbot
- DRM avanzato
- App mobile
- Recensioni e community

La regola di carrello era netta: nessun ordine misto. Un ordine contiene o solo cartacei, o solo e-book acquisto, o solo noleggi. Mai combinati.

La spec tecnica (`biblio_spec_tecnica_mvp_v0_1.md`) ha poi tradotto tutto in architettura: WordPress come CMS, WooCommerce come motore transazionale, un plugin custom Bibliò per la logica di dominio (accessi, scadenze, conversioni), tabelle SQL custom per le cose che WooCommerce non modella bene. MyBibliò con retrieval SQL prima dell'LLM: nessun vettore, nessuna allucinazione.

---

## Atto III — La svolta InfinityFree (12 maggio 2026)

Il progetto aveva un vincolo che ha cambiato tutto: il hosting è InfinityFree, gratuito, con limiti precisi. ~30.000 inode totali. CPU limitata. cURL outbound bloccato. Niente Stripe, niente Mailchimp. Memory limit 40-128 MB. Tutto il catalogo dei plugin si restringeva drasticamente.

L'ADR del 12 maggio (`ADR_WordPress_InfinityFree.md`) ha analizzato tre architetture possibili:

| Opzione | Approccio | Fit con InfinityFree |
|---|---|---|
| A | WordPress Theme ottimizzato | ✅ Perfetto |
| B | Headless (React frontend + WP API) | ❌ Impossibile |
| C | Static export | ❌ Perde tutto il dinamismo |

La decisione è stata **Option A**: convertire il design system React in un tema WordPress nativo, con PHP templates, CSS tokens portati 1:1 dal design originale, e vanilla JS minimale. WooCommerce da testare separatamente prima di attivarlo in produzione.

Questa scelta ha implicato alcune semplificazioni rispetto alla SPA React: routing multi-page invece di client-side, filtri catalogo via AJAX invece di React state, copertine gradient CSS invece di immagini reali (per ora), WooCommerce con template override invece di UI custom. Ma tutto il design system sopravviveva intatto.

Il mapping React → PHP era già pianificato: `Shell.jsx/Nav → header.php`, `ChatFab → footer.php`, `Home.jsx → front-page.php`, `BookCard.jsx → template-parts/book-card.php`, e così via.

---

## Atto IV — Il tema base (v0.2.x)

Il tema viene costruito partendo da quel mapping. La struttura iniziale (`biblio-theme_base`) conteneva:

```
biblio-theme/
├── style.css           (tutto il CSS in un solo file da 305 righe)
├── functions.php       (setup + asset + chatbot + chat widget HTML tutto insieme)
├── header.php / footer.php / front-page.php
├── woocommerce.php     (router unico: shop, categoria, singolo prodotto)
├── archive-book.php / single-book.php  (legacy CPT, mai usati)
├── inc/
│   ├── post-types.php      CPT 'book' + tax 'book_genre'
│   ├── helpers.php
│   ├── meta-boxes.php
│   └── wc-integration.php
└── assets/js/main.js
```

Il tema era funzionante. Il design system era fedele all'originale React. WooCommerce era integrato con una meta box personalizzata su ogni prodotto (`_biblio_author`, `_biblio_year`, `_biblio_pages`, `_biblio_rating`, `_biblio_rent`, `_biblio_rentable`, `_biblio_badge`, `_biblio_cover_idx`, `_biblio_blurb`). La pagina singolo prodotto, il catalogo con sidebar filtri, e la home con hero, griglia e banner Plus erano già costruite.

C'era già un chatbot: un endpoint REST in `functions.php` che chiamava Groq (`llama-3.1-8b-instant`), interrogava tutti i prodotti WC senza filtri, e restituiva una risposta. Il widget HTML era scritto direttamente dentro una `add_action('wp_footer')` in `functions.php`. Funzionava, ma era tutto nello stesso posto.

Tre problemi tecnici latenti convivevano nel tema:
1. `wc-integration.php` usava ancora `_visibility` come meta query, deprecata da WC 3.0.
2. `inc/post-types.php` registrava il CPT `book` con `has_archive => 'catalogo'`, in conflitto con l'URL della shop page WooCommerce.
3. Il chatbot non sapeva in quale sezione del catalogo si trovava l'utente.

---

## Atto V — L'audit (14 maggio 2026)

Prima di andare avanti con nuove feature, il tema è stato sottoposto a un audit completo con `/open-design`. Dodici problemi identificati, ordinati per priorità.

**P0 — Da correggere subito:**
- Emoji come icone feature ovunque: `🛒 👤 🔍` nell'header, `❤` nel footer, `✨` nel FAB, `📦` nelle card. Violazione del principio anti-AI-slop: le emoji come feature icon sono il segnale più riconoscibile di un output generato da LLM senza cura.
- Metriche inventate hardcodate: "50.000+ titoli", "1.240 recensioni", "4,8/5". Claims non sostenuti da dati reali.
- Copy hero generico: "La tua biblioteca in un click" suonava da pitch SaaS, non da libreria con voce propria.

**P1 — Manutenibilità e UX:**
- `style.css` monolitico da 305 righe difficile da navigare
- Inline styles diffusi nei PHP (almeno 30 occorrenze)
- CPT `book` legacy ancora caricato con file morti (archive-book.php, single-book.php)
- Mobile nav rotto: `.nav-links { display: none }` su mobile senza hamburger sostitutivo
- Nessuna icona MyBibliò in bottom nav mobile
- Pagina `/mybiblio/` dedicata mancante

**P2 — Performance:**
- Google Fonts via `@import` dentro CSS (blocca rendering)
- WP Super Cache non configurato

L'audit si è chiuso con un plan in 6 fasi: cleanup legacy, CSS split, icon set SVG + mobile nav, MyBibliò drawer, polish editoriale, performance. Decisione del giorno: solo audit, esecuzione nelle sessioni successive.

---

## Atto VI — La versione v0.3.0 (15 maggio 2026)

La sessione successiva ha eseguito la Fase 1 e la Fase 2 dell'audit, più tre fix critici al chatbot che erano emersi dall'uso reale.

### Fix chatbot (emersi dall'uso reale, non dall'audit)

Sulla pagina della sezione Gialli, il chatbot consigliava fantasy e thriller. Il problema era semplice: il backend mandava all'LLM tutti i prodotti WC senza filtri, quindi il modello pescava da tutto il catalogo ignorando dove si trovava l'utente.

La soluzione: JS legge il category slug dall'URL (`/wp-categoria-prodotto/<slug>/`) e lo invia con ogni messaggio. PHP riceve lo slug, lo risolve come termine `product_cat`, e filtra la `WP_Query`. Il system prompt include solo il catalogo della categoria corrente. Se l'utente è nella pagina shop generale, vede tutto.

Il secondo problema era la persistenza della conversazione. Cambiando pagina — per esempio passando dalla sezione Gialli alla pagina singolo prodotto — la storia della chat spariva. Era salvata in `sessionStorage`, che si azzera su ogni nuova scheda o redirect. Soluzione: `localStorage` con TTL 24 ore, chiave `biblio_chat_v2`. Ogni messaggio salvato ha tre campi: `role` (user/assistant), `raw` (testo pulito per l'API), `display` (HTML safe per il DOM). Separare i due campi evita di dover fare escape e unescape ogni volta che la history viene riletta.

Il terzo problema era la formattazione delle liste. L'LLM rispondeva con "1. Titolo 2. Titolo 3. Titolo" su una riga sola — nessun `\n` tra gli elementi. La funzione `botTextToHtml()` in main.js ora applica due trasformazioni: converte i `\n` reali in `<br>`, e rileva il pattern "spazio + numero + punto + spazio" sostituendolo con `<br>` prima del numero. Non è elegante, ma cattura esattamente come si comporta quel modello.

### CSS split in 4 file modulari (Fase 2 dell'audit)

`style.css` da 305 righe spezzato in:
- `css/tokens.css` — tutte le CSS custom properties: palette, font, spacing, shadows, easing
- `css/base.css` — reset, tipografia di base, `@import` Google Fonts, `:focus-visible`
- `css/components.css` — nav, bottoni, book-card, copertine gradient, footer, chat FAB
- `css/pages.css` — hero, catalogo con sidebar, pagina dettaglio prodotto, responsive

`functions.php` carica i 4 file in catena di dipendenza: tokens → base → components → pages. Così i custom properties sono sempre disponibili dove servono.

### Chatbot estratto in inc/chatbot.php (Fase 1 dell'audit)

La logica del chatbot — endpoint REST, query WC filtrata, system prompt, chiamata Groq — è stata estratta da `functions.php` e portata in `inc/chatbot.php`. `functions.php` ora fa solo `require_once` di quel file.

Il risultato: `functions.php` da 143 a 89 righe, leggibile. La struttura è: setup → asset → performance → includes. Niente HTML inline, niente logica di business.

### Fix bug WooCommerce silenti

Due problemi che non causavano errori visibili ma erano sbagliati:
- `wc-integration.php`: `biblio_products_query()` usava ancora `_visibility` come meta query (deprecata da WC 3.0). Corretto con `tax_query` su tassonomia `product_visibility`.
- `post-types.php`: CPT `book` registrato con `has_archive => 'catalogo'` creava un conflitto URL con `/catalogo/` della shop page WooCommerce. Corretto con `has_archive => false`.

### Cleanup front-page.php

Rimosso il ramo `if (!$use_wc)` che non veniva mai eseguito in produzione — dead code da una versione precedente in cui WooCommerce era opzionale.

---

## Stato attuale (v0.3.0)

Il sito è live su `biblio.web1337.net`. Il tema ha:
- CSS modulare in 4 file
- Chatbot category-aware con conversazione persistente 24h
- Zero conflitti WooCommerce noti
- 10 prodotti attivi (SKU BOOK-1001...BOOK-1010) con meta Bibliò completi

**Non ancora fatto (dall'audit):**
- Icon set SVG (ancora emoji) — Fase 3
- Mobile nav hamburger + bottom nav MyBibliò — Fase 3
- Pagina /mybiblio/ dedicata — Fase 4
- Self-hosting font — Fase 6
- WP Super Cache — Fase 6
- Polish editoriale (microcopy, metriche reali) — Fase 5

---

## Lezioni operative

**Su InfinityFree:** il deploy avviene file per file via File Manager. Se si aggiorna `functions.php` che ora include nuovi file CSS, quei file devono essere caricati sul server *prima*. L'ordine conta.

**Sul browser cache:** dopo ogni upload, Ctrl+Shift+R è necessario per vedere i cambiamenti. CSS e JS vengono cachati aggressivamente.

**Sul processo di modifica:** fare troppi cambiamenti in una sessione senza conferma intermedia porta a revert. La regola pratica che funziona: un file modificato, conferma che funziona, poi il prossimo.

**Sul chatbot:** il modello `llama-3.1-8b-instant` su Groq non inserisce `\n` tra elementi di lista numerata. Se cambierà provider o modello in futuro, la regex in `botTextToHtml()` potrebbe non essere necessaria — o potrebbe servire un pattern diverso.

---

*Aggiorna questo file dopo ogni sessione che cambia qualcosa di strutturale nel tema.*
