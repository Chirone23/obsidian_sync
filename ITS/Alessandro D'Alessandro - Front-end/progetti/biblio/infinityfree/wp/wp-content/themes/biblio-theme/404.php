<?php
if (!defined('ABSPATH')) exit;
get_header(); ?>
<div style="text-align:center;padding:80px 24px;">
  <div style="font-size:64px;margin-bottom:16px;">📕</div>
  <h1 style="margin-bottom:12px;">Pagina non trovata</h1>
  <p class="lead" style="margin-bottom:28px;">La pagina che cerchi non esiste o è stata spostata.</p>
  <a class="btn btn-primary btn-lg" href="<?php echo esc_url(home_url('/')); ?>">Torna alla home</a>
</div>
<?php get_footer();
