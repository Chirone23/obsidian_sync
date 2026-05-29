<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white border-0">
                <h2 class="card-title h4 mb-0">Accesso Negato</h2>
            </div>
            <div class="card-body">
                <div class="alert alert-danger d-flex align-items-start" role="alert">
                    <svg width="24" height="24" class="me-2 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    <div>
                        <strong>Errore 403:</strong> Non hai i permessi necessari per accedere a questa pagina.
                    </div>
                </div>
                <p class="mb-3">Il tuo ruolo non dispone delle autorizzazioni richieste per visualizzare questa risorsa.</p>
                <p class="text-muted mb-4">Se ritieni che questo sia un errore, contatta l'amministratore del sistema.</p>
                <div class="d-grid">
                    <a href="<?php echo esc_url( home_url( '/gestionale-dashboard/' ) ); ?>" class="btn btn-primary">
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
