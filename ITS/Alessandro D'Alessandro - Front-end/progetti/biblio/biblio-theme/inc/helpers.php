<?php
if (!defined('ABSPATH')) exit;

/**
 * Get book meta with default fallback.
 */
function biblio_meta($post_id, $key, $default = '') {
    $v = get_post_meta($post_id, '_biblio_' . $key, true);
    return $v !== '' ? $v : $default;
}

function biblio_price($v) {
    return number_format((float)$v, 2, ',', '.') . '€';
}

/**
 * Render book cover (CSS gradient fallback if no thumbnail).
 */
function biblio_book_cover($post_id, $size = 'md') {
    $idx = (int) biblio_meta($post_id, 'cover_idx', 0);
    $idx = max(0, min(5, $idx));
    $title = get_the_title($post_id);
    $author = biblio_meta($post_id, 'author', '');
    $thumb = has_post_thumbnail($post_id) ? get_the_post_thumbnail_url($post_id, 'medium') : '';
    $class = 'book-cover cover-' . $idx . ' size-' . esc_attr($size);
    if ($thumb) {
        echo '<div class="' . esc_attr($class) . '" style="background-image:url(' . esc_url($thumb) . ');background-size:cover;background-position:center;padding:0;"></div>';
    } else {
        echo '<div class="' . esc_attr($class) . '">';
        echo '<div class="book-cover-title">' . esc_html($title) . '</div>';
        echo '<div class="book-cover-author">' . esc_html($author) . '</div>';
        echo '</div>';
    }
}

/**
 * Render book card (used in grids).
 */
function biblio_book_card($post_id, $compact = false) {
    $author = biblio_meta($post_id, 'author', '');
    $price = (float) biblio_meta($post_id, 'price', 0);
    $rent = (float) biblio_meta($post_id, 'rent', 0);
    $rentable = (int) biblio_meta($post_id, 'rentable', 1);
    $badge = biblio_meta($post_id, 'badge', '');
    $url = get_permalink($post_id);
    $class = 'book-card' . ($compact ? ' compact' : '');
    ?>
    <a class="<?php echo esc_attr($class); ?>" href="<?php echo esc_url($url); ?>">
        <?php if ($badge): ?>
          <span class="book-card-badge <?php echo $badge === 'Novità' ? 'badge-novita' : 'badge-default'; ?>"><?php echo esc_html($badge); ?></span>
        <?php endif; ?>
        <div class="cover-wrap"><?php biblio_book_cover($post_id, $compact ? 'sm' : 'md'); ?></div>
        <div>
            <div class="book-card-title"><?php echo esc_html(get_the_title($post_id)); ?></div>
            <div class="book-card-author"><?php echo esc_html($author); ?></div>
        </div>
        <div class="book-card-footer">
            <span class="book-card-price"><?php echo biblio_price($price); ?></span>
            <?php if ($rentable && $rent > 0): ?>
              <span class="book-card-rent">📦 <?php echo biblio_price($rent); ?></span>
            <?php endif; ?>
        </div>
    </a>
    <?php
}

/**
 * Get featured / latest books query.
 */
function biblio_books_query($args = []) {
    $defaults = [
        'post_type' => 'book',
        'posts_per_page' => 6,
        'no_found_rows' => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => false,
    ];
    return new WP_Query(array_merge($defaults, $args));
}

/**
 * Get categories (genres) with count.
 */
function biblio_get_genres($limit = 8) {
    return get_terms([
        'taxonomy' => 'book_genre',
        'hide_empty' => false,
        'number' => $limit,
    ]);
}

/**
 * Genre icon map (extend as needed).
 */
function biblio_genre_icon($slug) {
    $map = [
        'narrativa' => '📖', 'saggistica' => '🧠', 'poesia' => '🪶', 'classici' => '🏛️',
        'storia' => '📜', 'arte' => '🎨', 'filosofia' => '💭', 'ragazzi' => '🧸',
        'gialli' => '🔍', 'fantasy' => '🐉', 'biografia' => '👤', 'cucina' => '🍝',
    ];
    return $map[$slug] ?? '📚';
}
