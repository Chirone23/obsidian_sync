<?php
/**
 * MyBibliò Chatbot — REST endpoint + footer UI.
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
    $params  = $request->get_json_params();
    $message = sanitize_text_field($params['message'] ?? '');

    if (empty($message)) {
        return new WP_Error('empty_message', 'Messaggio vuoto.', ['status' => 400]);
    }

    /* ── Contesto categoria ─────────────────────────────────── */
    $category_slug = sanitize_key($params['category_slug'] ?? '');
    $category_name = '';
    $cat_filter    = [];

    if ($category_slug) {
        $term = get_term_by('slug', $category_slug, 'product_cat');
        if ($term && !is_wp_error($term)) {
            $category_name = $term->name;
            $cat_filter    = [[
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => [$category_slug],
            ]];
        }
    }

    /* ── Catalogo prodotti (filtrato per categoria se disponibile) */
    $query_args = [
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
    ];
    if (!empty($cat_filter)) {
        $query_args['tax_query'] = $cat_filter;
    }

    $query    = new WP_Query($query_args);
    $contesto = '';
    foreach ($query->posts as $id) {
        $prezzo    = get_post_meta($id, '_price', true);
        $autore    = get_post_meta($id, '_biblio_author', true);
        $generi    = wp_get_post_terms($id, 'product_cat', ['fields' => 'names']);
        $genere    = !is_wp_error($generi) && !empty($generi) ? implode(', ', $generi) : '';
        $contesto .= '- ' . get_the_title($id)
            . ($autore ? ' (di ' . $autore . ')' : '')
            . ($genere ? ' [' . $genere . ']' : '')
            . ' — €' . $prezzo . "\n";
    }
    if (empty($contesto)) {
        $contesto = $category_name
            ? 'Nessun titolo trovato nella categoria "' . $category_name . '".'
            : 'Catalogo non disponibile.';
    }

    /* ── System prompt ──────────────────────────────────────── */
    $contesto_pagina = $category_name
        ? "L'utente sta navigando la categoria \"$category_name\": suggerisci preferibilmente titoli di quella categoria."
        : "L'utente è sul sito Bibliò.";

    $system = "Sei MyBibliò AI, assistente di una libreria italiana online. Parli italiano colto e conciso.
REGOLE:
1. Rispondi in massimo 3-4 frasi. Non essere logorroico.
2. Usa SOLO i titoli del seguente catalogo:\n$contesto
3. Se non conosci la trama, non inventarla. Dì: 'Abbiamo [Titolo] di [Autore] a €[Prezzo]'.
4. Non dire 'potrebbe essere' o 'non so'. Se non sai, non parlarne.
5. Evita traduzioni letterali dall'inglese.
6. $contesto_pagina";

    /* ── History multi-turn (max 6 messaggi) ────────────────── */
    $raw_history = $params['history'] ?? [];
    $messages    = [['role' => 'system', 'content' => $system]];

    if (is_array($raw_history)) {
        foreach (array_slice($raw_history, -6) as $turn) {
            $role    = in_array($turn['role'] ?? '', ['user', 'assistant']) ? $turn['role'] : 'user';
            $content = sanitize_text_field($turn['content'] ?? '');
            if ($content) {
                $messages[] = ['role' => $role, 'content' => $content];
            }
        }
    }

    $messages[] = ['role' => 'user', 'content' => $message];

    /* ── Chiamata Groq ──────────────────────────────────────── */
    $api_key = defined('BIBLIO_GROQ_KEY') ? BIBLIO_GROQ_KEY : '';

    $response = wp_remote_post('https://api.groq.com/openai/v1/chat/completions', [
        'headers'   => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ],
        'body'      => wp_json_encode([
            'model'       => 'llama-3.1-8b-instant',
            'messages'    => $messages,
            'temperature' => 0.2,
            'max_tokens'  => 200,
        ]),
        'timeout'   => 30,
        'sslverify' => false,
    ]);

    if (is_wp_error($response)) {
        return new WP_Error('groq_error', $response->get_error_message(), ['status' => 502]);
    }

    $body     = json_decode(wp_remote_retrieve_body($response), true);
    $risposta = $body['choices'][0]['message']['content'] ?? 'Errore tecnico.';

    return rest_ensure_response(['reply' => $risposta]);
}

/* ── Footer UI ──────────────────────────────────────────────── */
add_action('wp_footer', function () {
    if (!is_user_logged_in()) return;
    ?>
    <div id="biblio-chatbot-container" style="position:fixed;bottom:20px;right:20px;width:330px;z-index:9999;background:white;border:1px solid #ddd;border-radius:10px;overflow:hidden;box-shadow:0 5px 20px rgba(0,0,0,.1);font-family:sans-serif;">
        <div style="background:#1a1a1a;color:white;padding:12px;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:13px;font-weight:bold;">MyBibliò</span>
            <span id="chat-minimize" style="cursor:pointer;font-size:20px;line-height:1;">−</span>
        </div>
        <div id="chat-content" style="height:250px;overflow-y:auto;padding:15px;background:#fefefe;font-size:14px;display:flex;flex-direction:column;gap:6px;">
            <div style="align-self:flex-start;background:#f0f0f0;color:#333;padding:10px;border-radius:8px;border-left:3px solid #1a1a1a;max-width:85%;">
                <strong>MyBibliò:</strong><br>
                Ciao! Sono il tuo assistente.<br>Cercavi un libro o vuoi un consiglio?
            </div>
        </div>
        <div style="padding:10px;border-top:1px solid #eee;display:flex;gap:5px;background:#fff;">
            <input type="text" id="chat-input" placeholder="Scrivi qui..." style="flex:1;padding:8px;border:1px solid #ddd;border-radius:4px;outline:none;">
            <button id="chat-submit" style="background:#1a1a1a;color:white;border:none;padding:8px 15px;border-radius:4px;cursor:pointer;font-weight:bold;">&rarr;</button>
        </div>
    </div>
    <?php
});
