<?php if (!defined('ABSPATH')) exit; ?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
  <div class="search-input-wrap">
    <span class="icon">🔍</span>
    <input class="search-input" type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="Autore, titolo, ISBN…">
    <input type="hidden" name="post_type" value="book">
  </div>
</form>
