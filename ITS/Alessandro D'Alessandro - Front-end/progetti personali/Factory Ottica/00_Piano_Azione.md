# Piano d'Azione — Factory Ottica Demo E-commerce

**Versione:** 1.1
**Data:** 2026-05-17 (creato) · 2026-05-18 (aggiornato post-Discovery)
**Autore:** Chirone
**Status:** ✅ Fase 0 completata — vedi [[01_Discovery]] v1.1

> Demo WordPress+WooCommerce credibile su InfinityFree da mostrare alla titolare di Factory Ottica per venderle il sito. **Non** è un sito in produzione: niente pagamenti, niente ordini reali, catalogo finto realistico.

---

## Contesto

- **Cliente target:** Factory Ottica (Centro Ottico Prezzi di Fabbrica) — ottica locale
- **Fonti info disponibili:** solo 2 URL pubblici (Google Maps + Facebook) nella cartella progetto
- **Ruolo Chirone:** esercizio personale per portfolio + tentativo di vendita reale
- **Obiettivo commerciale:** convincere la titolare ad acquistare il sito mostrandole un demo già funzionante

**Stack:** WordPress 7.x · WooCommerce (solo catalogo + carrello, no checkout) · PHP 8.3 · MySQL 8 · InfinityFree free tier · tema custom generato via [open-design](https://github.com/nexu-io/open-design) → port manuale a PHP.

**Strumenti di ricerca:** Perplexity free (web manuale) con prompt costruiti via framework C.I.A.R.E. di [[Prompting MOC|Riccardo Raponi]]. Skill `skill/perplexity` esiste ma in fase di test → usata via copia-incolla manuale.

---

## 🎯 Insight chiave dalla Fase 0 (driver di tutte le scelte successive)

Dal [[01_Discovery]] v1.1 sono emersi 4 vincoli/opportunità che governano da qui in poi:

1. **Hook reale = "Controllo visivo gratuito"**, NON il prezzo. La pagina FB già spinge questo nei post promo → il CTA principale del sito deve essere *"Prenota controllo gratuito"*, non *"Acquista ora"*. Il prezzo è il **secondo** argomento.

2. **Brand visivo viola/lavanda esistente** — usare la palette FB reale (`#A98BD9` + `#8B5FBF` + bianco/`#F7F3FB` + accent `#FFC857`), NON imporre navy+oro. Riconoscibilità immediata in pitch.

3. **Tono educational già loro voice** (post tipo *"Non fidarti solo delle sensazioni"*) → copy sito deve continuare quella narrativa, non sostituirla con copy commerciale aggressivo.

4. **Community piccola (109 follower FB, 0 recensioni FB)** → il sito è anche **acquisition engine** per crescere il social, non solo vetrina. Newsletter + cattura lead = funzionalità prioritaria, non nice-to-have.

**Posizionamento finalizzato:** *"Outlet curato + centro diagnostico"* — non catena, non puro online, non cheap.

**Differenziatori killer per il pitch:**
- Zero presenza web oggi → invisibilità Google dimostrabile in 30s
- Critiche reali clienti su post-vendita opaco → il sito risolve esattamente quello
- Centro diagnostico = vantaggio vs catene low-cost online

---

## Fase 0 — Discovery (1-2 giorni)

**Output:** `01_Discovery.md`

1. **Estrazione info pubbliche** dai 2 URL salvati (Google Maps + Facebook): indirizzo, orari, telefono, recensioni, foto vetrina, post recenti, servizi citati (esame vista, lenti progressive, marche trattate). → skill Perplexity workflow `sintesi_da_fonte` sui due URL.
2. **Competitor scan** — 3 ottici locali + 2 e-commerce ottica italiani (Ottica Avanzi, Salmoiraghi online) per pattern UX del settore. → skill Perplexity workflow `analisi_competitiva`.
3. **ICP del cliente** (la titolare): cosa la convince? Probabilmente *"il sito sembra già pronto, non un mockup"* + *"vedo i miei prodotti, non placeholder Lorem"*.

**Domanda aperta da chiarire con lei prima del pitch (o da assumere e validare):** vuole vendere online davvero o solo presenza digitale + prenotazioni? La demo deve coprire entrambe le narrative.

---

## Fase 1 — Specifica Tecnica (mezza giornata)

Compilare il [[Template - Specifica Tecnica]] di Melani. Sezioni chiave:

- **FA:** catalogo occhiali (vista + sole), schede prodotto con varianti (colore/calibro), carrello → form *"Prenota in negozio"* invece di checkout, pagina servizi, contatti con mappa, blog vuoto-ready.
- **NON FA:** pagamenti, gestione magazzino reale, spedizioni, account utente, recensioni utenti.
- **Vincoli InfinityFree** dal `BRIEF_WordPress_InfinityFree.md`: max 25k inodi, ~128MB memoria, WooCommerce instabile sotto carico → mitigazione = caching aggressivo + pochissimi plugin + ≤20 prodotti demo.

---

## Fase 2 — Catalogo finto realistico (1 giorno)

15-20 prodotti popolati a mano:
- 8 montature vista (Ray-Ban, Persol, marche locali plausibili) — immagini da Unsplash/brand press kit
- 6 occhiali sole
- 3 servizi come *"prodotti"* WooCommerce (esame vista, controllo lenti, regolazione montatura) — pulsante *"Prenota"* invece di *"Acquista"*

CSV pronto per import via WooCommerce nativo (no plugin extra).

---

## Fase 3 — Design via open-design → WordPress theme (1-2 settimane)

Cuore tecnico del progetto. Workflow in 3 step:

**3a. Brief per open-design** (`02_Design_Brief.md`) — input alimentato da skill Perplexity workflow `analisi_competitiva` su 5 siti ottica top (estrazione palette, layout hero, struttura schede prodotto):
- Sezioni richieste: Homepage (hero + categorie + prodotti in evidenza + about + mappa), Shop, Single Product, Cart, Contatti, Servizi
- Brand: palette warm/professionale (occhio = settore tradizionale ma moderno), tipografia serif+sans, mood *"ottica di quartiere ma curata"*
- Output atteso da open-design: HTML + CSS production-ready, no framework JS pesanti

**3b. Generazione** — esegui open-design con il brief, ottieni artifact HTML/CSS.

**3c. Port a WordPress theme PHP:**
- `style.css` (header tema) + `functions.php` (enqueue, WooCommerce support, register menus)
- Template: `header.php`, `footer.php`, `front-page.php`, `page.php`
- WooCommerce override: `woocommerce/archive-product.php`, `single-product.php`, `cart/cart.php` (copia da `wp-content/plugins/woocommerce/templates/` e adatta markup all'HTML di open-design)
- Customizer per logo/colori così in demo puoi mostrare *"vede che posso cambiarle il colore al volo?"*

> ⚠️ Punto critico: open-design produce HTML statico. La conversione a template PHP **non è automatica** — stai sostituendo classi/struttura HTML con loop WP (`while have_posts()`, `wc_get_template_part`). Bisogna prevederlo nella stima tempo.

---

## Fase 4 — Setup InfinityFree (mezza giornata)

- Account free + sottodominio (es. `factoryottica.epizy.com`)
- WP install via Softaculous o manuale
- DB MySQL pulito
- Plugin minimi: **WooCommerce**, **WP Super Cache** (obbligatorio per i vincoli CPU), **WPForms Lite** (form prenotazione), niente altro
- Disabilita WP cron, usa cron InfinityFree
- `wp-config.php`: `WP_MEMORY_LIMIT 96M`, `DISABLE_WP_CRON true`

---

## Fase 5 — Pitch pack (1 giorno)

Per chiudere la vendita serve **più del sito**:
- 1 pagina PDF con: cosa ha visto, cosa include il pacchetto, prezzo, tempi, cosa serve da lei (logo, foto, lista prodotti reali)
- Account demo per farle cliccare lei dal telefono dopo la riunione
- Script di presentazione: 10 min max, focus *"questo è già il SUO sito, manca solo accendere i prodotti veri"*

---

## Rischi & Mitigazioni

| Rischio | Mitigazione |
|---|---|
| WooCommerce timeout su InfinityFree durante la demo live | Demo offline su laptop come piano B (LocalWP pronto); cache pre-warmata 1h prima |
| Port HTML→PHP più lungo del previsto | Budget 2x sul tempo della Fase 3c; in alternativa usare Astra+Elementor come fallback se open-design dà output difficile da portare |
| Cliente chiede *"e i pagamenti?"* in riunione | Risposta pronta: *"attivabili in 2 giorni con Stripe quando vuole, ho preferito mostrarle prima il negozio"* |
| open-design non genera ciò che serve | Itera il brief 2-3 volte max, poi fallback a tema gratuito (Botiga) |

---

## Timeline aggregata

| Fase | Durata | Cumulato |
|---|---|---|
| Discovery + Spec | 2 gg | 2 |
| Catalogo finto | 1 gg | 3 |
| Design + port theme | 7-10 gg | 10-13 |
| Hosting setup | 0.5 gg | 13.5 |
| Pitch pack | 1 gg | 14.5 |

**~3 settimane** part-time tipo progetto ITS.

---

## Prossimo passo concreto

✅ ~~Fase 0~~ chiusa il 2026-05-18.

**Da fare:**
1. **Fase 1** — compilare `02_Specifica_Tecnica.md` usando il template Melani + insight Discovery (30-60 min)
2. **Fase 3a** — scrivere `02_Design_Brief.md` per open-design partendo dalle sezioni 8.4-8.7 del Discovery + palette viola (1-2 ore)
3. **Rivalidazioni manuali** elencate in `01_Discovery.md` §9 (5-10 min al telefono):
   - Screenshot rating Google Maps + 1-2 recensioni testuali
   - Quick scan ottica-avanzi.it
   - Conferma numero principale (`324 906 8488` vs `06 9588307`)

---

## Connessioni

- [[BRIEF_WordPress_InfinityFree]] — vincoli hosting già ricercati
- [[ADR_WordPress_InfinityFree]] — decisioni architetturali correlate
- [[Template - Specifica Tecnica]] — template Melani da compilare in Fase 1
- [[Progettistica AI MOC]]
