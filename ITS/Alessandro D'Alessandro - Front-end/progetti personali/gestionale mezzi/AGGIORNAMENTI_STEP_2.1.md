# AGGIORNAMENTI STEP 2.1 — Navbar responsive + Impersonation + Fix layout

**Data:** 27 maggio 2026  
**Modifiche:** Navbar desktop orizzontale, sistema impersonation admin, fix CSS WordPress

---

## 🎯 Obiettivi completati

### 1. Sistema Impersonation (admin può testare come altri ruoli)
**File modificati:** `includes/impersonation.php` (nuovo), `templates/layout.php`, `assets/app.js`

**Funzionalità:**
- Admin può simulare la vista di Volontario/Direttivo/Amministrazione
- Dropdown "👁️ Visualizza come" nella navbar (solo per admin WordPress)
- Badge ruolo cambia colore quando si impersona (giallo warning invece di blu)
- Menu sidebar si adatta al ruolo impersonato in tempo reale
- Switch ruolo via AJAX senza reload pagina
- Sessione PHP `$_SESSION['gm_impersonate_role']` mantiene stato

**Codice chiave:**
```php
// includes/impersonation.php
function gm_is_admin_user() {
    $user = wp_get_current_user();
    return in_array( 'administrator', (array) $user->roles, true );
}

function gm_get_impersonated_role() {
    return $_SESSION['gm_impersonate_role'] ?? '';
}

function gm_user_can_impersonated( $capability ) {
    $impersonated = gm_get_impersonated_role();
    if ( ! empty( $impersonated ) && gm_is_admin_user() ) {
        $role_obj = get_role( $impersonated );
        return $role_obj && $role_obj->has_cap( $capability );
    }
    return current_user_can( $capability );
}
```

**AJAX handler:**
```php
add_action( 'wp_ajax_gm_switch_role', 'gm_ajax_switch_role' );

function gm_ajax_switch_role() {
    check_ajax_referer( 'gm_switch_role_nonce', 'nonce' );
    
    if ( ! gm_is_admin_user() ) {
        wp_send_json_error( 'Non autorizzato' );
    }

    $role = sanitize_text_field( $_POST['role'] ?? '' );
    
    if ( empty( $role ) ) {
        unset( $_SESSION['gm_impersonate_role'] );
    } else {
        $_SESSION['gm_impersonate_role'] = $role;
    }

    wp_send_json_success( [ 'role' => $role ] );
}
```

**UI Layout (navbar):**
```php
<?php if ( gm_is_admin_user() ) : ?>
    <div class="dropdown d-none d-lg-block">
        <button class="btn btn-sm btn-outline-primary dropdown-toggle" 
                id="gm-role-switch" 
                data-bs-toggle="dropdown">
            👁️ Visualizza come
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item gm-switch-role" href="#" data-role="">Amministrazione (me)</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item gm-switch-role" href="#" data-role="gm_direttivo">Direttivo</a></li>
            <li><a class="dropdown-item gm-switch-role" href="#" data-role="gm_volontario">Volontario</a></li>
        </ul>
    </div>
<?php endif; ?>
```

**Badge dinamico:**
```php
<?php if ( $is_impersonating ) : ?>
    <span class="badge bg-warning text-dark">
        👁️ <?php echo esc_html( $real_name ); ?> → <?php echo esc_html( $impersonated_name ); ?>
    </span>
<?php else : ?>
    <span class="badge bg-primary">
        <?php echo esc_html( $role_name ); ?>
    </span>
<?php endif; ?>
```

---

### 2. Navbar responsive orizzontale (desktop)
**File modificato:** `templates/layout.php`, `assets/style.css`

**Prima:** hamburger menu sempre, anche su desktop  
**Dopo:** menu orizzontale inline su desktop (≥992px), hamburger solo mobile

**Struttura navbar:**
```html
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm fixed-top" 
     style="margin-block-start: 0rem;">
    <div class="container-fluid" style="padding-left: 4rem !important; padding-right: 4rem !important;">
        
        <!-- Hamburger (solo mobile) -->
        <button class="btn btn-link d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#gm-sidebar">
            <svg>...</svg>
        </button>

        <!-- Logo -->
        <a class="navbar-brand" href="/gestionale-dashboard/">Gestionale Mezzi</a>

        <!-- Menu orizzontale (solo desktop ≥992px) -->
        <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <!-- Prime 4 voci visibili inline -->
                <li class="nav-item"><a href="..." class="nav-link">Dashboard</a></li>
                <li class="nav-item"><a href="..." class="nav-link">Nuovo Foglio</a></li>
                <li class="nav-item"><a href="..." class="nav-link">I Miei Fogli</a></li>
                <li class="nav-item"><a href="..." class="nav-link">Tutti i Fogli</a></li>

                <!-- Voci overflow in dropdown "Altro" -->
                <?php if ( count($menu_items) > 4 ) : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" 
                           id="navbarDropdown" 
                           data-bs-toggle="dropdown">Altro</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="...">Gestione Utenti</a></li>
                            <li><a class="dropdown-item" href="...">Gestione Veicoli</a></li>
                            <li><a class="dropdown-item" href="...">Log Attività</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Dropdown impersonate + badge + username + esci -->
        <div class="d-flex align-items-center gap-2 ms-auto">
            ...
        </div>
    </div>
</nav>
```

**Logica overflow menu:**
```php
$max_visible = 4; // Max link visibili inline
$visible_items = array_slice( $menu_items, 0, $max_visible );
$overflow_items = array_slice( $menu_items, $max_visible );
```

**Risultato:**
- **Direttivo** (4 voci): tutte visibili inline, no dropdown
- **Amministrazione** (7 voci): prime 4 inline + dropdown "Altro" con ultime 3

**CSS spacing menu desktop:**
```css
@media (min-width: 992px) {
    .navbar-nav .nav-item {
        margin: 0 0.5rem;
    }

    .navbar-nav .nav-link {
        padding: 0.5rem 1rem !important;
        font-weight: 500;
        transition: color 0.15s ease-in-out;
    }

    .navbar-nav .nav-link:hover {
        color: var(--gm-primary) !important;
    }
}
```

---

### 3. Fix layout WordPress (padding/margin)
**File modificati:** `templates/layout.php`, `includes/auth.php`, `assets/style.css`

**Problemi risolti:**

#### A. Menu tema WordPress duplicati
**Causa:** WordPress aggiungeva header/nav/footer del tema attivo  
**Fix:** CSS aggressivo + body class `gestionale-page`

```php
// includes/auth.php - Aggiungi body class
add_filter( 'body_class', 'gm_add_body_class' );

function gm_add_body_class( $classes ) {
    if ( is_page() ) {
        global $post;
        if ( $post && strpos( $post->post_name, 'gestionale-' ) === 0 ) {
            $classes[] = 'gestionale-page';
        }
    }
    return $classes;
}
```

```css
/* assets/style.css - Nascondi elementi tema */
body.gestionale-page header:not([class*="gm-"]),
body.gestionale-page nav:not(.navbar):not(.nav):not([class*="gm-"]),
body.gestionale-page footer:not([class*="gm-"]),
body.gestionale-page .site-header,
body.gestionale-page .site-footer,
body.gestionale-page #masthead,
body.gestionale-page #site-navigation,
body.gestionale-page .main-navigation,
body.gestionale-page .wp-block-navigation {
    display: none !important;
    visibility: hidden !important;
    height: 0 !important;
    overflow: hidden !important;
}
```

#### B. Admin bar WordPress visibile
**Fix:** Hook `show_admin_bar(false)` per ruoli gm_*

```php
// includes/auth.php
add_action( 'after_setup_theme', 'gm_hide_admin_bar' );

function gm_hide_admin_bar() {
    if ( ! is_user_logged_in() ) return;
    
    $user = wp_get_current_user();
    $gm_roles = [ 'gm_volontario', 'gm_direttivo', 'gm_amministrazione' ];
    
    if ( ! empty( array_intersect( $gm_roles, (array) $user->roles ) ) ) {
        show_admin_bar( false );
    }
}
```

```css
/* Fallback CSS */
#wpadminbar {
    display: none !important;
}

html {
    margin-top: 0 !important;
}
```

#### C. Padding eccessivo WordPress (70px theme default)
**Causa:** Tema WordPress aggiunge classi `.wp-block-group`, `.has-global-padding` con padding 70px  
**Fix:** Inline style con `!important` + rimozione CSS ridondante

```html
<!-- templates/layout.php -->
<nav style="margin-block-start: 0rem;" class="navbar ...">
    <div class="container-fluid" style="padding-left: 4rem !important; padding-right: 4rem !important;">
        ...
    </div>
</nav>

<main class="container-fluid px-4 py-2" style="padding-top: 64px;">
    ...
</main>
```

**CSS pulito (eliminati duplicati):**
- ❌ Rimosso: `.navbar { max-width: 100%; }` — ridondante con inline style
- ❌ Rimosso: `.navbar .container-fluid { padding-left: 1rem; }` — sostituito da inline 4rem
- ❌ Rimosso: `main.container-fluid { max-width: 100%; }` — già gestito da Bootstrap
- ❌ Rimosso: sezione touch-friendly vuota

---

### 4. Ottimizzazioni CSS finale
**File:** `assets/style.css`

**Prima:** 269 righe  
**Dopo:** 234 righe (-13%)

**Rimosso:**
1. Regole `max-width` navbar (inline style prioritario)
2. Regole `padding` navbar (inline style 4rem)
3. Regole full-width main (Bootstrap `container-fluid` le gestisce)
4. Sezione touch-friendly vuota
5. Margin-block constraints ridondanti

**Mantenuto:**
- ✅ Palette colori (`:root` custom properties)
- ✅ Override tema WordPress (nascondi header/footer/nav)
- ✅ Stili componenti (cards, buttons, alerts, forms)
- ✅ Menu desktop spacing (`.navbar-nav .nav-item`)
- ✅ Offcanvas sidebar
- ✅ Responsive breakpoint mobile

---

## 📊 Metriche finali

### Confronto dimensioni file

| File | Prima | Dopo | Δ |
|---|---|---|---|
| `style.css` | 269 righe | 234 righe | -13% |
| `layout.php` | 120 righe | 217 righe | +81% (impersonation + menu desktop) |
| `app.js` | 40 righe | 68 righe | +70% (AJAX switch ruolo) |

### File totali progetto
- **PHP:** 14 file (12 STEP 2 + 1 impersonation.php + 1 page-gestionale.php cancellato)
- **CSS/JS:** 2 file
- **Totale inode:** 16 file ✅ (< 30 limite InfinityFree)

---

## 🧪 Test eseguiti

### Desktop (≥992px)
- ✅ Menu orizzontale visibile inline (no hamburger)
- ✅ Dropdown "Altro" funziona per ruoli con >4 voci
- ✅ Dropdown impersonate visibile solo per admin
- ✅ Badge ruolo cambia colore quando impersona (giallo)
- ✅ Switch ruolo via AJAX senza reload
- ✅ Menu sidebar si aggiorna dinamicamente dopo switch
- ✅ Navbar full-width (padding 4rem ai lati)
- ✅ Contenuto main parte subito sotto navbar (padding-top 64px)

### Mobile (<992px)
- ✅ Hamburger visibile
- ✅ Menu inline nascosto (`d-none d-lg-flex`)
- ✅ Offcanvas funziona (swipe/tap/backdrop/ESC)
- ✅ Auto-close offcanvas al click link
- ✅ Badge ruolo responsive (`d-none d-md-inline`)

### Impersonation
- ✅ Admin vede dropdown "Visualizza come"
- ✅ Non-admin non vede dropdown
- ✅ Switch a "Volontario" → menu ridotto (solo Dashboard, Nuovo Foglio, I Miei Fogli)
- ✅ Switch a "Direttivo" → menu medio (+ Tutti i Fogli, Gestione Utenti/Veicoli)
- ✅ Switch a "Amministrazione (me)" → menu completo (+ Log Attività)
- ✅ Badge mostra "👁️ Amministrazione → Volontario" quando impersona
- ✅ Sessione mantiene stato impersonato tra pagine

### WordPress override
- ✅ Header tema nascosto
- ✅ Footer tema nascosto
- ✅ Menu tema nascosto
- ✅ Admin bar nascosta
- ✅ Padding WordPress 70px rimosso
- ✅ Solo navbar gestionale visibile

---

## 🔄 Modifiche breaking (nessuna)

Tutte le modifiche sono **backward compatible**:
- Utenti non-admin non vedono cambiamenti (no dropdown impersonate)
- Menu mobile funziona come prima (offcanvas)
- Tutte le pagine esistenti funzionano senza modifiche
- Capability check rimane invariato (`gm_user_can_impersonated()` è wrapper trasparente)

---

## 📝 Note implementazione

### Perché inline styles invece di CSS?
WordPress aggiunge classi CSS **dopo** il plugin, quindi CSS normale viene sovrascritto. Inline style con `!important` garantisce priorità massima.

### Perché overflow dropdown invece di scroll orizzontale?
- Più pulito visivamente
- Standard pattern Bootstrap
- Evita scrollbar orizzontale fastidiosa
- Mobile-friendly (no gesture conflict)

### Perché AJAX invece di form submit per switch ruolo?
- UX migliore (no reload pagina)
- Menu si aggiorna dinamicamente
- Badge cambia colore in tempo reale
- Sessione persiste tra pagine

### Perché `$_SESSION` invece di user meta?
- Temporaneo per design (solo simulazione, no modifica DB)
- Admin torna al suo ruolo facendo logout
- No inquinamento user meta
- Più veloce (no DB query)

---

## 🎯 Prossimi step

### Funzionalità da testare ulteriormente
- [ ] Impersonation con più admin contemporaneamente
- [ ] Sessione impersonate dopo timeout 7 giorni
- [ ] Comportamento AJAX se nonce scade

### Miglioramenti futuri (opzionali)
- [ ] Salvare ultimo ruolo impersonato in localStorage (persistenza tra sessioni)
- [ ] Animazione transizione badge ruolo (fade color change)
- [ ] Dropdown sticky su scroll (keep visible quando menu scorre)
- [ ] Keyboard shortcuts per switch ruolo (Alt+1/2/3)

---

## 🐛 Bug fix risolti

1. **Menu tema WordPress sovrapposto** → CSS body.gestionale-page selettore specifico
2. **Admin bar visibile** → Hook show_admin_bar(false) + CSS fallback
3. **Padding 70px WordPress non rimovibile** → Inline style !important
4. **Contenuto va sotto navbar quando scrolli** → padding-top 64px su main
5. **Menu desktop compresso** → padding navbar 4rem + spacing nav-item 0.5rem
6. **Dropdown "Altro" non si apre** → Aggiunto Bootstrap dropdown-toggle + data-bs-toggle

---

## 📦 File modificati (recap)

### Nuovi file
- `includes/impersonation.php` — sistema switch ruolo admin

### File modificati
- `templates/layout.php` — navbar responsive + dropdown impersonate + inline styles
- `assets/style.css` — pulizia CSS ridondante + override WordPress
- `assets/app.js` — AJAX switch ruolo
- `includes/auth.php` — hide admin bar + body class gestionale-page
- `includes/pages.php` — gm_get_menu_items() spostata qui (da layout.php)
- `gestionale-mezzi.php` — require impersonation.php + nonce AJAX

### File eliminati
- `templates/page-gestionale.php` — tentativo page template custom (non serviva)

---

**Fine aggiornamenti STEP 2.1**  
Pronto per STEP 3: form "Nuovo Foglio di Marcia" funzionante.
