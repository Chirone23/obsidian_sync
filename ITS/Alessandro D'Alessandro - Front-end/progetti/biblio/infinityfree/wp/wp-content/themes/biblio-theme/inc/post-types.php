<?php
if (!defined('ABSPATH')) exit;

function biblio_register_book_cpt() {
    register_post_type('book', [
        'labels' => [
            'name' => 'Libri',
            'singular_name' => 'Libro',
            'add_new' => 'Aggiungi libro',
            'add_new_item' => 'Aggiungi nuovo libro',
            'edit_item' => 'Modifica libro',
            'all_items' => 'Tutti i libri',
            'search_items' => 'Cerca libri',
            'menu_name' => 'Libri',
        ],
        'public' => true,
        'has_archive' => false, // era 'catalogo' — disabilitato per evitare conflitto URL con pagina WC /catalogo/
        'menu_icon' => 'dashicons-book-alt',
        'supports' => ['title','editor','thumbnail','excerpt'],
        'rewrite' => ['slug' => 'libro'],
        'show_in_rest' => true,
    ]);

    register_taxonomy('book_genre', 'book', [
        'labels' => [
            'name' => 'Generi',
            'singular_name' => 'Genere',
            'menu_name' => 'Generi',
        ],
        'public' => true,
        'hierarchical' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'genere'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'biblio_register_book_cpt');
