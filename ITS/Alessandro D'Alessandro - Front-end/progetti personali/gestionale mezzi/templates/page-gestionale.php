<?php
/**
 * Page Template: Gestionale (Full Custom)
 *
 * Sostituisce completamente header/footer del tema WordPress.
 * Usato automaticamente per tutte le pagine gestionale-*
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Nascondi admin bar
show_admin_bar( false );

// Prepara il contenuto (processa shortcode)
global $post;
setup_postdata( $post );

// Processa shortcode e content filters
$page_content = apply_filters( 'the_content', get_the_content() );

wp_reset_postdata();

// Carica layout completo con contenuto processato
$content = $page_content;
require GESTIONALE_MEZZI_PATH . 'templates/layout.php';
