<?php

function gm_register_pages() {
    $pages = [
        [
            'slug' => 'gestionale-dashboard',
            'title' => 'Dashboard Gestionale',
            'content' => '[gm_dashboard]'
        ],
        [
            'slug' => 'gestionale-nuovo-foglio',
            'title' => 'Nuovo Foglio di Marcia',
            'content' => '[gm_nuovo_foglio]'
        ],
        [
            'slug' => 'gestionale-i-miei-fogli',
            'title' => 'I Miei Fogli',
            'content' => '[gm_i_miei_fogli]'
        ],
        [
            'slug' => 'gestionale-tutti-i-fogli',
            'title' => 'Tutti i Fogli',
            'content' => '[gm_tutti_i_fogli]'
        ],
        [
            'slug' => 'gestionale-gestione-utenti',
            'title' => 'Gestione Utenti',
            'content' => '[gm_gestione_utenti]'
        ],
        [
            'slug' => 'gestionale-gestione-veicoli',
            'title' => 'Gestione Veicoli',
            'content' => '[gm_gestione_veicoli]'
        ],
        [
            'slug' => 'gestionale-log-attivita',
            'title' => 'Log Attività',
            'content' => '[gm_log_attivita]'
        ]
    ];

    foreach ( $pages as $page ) {
        $existing = get_page_by_path( $page['slug'] );
        if ( ! $existing ) {
            wp_insert_post( [
                'post_title' => $page['title'],
                'post_name' => $page['slug'],
                'post_content' => $page['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => 1,
                'comment_status' => 'closed',
                'ping_status' => 'closed'
            ] );
        }
    }
}

add_action( 'init', 'gm_register_shortcodes' );

function gm_register_shortcodes() {
    add_shortcode( 'gm_dashboard', 'gm_shortcode_dashboard' );
    add_shortcode( 'gm_nuovo_foglio', 'gm_shortcode_nuovo_foglio' );
    add_shortcode( 'gm_i_miei_fogli', 'gm_shortcode_i_miei_fogli' );
    add_shortcode( 'gm_tutti_i_fogli', 'gm_shortcode_tutti_i_fogli' );
    add_shortcode( 'gm_gestione_utenti', 'gm_shortcode_gestione_utenti' );
    add_shortcode( 'gm_gestione_veicoli', 'gm_shortcode_gestione_veicoli' );
    add_shortcode( 'gm_log_attivita', 'gm_shortcode_log_attivita' );
}

function gm_shortcode_dashboard( $atts ) {
    gm_redirect_if_not_logged_in();

    ob_start();
    gm_load_template( 'dashboard', [] );
    return ob_get_clean();
}

function gm_shortcode_nuovo_foglio( $atts ) {
    gm_redirect_if_not_logged_in();

    $user = wp_get_current_user();
    $can_create = gm_user_can_impersonated( 'gm_create_foglio' );

    if ( ! $can_create ) {
        ob_start();
        gm_load_template( 'error-403', [] );
        return ob_get_clean();
    }

    ob_start();
    gm_load_template( 'nuovo-foglio', [] );
    return ob_get_clean();
}

function gm_shortcode_i_miei_fogli( $atts ) {
    gm_redirect_if_not_logged_in();

    ob_start();
    gm_load_template( 'i-miei-fogli', [] );
    return ob_get_clean();
}

function gm_shortcode_tutti_i_fogli( $atts ) {
    gm_redirect_if_not_logged_in();

    $user = wp_get_current_user();
    $can_view_all = gm_user_can_impersonated( 'gm_read_all' );

    if ( ! $can_view_all ) {
        ob_start();
        gm_load_template( 'error-403', [] );
        return ob_get_clean();
    }

    ob_start();
    gm_load_template( 'tutti-i-fogli', [] );
    return ob_get_clean();
}

function gm_shortcode_gestione_utenti( $atts ) {
    gm_redirect_if_not_logged_in();

    $user = wp_get_current_user();
    $can_manage = gm_user_can_impersonated( 'gm_manage_users' );

    if ( ! $can_manage ) {
        ob_start();
        gm_load_template( 'error-403', [] );
        return ob_get_clean();
    }

    ob_start();
    gm_load_template( 'gestione-utenti', [] );
    return ob_get_clean();
}

function gm_shortcode_gestione_veicoli( $atts ) {
    gm_redirect_if_not_logged_in();

    $user = wp_get_current_user();
    $can_manage = gm_user_can_impersonated( 'gm_manage_veicoli' );

    if ( ! $can_manage ) {
        ob_start();
        gm_load_template( 'error-403', [] );
        return ob_get_clean();
    }

    ob_start();
    gm_load_template( 'gestione-veicoli', [] );
    return ob_get_clean();
}

function gm_shortcode_log_attivita( $atts ) {
    gm_redirect_if_not_logged_in();

    $user = wp_get_current_user();
    $can_view_log = gm_user_can_impersonated( 'gm_view_log' );

    if ( ! $can_view_log ) {
        ob_start();
        gm_load_template( 'error-403', [] );
        return ob_get_clean();
    }

    ob_start();
    gm_load_template( 'log-attivita', [] );
    return ob_get_clean();
}

function gm_load_template( $name, $args = [] ) {
    $template_path = GESTIONALE_MEZZI_PATH . 'templates/' . $name . '.php';

    if ( file_exists( $template_path ) ) {
        extract( $args );
        include $template_path;
    }
}
