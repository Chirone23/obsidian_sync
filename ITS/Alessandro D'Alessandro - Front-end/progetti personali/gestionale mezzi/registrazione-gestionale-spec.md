# Registrazione Utenti Gestionale — Specifica Funzionale

## Panoramica

Sfruttiamo il sistema di login/registrazione nativo di WordPress modificandolo tramite
hook ufficiali. Non tocchiamo mai `wp-login.php` direttamente. Il risultato è una
pagina di registrazione che sembra custom ma è 100% WordPress sotto — stabile,
sicura e sopravvive a qualsiasi aggiornamento.

---

## Flow completo

```
Admin genera link → utente apre /registrati →
compila form → WP crea account → email automatica con link set-password →
utente setta la psw → login → redirect al gestionale (in base al ruolo)
```

---

## Struttura file nel plugin

```
gestionale/
├── gestionale.php              ← file principale del plugin
└── includes/
    ├── registrazione.php       ← hook form registrazione
    ├── login.php               ← hook redirect e login
    ├── ruoli.php               ← definizione ruoli e capabilities
    ├── validazione.php         ← validazione e sanitizzazione input
    └── assets/
        └── login-style.css     ← stile custom pagina login/registrazione
```

---

## 1. Ruoli e Capabilities

Definiti all'attivazione del plugin, rimossi alla disattivazione.

```php
// includes/ruoli.php

function gestionale_crea_ruoli() {

    add_role('g_operatore', 'Operatore', [
        'read'                    => true,
        'gestionale_accesso'      => true,
        'gestionale_visualizza'   => true,
    ]);

    add_role('g_supervisore', 'Supervisore', [
        'read'                    => true,
        'gestionale_accesso'      => true,
        'gestionale_visualizza'   => true,
        'gestionale_modifica'     => true,
    ]);

    add_role('g_admin', 'Admin Gestionale', [
        'read'                    => true,
        'gestionale_accesso'      => true,
        'gestionale_visualizza'   => true,
        'gestionale_modifica'     => true,
        'gestionale_amministra'   => true,
    ]);
}
register_activation_hook(__FILE__, 'gestionale_crea_ruoli');

function gestionale_rimuovi_ruoli() {
    remove_role('g_operatore');
    remove_role('g_supervisore');
    remove_role('g_admin');
}
register_deactivation_hook(__FILE__, 'gestionale_rimuovi_ruoli');
```

| Ruolo | Visualizza | Modifica | Amministra |
|-------|-----------|---------|-----------|
| `g_operatore` | ✅ | ❌ | ❌ |
| `g_supervisore` | ✅ | ✅ | ❌ |
| `g_admin` | ✅ | ✅ | ✅ |

---

## 2. Form di Registrazione Custom

Aggiunge campi al form nativo tramite hook. WordPress mantiene la gestione
di nonce, anti-spam e invio email set-password.

```php
// includes/registrazione.php

// --- Campi aggiuntivi nel form ---
add_action('register_form', 'gestionale_campi_registrazione');
function gestionale_campi_registrazione() {
    $nome    = isset($_POST['g_nome'])    ? sanitize_text_field($_POST['g_nome'])    : '';
    $cognome = isset($_POST['g_cognome']) ? sanitize_text_field($_POST['g_cognome']) : '';
    ?>
    <p>
        <label for="g_nome">Nome <br>
        <input type="text" name="g_nome" id="g_nome"
               value="<?php echo esc_attr($nome); ?>"
               class="input" required autocomplete="given-name" /></label>
    </p>
    <p>
        <label for="g_cognome">Cognome <br>
        <input type="text" name="g_cognome" id="g_cognome"
               value="<?php echo esc_attr($cognome); ?>"
               class="input" required autocomplete="family-name" /></label>
    </p>

    <!--
        Il ruolo NON lo sceglie l'utente: viene assegnato dall'admin
        nel link di invito tramite parametro GET firmato (vedi sezione 5).
        Campo hidden per trasportarlo nel submit.
    -->
    <input type="hidden" name="g_ruolo"
           value="<?php echo esc_attr(gestionale_ruolo_dal_token()); ?>" />
    <?php
}

// --- Validazione prima della creazione ---
add_filter('registration_errors', 'gestionale_valida_registrazione', 10, 3);
function gestionale_valida_registrazione($errors, $sanitized_user_login, $user_email) {

    if (empty($_POST['g_nome'])) {
        $errors->add('g_nome_vuoto', '<strong>Errore:</strong> Inserisci il nome.');
    }
    if (empty($_POST['g_cognome'])) {
        $errors->add('g_cognome_vuoto', '<strong>Errore:</strong> Inserisci il cognome.');
    }

    $ruolo = $_POST['g_ruolo'] ?? '';
    $ruoli_validi = ['g_operatore', 'g_supervisore', 'g_admin'];
    if (!in_array($ruolo, $ruoli_validi, true)) {
        $errors->add('g_ruolo_invalido', '<strong>Errore:</strong> Link di invito non valido.');
    }

    return $errors;
}

// --- Salvataggio dati e assegnazione ruolo dopo la creazione ---
add_action('user_register', 'gestionale_salva_dati_utente');
function gestionale_salva_dati_utente($user_id) {

    update_user_meta($user_id, 'first_name', sanitize_text_field($_POST['g_nome']));
    update_user_meta($user_id, 'last_name',  sanitize_text_field($_POST['g_cognome']));

    $ruolo = sanitize_key($_POST['g_ruolo']);
    $user  = new WP_User($user_id);
    $user->set_role($ruolo);

    // Imposta display name subito
    wp_update_user([
        'ID'           => $user_id,
        'display_name' => sanitize_text_field($_POST['g_nome'] . ' ' . $_POST['g_cognome']),
    ]);
}
```

---

## 3. Redirect Login — Vai al Gestionale

Dopo il login l'utente non finisce in `/wp-admin` ma direttamente nella
sezione del gestionale di competenza.

```php
// includes/login.php

add_filter('login_redirect', 'gestionale_redirect_dopo_login', 10, 3);
function gestionale_redirect_dopo_login($redirect_to, $requested_redirect_to, $user) {

    if (is_wp_error($user)) return $redirect_to;

    if ($user->has_cap('gestionale_amministra')) {
        return home_url('/gestionale/admin/');
    }
    if ($user->has_cap('gestionale_modifica')) {
        return home_url('/gestionale/supervisore/');
    }
    if ($user->has_cap('gestionale_accesso')) {
        return home_url('/gestionale/');
    }

    // Utente WordPress normale senza accesso al gestionale → wp-admin
    return $redirect_to;
}

// Blocca accesso wp-admin agli utenti solo-gestionale
add_action('admin_init', 'gestionale_blocca_wp_admin');
function gestionale_blocca_wp_admin() {
    if (defined('DOING_AJAX') && DOING_AJAX) return;

    $user = wp_get_current_user();
    if ($user->has_cap('gestionale_accesso') && !$user->has_cap('manage_options')) {
        wp_redirect(home_url('/gestionale/'));
        exit;
    }
}
```

---

## 4. Stile Custom della Pagina

La pagina `/wp-login.php` si stila completamente via CSS e una funzione
dedicata — nessun tema coinvolto.

```php
// in registrazione.php

add_action('login_enqueue_scripts', 'gestionale_stile_login');
function gestionale_stile_login() {
    wp_enqueue_style(
        'gestionale-login',
        plugin_dir_url(__FILE__) . '../assets/login-style.css'
    );
}

// Logo custom sopra il form
add_filter('login_headerurl', fn() => home_url());
add_filter('login_headertext', fn() => get_bloginfo('name'));
```

```css
/* assets/login-style.css */
body.login {
    background: #f4f6f9;
    font-family: 'Inter', sans-serif;
}
#login {
    width: 380px;
}
.login h1 a {
    background-image: url('logo-gestionale.svg');
    background-size: contain;
    width: 200px;
    height: 60px;
}
.login label { font-size: 13px; color: #333; }
.login input.input {
    border-radius: 6px;
    border: 1px solid #d1d5db;
    padding: 10px 12px;
}
.login #wp-submit {
    background: #2563eb;
    border: none;
    border-radius: 6px;
    width: 100%;
    padding: 10px;
    font-size: 15px;
}
```

---

## 5. Link di Registrazione

Link libero, nessun token. Chiunque lo apra si registra sempre come `g_operatore`.
I ruoli superiori vengono assegnati manualmente dall'admin dopo la registrazione.

```php
// Ruolo assegnato a tutti i nuovi utenti
define('G_RUOLO_DEFAULT', 'g_operatore');

// La funzione restituisce sempre il ruolo base — nessuna logica token
function gestionale_ruolo_default() {
    return G_RUOLO_DEFAULT;
}
```

Nel form basta aggiornare il campo hidden:

```php
<input type="hidden" name="g_ruolo"
       value="<?php echo esc_attr(G_RUOLO_DEFAULT); ?>" />
```

**Link da condividere:**
```
/registrati   →   tutti si registrano come Operatore
```

---

## 6. URL Pulita (Opzionale)

Se `/wp-login.php?action=register` è brutto, si aggiunge un redirect in `functions.php`
del tema (o nel plugin):

```php
add_action('init', function() {
    if ($_SERVER['REQUEST_URI'] === '/registrati') {
        wp_redirect(wp_login_url() . '?action=register&invite=' . ($_GET['invite'] ?? ''));
        exit;
    }
});
```

Così il link che si condivide è `/registrati?invite=abc123`.

---

## 7. Riepilogo — Cosa fa WordPress, cosa fa il plugin

| Funzione | Chi la gestisce |
|---------|----------------|
| Form HTML base (email, username) | WordPress nativo |
| Nonce e protezione CSRF | WordPress nativo |
| Hash della password | WordPress nativo |
| Email "setta la tua password" | WordPress nativo |
| Reset password | WordPress nativo (`/wp-login.php?action=lostpassword`) |
| Campi Nome, Cognome | Plugin (hook `register_form`) |
| Assegnazione ruolo gestionale | Plugin (hook `user_register`) |
| Validazione campi custom | Plugin (filter `registration_errors`) |
| Redirect post-login per ruolo | Plugin (filter `login_redirect`) |
| Stile pagina | Plugin (hook `login_enqueue_scripts`) |
| Generazione link invito | Plugin (funzione admin) |

---

## 8. Cosa NON fare

- ❌ Modificare `wp-login.php` direttamente
- ❌ Copiare `wp-login.php` in un file custom (si desincronizza agli aggiornamenti)
- ❌ Mostrare il dropdown ruolo all'utente senza token firmato
- ❌ Usare servizi esterni (Auth0, Firebase) — WordPress gestisce già tutto
