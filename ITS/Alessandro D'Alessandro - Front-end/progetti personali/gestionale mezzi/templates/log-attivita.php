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
                <h2 class="card-title h4 mb-0 text-primary">Log Attività</h2>
            </div>
            <div class="card-body">
                <div class="alert alert-primary" role="alert">
                    <strong>In sviluppo:</strong> Il log delle attività sarà disponibile nel prossimo step.
                </div>
                <p class="mb-3">Questa pagina (disponibile solo per l'Amministrazione) permetterà di:</p>
                <ul class="list-unstyled">
                    <li class="mb-2">✓ Visualizzare tutte le azioni effettuate nel sistema</li>
                    <li class="mb-2">✓ Filtrare per utente, azione, data</li>
                    <li class="mb-2">✓ Monitorare modifiche ai fogli di marcia</li>
                    <li class="mb-2">✓ Tracciare accessi e operazioni critiche</li>
                </ul>
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
