<?php
if (!defined('ABSPATH')) exit;

/** Walker che applica classe .nav-link e .active */
if (!class_exists('Biblio_Nav_Walker')) {
class Biblio_Nav_Walker extends Walker_Nav_Menu {
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $cls = 'nav-link';
        if (in_array('current-menu-item', $item->classes)) $cls .= ' active';
        $output .= '<a class="' . $cls . '" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
    }
    public function end_el(&$output, $item, $depth = 0, $args = null) {}
}}

$biblio_search_url = class_exists('WooCommerce')
    ? add_query_arg(['s' => '', 'post_type' => 'product'], home_url('/'))
    : add_query_arg(['s' => '', 'post_type' => 'book'], home_url('/'));
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class('app'); ?>>
<?php wp_body_open(); ?>

<nav class="nav">
  <div class="nav-inner">
    <a class="nav-logo" href="<?php echo esc_url(home_url('/')); ?>">Bibli<span class="accent">ò</span></a>
    <?php
    if (has_nav_menu('primary')) {
        wp_nav_menu([
            'theme_location' => 'primary',
            'container' => false,
            'menu_class' => 'nav-links',
            'fallback_cb' => false,
            'depth' => 1,
            'items_wrap' => '<div class="nav-links">%3$s</div>',
            'walker' => new Biblio_Nav_Walker(),
        ]);
    } else {
        $cur = $_SERVER['REQUEST_URI'] ?? '';
        $links = [
            home_url('/negozio/') => 'Catalogo',
            home_url('/noleggio-vs-acquisto/') => 'Noleggio vs Acquisto',
            home_url('/plus/') => 'Plus',
            home_url('/contatti/') => 'Contatti',
        ];
        echo '<div class="nav-links">';
        foreach ($links as $u => $l) {
            $active = (strpos($cur, parse_url($u, PHP_URL_PATH)) !== false && parse_url($u, PHP_URL_PATH) !== '/') ? ' active' : '';
            echo '<a class="nav-link' . $active . '" href="' . esc_url($u) . '">' . esc_html($l) . '</a>';
        }
        echo '</div>';
    }
    ?>
    <div class="nav-spacer"></div>
    <div class="nav-actions">
      <a class="nav-icon" href="<?php echo esc_url($biblio_search_url); ?>" aria-label="Cerca">🔍</a>
      <?php if (class_exists('WooCommerce')): ?>
        <a class="nav-icon" href="<?php echo esc_url(wc_get_cart_url()); ?>" aria-label="Carrello">
          🛒
          <?php $c = WC()->cart ? WC()->cart->get_cart_contents_count() : 0; if ($c > 0): ?>
            <span class="pip"><?php echo (int) $c; ?></span>
          <?php endif; ?>
        </a>
        <a class="nav-icon" href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" aria-label="Account">👤</a>
      <?php else: ?>
        <a class="nav-icon" href="<?php echo esc_url(home_url('/carrello/')); ?>" aria-label="Carrello">🛒</a>
        <a class="nav-icon" href="<?php echo esc_url(home_url('/account/')); ?>" aria-label="Account">👤</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main class="page">
