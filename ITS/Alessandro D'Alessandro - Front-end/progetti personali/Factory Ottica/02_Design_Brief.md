# 02 — Design Brief: Factory Ottica (input per /open-design)

**Versione:** 1.1 — corretta pre-rigenerazione
**Data:** 2026-05-21
**Status:** pronto per generazione HTML/CSS via `/open-design`
**Output atteso:** 6 file HTML separati + style.css + script.js, commentati per port a tema PHP WordPress+WooCommerce.

> Brief operativo, denso, niente prosa decorativa. Input per LLM, non per umano. Fonti: [[01_Discovery]] §8.4–§8.7, [[00_Piano_Azione]] §Fase 3a.

---

## 0. Design system applicato (OBBLIGATORIO — non usare freeform)

Tre sistemi combinati, ognuno domina la sua area funzionale:

| Sistema | Area di applicazione | Contributo specifico |
|---|---|---|
| **Canva** | Identita' visiva generale | Palette viola/lavanda gia' brand loro, friendly geometry, generous spacing, tipografia bold |
| **Levels** | Architettura di conversione | CTA hierarchy, trust signals, friction removal, form prenotazione senza attrito |
| **Airbnb** | Product discovery | Photography-first, rounded UI, card prodotto con foto dominante, galleria immagini |

**Surface:** Responsive web (desktop + tablet + mobile, test obbligatorio a 375px).
**Fidelity:** High fidelity — non wireframe, non mockup. Il titolare deve vedere "gia' il suo sito".

---

## 1. Obiettivo

Demo e-commerce per **Centro Ottico Prezzi di Fabbrica** (San Cesareo, RM) — ottica locale outlet con centro diagnostico, oggi senza sito.

**Mood obbligatorio:** *"outlet curato + centro diagnostico"*.
**Mood vietato:** boutique premium, luxury, navy+oro, clinico-asettico, cheap/discount aggressivo.

**Funzione:** dimostrare alla titolare in pitch (10 min, da telefono) che il sito *"sembra gia' il suo"*. NON e' sito in produzione: niente checkout, niente pagamenti, niente account utente.

**Voice:** educational, continua il tono gia' loro su FB (*"La vista puo' cambiare nel tempo"*, *"Non aspettare"*). Mai copy aggressivo prezzo-first.

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

  --font-display: 'Poppins', 'Montserrat', sans-serif;
  --font-body:    'Inter', system-ui, sans-serif;

  --radius-card: 12px;
  --radius-btn:  8px;
  --shadow-card: 0 2px 12px rgba(94, 59, 149, 0.08);
}
```

**Gradient consentiti:** `linear-gradient(135deg, #F7F3FB 0%, #FFFFFF 100%)` per hero secondari. Niente gradient sul viola primario.

**Accessibilita':** contrasto WCAG AA minimo. CTA `#FFC857` su `#FFFFFF` → testo `#1A1A1A`. CTA `#8B5FBF` su `#FFFFFF` → testo `#FFFFFF`.

---

## 3. Sezioni da generare (6 pagine HTML separate)

### 3.1 `index.html` — Homepage

Ordine sezioni (top → bottom):

1. **Header sticky** — logo sx ("Factory Ottica"), nav (Shop · Vista · Sole · Outlet · Servizi · Contatti), icone (search, carrello), mobile hamburger.
2. **Hero split (50/50)**
   - Sinistra: H1 *"Occhiali di qualita', prezzo senza sorprese."* + sub *"Controllo vista incluso nel nostro centro diagnostico, a San Cesareo."* + due CTA: primaria gialla `[Prenota controllo gratuito]` + secondaria outline viola `[Scopri la collezione]`.
   - Destra: immagine modello con occhiali (Unsplash query: `eyeglasses+fashion+model`, `loading="lazy"`).
3. **Trust bar** — 4 colonne icona+testo: `👓 Controllo vista gratuito` · `🔧 Regolazione asta gratis` · `🔄 Reso 30gg` · `⭐ Centro diagnostico oculare`.
4. **Bestseller grid** — titolo *"I piu' scelti del mese"*, grid 4×2 (8 card). Card: foto Unsplash (`sunglasses+fashion`), nome modello, prezzo grassetto + originale barrato + badge `-25%` rosso, badge `Anche graduati` sui sole.
5. **Centro diagnostico** (sfondo `--color-bg-soft`) — H2 *"Piu' di un'ottica: un centro diagnostico"*, 3 colonne (Esame vista · Controllo lenti · Consulenza visiva) + CTA `[Prenota il tuo controllo →]`.
6. **Outlet / Occasioni** — banner orizzontale viola medio con CTA *"Vedi tutte le offerte"*.
7. **Brand row** — strip 6 logo placeholder grayscale (Ray-Ban, Persol, ecc.), opacita' 60% → 100% hover.
8. **Newsletter + Mappa** — split 50/50: sx form (email + checkbox privacy + CTA `[Iscriviti]`), dx mappa placeholder con indirizzo Via Casilina 160, telefono `06 9588307`, orari (Lun 16:30–19:30 · Mar–Sab 09:30–13:00/16:00–19:30 · Dom chiuso).
9. **Footer** — 4 colonne (Negozio · Servizi · Aiuto · Seguici) + copyright + P.IVA placeholder.

### 3.2 `shop.html` — Catalogo

- Breadcrumb `Home / Shop`.
- Layout 2 colonne: sinistra sidebar filtri sticky desktop / drawer mobile, destra grid.
- **Filtri faceted** (da Discovery §8.7): Genere · Forma · Colore montatura · Brand · Prezzo slider 30–300€ · ☑ Anche graduati · ☑ Blue420.
- Toolbar: count prodotti + dropdown ordinamento.
- Grid 3 col desktop / 2 tablet / 1 mobile, 12 card demo. Immagini Unsplash query: `eyeglasses+frame+white+background`.
- Paginazione in fondo.

### 3.3 `product.html` — Scheda prodotto

- Breadcrumb.
- **Sinistra 60%** — galleria: foto principale (Unsplash: `persol+sunglasses` o `rayban+eyeglasses`) + 3 thumbnail + bottone placeholder *"Virtual try-on"* (solo visivo, non funzionale).
- **Destra 40%**:
  - Brand + nome (es. "Persol PO3019S")
  - Stock indicator verde *"Disponibile — Pronta consegna"*
  - Prezzo: `79€` barrato `105€` badge `-25%`
  - Badge *"Anche graduati"*
  - **Configuratore 3 step** (stepper visuale):
    1. Tipo lente (Monofocali · Progressive · Solo montatura)
    2. Graduazione (OD/OS — disabilitati se "Solo montatura")
    3. Trattamenti (Antiriflesso · Blue420 · Fotocromatico)
  - **3 CTA gerarchiche**: primaria gialla `[Aggiungi al carrello — 79€]` · secondaria outline `[Prenota in negozio]` · terziaria link `📞 Consulenza WhatsApp`
- Tabs sotto-fold: Descrizione · Misure · Recensioni · Spedizioni.
- Strip 4 prodotti correlati.

### 3.4 `cart.html` — Carrello

- **NO checkout.** Termina con prenotazione in negozio.
- Tabella: thumb · nome · varianti · quantita' (- / +) · prezzo · subtotale · rimuovi.
- Box riepilogo sticky dx: subtotale, *"Spedizione: calcolata in negozio"*, totale.
- CTA primaria gialla: `[Prenota ritiro in negozio]` → modale form (nome, telefono, fascia oraria).
- CTA link: `← Continua lo shopping`.
- Stato vuoto: illustrazione + CTA `[Esplora il catalogo]`.

### 3.5 `contatti.html` — Contatti

- Hero ridotto: H1 *"Vieni a trovarci a San Cesareo"*.
- 2 colonne: sx info (indirizzo, telefono `06 9588307`, WhatsApp `324 906 8488`, email `prezzidifabbrica14@gmail.com`, orari, link FB+IG) · dx mappa placeholder.
- Form sotto: nome, email, telefono, motivo (dropdown), messaggio, CTA `[Invia richiesta]`.

### 3.6 `servizi.html` — Servizi

- H1 *"I nostri servizi"* + paragrafo educational (tono FB).
- Grid 3 card: Esame vista `Gratuito` · Controllo lenti progressive `Gratuito` · Regolazione asta `Gratuito`. Ogni card: descrizione, durata, CTA `[Prenota →]`.
- FAQ accordion (3-4 domande: *"Quanto dura l'esame?"*, *"Serve appuntamento?"*, *"E' davvero gratuito?"*).
- Banner CTA finale viola: `[Prenota controllo gratuito]`.

---

## 4. Vincoli tecnici

- **NO framework JS pesanti.** Vanilla JS minimal: hamburger, accordion, stepper configuratore, drawer filtri, slider prezzo.
- **CSS custom properties** per tutti i token (vedi §2). Niente Tailwind. CSS BEM semantico: `.product-card`, `.product-card__price`, `.product-card__price--sale`.
- **Markup compatibile WooCommerce.** Classi: `.products`, `.product`, `.woocommerce-loop-product__title`, `.price`, `.add_to_cart_button`. Marker HTML: `<!-- WC HOOK: woocommerce_before_shop_loop_item -->`.
- **Immagini Unsplash — query specifici per ottica:**
  - Hero: `eyeglasses+fashion+model`
  - Product grid / cards: `sunglasses+fashion+frame` o `eyeglasses+frame+white+background`
  - Scheda prodotto: `rayban+sunglasses` o `persol+eyeglasses`
  - NO foto generiche, orologi, oggetti non correlati a occhiali.
- **Catalogo demo ≤ 20 prodotti.** Nomi plausibili: Ray-Ban Aviator, Persol PO3019S, brand fittizi italiani.
- **Mobile-first.** Breakpoint: `768px` tablet, `1024px` desktop, `1280px` wide. Test 375px senza overflow.
- **Performance:** `loading="lazy"`, niente CDN eccetto Google Fonts.
- **Accessibilita':** HTML5 semantico, `aria-label` su icon-only, focus visibile, contrasto AA.

---

## 5. CTA principale e gerarchia

**CTA primaria:** `[Prenota controllo gratuito]` — solid `#FFC857`, testo `#1A1A1A`.

**MAI:** *"Acquista ora"* come CTA principale · urgenza aggressiva · copy inglese non necessario.

**Gerarchia:**
1. Primaria gialla → acquisition (prenota / iscriviti)
2. Secondaria outline viola → consultazione (scopri / prenota in negozio)
3. Terziaria link → contatto diretto (WhatsApp, telefono)

---

## 6. Commenti obbligatori per port PHP WordPress

```html
<!-- WP: get_header() -->
<header class="site-header">...</header>
<!-- /WP: get_header() -->

<!-- WP: front-page.php — hero section -->
<section class="hero">...</section>

<!-- WC LOOP START: while ( have_posts() ) : the_post(); -->
<article class="product">
  <!-- WC: woocommerce_template_loop_product_thumbnail -->
  <!-- WC: woocommerce_template_loop_product_title -->
  <!-- WC: woocommerce_template_loop_price -->
</article>
<!-- WC LOOP END: endwhile; -->

<!-- WP: get_footer() -->
```

---

## 7. Output atteso — struttura file (TASSATIVO)

```
factory-ottica-demo/
├── index.html
├── shop.html
├── product.html
├── cart.html
├── contatti.html
├── servizi.html
├── assets/
│   ├── style.css
│   ├── script.js
│   └── img/
└── README.md
```

**⚠️ CRITICO — NO single-page app:**
- Ogni file HTML e' STANDALONE con proprio `<head>`, `<header>`, `<footer>`.
- NO JavaScript che gestisce la navigazione tra pagine. I link tra pagine sono `<a href="shop.html">`.
- CSS e JS sono file separati in `assets/` linkati da ogni pagina: `<link rel="stylesheet" href="assets/style.css">`.
- Il file `index.html` contiene SOLO la homepage, non tutte le 6 pagine.

**Definition of Done:**
- Palette `#A98BD9` / `#8B5FBF` / `#FFC857` visibilmente dominante.
- CTA *"Prenota controllo gratuito"* in homepage (hero + centro diagnostico + footer).
- Configuratore 3-step in `product.html` funzionante visivamente.
- Filtri in `shop.html` funzionanti UI-side.
- Immagini tutte pertinenti a occhiali (no orologi, no oggetti generici).
- Mobile 375px senza overflow orizzontale.
- Marker `<!-- WP: -->` e `<!-- WC: -->` presenti.

---

## Connessioni

- [[01_Discovery]] — fonti palette, layout, claim
- [[03_Copy_Strategy]] — APSOC, CPB, copy sezioni
- [[00_Piano_Azione]] — Fase 3a (questo brief) → Fase 3b (esecuzione) → Fase 3c (port PHP)
- [[02_Processo_Creativo]] — modello/think/effort per ogni fase
