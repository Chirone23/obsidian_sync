<?php
/**
 * Bibliò — Rentals: tabella wp_biblio_rentals, CRUD, hook WC.
 */
if (!defined('ABSPATH')) exit;

/* ------------------------------------------------------------------ */
/* Migration — crea la tabella se non esiste                           */
/* ------------------------------------------------------------------ */

function biblio_rentals_create_table() {
    global $wpdb;
    $table   = $wpdb->prefix . 'biblio_rentals';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$table} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NOT NULL,
        book_id BIGINT UNSIGNED NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        source_order_id BIGINT UNSIGNED NULL,
        PRIMARY KEY (id),
        UNIQUE KEY user_book (user_id, book_id),
        KEY user_active (user_id, expires_at),
        KEY book_id (book_id)
    ) ENGINE=InnoDB {$charset};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
add_action('after_switch_theme', 'biblio_rentals_create_table');

/* ------------------------------------------------------------------ */
/* CRUD                                                                */
/* ------------------------------------------------------------------ */

/**
 * Verifica se user ha noleggio attivo su book.
 * Restituisce true/false.
 */
function biblio_has_active_rental(int $user_id, int $book_id): bool {
    global $wpdb;
    $table = $wpdb->prefix . 'biblio_rentals';
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $result = $wpdb->get_var($wpdb->prepare(
        "SELECT 1 FROM {$table} WHERE user_id = %d AND book_id = %d AND expires_at > NOW() LIMIT 1",
        $user_id,
        $book_id
    ));
    return $result === '1';
}

/**
 * Inserisce o aggiorna un noleggio.
 * Se esiste già (UNIQUE user+book), aggiorna expires_at e source_order_id.
 * Restituisce true on success, false on failure.
 */
function biblio_insert_rental(int $user_id, int $book_id, int $days = 30, ?int $order_id = null): bool {
    global $wpdb;
    $table      = $wpdb->prefix . 'biblio_rentals';
    $expires_at = gmdate('Y-m-d H:i:s', strtotime("+{$days} days"));

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $result = $wpdb->query($wpdb->prepare(
        "INSERT INTO {$table} (user_id, book_id, expires_at, source_order_id)
         VALUES (%d, %d, %s, %d)
         ON DUPLICATE KEY UPDATE expires_at = VALUES(expires_at), source_order_id = VALUES(source_order_id)",
        $user_id,
        $book_id,
        $expires_at,
        $order_id
    ));
    return $result !== false;
}

/**
 * Elenco noleggi attivi di un utente.
 * Restituisce array di oggetti con book_id, expires_at.
 */
function biblio_get_user_rentals(int $user_id): array {
    global $wpdb;
    $table = $wpdb->prefix . 'biblio_rentals';
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    return $wpdb->get_results($wpdb->prepare(
        "SELECT book_id, expires_at FROM {$table} WHERE user_id = %d AND expires_at > NOW() ORDER BY expires_at ASC",
        $user_id
    )) ?: [];
}

/* ------------------------------------------------------------------ */
/* Hook WooCommerce — inserisce rental quando ordine completato        */
/* ------------------------------------------------------------------ */

add_action('woocommerce_order_status_completed', 'biblio_rental_on_order_completed');

function biblio_rental_on_order_completed(int $order_id): void {
    $order = wc_get_order($order_id);
    if (!$order) return;

    $user_id = (int) $order->get_user_id();
    if (!$user_id) return; // ordine guest: niente rental

    foreach ($order->get_items() as $item) {
        $product_id = (int) $item->get_product_id();
        $rentable   = (int) get_post_meta($product_id, '_biblio_rentable', true);
        if (!$rentable) continue;

        // Determina book_id: il prodotto WC può essere associato a un
        // post-type 'book' tramite meta _biblio_book_id, oppure coincide
        // direttamente con il product_id se il book è il prodotto stesso.
        $book_id = (int) get_post_meta($product_id, '_biblio_book_id', true);
        if (!$book_id) $book_id = $product_id;

        biblio_insert_rental($user_id, $book_id, 30, $order_id);
    }
}
