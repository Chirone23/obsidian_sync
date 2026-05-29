<?php

add_action( 'admin_init', 'gm_block_admin_access' );

function gm_block_admin_access() {
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return;
    }
    $user = wp_get_current_user();
    $gm_roles = [ 'gm_volontario', 'gm_direttivo', 'gm_amministrazione' ];
    if ( ! empty( array_intersect( $gm_roles, (array) $user->roles ) ) ) {
        wp_safe_redirect( home_url() );
        exit;
    }
}

add_filter( 'auth_cookie_expiration', 'gm_auth_cookie_expiration', 10, 2 );

function gm_auth_cookie_expiration( $expiration, $user_id ) {
    $user = get_userdata( $user_id );
    $gm_roles = [ 'gm_volontario', 'gm_direttivo', 'gm_amministrazione' ];

    if ( ! empty( array_intersect( $gm_roles, (array) $user->roles ) ) ) {
        return WEEK_IN_SECONDS;
    }

    return $expiration;
}

function gm_current_user_can_role( $role ) {
    $user = wp_get_current_user();
    return in_array( $role, (array) $user->roles, true );
}

function gm_redirect_if_not_logged_in() {
    if ( ! is_user_logged_in() ) {
        wp_safe_redirect( wp_login_url( remove_query_arg( 'loggedout' ) ) );
        exit;
    }
}

/**
 * Nascondi admin bar per utenti gestionale
 */
add_action( 'after_setup_theme', 'gm_hide_admin_bar' );

function gm_hide_admin_bar() {
    if ( ! is_user_logged_in() ) {
        return;
    }
    $user = wp_get_current_user();
    $gm_roles = [ 'gm_volontario', 'gm_direttivo', 'gm_amministrazione' ];
    if ( ! empty( array_intersect( $gm_roles, (array) $user->roles ) ) ) {
        show_admin_bar( false );
    }
}

/**
 * Aggiungi body class alle pagine gestionale per targetare CSS
 */
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

/**
 * Redirect homepage pubblica a dashboard gestionale (o login se non loggato)
 */
add_action( 'template_redirect', 'gm_redirect_homepage' );

function gm_redirect_homepage() {
    // Se è la homepage e non è admin/ajax
    if ( is_front_page() && ! is_admin() && ! wp_doing_ajax() ) {
        if ( is_user_logged_in() ) {
            // Loggato → dashboard gestionale
            wp_safe_redirect( home_url( '/gestionale-dashboard/' ) );
            exit;
        } else {
            // Non loggato → login page
            wp_safe_redirect( wp_login_url() );
            exit;
        }
    }
}

/**
 * Nascondi pagine gestionale dal menu pubblico del tema
 */
add_filter( 'wp_get_nav_menu_items', 'gm_hide_pages_from_public_menu', 10, 3 );

function gm_hide_pages_from_public_menu( $items, $menu, $args ) {
    // Rimuovi pagine che iniziano con "gestionale-"
    foreach ( $items as $key => $item ) {
        if ( $item->object === 'page' ) {
            $page = get_post( $item->object_id );
            if ( $page && strpos( $page->post_name, 'gestionale-' ) === 0 ) {
                unset( $items[ $key ] );
            }
        }
    }
    return $items;
}

/**
 * Nascondi pagine gestionale dalla lista pagine automatica (se il tema usa wp_list_pages)
 */
add_filter( 'wp_list_pages_excludes', 'gm_exclude_gestionale_pages' );

function gm_exclude_gestionale_pages( $exclude_array ) {
    $gestionale_pages = get_pages( [
        'meta_key' => '_wp_page_template',
        'hierarchical' => 0
    ] );

    foreach ( $gestionale_pages as $page ) {
        if ( strpos( $page->post_name, 'gestionale-' ) === 0 ) {
            $exclude_array[] = $page->ID;
        }
    }

    return $exclude_array;
}
