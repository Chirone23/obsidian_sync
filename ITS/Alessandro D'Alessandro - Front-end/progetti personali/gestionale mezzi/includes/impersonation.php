<?php

/**
 * Sistema "Visualizza come ruolo" per admin
 * Permette all'admin di testare il gestionale come se fosse volontario/direttivo
 */

// Avvia sessione se non già attiva
add_action( 'init', 'gm_start_session' );

function gm_start_session() {
    if ( ! session_id() && ! headers_sent() ) {
        session_start();
    }
}

/**
 * Verifica se l'utente corrente è admin
 */
function gm_is_admin_user() {
    $user = wp_get_current_user();
    return in_array( 'administrator', (array) $user->roles, true );
}

/**
 * Imposta il ruolo impersonato (solo per admin)
 */
function gm_set_impersonated_role( $role ) {
    if ( ! gm_is_admin_user() ) {
        return false;
    }

    $allowed_roles = [ 'gm_amministrazione', 'gm_direttivo', 'gm_volontario', '' ];

    if ( in_array( $role, $allowed_roles, true ) ) {
        $_SESSION['gm_impersonated_role'] = $role;
        return true;
    }

    return false;
}

/**
 * Ottiene il ruolo impersonato
 */
function gm_get_impersonated_role() {
    return isset( $_SESSION['gm_impersonated_role'] ) ? $_SESSION['gm_impersonated_role'] : '';
}

/**
 * Rimuove l'impersonificazione
 */
function gm_clear_impersonated_role() {
    unset( $_SESSION['gm_impersonated_role'] );
}

/**
 * Verifica se l'utente può vedere una capability (considera impersonificazione)
 */
function gm_user_can_impersonated( $capability ) {
    $user = wp_get_current_user();

    // Se non è admin, usa il check normale
    if ( ! gm_is_admin_user() ) {
        return current_user_can( $capability );
    }

    // Se admin ma non sta impersonando, usa il check normale
    $impersonated_role = gm_get_impersonated_role();
    if ( empty( $impersonated_role ) ) {
        return current_user_can( $capability );
    }

    // Admin sta impersonando: simula le capability del ruolo impersonato
    $role_capabilities = [
        'gm_volontario' => [
            'read' => true,
            'gm_create_foglio' => true,
            'gm_edit_own_foglio' => true,
        ],
        'gm_direttivo' => [
            'read' => true,
            'gm_create_foglio' => true,
            'gm_edit_own_foglio' => true,
            'gm_read_all' => true,
            'gm_edit_all' => true,
            'gm_manage_users' => true,
            'gm_manage_veicoli' => true,
        ],
        'gm_amministrazione' => [
            'read' => true,
            'gm_create_foglio' => true,
            'gm_edit_own_foglio' => true,
            'gm_read_all' => true,
            'gm_edit_all' => true,
            'gm_manage_users' => true,
            'gm_manage_veicoli' => true,
            'gm_delete_any' => true,
            'gm_view_log' => true,
        ],
    ];

    if ( isset( $role_capabilities[ $impersonated_role ][ $capability ] ) ) {
        return $role_capabilities[ $impersonated_role ][ $capability ];
    }

    return false;
}

/**
 * Ottiene il nome leggibile del ruolo
 */
function gm_get_role_display_name( $role ) {
    $names = [
        'administrator' => 'Admin WordPress',
        'gm_amministrazione' => 'Amministrazione',
        'gm_direttivo' => 'Direttivo',
        'gm_volontario' => 'Volontario',
    ];

    return isset( $names[ $role ] ) ? $names[ $role ] : ucfirst( $role );
}

/**
 * Gestisce il cambio ruolo via AJAX
 */
add_action( 'wp_ajax_gm_switch_role', 'gm_ajax_switch_role' );

function gm_ajax_switch_role() {
    check_ajax_referer( 'gm_switch_role', 'nonce' );

    if ( ! gm_is_admin_user() ) {
        wp_send_json_error( [ 'message' => 'Non autorizzato' ] );
    }

    $role = isset( $_POST['role'] ) ? sanitize_text_field( $_POST['role'] ) : '';

    if ( empty( $role ) ) {
        gm_clear_impersonated_role();
        wp_send_json_success( [ 'message' => 'Vista admin ripristinata' ] );
    } else {
        if ( gm_set_impersonated_role( $role ) ) {
            $role_name = gm_get_role_display_name( $role );
            wp_send_json_success( [ 'message' => "Visualizzando come: {$role_name}" ] );
        } else {
            wp_send_json_error( [ 'message' => 'Ruolo non valido' ] );
        }
    }
}
