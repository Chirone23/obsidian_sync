<?php
/**
 * Single book.
 */
if (!defined('ABSPATH')) exit;
get_header();

if (have_posts()): the_post();
$id = get_the_ID();
$author = biblio_meta($id, 'author', '');
$price = (float) biblio_meta($id, 'price', 0);
$rent = (float) biblio_meta($id, 'rent', 0);
$rentable = (int) biblio_meta($id, 'rentable', 1);
$rating = biblio_meta($id, 'rating', '4,5');
$pages = biblio_meta($id, 'pages', '');
$year = biblio_meta($id, 'year', '');
$isbn = biblio_meta($id, 'isbn', '');
$blurb = biblio_meta($id, 'blurb', '');
$genres = get_the_terms($id, 'book_genre');
$genre_name = ($genres && !is_wp_error($genres)) ? $genres[0]->name : '';
?>

<div class="crumb">
  <a href="<?php echo esc_url(home_url('/')); ?>">Home</a> ›
  <a href="<?php echo esc_url(get_post_type_archive_link('book')); ?>">Catalogo</a> ›
  <b><?php the_title(); ?></b>
</div>

<div class="detail-grid">
  <div>
    <?php biblio_book_cover($id, 'lg'); ?>
    <div class="detail-cover-actions detail-actions">
      <button class="nav-icon" aria-label="Wishlist">❤️</button>
      <button class="nav-icon" aria-label="Condividi">↗</button>
    </div>
  </div>

  <div>
    <div class="eyebrow" style="margin-bottom:10px;">
      <?php echo esc_html($genre_name); ?><?php if ($year) echo ' · ' . esc_html($year); ?>
    </div>
    <h1 class="book-title" style="font-size:44px;margin-bottom:8px;font-style:italic;"><?php the_title(); ?></h1>
    <?php if ($author): ?>
      <p style="font:400 18px var(--font-sans);color:var(--fg-soft);margin-bottom:18px;">di <b style="color:var(--fg)"><?php echo esc_html($author); ?></b></p>
    <?php endif; ?>

    <div class="detail-meta-row">
      <span class="detail-rating">⭐ <b><?php echo esc_html($rating); ?></b><span class="meta"> · 1.240 recensioni</span></span>
      <?php if ($pages): ?><span class="meta"><?php echo esc_html($pages); ?> pagine</span><?php endif; ?>
      <?php if ($isbn): ?><span class="meta">ISBN <?php echo esc_html($isbn); ?></span><?php endif; ?>
    </div>

    <div class="detail-blurb">
      <?php echo $blurb ? esc_html($blurb) : 'Una delle opere più amate della letteratura italiana. Un viaggio attraverso le parole di un autore che ha segnato la sua epoca, ora disponibile su Bibliò in edizione originale.'; ?>
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
        <div class="buyrent-price buy"><?php echo biblio_price($price); ?></div>
        <div class="meta" style="margin-bottom:14px;">Cartaceo · spedizione in 48h</div>
        <?php
        $wc_product = (class_exists('WooCommerce') && function_exists('wc_get_product')) ? wc_get_product($id) : null;
        if ($wc_product && $wc_product->is_purchasable() && $wc_product->is_in_stock()) {
            echo '<a class="btn btn-primary btn-block" href="' . esc_url($wc_product->add_to_cart_url()) . '" data-product_id="' . esc_attr($id) . '" rel="nofollow">Aggiungi al carrello</a>';
        } elseif ($wc_product) {
            echo '<button class="btn btn-primary btn-block" disabled>Non disponibile</button>';
        } else {
            echo '<a class="btn btn-primary btn-block" href="#">Aggiungi al carrello</a>';
        }
        ?>
      </div>

      <?php if ($rentable && $rent > 0): ?>
        <div class="buyrent-rent-col">
          <div class="eyebrow" style="margin-bottom:6px;color:var(--biblio-rent);">📦 Noleggia</div>
          <div class="buyrent-price rent"><?php echo biblio_price($rent); ?></div>
          <div class="meta" style="margin-bottom:14px;">30 giorni · ritiro gratuito</div>
          <a class="btn btn-rent btn-block" href="#">Noleggia 30gg</a>
        </div>
      <?php endif; ?>
    </div>

    <p class="meta" style="max-width:620px;">
      ✨ <span style="color:var(--biblio-gold);font-weight:500;">Con Plus</span> questo noleggio è incluso nei tuoi 2 mensili.
      <a href="<?php echo esc_url(home_url('/plus/')); ?>">Scopri Plus →</a>
    </p>
  </div>
</div>

<?php
$related = biblio_books_query([
    'posts_per_page' => 4,
    'post__not_in' => [$id],
    'orderby' => 'rand',
]);
if ($related->have_posts()): ?>
<section class="section">
  <div class="section-head">
    <div>
      <div class="eyebrow" style="margin-bottom:8px;">MyBibliò suggerisce</div>
      <h2>Potrebbe piacerti anche…</h2>
    </div>
  </div>
  <div class="grid-4">
    <?php while ($related->have_posts()) { $related->the_post(); biblio_book_card(get_the_ID()); } wp_reset_postdata(); ?>
  </div>
</section>
<?php endif; ?>

<?php endif; get_footer();
