<?php
/**
 * Bibliò — functions
 */

if (!defined('ABSPATH')) exit;

define('BIBLIO_VERSION', '0.2.0');

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
    load_theme_textdomain('biblio', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'biblio_setup');

function biblio_enqueue() {
    wp_enqueue_style('biblio-style', get_stylesheet_uri(), [], BIBLIO_VERSION);
    wp_enqueue_script('biblio-main', get_template_directory_uri() . '/assets/js/main.js', [], BIBLIO_VERSION, true);
}
add_action('wp_enqueue_scripts', 'biblio_enqueue');

require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/helpers.php';
require get_template_directory() . '/inc/meta-boxes.php';
require get_template_directory() . '/inc/wc-integration.php';

/** Performance: disabilita emoji/oembed/wlw (CPU saving Infinity Free) */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
add_filter('heartbeat_settings', function($s){ $s['interval'] = 60; return $s; });
