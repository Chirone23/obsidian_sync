<?php
/**
 * WooCommerce integration — meta box Bibliò sui prodotti + helper.
 */
if (!defined('ABSPATH')) exit;

/** Carica solo se WC è attivo */
if (!class_exists('WooCommerce')) return;

/* ------------------------------------------------------------------ */
/* Meta box "Dettagli Bibliò" su prodotti                              */
/* ------------------------------------------------------------------ */
function biblio_wc_metabox() {
    add_meta_box('biblio_wc_details', 'Dettagli Bibliò (copertina, noleggio, blurb)', 'biblio_wc_metabox_html', 'product', 'normal', 'high');
}
add_action('add_meta_boxes', 'biblio_wc_metabox');

function biblio_wc_metabox_html($post) {
    wp_nonce_field('biblio_wc_save', 'biblio_wc_nonce');
    $get = function($k, $d='') use ($post) {
        $v = get_post_meta($post->ID, '_biblio_' . $k, true);
        return esc_attr($v !== '' ? $v : $d);
    };
    ?>
    <style>
      .biblio-mb { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 24px; padding: 10px 0; }
      .biblio-mb label { display: block; font-weight: 600; margin-bottom: 4px; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: #555; }
      .biblio-mb input[type=text], .biblio-mb input[type=number], .biblio-mb select, .biblio-mb textarea { width: 100%; }
      .biblio-mb .full { grid-column: 1 / -1; }
      .biblio-mb .hint { color: #777; font-size: 12px; margin-top: 3px; font-weight: 400; }
    </style>
    <div class="biblio-mb">
      <div><label>Autore</label><input type="text" name="biblio_author" value="<?php echo $get('author'); ?>"></div>
      <div><label>Anno pubblicazione</label><input type="number" name="biblio_year" value="<?php echo $get('year'); ?>"></div>
      <div><label>Pagine</label><input type="number" name="biblio_pages" value="<?php echo $get('pages'); ?>"></div>
      <div><label>Rating (0-5)</label><input type="number" step="0.1" min="0" max="5" name="biblio_rating" value="<?php echo $get('rating','4.5'); ?>"></div>
      <div><label>Prezzo noleggio 30gg (€) — opzionale</label><input type="number" step="0.01" name="biblio_rent" value="<?php echo $get('rent','0'); ?>"><div class="hint">Lascia 0 per disattivare il noleggio su questo prodotto.</div></div>
      <div><label>Noleggiabile</label>
        <select name="biblio_rentable">
          <option value="1" <?php selected($get('rentable','0'),'1'); ?>>Sì</option>
          <option value="0" <?php selected($get('rentable','0'),'0'); ?>>No</option>
        </select>
      </div>
      <div><label>Badge card</label>
        <select name="biblio_badge">
          <option value="" <?php selected($get('badge'),''); ?>>Nessuno</option>
          <option value="Novità" <?php selected($get('badge'),'Novità'); ?>>Novità</option>
          <option value="Bestseller" <?php selected($get('badge'),'Bestseller'); ?>>Bestseller</option>
          <option value="Plus" <?php selected($get('badge'),'Plus'); ?>>Plus</option>
        </select>
      </div>
      <div><label>Stile copertina (0-5)</label>
        <select name="biblio_cover_idx">
          <?php for($i=0;$i<6;$i++): ?>
            <option value="<?php echo $i; ?>" <?php selected($get('cover_idx','0'),(string)$i); ?>>Stile <?php echo $i; ?></option>
          <?php endfor; ?>
        </select>
        <div class="hint">Usato come copertina-gradiente se NON c'è immagine prodotto.</div>
      </div>
      <div class="full"><label>Blurb (riassunto breve in pagina prodotto)</label><textarea name="biblio_blurb" rows="3"><?php echo esc_textarea(get_post_meta($post->ID, '_biblio_blurb', true)); ?></textarea></div>
    </div>
    <?php
}

function biblio_wc_save($post_id) {
    if (!isset($_POST['biblio_wc_nonce']) || !wp_verify_nonce($_POST['biblio_wc_nonce'], 'biblio_wc_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = ['author','year','pages','rating','rent','rentable','badge','cover_idx','blurb'];
    foreach ($fields as $k) {
        $key = 'biblio_' . $k;
        if (isset($_POST[$key])) {
            $v = $_POST[$key];
            if (in_array($k, ['rent','rating'])) $v = (float) str_replace(',','.',$v);
            elseif (in_array($k, ['pages','year','rentable','cover_idx'])) $v = (int) $v;
            else $v = sanitize_text_field(wp_unslash($v));
            update_post_meta($post_id, '_biblio_' . $k, $v);
        }
    }
}
add_action('save_post_product', 'biblio_wc_save');

/* ------------------------------------------------------------------ */
/* Helpers Bibliò × WC                                                  */
/* ------------------------------------------------------------------ */

/** Cover di un prodotto WC con design Bibliò (gradient se no thumbnail). */
function biblio_product_cover($product_id, $size = 'md') {
    $idx = (int) biblio_meta($product_id, 'cover_idx', 0);
    $idx = max(0, min(5, $idx));
    $title = get_the_title($product_id);
    $author = biblio_meta($product_id, 'author', '');
    $thumb = has_post_thumbnail($product_id) ? get_the_post_thumbnail_url($product_id, 'large') : '';
    $class = 'book-cover cover-' . $idx . ' size-' . esc_attr($size);
    if ($thumb) {
        echo '<div class="' . esc_attr($class) . '" style="background-image:url(' . esc_url($thumb) . ');background-size:cover;background-position:center;padding:0;"></div>';
    } else {
        echo '<div class="' . esc_attr($class) . '">';
        echo '<div class="book-cover-title">' . esc_html($title) . '</div>';
        echo '<div class="book-cover-author">' . esc_html($author) . '</div>';
        echo '</div>';
    }
}

/** Card prodotto WC con design Bibliò. */
function biblio_product_card($product_id, $compact = false) {
    $product = wc_get_product($product_id);
    if (!$product) return;
    $author = biblio_meta($product_id, 'author', '');
    $rent = (float) biblio_meta($product_id, 'rent', 0);
    $rentable = (int) biblio_meta($product_id, 'rentable', 0);
    $badge = biblio_meta($product_id, 'badge', '');
    $url = get_permalink($product_id);
    $price = $product->get_price();
    $regular = $product->get_regular_price();
    $sale = $product->get_sale_price();
    $class = 'book-card' . ($compact ? ' compact' : '');
    ?>
    <a class="<?php echo esc_attr($class); ?>" href="<?php echo esc_url($url); ?>">
        <?php if ($badge): ?>
          <span class="book-card-badge <?php echo $badge === 'Novità' ? 'badge-novita' : 'badge-default'; ?>"><?php echo esc_html($badge); ?></span>
        <?php endif; ?>
        <div class="cover-wrap"><?php biblio_product_cover($product_id, $compact ? 'sm' : 'md'); ?></div>
        <div>
            <div class="book-card-title"><?php echo esc_html(get_the_title($product_id)); ?></div>
            <?php if ($author): ?><div class="book-card-author"><?php echo esc_html($author); ?></div><?php endif; ?>
        </div>
        <div class="book-card-footer">
            <span class="book-card-price">
              <?php if ($sale && $regular && $sale < $regular): ?>
                <span style="text-decoration:line-through;color:var(--fg-muted);font-weight:400;font-size:13px;margin-right:6px;"><?php echo biblio_price($regular); ?></span>
                <?php echo biblio_price($sale); ?>
              <?php else: ?>
                <?php echo biblio_price($price); ?>
              <?php endif; ?>
            </span>
            <?php if ($rentable && $rent > 0): ?>
              <span class="book-card-rent">📦 <?php echo biblio_price($rent); ?></span>
            <?php endif; ?>
        </div>
    </a>
    <?php
}

/** Query prodotti con buoni default per Bibliò. */
function biblio_products_query($args = []) {
    // WC 3.0+: _visibility è deprecata, usare product_visibility taxonomy
    $defaults = [
        'post_type'      => 'product',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'no_found_rows'  => false,
        'tax_query'      => [[
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => ['exclude-from-catalog'],
            'operator' => 'NOT IN',
        ]],
    ];
    return new WP_Query(array_merge($defaults, $args));
}
