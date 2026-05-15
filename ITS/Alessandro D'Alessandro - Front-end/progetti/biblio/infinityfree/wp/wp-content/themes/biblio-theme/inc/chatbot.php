<?php
/**
 * MyBibliò Chatbot — REST endpoint + footer UI.
 * Caricato da functions.php solo se WooCommerce è attivo.
 */
if (!defined('ABSPATH')) exit;

add_action('rest_api_init', function () {
    register_rest_route('biblio/v1', '/chat', [
        'methods'             => 'POST',
        'callback'            => 'biblio_risposta_chatbot',
        'permission_callback' => function () {
            return is_user_logged_in();
        },
    ]);
});

function biblio_risposta_chatbot(WP_REST_Request $request) {
    $parametri        = $request->get_json_params();
    $messaggio_utente = sanitize_text_field($parametri['message'] ?? '');

    if (empty($messaggio_utente)) {
        return new WP_Error('empty_message', 'Messaggio vuoto.', ['status' => 400]);
    }

    $query_libri = new WP_Query([
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
    ]);

    $contesto = '';
    foreach ($query_libri->posts as $id) {
        $prezzo    = get_post_meta($id, '_price', true);
        $contesto .= '- ' . get_the_title($id) . ' (Prezzo: €' . $prezzo . ")\n";
    }
    if (empty($contesto)) $contesto = 'Catalogo non disponibile.';

    $api_key = defined('BIBLIO_GROQ_KEY') ? BIBLIO_GROQ_KEY : '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';
    $url     = 'https://api.groq.com/openai/v1/chat/completions';

    $body = [
        'model'    => 'llama-3.1-8b-instant',
        'messages' => [
            [
                'role'    => 'system',
                'content' => "Sei MyBibliò AI. Parli un italiano perfetto, colto e conciso.\nREGOLE:\n1. Rispondi in massimo 3-4 frasi. Non essere logorroico.\n2. Usa SOLO i titoli del catalogo:\n$contesto\n3. Se non conosci la trama di un libro, non inventarla. Limitati a dire: 'Abbiamo [Titolo] a [Prezzo]€'.\n4. Non dire mai 'potrebbe essere' o 'non so'. Se non sai, non parlarne.\n5. Evita traduzioni letterali dall'inglese.",
            ],
            ['role' => 'user', 'content' => $messaggio_utente],
        ],
        'temperature' => 0.2,
        'max_tokens'  => 200,
    ];

    $response = wp_remote_post($url, [
        'headers'   => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ],
        'body'      => wp_json_encode($body),
        'timeout'   => 30,
        'sslverify' => false,
    ]);

    if (is_wp_error($response)) {
        return new WP_Error('groq_error', $response->get_error_message(), ['status' => 502]);
    }

    $res_body = json_decode(wp_remote_retrieve_body($response), true);
    $risposta = $res_body['choices'][0]['message']['content'] ?? 'Errore tecnico.';

    return rest_ensure_response(['reply' => $risposta]);
}

add_action('wp_footer', function () {
    if (!is_user_logged_in()) return;
    ?>
    <div id="biblio-chatbot-container" style="position:fixed;bottom:20px;right:20px;width:330px;z-index:9999;background:white;border:1px solid #ddd;border-radius:10px;overflow:hidden;box-shadow:0 5px 20px rgba(0,0,0,.1);font-family:sans-serif;">
        <div style="background:#1a1a1a;color:white;padding:12px;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:13px;font-weight:bold;">MyBibliò</span>
            <span id="chat-minimize" style="cursor:pointer;font-size:20px;line-height:1;">−</span>
        </div>
        <div id="chat-content" style="height:250px;overflow-y:auto;padding:15px;background:#fefefe;font-size:14px;display:flex;flex-direction:column;gap:10px;">
            <div class="chat-msg-bot" style="align-self:flex-start;background:#f0f0f0;color:#333;padding:10px;border-radius:8px;border-left:3px solid #1a1a1a;max-width:85%;">
                <strong>MyBibliò:</strong><br>
                Ciao! Sono il tuo assistente virtuale.<br>
                Cercavi un libro in particolare o vuoi un consiglio basato sul nostro catalogo?
            </div>
        </div>
        <div style="padding:10px;border-top:1px solid #eee;display:flex;gap:5px;background:#fff;">
            <input type="text" id="chat-input" placeholder="Scrivi qui..." style="flex:1;padding:8px;border:1px solid #ddd;border-radius:4px;outline:none;">
            <button id="chat-submit" style="background:#1a1a1a;color:white;border:none;padding:8px 15px;border-radius:4px;cursor:pointer;font-weight:bold;">&rarr;</button>
        </div>
    </div>
    <?php
});
