<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user = wp_get_current_user();
$user_display_name = $user->display_name;
$user_role = '';

// Se admin sta impersonando, mostra il ruolo impersonato
$impersonated_role = gm_get_impersonated_role();
if ( ! empty( $impersonated_role ) && gm_is_admin_user() ) {
    $user_role = gm_get_role_display_name( $impersonated_role );
} elseif ( gm_current_user_can_role( 'gm_amministrazione' ) ) {
    $user_role = 'Amministrazione';
} elseif ( gm_current_user_can_role( 'gm_direttivo' ) ) {
    $user_role = 'Direttivo';
} elseif ( gm_current_user_can_role( 'gm_volontario' ) ) {
    $user_role = 'Volontario';
}

ob_start();
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom">
                <h2 class="card-title h4 mb-0 text-primary">Benvenuto, <?php echo esc_html( $user_display_name ); ?></h2>
            </div>
            <div class="card-body">
                <p class="mb-2">Ruolo: <span class="badge bg-primary"><?php echo esc_html( $user_role ); ?></span></p>
                <p class="text-muted mb-0">Sistema di gestione veicoli e fogli di marcia per l'ente di protezione civile.</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom">
                <h3 class="card-title h5 mb-0 text-primary">Azioni rapide</h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <?php if ( gm_user_can_impersonated( 'gm_create_foglio' ) ) : ?>
                        <a href="<?php echo esc_url( home_url( '/gestionale-nuovo-foglio/' ) ); ?>" class="btn btn-primary btn-lg">
                            <svg width="20" height="20" class="me-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Crea Nuovo Foglio di Marcia
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo esc_url( home_url( '/gestionale-i-miei-fogli/' ) ); ?>" class="btn btn-outline-secondary btn-lg">
                        Visualizza i Miei Fogli
                    </a>

                    <?php if ( gm_user_can_impersonated( 'gm_read_all' ) ) : ?>
                        <a href="<?php echo esc_url( home_url( '/gestionale-tutti-i-fogli/' ) ); ?>" class="btn btn-outline-secondary btn-lg">
                            Visualizza Tutti i Fogli
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-primary d-flex align-items-start" role="alert">
            <svg width="24" height="24" class="me-2 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <div>
                <strong>Nota:</strong> Questa è la dashboard del Gestionale Mezzi. Le funzionalità complete saranno disponibili nei prossimi step di sviluppo.
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include GESTIONALE_MEZZI_PATH . 'templates/layout.php';
