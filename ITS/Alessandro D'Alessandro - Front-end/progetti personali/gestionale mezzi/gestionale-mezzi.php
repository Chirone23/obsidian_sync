<?php
/**
 * Plugin Name: Gestionale Mezzi
 * Plugin URI: https://gestionale-mezzi.example.com
 * Description: Gestionale veicoli per ente protezione civile
 * Version: 1.0
 * Author: Chirone
 * Text Domain: gestionale-mezzi
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'GESTIONALE_MEZZI_PATH', plugin_dir_path( __FILE__ ) );
define( 'GESTIONALE_MEZZI_URL', plugin_dir_url( __FILE__ ) );
define( 'GESTIONALE_MEZZI_VERSION', '1.0' );

require_once GESTIONALE_MEZZI_PATH . 'includes/db-setup.php';
require_once GESTIONALE_MEZZI_PATH . 'includes/roles.php';
require_once GESTIONALE_MEZZI_PATH . 'includes/auth.php';
require_once GESTIONALE_MEZZI_PATH . 'includes/impersonation.php';
require_once GESTIONALE_MEZZI_PATH . 'includes/pages.php';

register_activation_hook( __FILE__, 'gm_activate_plugin' );
register_deactivation_hook( __FILE__, 'gm_deactivate_plugin' );

function gm_activate_plugin() {
    gm_create_tables();
    gm_register_roles();
    gm_register_pages();
    flush_rewrite_rules();
}

function gm_deactivate_plugin() {
    gm_remove_roles();
}

add_action( 'wp_enqueue_scripts', 'gm_enqueue_assets' );

function gm_enqueue_assets() {
    if ( ! is_user_logged_in() ) {
        return;
    }

    $user = wp_get_current_user();
    $gm_roles = [ 'gm_volontario', 'gm_direttivo', 'gm_amministrazione' ];

    if ( empty( array_intersect( $gm_roles, (array) $user->roles ) ) ) {
        return;
    }

    wp_enqueue_style(
        'gestionale-mezzi-style',
        GESTIONALE_MEZZI_URL . 'assets/style.css',
        [],
        GESTIONALE_MEZZI_VERSION
    );

    wp_enqueue_script(
        'gestionale-mezzi-app',
        GESTIONALE_MEZZI_URL . 'assets/app.js',
        [],
        GESTIONALE_MEZZI_VERSION,
        true
    );

    // Passa URL AJAX e nonce al JavaScript
    wp_localize_script( 'gestionale-mezzi-app', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
    wp_localize_script( 'gestionale-mezzi-app', 'gm_ajax_nonce', wp_create_nonce( 'gm_switch_role' ) );
}
