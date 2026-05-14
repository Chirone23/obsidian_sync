<?php
if (!defined('ABSPATH')) exit;
get_header();
if (have_posts()): the_post(); ?>
  <article>
    <header style="margin-bottom:24px;">
      <h1 style="margin-bottom:8px;"><?php the_title(); ?></h1>
      <div class="meta"><?php echo esc_html(get_the_date()); ?></div>
    </header>
    <div class="entry-content">
      <?php the_content(); ?>
    </div>
  </article>
<?php endif; get_footer();
