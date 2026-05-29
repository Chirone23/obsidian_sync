<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

ob_start();
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom">
                <h2 class="card-title h4 mb-0 text-primary">I Miei Fogli di Marcia</h2>
            </div>
            <div class="card-body">
                <div class="alert alert-primary" role="alert">
                    <strong>In sviluppo:</strong> La lista dei tuoi fogli di marcia sarà disponibile nel prossimo step.
                </div>
                <p>Qui potrai visualizzare e gestire tutti i fogli di marcia che hai creato.</p>
                <div class="d-grid gap-2 d-md-block mt-4">
                    <a href="<?php echo esc_url( home_url( '/gestionale-dashboard/' ) ); ?>" class="btn btn-outline-secondary">
                        Torna alla Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include GESTIONALE_MEZZI_PATH . 'templates/layout.php';
