<?php
/**
 * Bibliò — functions.php v0.3.1
 */
if (!defined('ABSPATH')) exit;

define('BIBLIO_VERSION', '0.3.1');

/* Chiave API Groq — sovrascrivibile in wp-config.php */
if (!defined('BIBLIO_GROQ_KEY')) {
    define('BIBLIO_GROQ_KEY', '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');
}

// ── 1. SETUP ────────────────────────────────────────────────────
function biblio_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption']);
    add_theme_support('automatic-feed-links');
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    register_nav_menus([
        'primary' => __('Menu principale', 'biblio'),
        'footer'  => __('Menu footer', 'biblio'),
    ]);
}
add_action('after_setup_theme', 'biblio_setup');

// ── 2. ASSET ────────────────────────────────────────────────────
function biblio_enqueue() {
    wp_enqueue_style('biblio-style', get_stylesheet_uri(), [], BIBLIO_VERSION);
    wp_enqueue_script('biblio-main', get_template_directory_uri() . '/assets/js/main.js', [], BIBLIO_VERSION, true);
    wp_localize_script('biblio-main', 'biblio_api', [
        'url'   => esc_url_raw(rest_url('biblio/v1/chat')),
        'nonce' => wp_create_nonce('wp_rest'),
    ]);
}
add_action('wp_enqueue_scripts', 'biblio_enqueue');

// ── 3. PERFORMANCE ──────────────────────────────────────────────
add_action('init', function () {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'feed_links_extra', 3);
});

add_action('wp_default_scripts', function ($scripts) {
    if (isset($scripts->registered['heartbeat'])) {
        $scripts->registered['heartbeat']->extra['data'] = 'window.heartbeatSettings={interval:60}';
    }
});

// ── 4. INCLUDES ─────────────────────────────────────────────────
require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/meta-boxes.php';
require_once get_template_directory() . '/inc/wc-integration.php';
require_once get_template_directory() . '/inc/chatbot.php';
