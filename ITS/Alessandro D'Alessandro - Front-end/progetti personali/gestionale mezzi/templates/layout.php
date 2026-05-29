<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user = wp_get_current_user();
$user_display_name = $user->display_name;

$menu_items = gm_get_menu_items();
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <?php wp_head(); ?>
</head>
<body class="gm-body bg-light">

<!-- Header -->
<nav style="margin-block-start: 0rem;;" class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm fixed-top">
    <div class="container-fluid" style="padding-left: 4rem !important; padding-right: 4rem !important;">
        <!-- Hamburger (solo mobile) -->
        <button class="btn btn-link text-dark p-0 me-3 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#gm-sidebar" aria-controls="gm-sidebar" aria-label="Menu">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <a class="navbar-brand fw-bold text-primary mb-0" href="<?php echo esc_url( home_url( '/gestionale-dashboard/' ) ); ?>">
            Gestionale Mezzi
        </a>

        <!-- Menu orizzontale (solo desktop) -->
        <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php
                $max_visible = 4; // Max link visibili, resto in dropdown
                $visible_items = array_slice( $menu_items, 0, $max_visible );
                $overflow_items = array_slice( $menu_items, $max_visible );
                ?>

                <?php foreach ( $visible_items as $item ) : ?>
                    <li class="nav-item">
                        <a href="<?php echo esc_url( $item['url'] ); ?>" class="nav-link">
                            <?php echo esc_html( $item['label'] ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>

                <?php if ( ! empty( $overflow_items ) ) : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Altro
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php foreach ( $overflow_items as $item ) : ?>
                                <li>
                                    <a class="dropdown-item" href="<?php echo esc_url( $item['url'] ); ?>">
                                        <?php echo esc_html( $item['label'] ); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="d-flex align-items-center gap-2 ms-auto">
            <?php if ( gm_is_admin_user() ) : ?>
                <!-- Dropdown "Visualizza come" (solo admin) -->
                <div class="dropdown d-none d-lg-block">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="gm-role-switch" data-bs-toggle="dropdown" aria-expanded="false">
                        👁️ Visualizza come
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="gm-role-switch">
                        <li><a class="dropdown-item gm-switch-role" href="#" data-role="">Amministrazione (me)</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item gm-switch-role" href="#" data-role="gm_direttivo">Direttivo</a></li>
                        <li><a class="dropdown-item gm-switch-role" href="#" data-role="gm_volontario">Volontario</a></li>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Badge fisso ruolo corrente -->
            <?php
            $current_real_role = '';
            if ( gm_current_user_can_role( 'gm_amministrazione' ) ) {
                $current_real_role = 'gm_amministrazione';
            } elseif ( gm_current_user_can_role( 'gm_direttivo' ) ) {
                $current_real_role = 'gm_direttivo';
            } elseif ( gm_current_user_can_role( 'gm_volontario' ) ) {
                $current_real_role = 'gm_volontario';
            }

            $impersonated_role = gm_get_impersonated_role();
            $is_impersonating = ! empty( $impersonated_role ) && gm_is_admin_user();

            if ( $is_impersonating ) {
                $real_name = gm_get_role_display_name( $current_real_role );
                $impersonated_name = gm_get_role_display_name( $impersonated_role );
                echo '<span class="badge bg-warning text-dark d-none d-md-inline">👁️ ' . esc_html( $real_name ) . ' → ' . esc_html( $impersonated_name ) . '</span>';
            } else {
                $role_name = gm_get_role_display_name( $current_real_role );
                echo '<span class="badge bg-primary d-none d-md-inline">' . esc_html( $role_name ) . '</span>';
            }
            ?>

            <span class="d-none d-lg-inline text-muted small"><?php echo esc_html( $user_display_name ); ?></span>
            <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="btn btn-sm btn-outline-secondary">Esci</a>
        </div>
    </div>
</nav>

<!-- Sidebar Offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="gm-sidebar" aria-labelledby="gm-sidebar-label">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title text-primary fw-bold" id="gm-sidebar-label">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Chiudi"></button>
    </div>
    <div class="offcanvas-body p-0">
        <nav class="nav flex-column">
            <?php foreach ( $menu_items as $item ) : ?>
                <a href="<?php echo esc_url( $item['url'] ); ?>" class="nav-link text-dark py-3 px-4 border-bottom">
                    <?php echo esc_html( $item['label'] ); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</div>

<!-- Main Content -->
<main class="container-fluid px-4 py-2" style="padding-top: 64px;">
    <?php
    // Contenuto dinamico caricato dai template
    if ( isset( $content ) ) {
        echo $content;
    }
    ?>
</main>

<!-- Bootstrap 5 JS Bundle (include Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php wp_footer(); ?>
</body>
</html>

<?php
function gm_get_menu_items() {
    $user = wp_get_current_user();
    $items = [];

    // Dashboard - tutti
    $items[] = [
        'label' => 'Dashboard',
        'url' => home_url( '/gestionale-dashboard/' )
    ];

    // Nuovo Foglio - tutti con capability
    if ( gm_user_can_impersonated( 'gm_create_foglio' ) ) {
        $items[] = [
            'label' => 'Nuovo Foglio',
            'url' => home_url( '/gestionale-nuovo-foglio/' )
        ];
    }

    // I Miei Fogli - tutti
    $items[] = [
        'label' => 'I Miei Fogli',
        'url' => home_url( '/gestionale-i-miei-fogli/' )
    ];

    // Tutti i Fogli - solo direttivo/amministrazione
    if ( gm_user_can_impersonated( 'gm_read_all' ) ) {
        $items[] = [
            'label' => 'Tutti i Fogli',
            'url' => home_url( '/gestionale-tutti-i-fogli/' )
        ];
    }

    // Gestione Utenti - solo direttivo/amministrazione
    if ( gm_user_can_impersonated( 'gm_manage_users' ) ) {
        $items[] = [
            'label' => 'Gestione Utenti',
            'url' => home_url( '/gestionale-gestione-utenti/' )
        ];
    }

    // Gestione Veicoli - solo direttivo/amministrazione
    if ( gm_user_can_impersonated( 'gm_manage_veicoli' ) ) {
        $items[] = [
            'label' => 'Gestione Veicoli',
            'url' => home_url( '/gestionale-gestione-veicoli/' )
        ];
    }

    // Log Attività - solo amministrazione
    if ( gm_user_can_impersonated( 'gm_view_log' ) ) {
        $items[] = [
            'label' => 'Log Attività',
            'url' => home_url( '/gestionale-log-attivita/' )
        ];
    }

    return $items;
}
?>
