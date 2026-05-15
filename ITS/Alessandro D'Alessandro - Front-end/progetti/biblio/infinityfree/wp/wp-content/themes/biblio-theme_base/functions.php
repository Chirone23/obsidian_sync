<?php
/**
 * Bibliò — functions (RESTORED & FIXED)
 */

if (!defined('ABSPATH')) exit;

define('BIBLIO_VERSION', '0.2.3');

// 1. SETUP DEL TEMA (Originale)
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

// 2. CARICAMENTO ASSET
function biblio_enqueue() {
    wp_enqueue_style('biblio-style', get_stylesheet_uri(), [], BIBLIO_VERSION);
    wp_enqueue_script('biblio-main', get_template_directory_uri() . '/assets/js/main.js', [], BIBLIO_VERSION, true);
    wp_localize_script('biblio-main', 'biblio_api', [
        'url'   => esc_url_raw(rest_url('biblio/v1/chat')),
        'nonce' => wp_create_nonce('wp_rest')
    ]);
}
add_action('wp_enqueue_scripts', 'biblio_enqueue');



// 4. INCLUDE (Originali)
require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/meta-boxes.php';
require_once get_template_directory() . '/inc/wc-integration.php';

// 5. LOGICA CHATBOT
add_action('rest_api_init', function () {
    register_rest_route('biblio/v1', '/chat', [
        'methods' => 'POST',
        'callback' => 'biblio_risposta_chatbot',
        'permission_callback' => function () {
            return is_user_logged_in();
        }
    ]);
});

function biblio_risposta_chatbot($request) {
    $parametri = $request->get_json_params();
    $messaggio_utente = sanitize_text_field($parametri['message']);

    // Recupero Catalogo
    $query_libri = new WP_Query([
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'suppress_filters' => true
    ]);

    $contesto = "";
    if ($query_libri->have_posts()) {
        while ($query_libri->have_posts()) {
            $query_libri->the_post();
            $prezzo = get_post_meta(get_the_ID(), '_price', true);
            $contesto .= "- " . get_the_title() . " (Prezzo: €$prezzo)\n";
        }
        wp_reset_postdata();
    }

    if (empty($contesto)) $contesto = "Catalogo non disponibile.";

    // Chiamata GROQ
    $api_key = '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'; 
    $url = "https://api.groq.com/openai/v1/chat/completions";

    $body = [
        "model" => "llama-3.1-8b-instant",
        "messages" => [
            [
                "role" => "system", 
                "content" => "Sei MyBibliò AI. Parli un italiano perfetto, colto e conciso.
                REGOLE:
                1. Rispondi in massimo 3-4 frasi. Non essere logorroico.
                2. Usa SOLO i titoli del catalogo: \n$contesto
                3. Se non conosci la trama di un libro, non inventarla. Limitati a dire: 'Abbiamo [Titolo] a [Prezzo]€'.
                4. Non dire mai 'potrebbe essere' o 'non so'. Se non sai, non parlarne.
                5. Evita traduzioni letterali dall'inglese."
            ],
            ["role" => "user", "content" => $messaggio_utente]
        ],
        "temperature" => 0.2, // Abbassata drasticamente: più precisione, meno errori grammaticali
        "max_tokens" => 200   // Tagliamo le risposte troppo lunghe
    ];

    $response = wp_remote_post($url, [
        'headers' => ['Authorization' => 'Bearer ' . $api_key, 'Content-Type' => 'application/json'],
        'body' => json_encode($body),
        'timeout' => 30,
        'sslverify' => false
    ]);

    if (ob_get_length()) ob_clean();
    $res_body = json_decode(wp_remote_retrieve_body($response), true);
    $risposta = $res_body['choices'][0]['message']['content'] ?? "Errore tecnico.";
    
    wp_send_json(['reply' => $risposta]);
    exit;
}

// 6. INTERFACCIA FOOTER
add_action('wp_footer', function () {
    if (!is_user_logged_in()) return; 
    ?>
    <div id="biblio-chatbot-container" style="position:fixed; bottom:20px; right:20px; width:330px; z-index:9999; background:white; border:1px solid #ddd; border-radius:10px; overflow:hidden; box-shadow:0 5px 20px rgba(0,0,0,0.1); font-family:sans-serif;">
        <div style="background:#1a1a1a; color:white; padding:12px; display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:13px; font-weight:bold;">MyBibliò</span>
            <span id="chat-minimize" style="cursor:pointer; font-size:20px; line-height:1;">−</span>
        </div>
        
        <div id="chat-content" style="height:250px; overflow-y:auto; padding:15px; background:#fefefe; font-size:14px; display:flex; flex-direction:column; gap:10px;">
            <div class="chat-msg-bot" style="align-self:flex-start; background:#f0f0f0; color:#333; padding:10px; border-radius:8px; border-left:3px solid #1a1a1a; max-width:85%;">
                <strong>MyBibliò:</strong><br>
                Ciao! Sono il tuo assistente virtuale. 📚<br>
                Cercavi un libro in particolare o vuoi un consiglio basato sul nostro catalogo?
            </div>
        </div>

        <div style="padding:10px; border-top:1px solid #eee; display:flex; gap:5px; background:#fff;">
            <input type="text" id="chat-input" placeholder="Scrivi qui..." style="flex:1; padding:8px; border:1px solid #ddd; border-radius:4px; outline:none;">
            <button id="chat-submit" style="background:#1a1a1a; color:white; border:none; padding:8px 15px; border-radius:4px; cursor:pointer; font-weight:bold;">→</button>
        </div>
    </div>
    <?php
});