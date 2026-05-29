/**
 * GESTIONALE MEZZI - JavaScript (Bootstrap 5)
 * Bootstrap gestisce Offcanvas automaticamente
 */

(function() {
    'use strict';

    // DOM Ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        initSidebarAutoClose();
        initRoleSwitch();

        // Log per debug (rimuovi in produzione)
        console.log('Gestionale Mezzi initialized with Bootstrap 5');
    }

    /**
     * Auto-close sidebar su mobile quando clicco su un link
     */
    function initSidebarAutoClose() {
        const sidebar = document.getElementById('gm-sidebar');
        if (!sidebar) return;

        const sidebarLinks = sidebar.querySelectorAll('.nav-link');
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(sidebar) || new bootstrap.Offcanvas(sidebar);

        sidebarLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                // Chiudi solo su mobile/tablet
                if (window.innerWidth < 1024) {
                    bsOffcanvas.hide();
                }
            });
        });
    }

    /**
     * Gestione switch ruolo "Visualizza come" (solo admin)
     */
    function initRoleSwitch() {
        const switchLinks = document.querySelectorAll('.gm-switch-role');
        if (switchLinks.length === 0) return;

        switchLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                const role = this.getAttribute('data-role');
                const roleName = this.textContent.trim();

                // Chiamata AJAX per cambiare ruolo
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'gm_switch_role',
                        role: role,
                        nonce: gm_ajax_nonce
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Ricarica la pagina per applicare il nuovo ruolo
                        window.location.reload();
                    } else {
                        alert('Errore: ' + (data.data.message || 'Impossibile cambiare ruolo'));
                    }
                })
                .catch(error => {
                    console.error('Errore AJAX:', error);
                    alert('Errore di connessione');
                });
            });
        });
    }

})();
