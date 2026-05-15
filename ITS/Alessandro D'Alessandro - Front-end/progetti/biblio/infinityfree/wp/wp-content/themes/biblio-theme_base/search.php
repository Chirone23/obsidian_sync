<?php
if (!defined('ABSPATH')) exit;
get_header(); ?>

<div class="crumb">
  <a href="<?php echo esc_url(home_url('/')); ?>">Home</a> › <b>Ricerca</b>
</div>
<h1 style="margin-bottom:8px;">Risultati per «<?php echo esc_html(get_search_query()); ?>»</h1>
<p class="lead" style="margin-bottom:28px;"><?php
global $wp_query;
echo (int) $wp_query->found_posts . ' risultati trovati.';
?></p>

<?php if (have_posts()): ?>
  <div class="grid-4">
    <?php while (have_posts()): the_post();
      if (get_post_type() === 'book') { biblio_book_card(get_the_ID()); }
      else { ?>
        <a class="book-card" href="<?php the_permalink(); ?>">
          <h3 style="margin-bottom:8px;"><?php the_title(); ?></h3>
          <div style="color:var(--fg-soft);font-size:14px;"><?php echo wp_trim_words(get_the_excerpt(), 18); ?></div>
        </a>
      <?php }
    endwhile; ?>
  </div>
  <div style="margin-top:40px;display:flex;justify-content:center;gap:8px;">
    <?php the_posts_pagination(); ?>
  </div>
<?php else: ?>
  <div style="text-align:center;padding:60px;color:var(--fg-muted);">
    <div style="font-size:40px;margin-bottom:12px;">📚</div>
    <p>Nessun risultato. Prova con altre parole.</p>
  </div>
<?php endif; get_footer();
