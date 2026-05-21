# 02 — Design Brief: Factory Ottica (input per /open-design)

**Versione:** 1.0 — Fase 3a
**Data:** 2026-05-21
**Status:** pronto per generazione HTML/CSS via `/open-design`
**Output atteso:** HTML statico + CSS production-ready, una pagina per sezione, commentato per port a tema PHP WordPress+WooCommerce.

> Brief operativo, denso, niente prosa decorativa. Input per LLM, non per umano. Fonti: [[01_Discovery]] §8.4–§8.7, [[00_Piano_Azione]] §Fase 3a.

---

## 1. Obiettivo

Demo e-commerce per **Centro Ottico Prezzi di Fabbrica** (San Cesareo, RM) — ottica locale outlet con centro diagnostico, oggi senza sito.

**Mood obbligatorio:** *"outlet curato + centro diagnostico"*.
**Mood vietato:** boutique premium, luxury, navy+oro, clinico-asettico, cheap/discount aggressivo.

**Funzione:** dimostrare alla titolare in pitch (10 min, da telefono) che il sito *"sembra già il suo"*. NON è sito in produzione: niente checkout, niente pagamenti, niente account utente.

**Voice:** educational, continua il tono già loro su FB (*"La vista può cambiare nel tempo"*, *"Non aspettare"*). Mai copy aggressivo prezzo-first.

---

## 2. Palette & design tokens (vincolanti — copia esatta da Discovery §8.4)

CSS custom properties da esporre in `:root`:

```css
:root {
  --color-primary:        #A98BD9;  /* lavender brand FB */
  --color-primary-medium: #8B5FBF;  /* purple medio brand FB */
  --color-primary-deep:   #5E3B95;  /* purple deep — header, footer, testi forti */
  --color-bg:             #FFFFFF;
  --color-bg-soft:        #F7F3FB;  /* lavender mist — sezioni alternate */
  --color-accent:         #FFC857;  /* giallo caldo — CTA primarie */
  --color-text:           #1A1A1A;
  --color-sale:           #E63946;  /* prezzo scontato / urgenza */
  --color-border:         #F7F3FB;
  --color-border-hover:   #A98BD9;

  --font-display: 'Poppins', 'Montserrat', sans-serif;  /* titoli, bold */
  --font-body:    'Inter', system-ui, sans-serif;       /* corpo, prezzi, form */

  --radius-card: 12px;
  --radius-btn:  8px;
  --shadow-card: 0 2px 12px rgba(94, 59, 149, 0.08);
}
```

**Gradient consentiti:** `linear-gradient(135deg, #F7F3FB 0%, #FFFFFF 100%)` per hero secondari. Niente gradient sul viola primario.

**Accessibilità:** contrasto WCAG AA minimo. CTA `#FFC857` su `#FFFFFF` → testo `#1A1A1A`. CTA `#8B5FBF` su `#FFFFFF` → testo `#FFFFFF`.

---

## 3. Sezioni da generare (6 pagine HTML separate)

### 3.1 `index.html` — Homepage

Ordine sezioni (top → bottom):

1. **Header sticky** — logo a sx (placeholder testuale "Factory Ottica"), nav (Shop · Vista · Sole · Outlet · Servizi · Contatti), icone (search, account, carrello), su mobile hamburger.
2. **Hero split (50/50)**
   - Sinistra: H1 *"Occhiali di qualità, prezzo senza sorprese."* + sub *"Controllo vista incluso nel nostro centro diagnostico, a San Cesareo."* + due CTA: primaria gialla `[Prenota controllo gratuito]` + secondaria outline viola `[Scopri la collezione]`.
   - Destra: immagine modello con occhiali (placeholder `<img>` con alt descrittivo + `loading="lazy"`).
3. **Trust bar** — 4 colonne icona+testo: `👓 Controllo vista gratuito` · `🔧 Regolazione asta gratis` · `🔄 Reso 30gg` · `⭐ Centro diagnostico oculare`.
4. **Bestseller grid** — titolo *"I più scelti del mese"*, grid 4×2 (8 card prodotto). Card: foto, nome modello, prezzo grassetto + originale barrato + badge `-25%` (rosso `#E63946`), badge `Anche graduati` sui sole, hover bordo lavender.
5. **Centro diagnostico** (sezione full-width, sfondo `--color-bg-soft`) — H2 *"Più di un'ottica: un centro diagnostico"*, 3 colonne (Esame vista · Controllo lenti · Consulenza visiva) + CTA `[Prenota il tuo controllo →]`.
6. **Outlet / Occasioni** — banner orizzontale viola medio con CTA *"Vedi tutte le offerte"*.
7. **Brand row** — strip 6 logo grayscale placeholder (Ray-Ban, Persol, ecc.), opacità 60% → 100% hover.
8. **Newsletter + Mappa** — split 50/50: sx form newsletter (email + checkbox privacy + CTA `[Iscriviti]`), dx embed mappa placeholder con indirizzo Via Casilina 160, telefono `06 9588307`, orari (Lun 16:30–19:30 · Mar–Sab 09:30–13:00 / 16:00–19:30 · Dom chiuso).
9. **Footer** — 4 colonne (Negozio · Servizi · Aiuto · Seguici) + bottom bar copyright + P.IVA placeholder.

### 3.2 `shop.html` — Catalogo

- Breadcrumb `Home / Shop`.
- Layout 2 colonne: sinistra **sidebar filtri** (sticky desktop, drawer mobile), destra grid prodotti.
- **Filtri faceted** (da Discovery §8.7):
  - Genere (radio: Uomo · Donna · Bambino · Unisex)
  - Forma (checkbox: Rettangolare · Tondo · Aviator · Cat-eye · Squadrato)
  - Colore montatura (swatch colorati)
  - Brand (checkbox lista)
  - Prezzo (slider doppia maniglia 30–300€)
  - ☑ Anche graduati
  - ☑ Blue420 (lenti filtro luce blu)
- Toolbar in alto grid: count *"X prodotti"* + dropdown ordinamento (Popolarità · Prezzo ↑ · Prezzo ↓ · Novità).
- Grid 3 colonne desktop / 2 tablet / 1 mobile, 12 card prodotto demo.
- Paginazione semplice in fondo.

### 3.3 `product.html` — Scheda prodotto singolo

Layout da Discovery §8.6:

- Breadcrumb.
- **Sinistra 60%** — galleria: foto principale + 3 thumbnail sotto + placeholder bottone *"Virtual try-on"* (icona camera, non funzionale, solo visivo per pitch).
- **Destra 40%**:
  - Brand + nome modello (es. "Persol PO3019S")
  - Stock indicator verde *"Disponibile — Pronta consegna"*
  - Prezzo: `<strong>79€</strong> <s>105€</s> <span class="badge-sale">-25%</span>`
  - Badge *"Anche graduati"*
  - **Configuratore 3 step** (accordion o stepper visuale):
    1. Tipo lente (radio: Monofocali · Progressive · Solo montatura)
    2. Graduazione (campi numerici OD/OS sfera, cilindro — disabilitati se "Solo montatura")
    3. Trattamenti (checkbox: Antiriflesso · Blue420 · Fotocromatico)
  - **3 CTA gerarchiche**:
    - Primaria solid giallo: `[Aggiungi al carrello — 79€]`
    - Secondaria outline viola: `[Prenota in negozio — San Cesareo]`
    - Terziaria link con icona: `📞 Consulenza WhatsApp`
- Sotto-fold **tabs**: Descrizione · Misure (calibro/ponte/asta) · Recensioni · Spedizioni & Reso.
- Sezione *"Potrebbero piacerti"* — strip 4 prodotti correlati.

### 3.4 `cart.html` — Carrello semplificato

- **NO checkout.** Il sito è demo: il carrello termina con prenotazione in negozio.
- Tabella riga prodotto: thumb · nome · varianti configurate (riassunto) · quantità (- / +) · prezzo · subtotale · rimuovi.
- Box riepilogo destra (sticky desktop): subtotale, *"Spedizione: calcolata in negozio"*, totale stimato.
- CTA primaria gialla full-width: `[Prenota ritiro in negozio]` → apre placeholder modale con form (nome, telefono, fascia oraria preferita).
- CTA secondaria link: `← Continua lo shopping`.
- Stato vuoto: illustrazione + CTA `[Esplora il catalogo]`.

### 3.5 `contatti.html` — Contatti

- Hero ridotto: H1 *"Vieni a trovarci a San Cesareo"*.
- Layout 2 colonne:
  - Sinistra: scheda info (indirizzo, telefono, email `prezzidifabbrica14@gmail.com`, orari completi), link FB + IG.
  - Destra: embed mappa Google placeholder (`<iframe>` o div con sfondo placeholder).
- Sotto: **form contatto** (nome, email, telefono, motivo: dropdown [Info prodotto · Prenota controllo · Riparazione · Altro], messaggio textarea, CTA `[Invia richiesta]`).

### 3.6 `servizi.html` — Servizi (3 servizi come prodotti prenotabili)

- Intro: H1 *"I nostri servizi"* + paragrafo educational continua tono FB.
- Grid 3 card grandi:
  1. **Esame vista** — durata, cosa include, prezzo `Gratuito`, CTA `[Prenota →]`
  2. **Controllo lenti progressive** — descr, prezzo `Gratuito`, CTA `[Prenota →]`
  3. **Regolazione asta e montaggio lenti** — descr, prezzo `Gratuito`, CTA `[Prenota →]`
- Sezione FAQ accordion (3-4 domande tipiche: *"Quanto dura l'esame?"*, *"Serve appuntamento?"*, *"È davvero gratuito?"*).
- Banner CTA finale viola con `[Prenota controllo gratuito]`.

---

## 4. Vincoli tecnici (hard requirements)

- **NO framework JS pesanti.** Niente React/Vue/Svelte. Vanilla JS minimal per: hamburger menu, accordion, stepper configuratore, drawer filtri mobile, slider prezzo.
- **CSS custom properties** per tutti i token colore/font/radius (vedi §2). Niente Tailwind utility-class esplicite — CSS scritto a mano, BEM-friendly, classi semantiche (`.product-card`, `.product-card__price`, `.product-card__price--sale`).
- **Markup compatibile WooCommerce override.** Per `shop.html`, `product.html`, `cart.html` usa nomi classi/struttura compatibili con i template WooCommerce standard:
  - `.products`, `.product`, `.woocommerce-loop-product__title`, `.price`, `.add_to_cart_button`
  - Wrap blocchi con commenti HTML del tipo `<!-- WC HOOK: woocommerce_before_shop_loop_item -->` per facilitare port a PHP loop.
- **Catalogo demo ≤ 20 prodotti** — popola con dati plausibili (Ray-Ban / Persol / brand fittizi), foto placeholder Unsplash con alt descrittivi.
- **Mobile-first responsive.** Breakpoint `--bp-tablet: 768px`, `--bp-desktop: 1024px`, `--bp-wide: 1280px`. Test obbligatorio a 375px (iPhone SE).
- **Performance:** immagini `loading="lazy"`, `<picture>` con webp+fallback jpg, niente font subset oltre Poppins+Inter.
- **Accessibilità:** semantica HTML5 (`<header>`, `<nav>`, `<main>`, `<section>`, `<footer>`), `aria-label` su icon-only, focus visibile, contrasto AA.
- **InfinityFree friendly:** CSS+JS un solo file ciascuno (`style.css`, `script.js`), niente CDN esterni eccetto Google Fonts.

---

## 5. CTA principale e gerarchia

**CTA primaria sito-wide:** `[Prenota controllo gratuito]` — stile solid `--color-accent` (#FFC857), testo `#1A1A1A`, padding generoso, ombra leggera.

**MAI usare:**
- *"Acquista ora"* come CTA principale homepage
- Toni urgenza/discount come hook principale (*"COMPRA SUBITO"*, *"OFFERTA SCADE"*)
- CTA premium-luxury (*"Discover the collection"*, copy inglese non necessario)

**Gerarchia in pagina:**
1. Primaria gialla → azione di acquisition (prenota / iscriviti)
2. Secondaria outline viola → azione di consultazione (scopri / prenota in negozio)
3. Terziaria link → contatto diretto (WhatsApp, telefono)

Il prezzo è **secondo argomento**, mai prima del controllo vista (vedi [[01_Discovery]] §9).

---

## 6. Commenti obbligatori per port a PHP WordPress

Nei file HTML inserisci commenti che facilitino il porting manuale a tema PHP. Convenzioni:

```html
<!-- WP: get_header() -->
<header class="site-header">...</header>
<!-- /WP: get_header() -->

<!-- WP: front-page.php — hero section -->
<section class="hero">...</section>

<!-- WC LOOP START: while ( have_posts() ) : the_post(); -->
<article class="product">
  <!-- WC: woocommerce_template_loop_product_thumbnail -->
  <img ... >
  <!-- WC: woocommerce_template_loop_product_title -->
  <h2 class="woocommerce-loop-product__title">...</h2>
  <!-- WC: woocommerce_template_loop_price -->
  <span class="price">...</span>
</article>
<!-- WC LOOP END: endwhile; -->

<!-- WP: get_footer() -->
<footer>...</footer>
```

Inserire questi marker per: header/footer, hero, loop prodotti, single product summary, cart table, form contatti (mappabile a WPForms shortcode).

---

## 7. Output atteso da `/open-design`

Struttura file finale:

```
factory-ottica-demo/
├── index.html           (homepage — 9 sezioni)
├── shop.html            (catalogo + filtri faceted)
├── product.html         (scheda prodotto + configuratore 3-step)
├── cart.html            (carrello no-checkout)
├── contatti.html        (info + mappa + form)
├── servizi.html         (3 servizi prenotabili + FAQ)
├── assets/
│   ├── style.css        (un solo file, custom properties + BEM)
│   ├── script.js        (vanilla, ≤ 200 righe)
│   └── img/             (placeholder Unsplash, alt descrittivi)
└── README.md            (note di port: dove inserire hook WP/WC, customizer)
```

**Definition of Done:**
- Tutte le 6 pagine renderizzano correttamente offline (apertura diretta `index.html`).
- Palette `#A98BD9` / `#8B5FBF` / `#FFC857` visibilmente dominante.
- CTA *"Prenota controllo gratuito"* presente in homepage (hero + centro diagnostico + footer banner) e su servizi.
- Configuratore 3-step in `product.html` funzionante visivamente (stepper avanza/retrocede).
- Filtri in `shop.html` funzionanti almeno UI-side (toggle visibile, no obbligo logica reale).
- Mobile a 375px senza overflow orizzontale.
- Markup contiene marker `<!-- WP: -->` e `<!-- WC: -->` come da §6.

---

## Connessioni

- [[01_Discovery]] — fonti palette, layout, claim
- [[00_Piano_Azione]] — Fase 3a (questo brief) → Fase 3b (esecuzione open-design) → Fase 3c (port PHP)
- [[BRIEF_WordPress_InfinityFree]] — vincoli hosting che impongono CSS/JS leggeri
