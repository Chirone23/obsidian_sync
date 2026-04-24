# 📚 Biblio WordPress Template 2026 — Enterprise Edition

> **Template WordPress + WooCommerce + Digital Distribution** per la piattaforma di noleggio/vendita ebook Biblio.  
> Architettura moderna, sicurezza enterprise, performance ottimizzata per Lighthouse, automazioni avanzate.

---

## 📋 Indice

1. [Stack Tecnologico](#stack-tecnologico)
2. [Hosting & Infrastructure](#hosting--infrastructure)
3. [Configurazione WordPress Core](#configurazione-wordpress-core)
4. [Security Hardening Checklist](#security-hardening-checklist)
5. [WooCommerce Configuration](#woocommerce-configuration)
6. [Plugin Stack Consigliato](#plugin-stack-consigliato)
7. [Database Schema Custom](#database-schema-custom)
8. [Performance & Lighthouse Optimization](#performance--lighthouse-optimization)
9. [Theme Setup (Child Theme Moderno)](#theme-setup-child-theme-moderno)
10. [API Endpoints & Custom Post Types](#api-endpoints--custom-post-types)
11. [Digital Products Workflow](#digital-products-workflow)
12. [Automazioni & Cron Jobs](#automazioni--cron-jobs)
13. [Backup & Disaster Recovery](#backup--disaster-recovery)
14. [Testing & CI/CD](#testing--cicd)

---

## Stack Tecnologico

```
┌─────────────────────────────────────────────────────┐
│           BIBLIO WORDPRESS 2026 STACK              │
├─────────────────────────────────────────────────────┤
│ OS: Ubuntu 22.04 LTS | PHP 8.3+ | MySQL 8.0+      │
│ Web Server: Nginx (reverse proxy + caching)         │
│ Cache Layer: Redis 7.0+ (object + session cache)    │
│ CDN: Cloudflare (images + static assets)            │
│ Database: MySQL 8.0 (custom tables + InnoDB)        │
│ File Storage: S3-compatible (Wasabi / Backblaze)    │
├─────────────────────────────────────────────────────┤
│ WordPress: 6.6+ (Latest)                            │
│ WooCommerce: 9.1+ (Latest)                          │
│ Theme: Blocksy / Neve (block-based, FSE-ready)      │
│ Payment: WooPayments + Stripe               │
└─────────────────────────────────────────────────────┘
```

---

## Hosting & Infrastructure

### Raccomandazioni

| Aspetto | Scelta | Motivo |
|---------|--------|--------|
| **Hosting** | Managed WordPress (Kinsta, WP Engine, Pagely) | PHP 8.3, Redis incluso, backup automatici, CDN |
| **Database** | MySQL 8.0 con Percona (per performance) | Migliore per complex queries, custom tables |
| **Caching** | Redis + Nginx Fastcgi Cache | Object caching + page caching + query caching |
| **CDN** | Cloudflare Pro | DDoS protection, image optimization, automatic WebP |
| **File Storage** | Wasabi S3 + WP Offload Media | Backup offsite, riduce carico server |
| **Monitoring** | Datadog / New Relic | APM, error tracking, performance insights |
| **Backup** | UpdraftPlus + Wasabi | Incremental, crittografato, testato settimanalmente |

### Nginx Configuration (Fastcgi Cache)

```nginx
# /etc/nginx/conf.d/biblio-cache.conf
fastcgi_cache_path /var/run/nginx-cache levels=1:2 keys_zone=BIBLIO:100m 
                   inactive=60m use_temp_path=off;

# WP Cache Headers
add_header X-Cache-Status $upstream_cache_status;
add_header X-Response-Time $request_time;

server {
    # ... server config ...
    
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        
        # Skip cache per admin, cart, checkout
        set $skip_cache 0;
        if ($request_uri ~* "/wp-admin/|/wp-login.php|/cart/|/checkout/|/my-account/") {
            set $skip_cache 1;
        }
        if ($request_method = POST) {
            set $skip_cache 1;
        }
        
        fastcgi_cache BIBLIO;
        fastcgi_cache_valid 200 60m;
        fastcgi_cache_bypass $skip_cache;
        fastcgi_no_cache $skip_cache;
        fastcgi_cache_methods GET HEAD;
    }
}
```

---

## Configurazione WordPress Core

### wp-config.php — Hardening & Optimization

```php
<?php
// Biblio wp-config.php — Security + Performance hardened

// ===== SECURITY =====
// Salts and Keys (generate via https://api.wordpress.org/secret-key/1.1/salt/)
define('AUTH_KEY',         'your-unique-key-here');
define('SECURE_AUTH_KEY',  'your-unique-key-here');
define('LOGGED_IN_KEY',    'your-unique-key-here');
define('NONCE_KEY',        'your-unique-key-here');
define('AUTH_SALT',        'your-unique-key-here');
define('SECURE_AUTH_SALT', 'your-unique-key-here');
define('LOGGED_IN_SALT',   'your-unique-key-here');
define('NONCE_SALT',       'your-unique-key-here');

// Disable file editing
define('DISALLOW_FILE_EDIT', true);

// Disable unfiltered uploads (security risk)
define('DISALLOW_UNFILTERED_UPLOADS', true);

// Force HTTPS
define('FORCE_SSL_ADMIN', true);
if ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
    $_SERVER['HTTPS'] = 'on';
}

// Hide WordPress version
define('WP_AUTO_UPDATE_CORE', 'minor');

// ===== PERFORMANCE =====
// Object Caching (Redis)
define('WP_CACHE', true);
define('WP_CACHE_KEY_SALT', 'biblio_');

// Database optimization
define('EMPTY_TRASH_DAYS', 7);
define('WP_POST_REVISIONS', 3);

// Disable auto-save for performance
define('AUTOSAVE_INTERVAL', false);

// ===== DEBUGGING (disable in production) =====
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', false);

// ===== MEMORY =====
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');

// Database
define('DB_NAME', 'biblio_prod');
define('DB_USER', 'biblio_user');
define('DB_PASSWORD', 'your-secure-password');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');

// Table prefix (non-standard for security)
$table_prefix = 'bibl_';
```

### .htaccess — Security + Caching

```apache
# .htaccess — Biblio Security + Performance

# Disable directory listing
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Prevent PHP execution in uploads
<Directory wp-content/uploads>
    <FilesMatch "\.php$">
        Order Deny,Allow
        Deny from all
    </FilesMatch>
</Directory>

# Disable XML-RPC (DDoS vector)
<Files xmlrpc.php>
    Order Deny,Allow
    Deny from all
</Files>

# Block access to sensitive files
<FilesMatch "(wp-config\.php|wp-content/plugins/.*\.php|\.env)">
    Order allow,deny
    Deny from all
</FilesMatch>

# Browser caching headers
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresDefault "access plus 30 days"
    ExpiresByType text/html "access plus 1 day"
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
</IfModule>

# GZIP compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# WordPress permalink structure
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.php$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]
</IfModule>
```

---

## Security Hardening Checklist

### 🔐 Pre-Launch Security

- [ ] **SSL/TLS Certificate** — Let's Encrypt (auto-renew via certbot)
  ```bash
  certbot certonly --webroot -w /var/www/biblio -d biblio.com -d www.biblio.com --agree-tos -m admin@biblio.com
  ```

- [ ] **Two-Factor Authentication (2FA)**
  - Install: Two-Factor (official WP plugin)
  - Enforce for all admin/shop manager accounts
  - Use authenticator app (Google Authenticator / Authy), not SMS

- [ ] **User Roles & Capabilities**
  ```php
  // In functions.php
  remove_cap('editor', 'unfiltered_html');
  remove_cap('author', 'upload_files');
  // Add custom role 'librarian'
  add_role('librarian', 'Librarian', [
      'read' => true,
      'manage_product' => true,
      'edit_products' => true,
  ]);
  ```

- [ ] **Firewall & Rate Limiting**
  - Install: Wordfence Security (basic) + Cloudflare WAF (advanced)
  - Rules: Block brute force (5 fails = 1h lockout), SQL injection, XSS

- [ ] **File & Directory Permissions**
  ```bash
  # Correct permissions
  find /var/www/biblio -type d -exec chmod 755 {} \;
  find /var/www/biblio -type f -exec chmod 644 {} \;
  chmod 600 /var/www/biblio/wp-config.php
  chmod 755 /var/www/biblio/wp-content/uploads
  ```

- [ ] **SSH Hardening**
  ```bash
  # /etc/ssh/sshd_config
  Port 22xxx # Change default port
  PermitRootLogin no
  PasswordAuthentication no
  PubkeyAuthentication yes
  X11Forwarding no
  MaxAuthTries 3
  LoginGraceTime 20
  ```

- [ ] **Database Security**
  ```sql
  -- Separate user for WP (limited privileges)
  CREATE USER 'biblio_wp'@'localhost' IDENTIFIED BY 'strong_password';
  GRANT SELECT, INSERT, UPDATE, DELETE ON biblio_prod.* TO 'biblio_wp'@'localhost';
  
  -- Backup user (read-only)
  CREATE USER 'biblio_backup'@'localhost' IDENTIFIED BY 'backup_password';
  GRANT SELECT ON biblio_prod.* TO 'biblio_backup'@'localhost';
  ```

- [ ] **Content Security Policy (CSP) Header**
  ```nginx
  add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' fonts.googleapis.com;" always;
  ```

- [ ] **Regular Updates**
  - WordPress Core: Auto-update minors + notifications for majors
  - Plugins: Enable auto-update for security patches
  - Theme: Manual review before updating
  - PHP: Schedule updates quarterly

- [ ] **Monitoring & Logging**
  ```php
  // wp-config.php
  define('WP_DEBUG_LOG', true);
  define('WP_DEBUG_DISPLAY', false);
  define('WP_LOG_DEBUG_ON', __DIR__ . '/wp-content/debug.log');
  ```

---

## WooCommerce Configuration

### Configurazione Core

**WooCommerce → Settings:**

| Sezione | Configurazione | Valore |
|---------|---|---|
| **General** | Store Address | Complete with country |
| | Currency | EUR (o rilevante) |
| | Online Store Language | it_IT |
| **Products** | Manage Product Data Tabs | Disable "Downloadable" se non usato |
| **Inventory** | Stock Management | Enable per tutti i prodotti |
| | Low Stock Threshold | 5 |
| | Out of Stock Status | Hidden from catalog |
| **Shipping** | Shipping Zones | Setup: Domestic + International |
| | Shipping Methods | Free over €50, Standard €5 |
| **Payments** | Primary Gateway | WooPayments (built-in) |
| | Secondary Gateway | Stripe (fallback) |
| **Checkout** | Guest Checkout | Enabled |
| | Account Creation | Optional |
| | Checkout Pages | Use block-based checkout |
| **Account** | My Account Pages | Use block-based templates |

### Custom Product Types — Biblio

```php
// In functions.php (or custom plugin)

add_filter('woocommerce_product_class_name', function($classname, $product_type) {
    if ($product_type == 'biblio_ebook') {
        return 'WC_Product_Biblio_Ebook';
    }
    return $classname;
}, 10, 2);

class WC_Product_Biblio_Ebook extends WC_Product_Download {
    public $product_type = 'biblio_ebook';
    
    public function get_purchase_note() {
        return sprintf(
            __('Accedi alla tua libreria digitale: %s', 'biblio'),
            home_url('/my-library/')
        );
    }
    
    public function get_file_download_path($download_id) {
        // Streaming via custom endpoint (no direct access)
        return home_url('/api/v1/download/' . $download_id);
    }
}
```

---

## Plugin Stack Consigliato

### 🔴 Essenziali (Must-Have)

| Plugin | Versione | Uso | Alternativa |
|--------|----------|-----|-----------|
| **WooCommerce** | 9.1+ | E-commerce core | Magento (overkill) |
| **WP Rocket** | Latest | Caching + Performance | W3 Total Cache |
| **Wordfence** | Latest | Security scanning | Sucuri |
| **Yoast SEO** | Latest | SEO optimization | Rank Math |
| **Akismet** | Latest | Comment spam | Antispam Bee |

### 🟠 Recommended (Highly Useful)

| Plugin | Uso |
|--------|-----|
| **Elementor** (free) | Page builder (fallback if blocks aren't enough) |
| **Advanced Custom Fields (ACF)** | Custom fields for books, authors |
| **WooCommerce Subscriptions** | Subscription management (if offering memberships) |
| **Mailchimp for WooCommerce** | Email marketing automation |
| **MonsterInsights** | Google Analytics integration |
| **Backup & Migration** (All-in-One WP Migration) | Easy migrations + backups |

### 🟡 Performance & Optimization

| Plugin | Uso |
|--------|-----|
| **ShortPixel** | Image compression + WebP conversion |
| **Imagify** | Automated image optimization |
| **UpdraftPlus** | Incremental backups (+ cloud storage) |
| **Query Monitor** | Debug queries + performance profiling |
| **Asset Cleanup** | Disable unnecessary scripts/styles per page |

### 🟢 Custom / Niche

| Plugin | Uso |
|--------|-----|
| **BiblioDB Custom** (custom plugin) | Integration con custom tables |
| **REST API custom endpoints** | API per mobile app futura |
| **n8n integration** | Automazioni workflow |

### Plugins da EVITARE

- ❌ Too many security plugins (Wordfence alone is enough)
- ❌ Page builders con caching disabled (Elementor Pro in some cases)
- ❌ Multiple SEO plugins (Yoast OR Rank Math, non entrambi)
- ❌ Unnecessary "optimization" plugins (slow down WP)

---

## Database Schema Custom

### Tabelle Essenziali per Biblio

```sql
-- ===== BIBLIO CUSTOM TABLES =====

-- 1. Libri metadata (supplementare ai WP posts)
CREATE TABLE IF NOT EXISTS biblio_libri (
  book_id         VARCHAR(50)   PRIMARY KEY,
  wp_post_id      BIGINT        UNIQUE,
  isbn            VARCHAR(20)   UNIQUE,
  autore_cognome  VARCHAR(100),
  autore_nome     VARCHAR(100),
  editore         VARCHAR(100),
  anno_pubblicazione INT,
  numero_pagine   INT,
  lingua          VARCHAR(10)   DEFAULT 'it',
  genre_tag       VARCHAR(255),
  synopsis        LONGTEXT,
  pdf_hash        VARCHAR(64),  -- SHA-256 del PDF
  pdf_presente    TINYINT(1)    DEFAULT 0,
  ebook_presente  TINYINT(1)    DEFAULT 0,
  created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP     ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_isbn (isbn),
  INDEX idx_post_id (wp_post_id),
  INDEX idx_autore (autore_cognome, autore_nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Modalità disponibili (Cartaceo, eBook Acquisto, eBook Noleggio)
CREATE TABLE IF NOT EXISTS biblio_modalita (
  modalita_id     VARCHAR(50)   PRIMARY KEY,
  book_id         VARCHAR(50)   NOT NULL,
  tipo_modalita   ENUM('cartaceo', 'ebook_acquisto', 'ebook_noleggio'),
  prezzo          DECIMAL(10,2),
  woo_product_id  BIGINT,       -- Link a WooCommerce product
  attivo          TINYINT(1)    DEFAULT 1,
  created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (book_id) REFERENCES biblio_libri(book_id),
  INDEX idx_book (book_id),
  INDEX idx_woo (woo_product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Piani di noleggio (solo per ebook_noleggio)
CREATE TABLE IF NOT EXISTS biblio_piani_noleggio (
  piano_id        VARCHAR(50)   PRIMARY KEY,
  modalita_id     VARCHAR(50)   NOT NULL,
  durata_giorni   INT,
  prezzo          DECIMAL(10,2),
  attivo          TINYINT(1)    DEFAULT 1,
  FOREIGN KEY (modalita_id) REFERENCES biblio_modalita(modalita_id),
  INDEX idx_modalita (modalita_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Accessi eBook per utente (noleggio + acquisto)
CREATE TABLE IF NOT EXISTS biblio_accessi_ebook (
  accesso_id      BIGINT        AUTO_INCREMENT PRIMARY KEY,
  user_id         BIGINT        NOT NULL,
  book_id         VARCHAR(50)   NOT NULL,
  tipo_accesso    ENUM('acquisto', 'noleggio'),
  data_inizio     DATETIME,
  data_scadenza   DATETIME,     -- NULL se acquisto perpetuo
  stato           ENUM('attivo', 'scaduto', 'revocato') DEFAULT 'attivo',
  download_count  INT           DEFAULT 0,
  last_accessed   DATETIME,
  created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES wp_users(ID),
  FOREIGN KEY (book_id) REFERENCES biblio_libri(book_id),
  INDEX idx_user_book (user_id, book_id),
  INDEX idx_scadenza (data_scadenza),
  INDEX idx_stato (stato)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Conversioni noleggio → acquisto
CREATE TABLE IF NOT EXISTS biblio_conversioni (
  conversione_id  BIGINT        AUTO_INCREMENT PRIMARY KEY,
  user_id         BIGINT        NOT NULL,
  book_id         VARCHAR(50)   NOT NULL,
  accesso_noleggio_id BIGINT,
  prezzo_upgrade  DECIMAL(10,2),
  data_conversione DATETIME,
  woo_order_id    BIGINT,
  created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES wp_users(ID),
  FOREIGN KEY (book_id) REFERENCES biblio_libri(book_id),
  FOREIGN KEY (accesso_noleggio_id) REFERENCES biblio_accessi_ebook(accesso_id),
  INDEX idx_user_book (user_id, book_id),
  INDEX idx_order (woo_order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Log dei download (compliance + analytics)
CREATE TABLE IF NOT EXISTS biblio_download_log (
  log_id          BIGINT        AUTO_INCREMENT PRIMARY KEY,
  user_id         BIGINT        NOT NULL,
  book_id         VARCHAR(50)   NOT NULL,
  accesso_id      BIGINT,
  ip_address      VARCHAR(45),
  user_agent      TEXT,
  file_size       BIGINT,
  download_time_s INT,
  http_status     INT,
  created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES wp_users(ID),
  FOREIGN KEY (accesso_id) REFERENCES biblio_accessi_ebook(accesso_id),
  INDEX idx_user (user_id),
  INDEX idx_timestamp (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## Performance & Lighthouse Optimization

### Obiettivi Lighthouse

```
┌─────────────────────────────────┐
│   TARGET SCORES (Desktop)       │
├─────────────────────────────────┤
│ Performance:      90+  (LCP<2.5s)│
│ Accessibility:    95+            │
│ Best Practices:   95+            │
│ SEO:              95+            │
│ PWA (opzionale):  80+            │
└─────────────────────────────────┘
```

### Ottimizzazioni Core

#### 1. **LCP (Largest Contentful Paint)** — Target: <2.5s

```php
// In functions.php
// Prioritize hero image
add_filter('wp_calculate_image_srcset_meta', function($srcset_data) {
    if (is_front_page()) {
        add_filter('wp_lazyload_enabled', '__return_false');
    }
    return $srcset_data;
});

// Preload critical resources
add_action('wp_head', function() {
    echo '<link rel="preload" as="image" href="' . get_template_directory_uri() . '/images/hero-bg.webp">';
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">';
});

// Inline critical CSS
add_action('wp_head', function() {
    $critical_css = file_get_contents(get_template_directory() . '/css/critical.css');
    echo '<style>' . wp_strip_all_tags($critical_css) . '</style>';
}, 1);
```

#### 2. **Image Optimization**

```php
// WP Rocket settings
define('WP_ROCKET_SLUG', 'wp-rocket');

// In wp-config.php
// Force WebP via ShortPixel
add_filter('image_editor_default_mime_type', function() {
    return 'image/webp';
});

// Responsive images (srcset)
add_filter('wp_calculate_image_srcset', function($sources) {
    // Force modern formats
    return array_filter($sources, function($src) {
        return strpos($src, '.webp') !== false || strpos($src, '.jpg') !== false;
    });
});
```

#### 3. **Code Splitting & Lazy Loading**

```php
// Defer non-critical JavaScript
add_filter('script_loader_tag', function($tag, $handle) {
    if (in_array($handle, ['stripe-js', 'google-analytics', 'secondary-script'])) {
        return str_replace('src=', 'defer src=', $tag);
    }
    return $tag;
}, 10, 2);

// Lazy load iframes
add_filter('wp_iframe_tag_post_default_html', function($html) {
    return str_replace('<iframe', '<iframe loading="lazy"', $html);
});
```

#### 4. **CLS (Cumulative Layout Shift)** — Target: <0.1

```css
/* In child theme style.css */

/* Prevent layout shift for images */
img {
    max-width: 100%;
    height: auto;
    aspect-ratio: attr(width) / attr(height);
}

/* Add size containers for dynamic content */
.biblio-card {
    aspect-ratio: 1/1.5;
    overflow: hidden;
}

/* Font loading strategy */
@font-face {
    font-family: 'Merriweather';
    font-display: swap; /* Show fallback immediately */
    src: url(...);
}
```

#### 5. **Caching Strategy (multi-layer)**

```
┌──────────────────────────────────────┐
│   CACHING LAYERS (Biblio)            │
├──────────────────────────────────────┤
│ 1. Browser Cache (1 year for assets) │
│ 2. Cloudflare Edge Cache (30m)       │
│ 3. Redis Object Cache (1h)           │
│ 4. Nginx Fastcgi Cache (60m)         │
│ 5. WordPress Query Cache (via Redis) │
└──────────────────────────────────────┘
```

**WP Rocket Configuration:**
- Cache lifespan: 10 hours
- Mobile cache: Enabled (separate)
- Minify CSS/JS: Enabled
- Remove unused CSS: Enabled (safe mode)
- Lazy load: Images + iframes + videos

---

## Theme Setup (Child Theme Moderno)

### Struttura di base

```
/wp-content/themes/biblio-child/
├── style.css               # Theme metadata + base styles
├── functions.php           # Hooks, filters, CPT registration
├── template-parts/
│   ├── header/
│   │   ├── header.html
│   │   └── nav.html
│   ├── footer/
│   │   └── footer.html
│   ├── content/
│   │   ├── single-product.html
│   │   ├── archive-product.html
│   │   └── search.html
│   └── library/
│       ├── my-library.html
│       └── book-item.html
├── patterns/               # Reusable blocks (FSE)
│   ├── hero.php
│   ├── cta-section.php
│   └── testimonials.php
├── css/
│   ├── critical.css        # Inlined in <head>
│   ├── main.css
│   ├── responsive.css
│   └── print.css
├── js/
│   ├── main.js
│   ├── library.js          # My Library functionality
│   └── checkout.js
└── readme.txt
```

### functions.php — Setup Completo

```php
<?php
/**
 * Biblio Child Theme — Functions
 * 
 * @package biblio
 */

// Enqueue parent theme
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
});

// ===== CUSTOM POST TYPES =====

// CPT: Book (supplementa WC Product)
register_post_type('biblio_book', [
    'labels' => ['name' => 'Books', 'singular_name' => 'Book'],
    'public' => true,
    'show_in_rest' => true,
    'has_archive' => true,
    'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
]);

// ===== CUSTOM TAXONOMIES =====

register_taxonomy('biblio_genre', 'biblio_book', [
    'labels' => ['name' => 'Genere'],
    'show_in_rest' => true,
    'hierarchical' => true,
]);

// ===== ADMIN ENHANCEMENTS =====

// Hide non-essential post types
add_filter('register_post_type_args', function($args, $post_type) {
    if (in_array($post_type, ['attachment', 'revision'])) {
        $args['show_in_menu'] = false;
    }
    return $args;
}, 10, 2);

// Custom admin columns for books
add_filter('manage_biblio_book_posts_columns', function($columns) {
    unset($columns['date']);
    $columns['isbn'] = 'ISBN';
    $columns['author'] = 'Autore';
    $columns['date'] = 'Data';
    return $columns;
});

// ===== PERFORMANCE =====

// Disable emojis (reduce HTTP requests)
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Disable dashicons for non-admin
if (!is_admin()) {
    wp_deregister_style('dashicons');
}

// Remove unnecessary meta tags
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');

// ===== SECURITY =====

// Remove REST API endpoints for non-authenticated users
add_filter('rest_authentication_errors', function($result) {
    if (!is_user_logged_in() && !in_array($_SERVER['REQUEST_METHOD'], ['GET'])) {
        return new WP_Error('rest_forbidden', 'API write requires authentication', ['status' => 403]);
    }
    return $result;
});

// ===== CUSTOM FILTERS & ACTIONS =====

// Hook: When order is completed, grant ebook access
add_action('woocommerce_payment_complete', function($order_id) {
    $order = wc_get_order($order_id);
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        $book_id = get_post_meta($product->get_id(), '_biblio_book_id', true);
        if ($book_id) {
            grant_ebook_access($order->get_customer_id(), $book_id, 'acquisto');
        }
    }
});

// Cron: Clean expired ebook access
add_action('wp_scheduled_event_biblio_cleanup', function() {
    global $wpdb;
    $wpdb->query("UPDATE {$wpdb->prefix}biblio_accessi_ebook 
                  SET stato='scaduto' 
                  WHERE data_scadenza < NOW() AND stato='attivo'");
});

if (!wp_next_scheduled('wp_scheduled_event_biblio_cleanup')) {
    wp_schedule_event(time(), 'hourly', 'wp_scheduled_event_biblio_cleanup');
}
```

---

## API Endpoints & Custom Post Types

### REST API Endpoints (v1)

```php
// In functions.php
add_action('rest_api_init', function() {
    // GET /wp-json/biblio/v1/books
    register_rest_route('biblio/v1', '/books', [
        'methods' => 'GET',
        'callback' => 'biblio_get_books',
        'permission_callback' => '__return_true',
    ]);
    
    // GET /wp-json/biblio/v1/user/library
    register_rest_route('biblio/v1', '/user/library', [
        'methods' => 'GET',
        'callback' => 'biblio_get_user_library',
        'permission_callback' => 'is_user_logged_in',
    ]);
    
    // POST /wp-json/biblio/v1/download/(?P<accesso_id>\d+)
    register_rest_route('biblio/v1', '/download/(?P<accesso_id>\d+)', [
        'methods' => 'POST',
        'callback' => 'biblio_download_ebook',
        'permission_callback' => 'is_user_logged_in',
        'args' => ['accesso_id' => ['validate_callback' => 'is_numeric']],
    ]);
});

function biblio_get_books($request) {
    global $wpdb;
    $genre = $request->get_param('genre');
    $limit = min($request->get_param('per_page') ?? 20, 100);
    $offset = $request->get_param('offset') ?? 0;
    
    $query = "SELECT b.*, m.modalita_id, m.prezzo 
              FROM {$wpdb->prefix}biblio_libri b
              LEFT JOIN {$wpdb->prefix}biblio_modalita m ON b.book_id = m.book_id
              WHERE b.ebook_presente = 1";
    
    if ($genre) {
        $query .= $wpdb->prepare(" AND b.genre_tag LIKE %s", '%' . $genre . '%');
    }
    
    $query .= " LIMIT $limit OFFSET $offset";
    $results = $wpdb->get_results($query);
    
    return new WP_REST_Response($results, 200);
}

function biblio_get_user_library($request) {
    $user_id = get_current_user_id();
    global $wpdb;
    
    $accessi = $wpdb->get_results($wpdb->prepare(
        "SELECT a.*, b.*, m.modalita_id 
         FROM {$wpdb->prefix}biblio_accessi_ebook a
         JOIN {$wpdb->prefix}biblio_libri b ON a.book_id = b.book_id
         LEFT JOIN {$wpdb->prefix}biblio_modalita m ON b.book_id = m.book_id
         WHERE a.user_id = %d AND a.stato = 'attivo'
         ORDER BY a.last_accessed DESC",
        $user_id
    ));
    
    return new WP_REST_Response($accessi, 200);
}

function biblio_download_ebook($request) {
    $accesso_id = $request['accesso_id'];
    $user_id = get_current_user_id();
    global $wpdb;
    
    // Verify ownership
    $accesso = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}biblio_accessi_ebook WHERE accesso_id = %d",
        $accesso_id
    ));
    
    if (!$accesso || $accesso->user_id != $user_id) {
        return new WP_Error('forbidden', 'Access denied', ['status' => 403]);
    }
    
    if ($accesso->stato !== 'attivo') {
        return new WP_Error('expired', 'Ebook access expired', ['status' => 410]);
    }
    
    // Log download
    $wpdb->insert("{$wpdb->prefix}biblio_download_log", [
        'user_id' => $user_id,
        'book_id' => $accesso->book_id,
        'accesso_id' => $accesso_id,
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'http_status' => 200,
    ]);
    
    // Get file from S3 (via WP Offload Media)
    // ... implement streaming
    
    return new WP_REST_Response(['status' => 'downloading'], 200);
}
```

---

## Digital Products Workflow

### 1. **Creazione Prodotto WooCommerce**

```php
// Script per crear un prodotto ebook in WC
$product = new WC_Product_Download();
$product->set_name('L\'Algoritmo del Silenzio — eBook Acquisto');
$product->set_price(9.99);
$product->set_downloadable(true);
$product->set_status('publish');

// File di download
$download = [
    'name' => 'L_Algoritmo_Silenzio.pdf',
    'file' => 's3://biblio-ebooks/L_Algoritmo_Silenzio.pdf',
];
$product->add_file('_download_1', $download);
$product->set_download_limit(null); // Unlimited downloads
$product->set_download_expiry(365); // 1 year access

$product_id = $product->save();

// Link a custom table
add_post_meta($product_id, '_biblio_book_id', 'BLIB-001');
add_post_meta($product_id, '_biblio_modalita_id', 'MOD-001-EA');
```

### 2. **Gestione dei Download**

```php
// Custom download handler (non direct access)
// Rerouta a: GET /wp-json/biblio/v1/download/{accesso_id}

add_filter('woocommerce_download_file_force_download', function($file) {
    // Stream da S3 via presigned URL
    $s3_client = ...;
    $cmd = $s3_client->getCommand('GetObject', [
        'Bucket' => 'biblio-ebooks',
        'Key' => basename($file),
    ]);
    $request = $s3_client->createPresignedRequest($cmd, '+20 minutes');
    return (string)$request->getUri();
});
```

### 3. **Conversione Noleggio → Acquisto**

```php
add_action('woocommerce_thankyou', function($order_id) {
    $order = wc_get_order($order_id);
    
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        $book_id = get_post_meta($product->get_id(), '_biblio_book_id', true);
        
        // Check if noleggio before
        $existing = get_user_meta(
            $order->get_customer_id(),
            "_biblio_noleggio_{$book_id}",
            true
        );
        
        if ($existing) {
            // Calculate upgrade cost
            global $wpdb;
            $wpdb->insert("{$wpdb->prefix}biblio_conversioni", [
                'user_id' => $order->get_customer_id(),
                'book_id' => $book_id,
                'woo_order_id' => $order_id,
                'data_conversione' => current_time('mysql'),
            ]);
            
            // Grant perpetual access
            grant_ebook_access($order->get_customer_id(), $book_id, 'acquisto', null);
        }
    }
});
```

---

## Automazioni & Cron Jobs

### n8n Workflow Integration

```yaml
# Biblio Automations via n8n

workflows:
  - id: sync-inventory
    trigger: daily-6am
    steps:
      1. Fetch WooCommerce inventory
      2. Update biblio_libri stock counts
      3. Alert if low stock (<5)
  
  - id: cleanup-expired-access
    trigger: hourly
    steps:
      1. Query biblio_accessi_ebook WHERE data_scadenza < NOW()
      2. Update stato='scaduto'
      3. Send email notification to user
  
  - id: generate-monthly-report
    trigger: 1st of month at 8am
    steps:
      1. Count new users
      2. Count downloads
      3. Revenue summary
      4. Email to admin
```

### WordPress Cron Jobs

```php
// In functions.php

// 1. Cleanup expired rentals (hourly)
add_action('wp_scheduled_event_biblio_expire_rentals', function() {
    global $wpdb;
    $wpdb->query("UPDATE {$wpdb->prefix}biblio_accessi_ebook 
                  SET stato='scaduto' 
                  WHERE data_scadenza < NOW() AND tipo_accesso='noleggio'");
});
if (!wp_next_scheduled('wp_scheduled_event_biblio_expire_rentals')) {
    wp_schedule_event(time(), 'hourly', 'wp_scheduled_event_biblio_expire_rentals');
}

// 2. Generate daily sales report (daily at 7am)
add_action('wp_scheduled_event_biblio_daily_report', function() {
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $orders = wc_get_orders([
        'after' => $yesterday . ' 00:00:00',
        'before' => $yesterday . ' 23:59:59',
        'status' => 'completed',
    ]);
    
    $report = [
        'date' => $yesterday,
        'total_orders' => count($orders),
        'revenue' => array_sum(array_map(fn($o) => $o->get_total(), $orders)),
        'unique_books' => count_unique_books($orders),
    ];
    
    wp_mail('admin@biblio.com', "Daily Report - $yesterday", json_encode($report, JSON_PRETTY_PRINT));
});
if (!wp_next_scheduled('wp_scheduled_event_biblio_daily_report')) {
    wp_schedule_event(strtotime('07:00:00'), 'daily', 'wp_scheduled_event_biblio_daily_report');
}

// 3. Database optimization (weekly)
add_action('wp_scheduled_event_biblio_db_optimize', function() {
    global $wpdb;
    $tables = ['biblio_libri', 'biblio_modalita', 'biblio_accessi_ebook', 'biblio_download_log'];
    foreach ($tables as $table) {
        $wpdb->query("OPTIMIZE TABLE {$wpdb->prefix}{$table}");
    }
});
if (!wp_next_scheduled('wp_scheduled_event_biblio_db_optimize')) {
    wp_schedule_event(time(), 'weekly', 'wp_scheduled_event_biblio_db_optimize');
}
```

---

## Backup & Disaster Recovery

### Backup Strategy

```
┌─────────────────────────────────────┐
│   BIBLIO BACKUP ARCHITECTURE        │
├─────────────────────────────────────┤
│ Daily Incremental    → Wasabi S3    │
│ Weekly Full          → Backblaze B2 │
│ Monthly Archive      → Google Cloud │
│ Retention: 90 days (rolling)        │
└─────────────────────────────────────┘
```

### UpdraftPlus Configuration

```php
// WP Admin → Settings → UpdraftPlus

// Backup schedule
- Full backup: Daily at 2 AM (off-peak)
- Database: Every 6 hours
- Incremental: Every 2 hours

// Storage
- Primary: Wasabi S3 (redundant, 10x cheaper than AWS)
  - Bucket: biblio-backups
  - Prefix: wordpress/
  - Lifecycle: 90-day retention
  
- Secondary: Backblaze B2 (cold storage)
  - Bucket: biblio-archive
  - Lifecycle: 1-year retention

// Test restore monthly (simulate disaster)
```

### Disaster Recovery Runbook

```markdown
# DR Runbook — Biblio WordPress

## Scenario 1: Data Loss (Database corruption)
1. Stop WP (put in maintenance mode)
2. Restore latest backup from Wasabi
3. Verify biblio_* custom tables integrity
4. Run database optimization
5. Test critical flows (purchase, download)

## Scenario 2: Security Breach (malware)
1. Take site offline
2. Restore from Wasabi backup (before breach date)
3. Audit database for unauthorized users
4. Update all passwords + keys
5. Re-scan with Wordfence
6. Deploy code fixes (if patch available)

## Scenario 3: Server Failure
1. Provision new server (same specs)
2. Deploy OS + PHP 8.3 + MySQL 8.0
3. Restore WP files from S3
4. Restore database from UpdraftPlus
5. Update DNS to new IP
6. Verify SSL certificate
7. Run smoke tests

## Recovery Time Objective (RTO): 4 hours
## Recovery Point Objective (RPO): 1 hour
```

---

## Testing & CI/CD

### Automated Testing

```yaml
# .github/workflows/biblio-tests.yml

name: Biblio CI/CD

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_DATABASE: biblio_test
          MYSQL_ROOT_PASSWORD: test
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP 8.3
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: mysqli, imagick, zip
      
      - name: Install dependencies
        run: |
          composer install
          npm install
      
      - name: PHP Lint
        run: find . -name '*.php' -exec php -l {} \;
      
      - name: PHPCS (Code style)
        run: phpcs --standard=PSR12 wp-content/themes/biblio-child/
      
      - name: PHPUnit
        run: phpunit --testdox
      
      - name: JS Tests
        run: npm test
      
      - name: Lighthouse CI
        uses: treosh/lighthouse-ci-action@v9
        with:
          configPath: './lighthouserc.json'
      
      - name: Performance Report
        run: |
          echo "Performance audit completed"
          # Upload to artifact
```

### Lighthouse Config

```json
{
  "ci": {
    "collect": {
      "numberOfRuns": 3,
      "url": ["https://biblio.com/", "https://biblio.com/shop/"],
      "settings": {
        "chromeFlags": ["--no-sandbox"],
        "onlyCategories": ["performance", "accessibility", "best-practices", "seo"]
      }
    },
    "upload": {
      "target": "temporary-public-storage"
    },
    "assert": {
      "preset": "lighthouse:recommended",
      "assertions": {
        "categories:performance": ["error", {"minScore": 0.9}],
        "categories:accessibility": ["error", {"minScore": 0.95}],
        "categories:best-practices": ["error", {"minScore": 0.95}],
        "categories:seo": ["error", {"minScore": 0.95}]
      }
    }
  }
}
```

---

## Checklist Pre-Launch

- [ ] SSL/TLS configured (HTTPS everywhere)
- [ ] 2FA enabled for all admin accounts
- [ ] Database users with limited privileges
- [ ] File permissions hardened (644 files, 755 dirs)
- [ ] wp-config.php secured (600 permissions)
- [ ] WP_DEBUG = false in production
- [ ] Backups tested (restore from backup once)
- [ ] Lighthouse scores: all >90
- [ ] Load testing passed (100+ concurrent users)
- [ ] Security scanning clean (Wordfence + manual)
- [ ] SEO verified (sitemap, robots.txt, structured data)
- [ ] Analytics configured (Google Analytics + Hotjar)
- [ ] Email configuration tested (transactional + marketing)
- [ ] Payment gateway tested (test + live)
- [ ] Mobile responsiveness verified
- [ ] Custom API endpoints tested
- [ ] Cron jobs running (verify in logs)
- [ ] CDN / Cache purge working
- [ ] Monitoring & alerting active (Datadog)

---

## Summary

Questo template WordPress **2026-ready** per Biblio offre:

✅ **Architettura moderna** — Block themes, FSE, custom post types  
✅ **Security enterprise** — 2FA, hardening completo, compliance-ready  
✅ **Performance ottimizzata** — Lighthouse 90+, caching multi-layer  
✅ **Scalabilità** — Custom database schema, Redis, CDN  
✅ **Automazioni** — n8n + WP Cron jobs, compliance workflows  
✅ **Disaster recovery** — 3-tier backup strategy, RTO 4h  
✅ **Digital products** — Ebook rental + acquisto, access control  

**Next Steps:**
1. Adatta i nomi/URL al tuo dominio
2. Configura S3 (Wasabi) + Cloudflare
3. Installa plugin stack (WP Rocket, Wordfence, ACF)
4. Copia schema database
5. Test su staging environment
6. Esegui Lighthouse audit
7. Deploy a produzione

---

*Template versione: 2.0 (2026-04-24)*  
*Mantenuto in: [[Biblio WordPress MOC]]*  
*Backlinks: [[skill/WordPress]], [[skill/WooCommerce]], [[skill/Security]]*
