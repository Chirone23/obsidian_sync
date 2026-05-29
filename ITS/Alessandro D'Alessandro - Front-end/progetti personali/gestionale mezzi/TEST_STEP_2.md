# TEST STEP 2 — Sidebar drawer + layout base (Bootstrap 5)

## Modifiche rispetto alla versione iniziale

**Stack frontend aggiornato:**
- Bootstrap 5.3.0 via CDN (CSS + JS)
- CSS custom ridotto: solo override palette + utility minime
- JS semplificato: Bootstrap Offcanvas gestisce il drawer automaticamente

## File creati/modificati

### PHP
- `includes/pages.php` — registrazione pagine WP e shortcode
- `templates/layout.php` — layout base con Bootstrap Navbar + Offcanvas sidebar
- `templates/dashboard.php` — pagina dashboard (Bootstrap cards + utilities)
- `templates/error-403.php` — pagina errore 403
- `templates/nuovo-foglio.php` — placeholder nuovo foglio
- `templates/i-miei-fogli.php` — placeholder i miei fogli
- `templates/tutti-i-fogli.php` — placeholder tutti i fogli
- `templates/gestione-utenti.php` — placeholder gestione utenti
- `templates/gestione-veicoli.php` — placeholder gestione veicoli
- `templates/log-attivita.php` — placeholder log attività

### CSS/JS
- `assets/style.css` — override palette custom + utility Bootstrap
- `assets/app.js` — auto-close offcanvas su mobile al click link

### Modificato
- `gestionale-mezzi.php` — aggiunto require pages.php, enqueue CSS/JS, chiamata gm_register_pages()

---

## Checklist test in localhost (XAMPP)

### 1. Riattiva il plugin
```
WP Admin → Plugin → Disattiva "Gestionale Mezzi"
WP Admin → Plugin → Attiva "Gestionale Mezzi"
```

Questo esegue:
- `gm_create_tables()` (già fatto, skip)
- `gm_register_roles()` (già fatto, skip)
- **`gm_register_pages()`** (NUOVO — crea le 7 pagine)
- `flush_rewrite_rules()`

### 2. Verifica creazione pagine
```
WP Admin → Pagine → Tutte le pagine
```

Devono esistere (stato: Pubblicato):
- gestionale-dashboard
- gestionale-nuovo-foglio
- gestionale-i-miei-fogli
- gestionale-tutti-i-fogli
- gestionale-gestione-utenti
- gestionale-gestione-veicoli
- gestionale-log-attivita

### 3. Verifica caricamento Bootstrap CDN
Apri DevTools (F12) → Network tab

**Vai a:** `http://localhost/[sito]/gestionale-dashboard/`

**Verifica:**
- [x] Bootstrap CSS caricato: `https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css`
- [x] Bootstrap JS caricato: `https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js`
- [x] `style.css` caricato (custom override)
- [x] `app.js` caricato
- [x] Nessun errore 404

### 4. Test con utente **volontario_test**
```
Logout da WP Admin
Login come: volontario_test / test1234
```

**Vai a:** `http://localhost/[sito]/gestionale-dashboard/`

**Verifica header Bootstrap:**
- [ ] Navbar fixed-top visibile (sfondo bianco, border-bottom, shadow)
- [ ] Hamburger icon (3 linee orizzontali) visibile a sinistra
- [ ] Logo "Gestionale Mezzi" (blu, fw-bold) al centro
- [ ] Nome utente visibile su desktop (nascosto su mobile < 768px)
- [ ] Bottone "Esci" (btn-sm btn-outline-secondary) visibile

**Verifica Offcanvas sidebar (Bootstrap):**
- [ ] Click su hamburger → offcanvas si apre da sinistra con animazione smooth
- [ ] Backdrop scuro appare (overlay Bootstrap)
- [ ] Header sidebar: "Menu" (blu, fw-bold) + bottone X (btn-close)
- [ ] Voci menu visibili:
  - Dashboard
  - Nuovo Foglio
  - I Miei Fogli
  - ~~Tutti i Fogli~~ (NON deve apparire — solo direttivo/amministrazione)
  - ~~Gestione Utenti~~ (NON deve apparire)
  - ~~Gestione Veicoli~~ (NON deve apparire)
  - ~~Log Attività~~ (NON deve apparire)
- [ ] Hover su voce menu → sfondo grigio chiaro + testo blu
- [ ] Click su backdrop → offcanvas si chiude
- [ ] ESC key → offcanvas si chiude
- [ ] Click su "X" → offcanvas si chiude

**Verifica dashboard:**
- [ ] Card "Benvenuto" con header border-bottom
- [ ] Badge "Volontario" (bg-primary)
- [ ] Card "Azioni rapide" con bottoni:
  - "Crea Nuovo Foglio di Marcia" (btn-primary btn-lg, icon SVG)
  - "Visualizza i Miei Fogli" (btn-outline-secondary btn-lg)
  - ~~"Visualizza Tutti i Fogli"~~ (NON deve apparire)
- [ ] Alert blu "Nota: Questa è la dashboard..." (alert-primary, icon SVG)

**Click su "Nuovo Foglio":**
- [ ] Apre `/gestionale-nuovo-foglio/`
- [ ] Card con header "Nuovo Foglio di Marcia" (text-primary)
- [ ] Alert "In sviluppo" (alert-primary)
- [ ] Lista con checkmark (✓) styled Bootstrap

**Click su "I Miei Fogli":**
- [ ] Apre `/gestionale-i-miei-fogli/`
- [ ] Card con placeholder "In sviluppo"

**Prova ad accedere manualmente a:** `http://localhost/[sito]/gestionale-tutti-i-fogli/`
- [ ] Mostra pagina "Accesso Negato"
- [ ] Card con header rosso (bg-danger text-white)
- [ ] Alert rosso con icon SVG
- [ ] Bottone "Torna alla Dashboard" (btn-primary, d-grid)

### 5. Test con utente **direttivo_test**
```
Logout
Login come: direttivo_test / test1234
```

**Vai a:** `http://localhost/[sito]/gestionale-dashboard/`

**Verifica:**
- [ ] Badge ruolo "Direttivo" (bg-primary)
- [ ] Bottone "Visualizza Tutti i Fogli" ORA visibile
- [ ] Voci menu offcanvas:
  - Dashboard
  - Nuovo Foglio
  - I Miei Fogli
  - **Tutti i Fogli** (ORA deve apparire)
  - **Gestione Utenti** (ORA deve apparire)
  - **Gestione Veicoli** (ORA deve apparire)
  - ~~Log Attività~~ (NON deve apparire — solo amministrazione)
- [ ] Click su "Tutti i Fogli" → apre la pagina (placeholder)
- [ ] Click su "Gestione Utenti" → apre la pagina (placeholder)
- [ ] Click su "Gestione Veicoli" → apre la pagina (placeholder)

**Prova ad accedere manualmente a:** `http://localhost/[sito]/gestionale-log-attivita/`
- [ ] Mostra pagina "Accesso Negato" (errore 403)

### 6. Test con utente **amm_test**
```
Logout
Login come: amm_test / test1234
```

**Vai a:** `http://localhost/[sito]/gestionale-dashboard/`

**Verifica:**
- [ ] Badge ruolo "Amministrazione" (bg-primary)
- [ ] Tutte le voci menu visibili (incluso "Log Attività")
- [ ] Click su "Log Attività" → apre la pagina (placeholder)

### 7. Test responsività mobile (Bootstrap breakpoints)
Usa DevTools (F12) → Toggle device toolbar (Ctrl+Shift+M)

**iPhone SE (375px):**
- [ ] Navbar compatta: username nascosto (d-none d-md-inline)
- [ ] Hamburger icon ben visibile (touch-friendly)
- [ ] Offcanvas copre tutta la larghezza schermo
- [ ] Bottoni "Azioni rapide": d-grid (100% larghezza, stacked)
- [ ] Click su link menu → offcanvas si chiude automaticamente (JS custom)

**iPad (768px):**
- [ ] Username visibile nell'header (d-md-inline attivo)
- [ ] Offcanvas mantiene larghezza 320px (non copre tutto)
- [ ] Bottoni "Azioni rapide": d-md-block (inline, non stacked)

**Desktop (1024px+):**
- [ ] Layout più arioso (container-fluid con padding)
- [ ] Card con shadow-sm visibili
- [ ] Offcanvas si chiude al click link solo se < 1024px (JS check)

### 8. Test palette custom (CSS override Bootstrap)
Ispeziona elementi (F12) e verifica custom properties:

```css
--gm-primary: #2563eb (blu custom, override --bs-primary)
--gm-accent: #fe7b02 (arancione accent, non usato ancora)
--gm-bg: #fcfbf8 (sfondo crema, override --bs-body-bg)
```

**Verifica colori applicati:**
- [ ] Body background: `#fcfbf8` (crema)
- [ ] Navbar brand: `#2563eb` (blu custom)
- [ ] Badge primary: `#2563eb`
- [ ] Bottone primary: `#2563eb`
- [ ] Alert primary: sfondo blu 10% trasparente, border blu

### 9. Test JavaScript (console)
Apri DevTools → Console

**Verifica:**
- [ ] Nessun errore JavaScript
- [ ] Bootstrap bundle caricato (verifica `typeof bootstrap !== 'undefined'` in console)
- [ ] Log "Gestionale Mezzi initialized with Bootstrap 5" (da app.js)

**Test auto-close offcanvas su mobile:**
1. Riduci viewport a 375px (iPhone)
2. Apri offcanvas
3. Clicca su "Dashboard" nel menu
4. [ ] Offcanvas si chiude automaticamente (chiamata `bsOffcanvas.hide()`)

**Test auto-close NON attivo su desktop:**
1. Viewport >= 1024px
2. Apri offcanvas
3. Clicca su "Dashboard" nel menu
4. [ ] Offcanvas NON si chiude (check `window.innerWidth < 1024` false)

### 10. Test blocco wp-admin
**Con volontario_test loggato:**

Prova ad accedere a: `http://localhost/[sito]/wp-admin/`

- [ ] Redirect automatico a `home_url()` (homepage)
- [ ] Nessun accesso a wp-admin

---

## Problemi comuni

### Bootstrap CDN non si carica (errore 404 in Network)
**Soluzione:**
- Verifica connessione Internet
- Se offline, scarica Bootstrap e servilo localmente da `assets/bootstrap/`
- Aggiorna URL in `layout.php`

### Le pagine non vengono create
**Soluzione:** Disattiva e riattiva il plugin. Se già esistono, non vengono ricreate (check con `get_page_by_path()`).

### CSS custom non si applica (palette sbagliata)
**Soluzione:**
- Hard refresh (Ctrl+F5)
- Verifica che `style.css` sia caricato DOPO Bootstrap (wp_head in layout.php)
- Controlla ordine enqueue in `gestionale-mezzi.php`

### Offcanvas non si apre
**Soluzione:**
- Verifica che Bootstrap JS sia caricato (check in Network tab)
- Apri Console e cerca errori Bootstrap
- Verifica che `data-bs-toggle="offcanvas"` e `data-bs-target="#gm-sidebar"` siano corretti in layout.php

### Errore 500 su una pagina
**Soluzione:**
- Controlla error.log di Apache
- Verifica che tutti i template esistano in `templates/`
- Verifica sintassi PHP (`php -l file.php`)

### Offcanvas non si chiude automaticamente su mobile
**Soluzione:**
- Verifica che `app.js` sia caricato (Network tab)
- Apri Console e verifica errore JS
- Controlla che `bootstrap.Offcanvas.getInstance()` funzioni

---

## Step 2 COMPLETATO se:
- ✅ Tutte le 7 pagine create e accessibili
- ✅ Bootstrap 5 caricato via CDN (CSS + JS)
- ✅ Offcanvas sidebar funzionante (apre/chiude, backdrop, ESC key)
- ✅ Menu filtrato per ruolo (volontario < direttivo < amministrazione)
- ✅ Palette custom applicata (blu #2563eb, sfondo crema #fcfbf8)
- ✅ Layout mobile-first responsive (Bootstrap breakpoints)
- ✅ Protezione 403 su pagine senza permessi
- ✅ Blocco wp-admin per ruoli gm_*
- ✅ Auto-close offcanvas su mobile al click link

---

## Vantaggi Bootstrap vs CSS custom

✅ **Meno codice:** 150 righe CSS vs 600+ linee custom
✅ **Componenti pronti:** Offcanvas, Navbar, Cards, Alerts, Buttons, Grid
✅ **Responsive nativo:** Breakpoints, utilities (d-none, d-md-inline, etc.)
✅ **JS già gestito:** Offcanvas con backdrop, ESC key, ARIA a11y
✅ **Manutenibilità:** Bootstrap è documentato, testato, cross-browser
✅ **Palette custom:** Override via CSS custom properties (--gm-primary, etc.)

---

## Prossimi step (Step 3)
- Form "Nuovo Foglio di Marcia" funzionante (Bootstrap form components)
- Inserimento dati in `wp_gm_fogli_di_marcia` e `wp_gm_foglio_passeggeri`
- Generazione numero progressivo annuale
- Validazione lato server + client (Bootstrap validation)
- Log attività su inserimento
