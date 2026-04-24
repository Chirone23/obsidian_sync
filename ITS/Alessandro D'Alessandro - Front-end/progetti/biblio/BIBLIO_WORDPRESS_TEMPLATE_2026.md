# Biblio — Template WordPress (InfinityFree)

> Template operativo per mettere su il sito Biblio su **InfinityFree** + WordPress + WooCommerce.
> File pronti da caricare, configurazioni realistiche per shared hosting gratuito, workaround documentati.
>
> ⚠️ Questo NON è un template "enterprise". Rispetta i vincoli reali di InfinityFree.

---

## 🚨 Limitazioni InfinityFree — leggere PRIMA di tutto

InfinityFree è hosting **gratuito** con limiti severi. Alcune cose che normalmente daresti per scontate su WordPress **non funzioneranno**.

| Vincolo | Limite InfinityFree | Impatto su Biblio |
|---|---|---|
| **Connessioni esterne (cURL outbound)** | Bloccato verso gran parte dei domini esterni | ❌ **Stripe / WooPayments reali non funzionano** — solo modalità "test" o pagamento manuale / bonifico |
| **Cron job reali** | Non disponibili (solo `wp-cron` via traffico web) | Scadenze noleggi si aggiornano solo se arriva traffico. Serve workaround |
| **max_execution_time** | ~10 secondi | Import Excel grandi possono fallire. Serve splittare in batch |
| **memory_limit** | 256 MB (hard cap) | Page builder pesanti (Elementor Pro) vanno stretti — usa blocchi Gutenberg |
| **SSH / WP-CLI** | Non disponibili | Tutto va fatto da dashboard o via FTP/File Manager |
| **Redis / Memcached** | Non disponibili | Niente object caching — solo page caching file-based |
| **Database** | Max ~400 MB, max 10 connessioni simultanee | Indicizza le custom tables, evita query N+1 |
| **Upload file** | Max 10 MB di default | PDF sopra 10 MB → caricare via FTP, non da WP admin |
| **Daily hits** | ~50.000/giorno | Sufficiente per MVP di test, non per produzione vera |
| **SSL** | Let's Encrypt via pannello interno | Funziona ma setup manuale |
| **Email (mail())** | Spesso bloccata / inaffidabile | Serve SMTP esterno (Brevo, Mailtrap, Gmail SMTP) |
| **File index obbligatorio** | Ogni directory deve avere `index.html` o `index.php` | Pulito — nulla da modificare per WP |

### Conseguenza pratica per Biblio MVP

Per il **progetto ITS di dimostrazione**, InfinityFree va bene se:
- I pagamenti possono essere **mockati** (modalità test Stripe) oppure gestiti in modo manuale (bonifico, contrassegno)
- Non ci sono clienti reali che aspettano ordini
- Il PDF di test è <10 MB, o viene caricato via FTP

Se in futuro il progetto va in produzione reale → **migrare a hosting con cURL libero** (es. SiteGround StartUp ~4€/mese, Keliweb, IONOS).

---

## 📋 Indice

1. [Setup iniziale WordPress su InfinityFree](#1-setup-iniziale)
2. [Configurazione wp-config.php](#2-wp-config)
3. [Plugin stack leggero](#3-plugin-stack)
4. [Child Theme Biblio — file completi](#4-child-theme)
5. [Template pagine principali](#5-template-pagine)
6. [Shortcode per il catalogo](#6-shortcode-catalogo)
7. [Workaround per i limiti di InfinityFree](#7-workaround)
8. [Troubleshooting comune](#8-troubleshooting)

---

## 1. Setup iniziale

### 1.1 Crea account + dominio
1. Registrati su [dash.infinityfree.com](https://dash.infinityfree.com)
2. Crea un nuovo hosting account
3. Usa il sottodominio gratuito `biblio.rf.gd` (o simile) per iniziare — puoi collegare un dominio custom dopo
4. Annota: **FTP host, FTP user, password**, URL del **control panel** del singolo account

### 1.2 Attiva SSL (Let's Encrypt)
1. Control panel → **SSL/TLS Certificates**
2. Seleziona il dominio → **Free Let's Encrypt Certificate**
3. Aspetta ~15 minuti per l'emissione
4. In **Force HTTPS** → abilita

### 1.3 Crea il database
1. Control panel → **MySQL Databases**
2. Crea un nuovo database (es. `epiz_XXXX_biblio`)
3. Annota: **host, nome db, user, password**

### 1.4 Installa WordPress
**Opzione A — Softaculous (consigliata):**
1. Control panel → **Softaculous Apps Installer**
2. Cerca "WordPress" → **Install Now**
3. Compila:
   - Protocol: `https://`
   - Directory: lascia vuoto (= root)
   - Site Name: `Biblio`
   - Admin username: **NON** `admin` (es. `biblio_admin`)
   - Admin password: usa password manager, minimo 20 caratteri
4. Installa

**Opzione B — Manuale via FTP:**
1. Scarica `wordpress-latest.zip` da wordpress.org
2. Carica via FTP su `/htdocs/`
3. Estrai
4. Visita il dominio → setup wizard

### 1.5 Verifica versioni
Dopo il login WP admin, vai in **Strumenti → Stato di salute del sito → Info**:
- WordPress: 6.6+
- PHP: 8.1+ (se InfinityFree offre versioni recenti — verifica in Control Panel → PHP Selector)
- MySQL: 5.7+ o MariaDB 10.3+

---

## 2. wp-config.php

Modifica `wp-config.php` via FTP o File Manager. Configurazione realistica per shared hosting.

```php
<?php
// ==============================================
// BIBLIO — wp-config.php per InfinityFree
// ==============================================

// DATABASE (valori dal pannello InfinityFree MySQL Databases)
define( 'DB_NAME',     'epiz_XXXX_biblio' );
define( 'DB_USER',     'epiz_XXXX' );
define( 'DB_PASSWORD', 'la-tua-password' );
define( 'DB_HOST',     'sqlXXX.infinityfree.com' );
define( 'DB_CHARSET',  'utf8mb4' );
define( 'DB_COLLATE',  '' );

// SALTS — rigenera da https://api.wordpress.org/secret-key/1.1/salt/
define( 'AUTH_KEY',         '...' );
define( 'SECURE_AUTH_KEY',  '...' );
define( 'LOGGED_IN_KEY',    '...' );
define( 'NONCE_KEY',        '...' );
define( 'AUTH_SALT',        '...' );
define( 'SECURE_AUTH_SALT', '...' );
define( 'LOGGED_IN_SALT',   '...' );
define( 'NONCE_SALT',       '...' );

// PREFISSO TABELLE — non usare wp_ di default
$table_prefix = 'bibl_';

// ====== SICUREZZA MINIMA ======
define( 'DISALLOW_FILE_EDIT', true );          // No editing plugin/theme da admin
define( 'WP_POST_REVISIONS',  3 );             // Limita revisioni (DB small)
define( 'EMPTY_TRASH_DAYS',   7 );
define( 'AUTOSAVE_INTERVAL',  120 );           // Auto-save ogni 2 min invece di 60s

// ====== CRON ======
// wp-cron via HTTP è instabile su InfinityFree → disabilitalo qui
// e chiamalo da un cron esterno (vedi sezione Workaround)
define( 'DISABLE_WP_CRON', true );

// ====== MEMORIA ======
define( 'WP_MEMORY_LIMIT',     '128M' );       // Stare sotto il cap InfinityFree
define( 'WP_MAX_MEMORY_LIMIT', '256M' );

// ====== DEBUG (spegnere in prod) ======
define( 'WP_DEBUG',         false );
define( 'WP_DEBUG_LOG',     false );
define( 'WP_DEBUG_DISPLAY', false );

// ====== HTTPS DIETRO PROXY ======
// InfinityFree termina SSL a monte → forza WP a capirlo
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
    $_SERVER['HTTPS'] = 'on';
}
define( 'FORCE_SSL_ADMIN', true );

// ====== BOOTSTRAP ======
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
```

### .htaccess base

```apache
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress

# Blocca accesso a file sensibili
<FilesMatch "^(wp-config\.php|\.htaccess|\.user\.ini)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Blocca esecuzione PHP in uploads (anti-shell)
<Directory "wp-content/uploads">
    <FilesMatch "\.php$">
        Order Deny,Allow
        Deny from all
    </FilesMatch>
</Directory>

# Disabilita XML-RPC (vettore brute force)
<Files xmlrpc.php>
    Order Deny,Allow
    Deny from all
</Files>
```

### .user.ini (limiti PHP)

Crea `.user.ini` in `/htdocs/`:
```ini
upload_max_filesize = 10M
post_max_size = 12M
memory_limit = 256M
max_execution_time = 30
max_input_time = 30
```

> Nota: InfinityFree impone comunque i suoi limiti. Questi valori **non possono superare** il cap del piano.

---

## 3. Plugin stack leggero

Solo plugin che funzionano bene in **shared hosting senza cURL outbound**.

### Essenziali

| Plugin | Perché | Note InfinityFree |
|---|---|---|
| **WooCommerce** | E-commerce core | Disabilita "WooCommerce Analytics" se rallenta (Settings → Advanced → Features) |
| **Astra** (free) | Tema base leggero | Usato come parent del child theme Biblio |
| **WP Super Cache** | Page cache file-based | **Preferibile a WP Rocket** su InfinityFree (gratis, meno overhead) |
| **WPS Hide Login** | Cambia URL di login | Riduce drasticamente brute force |
| **Two-Factor** | 2FA ufficiale WP | Obbligatorio per admin |
| **UpdraftPlus** | Backup | Destinazione: Google Drive (gratuito, funziona da WP → browser → GD, non serve outbound diretto) |

### Utili

| Plugin | Uso |
|---|---|
| **Advanced Custom Fields (ACF)** free | Campi extra per libri (ISBN, autore, etc.) |
| **WP Mail SMTP** | Per mandare email (config con Brevo/Mailtrap) |
| **Classic Editor** | Se preferisci editor classico per form semplici |
| **Query Monitor** | Solo in dev — spegnere in produzione |

### Plugin da EVITARE su InfinityFree

- ❌ **Wordfence** free: scansiona ogni file, consuma CPU → rallenta il sito e rischi sospensione
- ❌ **WP Rocket** (a pagamento + overhead): WP Super Cache è sufficiente
- ❌ **Elementor Pro** pesante: usa blocchi Gutenberg nativi
- ❌ **JetPack** con tutti i moduli attivi: tantissime chiamate outbound (bloccate da IF)
- ❌ **iThemes Security Pro**: troppo aggressivo per shared hosting
- ❌ Plugin che richiedono **webhook da servizi esterni** (Mailchimp sync automatico, etc.)

### Sicurezza leggera (sostituisce Wordfence)

Combinazione:
- **WPS Hide Login** (URL admin non prevedibile)
- **Two-Factor** (2FA TOTP)
- **Limit Login Attempts Reloaded** (blocca brute force)
- `.htaccess` che blocca `xmlrpc.php` (vedi sopra)

---

## 4. Child Theme Biblio — file completi

Struttura da creare in `/htdocs/wp-content/themes/astra-biblio/`:

```
astra-biblio/
├── style.css
├── functions.php
├── screenshot.png       (opzionale, 1200x900)
├── page-catalogo.php
├── single-biblio_book.php
├── page-libreria.php
└── assets/
    ├── css/
    │   └── biblio.css
    └── js/
        └── biblio.js
```

### 4.1 `style.css`

```css
/*
 Theme Name:   Biblio Child
 Template:     astra
 Theme URI:    https://biblio.rf.gd
 Description:  Child theme Astra per Biblio — catalogo noleggio/vendita ebook
 Author:       Alessandro D'Alessandro
 Version:      1.0.0
 Text Domain:  biblio
*/

/* ========================================
   DESIGN TOKENS — "Editorial Contemporary"
   ======================================== */
:root {
  --color-inchiostro:     #1a1a1a;
  --color-avorio:         #faf7f2;
  --color-sabbia:         #e8dfd3;
  --color-terracotta:     #c67b5c;
  --color-grigio-caldo:   #8a8075;
  --color-bianco:         #ffffff;

  --font-display: Georgia, 'Times New Roman', serif;
  --font-body:    -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;

  --radius-card:  4px;
  --shadow-card:  0 2px 8px rgba(26, 26, 26, 0.06);
  --shadow-hover: 0 8px 24px rgba(26, 26, 26, 0.12);
  --transition:   200ms ease;
}

/* Base */
body {
  font-family: var(--font-body);
  color: var(--color-inchiostro);
  background: var(--color-avorio);
  font-size: 16px;
  line-height: 1.65;
}

h1, h2, h3 { font-family: var(--font-display); font-weight: 700; }

a { color: var(--color-terracotta); text-decoration: none; transition: color var(--transition); }
a:hover { color: var(--color-inchiostro); }

/* Header override Astra */
.site-header { background: var(--color-inchiostro) !important; }
.main-header-bar { background: var(--color-inchiostro) !important; }
.main-header-menu a, .site-title a { color: var(--color-avorio) !important; }

/* Hero catalogo */
.biblio-hero {
  background: var(--color-inchiostro);
  color: var(--color-avorio);
  padding: 64px 24px;
  text-align: center;
}
.biblio-hero h1 {
  font-size: clamp(32px, 5vw, 56px);
  font-style: italic;
  margin-bottom: 12px;
  color: var(--color-avorio);
}
.biblio-hero p {
  max-width: 560px;
  margin: 0 auto;
  color: var(--color-sabbia);
  font-size: 17px;
}

/* Griglia catalogo */
.biblio-catalogo {
  max-width: 1200px;
  margin: 48px auto;
  padding: 0 24px;
}
.biblio-filtri {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 32px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--color-sabbia);
}
.biblio-filtro-btn {
  background: transparent;
  border: 1.5px solid var(--color-sabbia);
  color: var(--color-grigio-caldo);
  padding: 8px 18px;
  border-radius: 20px;
  font-size: 13px;
  cursor: pointer;
  transition: all var(--transition);
}
.biblio-filtro-btn:hover,
.biblio-filtro-btn.active {
  background: var(--color-terracotta);
  border-color: var(--color-terracotta);
  color: white;
}

.biblio-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 28px;
}

/* Card libro */
.biblio-card {
  background: var(--color-bianco);
  border-radius: var(--radius-card);
  overflow: hidden;
  box-shadow: var(--shadow-card);
  transition: transform var(--transition), box-shadow var(--transition);
  display: block;
  color: inherit;
}
.biblio-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-hover);
}
.biblio-card-cover {
  width: 100%;
  aspect-ratio: 2 / 3;
  object-fit: cover;
  background: var(--color-sabbia);
  display: block;
}
.biblio-card-body { padding: 14px 16px 18px; }
.biblio-card-title {
  font-family: var(--font-display);
  font-size: 17px;
  font-weight: 700;
  margin: 0 0 4px;
  line-height: 1.3;
}
.biblio-card-author {
  font-size: 13px;
  color: var(--color-grigio-caldo);
  margin: 0 0 10px;
}
.biblio-card-modalita {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}
.biblio-tag {
  font-size: 11px;
  padding: 3px 8px;
  border-radius: 10px;
  background: var(--color-sabbia);
  color: var(--color-inchiostro);
}

/* Pagina singolo libro */
.biblio-single {
  max-width: 1000px;
  margin: 48px auto;
  padding: 0 24px;
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 48px;
}
@media (max-width: 720px) {
  .biblio-single { grid-template-columns: 1fr; }
}
.biblio-single-cover {
  width: 100%;
  aspect-ratio: 2 / 3;
  object-fit: cover;
  border-radius: var(--radius-card);
  box-shadow: var(--shadow-card);
}
.biblio-single h1 {
  font-size: 36px;
  margin-bottom: 8px;
}
.biblio-single-author {
  color: var(--color-grigio-caldo);
  font-size: 17px;
  margin-bottom: 24px;
}
.biblio-modalita-box {
  border: 1px solid var(--color-sabbia);
  border-radius: var(--radius-card);
  padding: 20px;
  margin-bottom: 14px;
  background: var(--color-bianco);
}
.biblio-modalita-box h3 {
  margin: 0 0 8px;
  font-size: 18px;
}
.biblio-modalita-prezzo {
  font-size: 22px;
  font-weight: 700;
  color: var(--color-terracotta);
  margin: 8px 0 14px;
}

/* Libreria utente */
.biblio-libreria {
  max-width: 1100px;
  margin: 48px auto;
  padding: 0 24px;
}
.biblio-libreria-empty {
  text-align: center;
  padding: 64px 24px;
  color: var(--color-grigio-caldo);
}

.biblio-stato {
  display: inline-block;
  font-size: 11px;
  padding: 3px 8px;
  border-radius: 10px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.biblio-stato.attivo   { background: #d4edda; color: #155724; }
.biblio-stato.scaduto  { background: #f8d7da; color: #721c24; }
.biblio-stato.convertito { background: #d1ecf1; color: #0c5460; }

/* Bottoni */
.biblio-btn {
  display: inline-block;
  padding: 10px 20px;
  background: var(--color-terracotta);
  color: white;
  border: none;
  border-radius: var(--radius-card);
  font-size: 14px;
  cursor: pointer;
  transition: background var(--transition);
  text-decoration: none;
}
.biblio-btn:hover { background: var(--color-inchiostro); color: white; }
.biblio-btn-secondary {
  background: transparent;
  color: var(--color-inchiostro);
  border: 1.5px solid var(--color-inchiostro);
}
```

### 4.2 `functions.php`

```php
<?php
/**
 * Biblio Child Theme — functions.php
 * Compatibile con shared hosting InfinityFree (nessuna dipendenza esterna)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ============================================
// 1. ENQUEUE STYLES
// ============================================
add_action( 'wp_enqueue_scripts', function() {
    // Parent Astra
    wp_enqueue_style( 'astra-parent', get_template_directory_uri() . '/style.css' );
    // Child Biblio
    wp_enqueue_style(
        'biblio-child',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'astra-parent' ),
        '1.0.0'
    );
}, 20 );

// ============================================
// 2. REGISTRA CPT biblio_book (supplementa WC)
// ============================================
add_action( 'init', function() {
    register_post_type( 'biblio_book', array(
        'label'          => 'Libri Biblio',
        'public'         => true,
        'has_archive'    => true,
        'show_in_rest'   => true,
        'supports'       => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'menu_icon'      => 'dashicons-book-alt',
        'rewrite'        => array( 'slug' => 'libro' ),
    ) );

    register_taxonomy( 'biblio_genere', 'biblio_book', array(
        'label'        => 'Genere',
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => 'genere' ),
    ) );
} );

// ============================================
// 3. HELPER QUERY SU CUSTOM TABLES
// ============================================

/**
 * Recupera tutte le modalità attive per un book_id.
 */
function biblio_get_modalita( $book_id ) {
    global $wpdb;
    return $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM biblio_modalita WHERE book_id = %s AND attivo = 1",
        $book_id
    ) );
}

/**
 * Recupera accessi attivi di un utente.
 */
function biblio_get_user_accessi( $user_id ) {
    global $wpdb;
    return $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM biblio_accessi_ebook
         WHERE user_id = %d AND stato = 'attivo'
         ORDER BY created_at DESC",
        $user_id
    ) );
}

/**
 * Verifica se l'utente ha accesso attivo a un libro.
 */
function biblio_user_has_access( $user_id, $book_id ) {
    global $wpdb;
    $count = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM biblio_accessi_ebook
         WHERE user_id = %d AND book_id = %s AND stato = 'attivo'
         AND ( data_fine IS NULL OR data_fine > NOW() )",
        $user_id, $book_id
    ) );
    return $count > 0;
}

// ============================================
// 4. HOOK — ORDINE PAGATO → CREA ACCESSO
// ============================================
add_action( 'woocommerce_order_status_completed', function( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    global $wpdb;
    $user_id = $order->get_user_id();
    if ( ! $user_id ) return;  // guest checkout — da gestire separatamente

    foreach ( $order->get_items() as $item ) {
        $product = $item->get_product();
        $woo_product_id = $product->get_id();

        // Cerca modalità collegata a questo prodotto WC
        $modalita = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM biblio_modalita WHERE woo_product_id = %d",
            $woo_product_id
        ) );
        if ( ! $modalita ) continue;

        // Cartaceo → nessun accesso digitale
        if ( $modalita->tipo_modalita === 'cartaceo' ) continue;

        // Calcola scadenza per noleggio
        $data_fine = null;
        $piano_id  = null;
        if ( $modalita->tipo_modalita === 'ebook_noleggio' ) {
            $piano = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM biblio_piani_noleggio
                 WHERE modalita_id = %s AND attivo = 1 LIMIT 1",
                $modalita->modalita_id
            ) );
            if ( $piano ) {
                $data_fine = date( 'Y-m-d H:i:s', strtotime( '+' . $piano->durata_giorni . ' days' ) );
                $piano_id  = $piano->piano_id;
            }
        }

        $wpdb->insert( 'biblio_accessi_ebook', array(
            'user_id'      => $user_id,
            'book_id'      => $modalita->book_id,
            'modalita_id'  => $modalita->modalita_id,
            'piano_id'     => $piano_id,
            'tipo_accesso' => $modalita->tipo_modalita === 'ebook_acquisto' ? 'acquisto' : 'noleggio',
            'data_inizio'  => current_time( 'mysql' ),
            'data_fine'    => $data_fine,
            'stato'        => 'attivo',
            'order_id'     => $order_id,
        ) );
    }
} );

// ============================================
// 5. SCADENZA NOLEGGI — chiamato via wp-cron o endpoint manuale
// ============================================
add_action( 'biblio_cron_scadenze', 'biblio_aggiorna_scadenze' );
function biblio_aggiorna_scadenze() {
    global $wpdb;
    $wpdb->query(
        "UPDATE biblio_accessi_ebook
         SET stato = 'scaduto'
         WHERE stato = 'attivo'
           AND data_fine IS NOT NULL
           AND data_fine < NOW()"
    );
}
if ( ! wp_next_scheduled( 'biblio_cron_scadenze' ) ) {
    wp_schedule_event( time(), 'hourly', 'biblio_cron_scadenze' );
}

// Endpoint manuale (per cron esterno — vedi sezione Workaround)
add_action( 'init', function() {
    if ( isset( $_GET['biblio_cron_key'] ) && $_GET['biblio_cron_key'] === 'METTI_UNA_CHIAVE_SEGRETA_QUI' ) {
        biblio_aggiorna_scadenze();
        wp_die( 'Scadenze aggiornate.', 'OK', array( 'response' => 200 ) );
    }
} );

// ============================================
// 6. VALIDAZIONE CARRELLO — ordine di un solo tipo
// ============================================
add_filter( 'woocommerce_add_to_cart_validation', function( $valid, $product_id ) {
    global $wpdb;

    $nuovo_tipo = $wpdb->get_var( $wpdb->prepare(
        "SELECT tipo_modalita FROM biblio_modalita WHERE woo_product_id = %d LIMIT 1",
        $product_id
    ) );
    if ( ! $nuovo_tipo ) return $valid;

    foreach ( WC()->cart->get_cart() as $cart_item ) {
        $esistente = $wpdb->get_var( $wpdb->prepare(
            "SELECT tipo_modalita FROM biblio_modalita WHERE woo_product_id = %d LIMIT 1",
            $cart_item['product_id']
        ) );
        if ( $esistente && $esistente !== $nuovo_tipo ) {
            wc_add_notice(
                'Il carrello contiene già un prodotto di tipo diverso. Completa l\'ordine o svuota il carrello.',
                'error'
            );
            return false;
        }
    }
    return $valid;
}, 10, 2 );

// ============================================
// 7. PERFORMANCE — rimuovi roba inutile
// ============================================
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );

// Disabilita embed ovunque (riduce JS)
add_action( 'init', function() {
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );
    remove_filter( 'rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
} );
```

---

## 5. Template pagine principali

### 5.1 `page-catalogo.php`

Crea la pagina in WP Admin (Pagine → Aggiungi → slug `catalogo`) e assegna questo template.

```php
<?php
/**
 * Template Name: Biblio — Catalogo
 */
get_header();

global $wpdb;
$libri = $wpdb->get_results(
    "SELECT DISTINCT m.book_id, p.ID as wp_post_id
     FROM biblio_modalita m
     LEFT JOIN {$wpdb->posts} p ON p.post_type = 'biblio_book' AND p.post_status = 'publish'
     WHERE m.attivo = 1"
);
?>

<section class="biblio-hero">
    <h1>Catalogo Biblio</h1>
    <p>Scopri i nostri titoli. Cartaceo, ebook in acquisto o in noleggio temporaneo.</p>
</section>

<section class="biblio-catalogo">
    <div class="biblio-filtri">
        <button class="biblio-filtro-btn active" data-filtro="tutti">Tutti</button>
        <button class="biblio-filtro-btn" data-filtro="cartaceo">Cartaceo</button>
        <button class="biblio-filtro-btn" data-filtro="ebook_acquisto">Ebook acquisto</button>
        <button class="biblio-filtro-btn" data-filtro="ebook_noleggio">Noleggio</button>
    </div>

    <div class="biblio-grid">
        <?php foreach ( $libri as $libro ):
            $post = get_post( $libro->wp_post_id );
            if ( ! $post ) continue;
            $titolo    = get_the_title( $post );
            $autore    = get_post_meta( $post->ID, 'autore', true );
            $copertina = get_the_post_thumbnail_url( $post, 'medium' ) ?: get_stylesheet_directory_uri() . '/assets/placeholder.jpg';
            $modalita  = biblio_get_modalita( $libro->book_id );
            $tipi      = wp_list_pluck( $modalita, 'tipo_modalita' );
        ?>
            <a href="<?php echo get_permalink( $post ); ?>" class="biblio-card" data-tipi="<?php echo esc_attr( implode( ',', $tipi ) ); ?>">
                <img src="<?php echo esc_url( $copertina ); ?>" alt="<?php echo esc_attr( $titolo ); ?>" class="biblio-card-cover" loading="lazy">
                <div class="biblio-card-body">
                    <h3 class="biblio-card-title"><?php echo esc_html( $titolo ); ?></h3>
                    <p class="biblio-card-author"><?php echo esc_html( $autore ); ?></p>
                    <div class="biblio-card-modalita">
                        <?php foreach ( $tipi as $tipo ): ?>
                            <span class="biblio-tag"><?php echo esc_html( str_replace( '_', ' ', $tipo ) ); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<script>
document.querySelectorAll('.biblio-filtro-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.biblio-filtro-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const filtro = btn.dataset.filtro;
        document.querySelectorAll('.biblio-card').forEach(card => {
            const tipi = card.dataset.tipi.split(',');
            card.style.display = (filtro === 'tutti' || tipi.includes(filtro)) ? '' : 'none';
        });
    });
});
</script>

<?php get_footer(); ?>
```

### 5.2 `single-biblio_book.php`

```php
<?php
/**
 * Template singolo libro
 */
get_header();
while ( have_posts() ): the_post();
    $book_id   = get_post_meta( get_the_ID(), 'book_id', true );
    $autore    = get_post_meta( get_the_ID(), 'autore', true );
    $isbn      = get_post_meta( get_the_ID(), 'isbn', true );
    $modalita  = biblio_get_modalita( $book_id );
?>

<article class="biblio-single">
    <div>
        <?php the_post_thumbnail( 'large', array( 'class' => 'biblio-single-cover' ) ); ?>
    </div>
    <div>
        <h1><?php the_title(); ?></h1>
        <p class="biblio-single-author"><?php echo esc_html( $autore ); ?></p>

        <?php if ( $isbn ): ?>
            <p><strong>ISBN:</strong> <?php echo esc_html( $isbn ); ?></p>
        <?php endif; ?>

        <div class="biblio-descrizione"><?php the_content(); ?></div>

        <h2>Scegli la modalità</h2>
        <?php foreach ( $modalita as $m ):
            $label = array(
                'cartaceo'        => 'Cartaceo',
                'ebook_acquisto'  => 'Ebook — Acquisto definitivo',
                'ebook_noleggio'  => 'Ebook — Noleggio',
            )[ $m->tipo_modalita ];
        ?>
            <div class="biblio-modalita-box">
                <h3><?php echo esc_html( $label ); ?></h3>

                <?php if ( $m->tipo_modalita === 'ebook_noleggio' ):
                    global $wpdb;
                    $piani = $wpdb->get_results( $wpdb->prepare(
                        "SELECT * FROM biblio_piani_noleggio WHERE modalita_id = %s AND attivo = 1",
                        $m->modalita_id
                    ) );
                ?>
                    <?php foreach ( $piani as $p ): ?>
                        <p><?php echo esc_html( $p->durata_giorni ); ?> giorni — <strong>€ <?php echo number_format( $p->prezzo, 2, ',', '.' ); ?></strong></p>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="biblio-modalita-prezzo">€ <?php echo number_format( $m->prezzo, 2, ',', '.' ); ?></p>
                <?php endif; ?>

                <?php if ( $m->woo_product_id ): ?>
                    <a href="<?php echo esc_url( add_query_arg( 'add-to-cart', $m->woo_product_id, wc_get_cart_url() ) ); ?>" class="biblio-btn">
                        Aggiungi al carrello
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</article>

<?php endwhile; get_footer(); ?>
```

### 5.3 `page-libreria.php`

Pagina "La mia libreria" (slug `libreria`, richiede login).

```php
<?php
/**
 * Template Name: Biblio — Libreria utente
 */
get_header();

if ( ! is_user_logged_in() ) {
    echo '<div class="biblio-libreria-empty">';
    echo '<p>Devi <a href="' . esc_url( wp_login_url( get_permalink() ) ) . '">accedere</a> per vedere la tua libreria.</p>';
    echo '</div>';
    get_footer();
    return;
}

$user_id = get_current_user_id();
$accessi = biblio_get_user_accessi( $user_id );
?>

<section class="biblio-libreria">
    <h1>La mia libreria</h1>

    <?php if ( empty( $accessi ) ): ?>
        <div class="biblio-libreria-empty">
            <p>Non hai ancora libri nella tua libreria.</p>
            <a href="/catalogo" class="biblio-btn">Sfoglia il catalogo</a>
        </div>
    <?php else: ?>
        <div class="biblio-grid">
        <?php foreach ( $accessi as $acc ):
            // Trova il post collegato
            $posts = get_posts( array(
                'post_type'  => 'biblio_book',
                'meta_key'   => 'book_id',
                'meta_value' => $acc->book_id,
                'numberposts' => 1,
            ) );
            if ( empty( $posts ) ) continue;
            $post = $posts[0];
            $copertina = get_the_post_thumbnail_url( $post->ID, 'medium' );
        ?>
            <div class="biblio-card">
                <img src="<?php echo esc_url( $copertina ); ?>" alt="" class="biblio-card-cover" loading="lazy">
                <div class="biblio-card-body">
                    <h3 class="biblio-card-title"><?php echo esc_html( get_the_title( $post ) ); ?></h3>
                    <span class="biblio-stato <?php echo esc_attr( $acc->stato ); ?>"><?php echo esc_html( $acc->stato ); ?></span>
                    <?php if ( $acc->data_fine ): ?>
                        <p class="biblio-card-author">Scade: <?php echo esc_html( date_i18n( 'd/m/Y', strtotime( $acc->data_fine ) ) ); ?></p>
                    <?php endif; ?>
                    <?php if ( $acc->stato === 'attivo' ): ?>
                        <a href="/reader?accesso=<?php echo (int) $acc->accesso_id; ?>" class="biblio-btn">Leggi</a>
                    <?php elseif ( $acc->stato === 'scaduto' ): ?>
                        <a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="biblio-btn biblio-btn-secondary">Rinnova / Acquista</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php get_footer(); ?>
```

---

## 6. Shortcode catalogo (alternativa al template)

Se preferisci inserire il catalogo in una pagina Gutenberg invece di usare un template custom, aggiungi questo shortcode in `functions.php`:

```php
add_shortcode( 'biblio_catalogo', function() {
    ob_start();
    include get_stylesheet_directory() . '/page-catalogo.php';
    return ob_get_clean();
} );
```

Poi in una pagina qualunque scrivi: `[biblio_catalogo]`

---

## 7. Workaround per i limiti di InfinityFree

### 7.1 wp-cron inaffidabile → cron esterno gratuito

Su InfinityFree `wp-cron.php` gira solo quando arriva traffico. Soluzione:

1. In `wp-config.php`: `define( 'DISABLE_WP_CRON', true );` (già fatto sopra)
2. Registrati su **[cron-job.org](https://cron-job.org)** (gratis)
3. Crea un job che chiami ogni 15 min:
   `https://biblio.rf.gd/wp-cron.php?doing_wp_cron`
4. In aggiunta, per lo scadenzario Biblio specifico (vedi endpoint in `functions.php`):
   `https://biblio.rf.gd/?biblio_cron_key=LA_TUA_CHIAVE_SEGRETA`

### 7.2 Email (mail() inaffidabile) → SMTP esterno

1. Installa **WP Mail SMTP**
2. Usa **Brevo** (ex Sendinblue) — piano gratuito 300 email/giorno, nessun outbound bloccato perché è un servizio che *riceve* le chiamate dal server
3. Configura: Host `smtp-relay.brevo.com`, Port `587`, TLS, user + API key da account Brevo

### 7.3 Pagamenti — Stripe bloccato

Scelte possibili per MVP:

**Opzione A — Stripe in modalità test only:**
- Funziona limitatamente perché InfinityFree blocca cURL verso api.stripe.com
- **Verifica prima** se sul tuo account Stripe riesce a completare pagamenti test (probabilmente no)

**Opzione B — Bonifico bancario / "pagamento manuale" (realistica per MVP ITS):**
- WooCommerce → Pagamenti → Bonifico bancario → Abilita
- L'ordine va in stato "in attesa", admin completa manualmente
- Per la dimostrazione ITS basta — nessuno sta aspettando soldi veri

**Opzione C — PayPal:**
- PayPal ha bridge che a volte bypassa il blocco cURL (non garantito)
- Test con account sandbox

### 7.4 Backup → UpdraftPlus + Google Drive

- UpdraftPlus usa OAuth browser-based per Google Drive
- Non serve cURL outbound diretto (il browser dell'admin fa da ponte)
- Schedula backup settimanale del sito + database

### 7.5 Upload PDF > 10 MB

1. Carica via **File Manager InfinityFree** o FTP in `/htdocs/wp-content/uploads/ebooks/`
2. Registra il path nel DB custom (`biblio_libri.pdf_path`)
3. Servi con endpoint protetto PHP che verifica accesso prima di stream

---

## 8. Troubleshooting comune

| Sintomo | Causa probabile | Soluzione |
|---|---|---|
| "Error establishing database connection" | Host DB sbagliato in wp-config | Ricontrolla in InfinityFree → MySQL Databases |
| 403 Forbidden su /wp-admin | IP flaggato da anti-DDoS IF | Aspetta 10 min o cambia IP / VPN off |
| 500 su pagine specifiche | max_execution_time superato | Splitta operazione (batch), aumenta .user.ini |
| Plugin update fallisce | cURL bloccato verso wordpress.org | Scarica zip manualmente, carica via FTP |
| Email WP non arrivano | mail() bloccata | Configura WP Mail SMTP + Brevo |
| White screen dopo modifica functions.php | Errore PHP fatale | Ripristina via FTP, attiva `WP_DEBUG_LOG` |
| "413 Request Entity Too Large" | File oltre 10 MB | Carica via FTP, non da WP admin |
| Cron Biblio non parte | wp-cron disabilitato e nessun cron esterno configurato | Setup cron-job.org (vedi 7.1) |
| Astra child non si attiva | `Template: astra` scritto male in style.css | Verifica che header CSS sia esatto |

---

## Checklist finale pre-consegna

- [ ] SSL Let's Encrypt attivo + Force HTTPS
- [ ] URL di login cambiato (WPS Hide Login)
- [ ] Admin user **non** è `admin` + 2FA attivo
- [ ] `wp-config.php` con salts rigenerati + `DISALLOW_FILE_EDIT`
- [ ] `.htaccess` blocca xmlrpc, wp-config, esecuzione PHP in uploads
- [ ] WP Super Cache attivo
- [ ] UpdraftPlus schedulato su Google Drive
- [ ] SMTP configurato (Brevo o simile)
- [ ] Cron esterno cron-job.org attivo su `wp-cron.php` + endpoint Biblio
- [ ] Custom tables Biblio create (vedi `BIBLIO_SETUP_GUIDE`)
- [ ] 5 libri di prova caricati come `biblio_book` + prodotti WC collegati
- [ ] Child theme `astra-biblio` attivo
- [ ] Pagine create: Home, Catalogo, Libreria, Carrello, Checkout, Account
- [ ] Menu navigazione configurato
- [ ] WooCommerce: metodo pagamento "Bonifico bancario" attivo
- [ ] Test: acquisto cartaceo → ordine creato
- [ ] Test: acquisto ebook → accesso creato in `biblio_accessi_ebook`
- [ ] Test: noleggio ebook → `data_fine` calcolata
- [ ] Test: login → libreria visibile

---

## Come aggiustare dopo

Questo template è una base. Modifiche frequenti:

**Cambiare colori / stile:**
→ Modifica le variabili CSS in `style.css` (`:root { --color-... }`)

**Aggiungere un campo custom al libro (es. "casa editrice"):**
1. In `functions.php`: aggiungi `register_post_meta()` o usa ACF
2. In `single-biblio_book.php`: stampa con `get_post_meta()`

**Cambiare durate piani noleggio:**
→ `UPDATE biblio_piani_noleggio SET durata_giorni = X WHERE piano_id = '...'`

**Aggiungere filtri al catalogo (es. per prezzo):**
→ Estendi `biblio-filtri` in `page-catalogo.php` + JS

**Aggiungere genere al catalogo:**
→ Usa la tassonomia `biblio_genere` già registrata in `functions.php`

**Migrare a hosting serio (quando serve):**
1. UpdraftPlus: crea backup completo
2. Su nuovo host: installa WP pulito
3. UpdraftPlus: ripristina backup
4. Aggiorna DNS
5. Rimuovi workaround InfinityFree (cron esterno, SMTP se non serve più)

---

*Template v1.1 — allineato a hosting InfinityFree + Astra child + `BIBLIO_SETUP_GUIDE`*
*Per i passi di setup database vedi [[BIBLIO_SETUP_GUIDE]]*
*Per la spec funzionale vedi [[biblio_specs_funzionale_mvp]]*
