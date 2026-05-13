<?php
/**
 * Fallback template.
 */
if (!defined('ABSPATH')) exit;
get_header(); ?>

<?php if (have_posts()): ?>
  <?php if (is_home() && !is_front_page()): ?>
    <h1 style="margin-bottom:32px;"><?php single_post_title(); ?></h1>
  <?php endif; ?>

  <div class="grid-4">
    <?php while (have_posts()): the_post(); ?>
      <article class="book-card" style="cursor:default;">
        <h3 style="margin-bottom:8px;"><a href="<?php the_permalink(); ?>" style="color:var(--fg);text-decoration:none;"><?php the_title(); ?></a></h3>
        <div class="meta" style="margin-bottom:10px;"><?php echo esc_html(get_the_date()); ?></div>
        <div style="color:var(--fg-soft);"><?php the_excerpt(); ?></div>
      </article>
    <?php endwhile; ?>
  </div>

  <div style="margin-top:40px;display:flex;justify-content:center;gap:8px;">
    <?php the_posts_pagination(['prev_text' => '←', 'next_text' => '→']); ?>
  </div>
<?php else: ?>
  <div style="text-align:center;padding:60px;">
    <h2>Nessun contenuto</h2>
    <p>Non c'è ancora nulla da mostrare qui.</p>
  </div>
<?php endif; ?>

<?php get_footer();
