<?php
if (!defined('ABSPATH')) exit;
get_header();
if (have_posts()): the_post(); ?>
  <article>
    <header style="margin-bottom:32px;">
      <h1 style="margin-bottom:8px;"><?php the_title(); ?></h1>
    </header>
    <div class="entry-content">
      <?php the_content(); ?>
    </div>
  </article>
<?php endif; get_footer();
