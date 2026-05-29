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
                <h2 class="card-title h4 mb-0 text-primary">Nuovo Foglio di Marcia</h2>
            </div>
            <div class="card-body">
                <div class="alert alert-primary" role="alert">
                    <strong>In sviluppo:</strong> Il form per la creazione del foglio di marcia sarà disponibile nel prossimo step.
                </div>
                <p class="mb-3">Questa pagina permetterà di creare un nuovo foglio di marcia compilando i seguenti campi:</p>
                <ul class="list-unstyled">
                    <li class="mb-2">✓ Conducente</li>
                    <li class="mb-2">✓ Veicolo</li>
                    <li class="mb-2">✓ Merci trasportate</li>
                    <li class="mb-2">✓ Motivo della missione</li>
                    <li class="mb-2">✓ Dati del richiedente</li>
                    <li class="mb-2">✓ Data/ora inizio e fine</li>
                    <li class="mb-2">✓ Chilometraggio iniziale e finale</li>
                    <li class="mb-2">✓ Rifornimenti (carburante/olio)</li>
                    <li class="mb-2">✓ Passeggeri</li>
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
