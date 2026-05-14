<?php
if (!defined('ABSPATH')) exit;
get_header();

if (is_singular('product')):
    while (have_posts()): the_post();
    global $product;
    if (!$product) $product = wc_get_product(get_the_ID());
    $id = get_the_ID();
    $author = biblio_meta($id, 'author', '');
    $rent = (float) biblio_meta($id, 'rent', 0);
    $rentable = (int) biblio_meta($id, 'rentable', 0);
    $rating = biblio_meta($id, 'rating', '4,5');
    $pages = biblio_meta($id, 'pages', '');
    $year = biblio_meta($id, 'year', '');
    $blurb = biblio_meta($id, 'blurb', '');
    $sku = $product->get_sku();
    $cats_terms = get_the_terms($id, 'product_cat');
    $cat_name = ($cats_terms && !is_wp_error($cats_terms)) ? $cats_terms[0]->name : '';
?>

<div class="crumb">
  <a href="<?php echo esc_url(home_url('/')); ?>">Home</a> ›
  <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Catalogo</a> ›
  <b><?php the_title(); ?></b>
</div>

<div class="detail-grid">
  <div>
    <?php biblio_product_cover($id, 'lg'); ?>
    <div class="detail-cover-actions detail-actions">
      <button class="nav-icon" aria-label="Wishlist">❤️</button>
      <button class="nav-icon" aria-label="Condividi">↗</button>
    </div>
  </div>

  <div>
    <div class="eyebrow" style="margin-bottom:10px;">
      <?php echo esc_html($cat_name); ?><?php if ($year) echo ' · ' . esc_html($year); ?>
    </div>
    <h1 class="book-title" style="font-size:44px;margin-bottom:8px;font-style:italic;"><?php the_title(); ?></h1>
    <?php if ($author): ?>
      <p style="font:400 18px var(--font-sans);color:var(--fg-soft);margin-bottom:18px;">di <b style="color:var(--fg)"><?php echo esc_html($author); ?></b></p>
    <?php endif; ?>

    <div class="detail-meta-row">
      <span class="detail-rating">⭐ <b><?php echo esc_html($rating); ?></b><span class="meta"> · 1.240 recensioni</span></span>
      <?php if ($pages): ?><span class="meta"><?php echo esc_html($pages); ?> pagine</span><?php endif; ?>
      <?php if ($sku): ?><span class="meta">SKU <?php echo esc_html($sku); ?></span><?php endif; ?>
    </div>

    <div class="detail-blurb">
      <?php
      if ($blurb) {
          echo esc_html($blurb);
      } elseif ($product->get_short_description()) {
          echo wp_kses_post($product->get_short_description());
      } else {
          echo 'Una delle opere più amate della letteratura italiana. Un viaggio attraverso le parole di un autore che ha segnato la sua epoca, ora disponibile su Bibliò.';
      }
      ?>
    </div>

    <?php
    $biblio_content_raw = get_post_field('post_content', $id);
    if (trim($biblio_content_raw) !== ''):
        $biblio_content_html = apply_filters('the_content', $biblio_content_raw);
        $biblio_content_html = str_replace(']]>', ']]&gt;', $biblio_content_html);
    ?>
      <div class="entry-content" style="margin-bottom:32px;"><?php echo $biblio_content_html; ?></div>
    <?php endif; ?>

    <div class="buyrent-card <?php echo ($rentable && $rent > 0) ? '' : 'no-rent'; ?>">
      <div>
        <div class="eyebrow" style="margin-bottom:6px;">🛒 Acquista</div>
        <div class="buyrent-price buy">
          <?php
          $regular = $product->get_regular_price();
          $sale = $product->get_sale_price();
          if ($sale && $regular && (float)$sale < (float)$regular) {
              echo '<span style="text-decoration:line-through;color:var(--fg-muted);font-weight:400;font-size:20px;margin-right:10px;">' . esc_html(biblio_price($regular)) . '</span>';
              echo esc_html(biblio_price($sale));
          } else {
              echo esc_html(biblio_price($product->get_price()));
          }
          ?>
        </div>
        <div class="meta" style="margin-bottom:14px;">
          <?php echo $product->is_in_stock() ? 'Cartaceo · spedizione in 48h' : 'Non disponibile al momento'; ?>
        </div>
        <?php if ($product->is_in_stock() && $product->is_purchasable()): ?>
          <a class="btn btn-primary btn-block" href="<?php echo esc_url($product->add_to_cart_url()); ?>" data-product_id="<?php echo esc_attr($id); ?>" rel="nofollow">Aggiungi al carrello</a>
        <?php else: ?>
          <button class="btn btn-primary btn-block" disabled>Non disponibile</button>
        <?php endif; ?>
      </div>

      <?php if ($rentable && $rent > 0): ?>
        <div class="buyrent-rent-col">
          <div class="eyebrow" style="margin-bottom:6px;color:var(--biblio-rent);">📦 Noleggia</div>
          <div class="buyrent-price rent"><?php echo esc_html(biblio_price($rent)); ?></div>
          <div class="meta" style="margin-bottom:14px;">30 giorni · ritiro gratuito</div>
          <a class="btn btn-rent btn-block" href="#">Noleggia 30gg</a>
        </div>
      <?php endif; ?>
    </div>

    <p class="meta" style="max-width:620px;">
      ✨ <span style="color:var(--biblio-gold);font-weight:500;">Con Plus</span> spedizione gratuita e accesso anticipato alle novità.
      <a href="<?php echo esc_url(home_url('/plus/')); ?>">Scopri Plus →</a>
    </p>
  </div>
</div>

<?php
$related_ids = wc_get_related_products($id, 4);
if (!empty($related_ids)): ?>
<section class="section">
  <div class="section-head">
    <div>
      <div class="eyebrow" style="margin-bottom:8px;">MyBibliò suggerisce</div>
      <h2>Potrebbe piacerti anche…</h2>
    </div>
  </div>
  <div class="grid-4">
    <?php foreach ($related_ids as $rid) biblio_product_card($rid); ?>
  </div>
</section>
<?php endif; ?>

<?php
    endwhile;

elseif (is_shop() || is_product_taxonomy()):

    $current_search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $current_sort = isset($_GET['orderby']) ? sanitize_key($_GET['orderby']) : 'rilevanza';
    $current_format = isset($_GET['formato']) ? sanitize_key($_GET['formato']) : 'all';

    $args = [
        'post_type' => 'product',
        'posts_per_page' => 24,
        'paged' => max(1, get_query_var('paged')),
        'post_status' => 'publish',
    ];
    if ($current_search) $args['s'] = $current_search;
    if ($current_format === 'rentable') {
        $args['meta_query'] = [['key' => '_biblio_rentable', 'value' => '1']];
    }
    if ($current_sort === 'prezzo') {
        $args['meta_key'] = '_price';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'ASC';
    } elseif ($current_sort === 'rating') {
        $args['meta_key'] = '_biblio_rating';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'DESC';
    }
    if (is_product_taxonomy()) {
        $term = get_queried_object();
        $args['tax_query'] = [['taxonomy' => $term->taxonomy, 'field' => 'slug', 'terms' => $term->slug]];
    }
    $query = new WP_Query($args);
    $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 12]);
    $shop_url = get_permalink(wc_get_page_id('shop'));
?>

<div class="crumb">
  <a href="<?php echo esc_url(home_url('/')); ?>">Home</a> › <b>Catalogo</b>
  <?php if (is_product_taxonomy()) { $t = get_queried_object(); echo ' › <b>' . esc_html($t->name) . '</b>'; } ?>
</div>

<h1 style="margin-bottom:8px;"><?php echo is_product_taxonomy() ? esc_html(single_term_title('', false)) : 'Catalogo'; ?></h1>
<p class="lead" style="max-width:620px;margin-bottom:28px;">Oltre 50.000 titoli: acquista, noleggia per 30 giorni, o lasciati guidare da MyBibliò.</p>

<div class="catalog-layout">
  <aside class="catalog-sidebar">
    <form method="get" action="<?php echo esc_url($shop_url); ?>">
      <div class="filter-group">
        <div class="eyebrow">Ricerca</div>
        <div class="search-input-wrap">
          <span class="icon">🔍</span>
          <input class="search-input" type="search" name="s" value="<?php echo esc_attr($current_search); ?>" placeholder="Autore, titolo, SKU…">
        </div>
      </div>
    </form>

    <?php if (!empty($cats) && !is_wp_error($cats)): ?>
    <div class="filter-group">
      <div class="eyebrow">Categoria</div>
      <a class="filter-item <?php echo !is_product_taxonomy() ? 'active' : ''; ?>" href="<?php echo esc_url($shop_url); ?>">Tutte</a>
      <?php foreach ($cats as $c): $active = is_tax('product_cat', $c->slug); ?>
        <a class="filter-item <?php echo $active ? 'active' : ''; ?>" href="<?php echo esc_url(get_term_link($c)); ?>">
          <span><?php echo esc_html($c->name); ?></span><span class="count"><?php echo number_format_i18n($c->count); ?></span>
        </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="filter-group">
      <div class="eyebrow">Formato</div>
      <?php
      $cur_base = is_product_taxonomy() ? get_term_link(get_queried_object()) : $shop_url;
      $formats = ['all' => 'Tutti', 'rentable' => 'Noleggiabili'];
      foreach ($formats as $k => $l) {
          $url = add_query_arg('formato', $k, $cur_base);
          $active = $current_format === $k ? 'active' : '';
          echo '<a class="filter-item ' . $active . '" href="' . esc_url($url) . '">' . esc_html($l) . '</a>';
      }
      ?>
    </div>

    <div class="sidebar-cta">
      <div style="font:500 14px var(--font-serif);margin-bottom:4px;">Non trovi nulla?</div>
      <p class="meta" style="margin-bottom:10px;">MyBibliò conosce tutto il catalogo. Raccontagli cosa cerchi.</p>
      <a class="btn btn-sm btn-primary" href="<?php echo esc_url(home_url('/mybiblio/')); ?>">✨ Apri la chat</a>
    </div>
  </aside>

  <div>
    <div class="results-head">
      <div class="meta"><b style="color:var(--fg)"><?php echo (int) $query->found_posts; ?></b> titoli</div>
      <form method="get" style="display:flex;align-items:center;gap:10px;">
        <?php foreach ($_GET as $k => $v) { if ($k === 'orderby' || !is_string($v)) continue; echo '<input type="hidden" name="' . esc_attr($k) . '" value="' . esc_attr($v) . '">'; } ?>
        <span class="meta">Ordina per</span>
        <select class="sort-select" name="orderby" onchange="this.form.submit()">
          <option value="rilevanza" <?php selected($current_sort,'rilevanza'); ?>>Rilevanza</option>
          <option value="prezzo" <?php selected($current_sort,'prezzo'); ?>>Prezzo crescente</option>
          <option value="rating" <?php selected($current_sort,'rating'); ?>>Valutazione</option>
        </select>
      </form>
    </div>

    <?php if ($query->have_posts()): ?>
      <div class="grid-4">
        <?php while ($query->have_posts()) { $query->the_post(); biblio_product_card(get_the_ID()); } ?>
      </div>
      <div style="margin-top:40px;display:flex;justify-content:center;gap:8px;">
        <?php echo paginate_links([
            'total' => $query->max_num_pages,
            'prev_text' => '← Precedente',
            'next_text' => 'Successiva →',
        ]); ?>
      </div>
    <?php else: ?>
      <div style="text-align:center;padding:60px;color:var(--fg-muted);">
        <div style="font-size:40px;margin-bottom:12px;">📚</div>
        <p>Nessun risultato. Prova con altri filtri.</p>
      </div>
    <?php endif; wp_reset_postdata(); ?>
  </div>
</div>

<?php
else:
    if (function_exists('woocommerce_content')) {
        echo '<div class="entry-content" style="max-width:1100px;margin:0 auto;">';
        woocommerce_content();
        echo '</div>';
    }
endif;

get_footer();
?>
