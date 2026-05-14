<?php
if (!defined('ABSPATH')) exit;

/**
 * Meta box "Dettagli libro" — autore, prezzo, noleggio, ISBN, ecc.
 * Niente plugin (ACF) per rispettare il vincolo inode di Infinity Free.
 */
function biblio_book_metabox() {
    add_meta_box('biblio_book_details', 'Dettagli libro', 'biblio_book_metabox_html', 'book', 'normal', 'high');
}
add_action('add_meta_boxes', 'biblio_book_metabox');

function biblio_book_metabox_html($post) {
    wp_nonce_field('biblio_book_save', 'biblio_book_nonce');
    $f = function($k, $d='') use ($post) {
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
      <div><label>Autore</label><input type="text" name="biblio_author" value="<?php echo $f('author'); ?>"></div>
      <div><label>ISBN</label><input type="text" name="biblio_isbn" value="<?php echo $f('isbn'); ?>"></div>
      <div><label>Prezzo (€)</label><input type="number" step="0.01" name="biblio_price" value="<?php echo $f('price','0'); ?>"></div>
      <div><label>Prezzo noleggio 30gg (€)</label><input type="number" step="0.01" name="biblio_rent" value="<?php echo $f('rent','0'); ?>"></div>
      <div><label>Noleggiabile</label>
        <select name="biblio_rentable">
          <option value="1" <?php selected($f('rentable','1'),'1'); ?>>Sì</option>
          <option value="0" <?php selected($f('rentable','1'),'0'); ?>>No</option>
        </select>
      </div>
      <div><label>Pagine</label><input type="number" name="biblio_pages" value="<?php echo $f('pages'); ?>"></div>
      <div><label>Anno</label><input type="number" name="biblio_year" value="<?php echo $f('year'); ?>"></div>
      <div><label>Rating (0-5)</label><input type="number" step="0.1" min="0" max="5" name="biblio_rating" value="<?php echo $f('rating','4.5'); ?>"></div>
      <div><label>Badge</label>
        <select name="biblio_badge">
          <option value="" <?php selected($f('badge'),''); ?>>Nessuno</option>
          <option value="Novità" <?php selected($f('badge'),'Novità'); ?>>Novità</option>
          <option value="Bestseller" <?php selected($f('badge'),'Bestseller'); ?>>Bestseller</option>
          <option value="Plus" <?php selected($f('badge'),'Plus'); ?>>Plus</option>
        </select>
      </div>
      <div><label>Stile copertina (0-5)</label>
        <select name="biblio_cover_idx">
          <?php for($i=0;$i<6;$i++): ?>
            <option value="<?php echo $i; ?>" <?php selected($f('cover_idx','0'),(string)$i); ?>>Stile <?php echo $i; ?></option>
          <?php endfor; ?>
        </select>
        <div class="hint">Determina i colori della copertina generata se non c'è immagine in evidenza.</div>
      </div>
      <div class="full"><label>Blurb (riassunto breve)</label><textarea name="biblio_blurb" rows="3"><?php echo esc_textarea(get_post_meta($post->ID, '_biblio_blurb', true)); ?></textarea></div>
    </div>
    <?php
}

function biblio_book_save($post_id) {
    if (!isset($_POST['biblio_book_nonce']) || !wp_verify_nonce($_POST['biblio_book_nonce'], 'biblio_book_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = ['author','isbn','price','rent','rentable','pages','year','rating','badge','cover_idx','blurb'];
    foreach ($fields as $k) {
        $key = 'biblio_' . $k;
        if (isset($_POST[$key])) {
            $v = $_POST[$key];
            if (in_array($k, ['price','rent','rating'])) $v = (float) str_replace(',','.',$v);
            elseif (in_array($k, ['pages','year','rentable','cover_idx'])) $v = (int) $v;
            else $v = sanitize_text_field(wp_unslash($v));
            update_post_meta($post_id, '_biblio_' . $k, $v);
        }
    }
}
add_action('save_post_book', 'biblio_book_save');
