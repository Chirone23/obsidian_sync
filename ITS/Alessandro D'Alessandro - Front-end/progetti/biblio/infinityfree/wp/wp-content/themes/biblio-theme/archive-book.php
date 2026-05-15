<?php
/**
 * Catalogo — archivio CPT book.
 */
if (!defined('ABSPATH')) exit;
get_header();

$current_genre = get_query_var('book_genre');
$current_search = get_query_var('s');
$current_sort = isset($_GET['orderby']) ? sanitize_key($_GET['orderby']) : 'rilevanza';
$current_format = isset($_GET['formato']) ? sanitize_key($_GET['formato']) : 'all';

$args = [
    'post_type' => 'book',
    'posts_per_page' => 24,
    'paged' => max(1, get_query_var('paged')),
];
if ($current_search) $args['s'] = $current_search;
if ($current_format === 'rentable') {
    $args['meta_query'] = [[ 'key' => '_biblio_rentable', 'value' => '1' ]];
}
if ($current_sort === 'prezzo') {
    $args['meta_key'] = '_biblio_price';
    $args['orderby'] = 'meta_value_num';
    $args['order'] = 'ASC';
} elseif ($current_sort === 'rating') {
    $args['meta_key'] = '_biblio_rating';
    $args['orderby'] = 'meta_value_num';
    $args['order'] = 'DESC';
}
if (is_tax('book_genre')) {
    $term = get_queried_object();
    $args['tax_query'] = [[ 'taxonomy' => 'book_genre', 'field' => 'slug', 'terms' => $term->slug ]];
}

$query = new WP_Query($args);
$genres = biblio_get_genres(8);
?>

<div class="crumb">
  <a href="<?php echo esc_url(home_url('/')); ?>">Home</a> › <b>Catalogo</b>
  <?php if (is_tax('book_genre')) { $t = get_queried_object(); echo ' › <b>' . esc_html($t->name) . '</b>'; } ?>
</div>

<h1 style="margin-bottom:8px;">Catalogo</h1>
<p class="lead" style="max-width:620px;margin-bottom:28px;">Oltre 50.000 titoli: acquista, noleggia per 30 giorni, o lasciati guidare da MyBibliò.</p>

<div class="catalog-layout">
  <aside class="catalog-sidebar">
    <form method="get" action="<?php echo esc_url(home_url('/')); ?>">
      <input type="hidden" name="post_type" value="book">
      <div class="filter-group">
        <div class="eyebrow">Ricerca</div>
        <div class="search-input-wrap">
          <span class="icon">🔍</span>
          <input class="search-input" type="search" name="s" value="<?php echo esc_attr($current_search); ?>" placeholder="Autore, titolo, ISBN…">
        </div>
      </div>
    </form>

    <?php if (!empty($genres) && !is_wp_error($genres)): ?>
    <div class="filter-group">
      <div class="eyebrow">Categoria</div>
      <a class="filter-item <?php echo !is_tax('book_genre') ? 'active' : ''; ?>" href="<?php echo esc_url(get_post_type_archive_link('book')); ?>">Tutte</a>
      <?php foreach ($genres as $g): $active = is_tax('book_genre', $g->slug); ?>
        <a class="filter-item <?php echo $active ? 'active' : ''; ?>" href="<?php echo esc_url(get_term_link($g)); ?>">
          <span><?php echo esc_html($g->name); ?></span><span class="count"><?php echo number_format_i18n($g->count); ?></span>
        </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="filter-group">
      <div class="eyebrow">Formato</div>
      <?php
      $cur_base = is_tax('book_genre') ? get_term_link(get_queried_object()) : get_post_type_archive_link('book');
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
        <?php while ($query->have_posts()) { $query->the_post(); biblio_book_card(get_the_ID()); } ?>
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
        <p>Nessun risultato. Prova con un autore, un genere, o chiedi a MyBibliò.</p>
      </div>
    <?php endif; wp_reset_postdata(); ?>
  </div>
</div>

<?php get_footer();
