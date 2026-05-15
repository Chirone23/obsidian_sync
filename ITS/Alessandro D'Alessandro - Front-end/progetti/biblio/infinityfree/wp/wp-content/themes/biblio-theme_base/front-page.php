<?php
/**
 * Front page — Home Bibliò.
 * Pesca da prodotti WooCommerce se WC attivo, altrimenti da CPT book.
 */
if (!defined('ABSPATH')) exit;
get_header();

$use_wc = class_exists('WooCommerce');

// Featured products
if ($use_wc) {
    $featured = biblio_products_query(['posts_per_page' => 6]);
    $news_q = biblio_products_query(['posts_per_page' => 6, 'orderby' => 'date', 'order' => 'DESC']);
    $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 8]);
} else {
    $featured = biblio_books_query(['posts_per_page' => 6]);
    $news_q = biblio_books_query(['posts_per_page' => 6, 'orderby' => 'date', 'order' => 'DESC']);
    $cats = biblio_get_genres(8);
}

$shop_url = $use_wc ? get_permalink(wc_get_page_id('shop')) : get_post_type_archive_link('book');
if (!$shop_url) $shop_url = home_url('/');

$render_card = function($id, $compact = true) use ($use_wc) {
    if ($use_wc) biblio_product_card($id, $compact);
    else biblio_book_card($id, $compact);
};
$render_cover = function($id, $size = 'md') use ($use_wc) {
    if ($use_wc) biblio_product_cover($id, $size);
    else biblio_book_cover($id, $size);
};
?>

<section class="hero">
  <div>
    <div class="eyebrow" style="margin-bottom:16px;">Bibliò · dal 2024</div>
    <h1 class="display" style="margin-bottom:20px;max-width:520px;">
      La tua biblioteca<br>in un click.
    </h1>
    <p class="lead" style="max-width:480px;margin-bottom:32px;">
      Acquista, noleggia o fatti guidare da <b style="color:var(--fg)">MyBibliò</b>.
      Oltre 50.000 titoli, consigli personalizzati, spedizione in 48 ore.
    </p>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
      <a class="btn btn-primary btn-lg" href="<?php echo esc_url($shop_url); ?>">Esplora il catalogo</a>
    </div>
  </div>

  <div class="hero-stack">
    <?php
    $covers = [];
    $featured_ids = [];
    if ($featured->have_posts()) {
        while ($featured->have_posts()) { $featured->the_post(); $covers[] = get_the_ID(); $featured_ids[] = get_the_ID(); }
        wp_reset_postdata();
    }
    if (count($covers) >= 3): ?>
      <div class="cover cover-1"><?php $render_cover($covers[1]); ?></div>
      <div class="cover cover-2"><?php $render_cover($covers[0]); ?></div>
      <div class="cover cover-3"><?php $render_cover($covers[2]); ?></div>
    <?php endif; ?>
  </div>
</section>

<div class="trust">
  <div class="trust-item"><div class="trust-num">50.000+</div><div class="meta">titoli disponibili</div></div>
  <div class="trust-item"><div class="trust-num">48 ore</div><div class="meta">consegna in Italia</div></div>
  <div class="trust-item"><div class="trust-num">30 giorni</div><div class="meta">noleggio flessibile</div></div>
  <div class="trust-item"><div class="trust-num">⭐ 4,8/5</div><div class="meta">giudizio dei lettori</div></div>
</div>

<section class="section">
  <div class="section-head">
    <div>
      <div class="eyebrow" style="margin-bottom:8px;">In evidenza</div>
      <h2>Selezione della settimana</h2>
    </div>
    <a class="btn btn-ghost" href="<?php echo esc_url($shop_url); ?>">Vedi tutti →</a>
  </div>
  <div class="grid-6">
    <?php
    if (!empty($featured_ids)) {
        foreach ($featured_ids as $fid) { $render_card($fid, true); }
    }
    ?>
  </div>
</section>

<?php if (!empty($cats) && !is_wp_error($cats)): ?>
<section class="section">
  <div class="section-head">
    <div>
      <div class="eyebrow" style="margin-bottom:8px;">Esplora</div>
      <h2>Categorie</h2>
    </div>
  </div>
  <div class="grid-cat">
    <?php foreach (array_slice($cats, 0, 8) as $c): ?>
      <a class="cat-card" href="<?php echo esc_url(get_term_link($c)); ?>">
        <span class="cat-icon"><?php echo biblio_genre_icon($c->slug); ?></span>
        <div>
          <div class="cat-name"><?php echo esc_html($c->name); ?></div>
          <div class="meta"><?php echo number_format_i18n($c->count); ?> titoli</div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<section class="plus-banner">
  <div style="flex:1;min-width:280px;">
    <span class="plus-pill">✨ Plus</span>
    <h2>Leggi di più, spendi meno.</h2>
    <p><b style="color:var(--biblio-gold)">9,99€/mese.</b> 2 noleggi inclusi, spedizione gratuita, zero pubblicità, accesso anticipato alle novità.</p>
  </div>
  <a class="btn btn-lg btn-gold" href="<?php echo esc_url(home_url('/plus/')); ?>">Attiva Plus →</a>
</section>

<section class="section">
  <div class="section-head">
    <div>
      <div class="eyebrow" style="margin-bottom:8px;">Appena arrivati</div>
      <h2>Novità editoriali</h2>
    </div>
    <a class="btn btn-ghost" href="<?php echo esc_url($shop_url); ?>">Tutte le novità →</a>
  </div>
  <div class="grid-6">
    <?php
    if ($news_q->have_posts()) while ($news_q->have_posts()) { $news_q->the_post(); $render_card(get_the_ID(), true); }
    wp_reset_postdata();
    ?>
  </div>
</section>

<?php get_footer();
