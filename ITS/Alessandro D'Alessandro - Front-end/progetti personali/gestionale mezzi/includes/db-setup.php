<?php

function gm_create_tables() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $prefix = $wpdb->prefix;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    // 1. Categorie Patente
    $sql = "CREATE TABLE {$prefix}gm_categorie_patente (
        id INT AUTO_INCREMENT PRIMARY KEY,
        codice VARCHAR(10) NOT NULL UNIQUE,
        nome VARCHAR(255) NOT NULL,
        descrizione LONGTEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) {$charset_collate} ENGINE=InnoDB;";

    dbDelta( $sql );

    $count = $wpdb->get_var( "SELECT COUNT(*) FROM {$prefix}gm_categorie_patente" );
    if ( 0 === (int) $count ) {
        $wpdb->insert( "{$prefix}gm_categorie_patente", [
            'codice' => 'B',
            'nome' => 'Patente B',
            'descrizione' => 'Abilitazione alla guida di autovetture e veicoli leggeri fino a 3.5t.'
        ], [ '%s', '%s', '%s' ] );

        $wpdb->insert( "{$prefix}gm_categorie_patente", [
            'codice' => 'C',
            'nome' => 'Patente C',
            'descrizione' => 'Abilitazione alla guida di autocarri e veicoli pesanti oltre 3.5t. Include i veicoli di categoria B.'
        ], [ '%s', '%s', '%s' ] );

        $wpdb->insert( "{$prefix}gm_categorie_patente", [
            'codice' => 'MMT',
            'nome' => 'Abilitazione Movimento Terra',
            'descrizione' => 'Patentino per la conduzione di macchine operatrici: escavatori, bobcat, pale meccaniche, ecc. (Accordo Stato-Regioni 22/02/2012). Include i veicoli di categoria B.'
        ], [ '%s', '%s', '%s' ] );
    }

    // 2. Categorie Veicolo
    $sql = "CREATE TABLE {$prefix}gm_categorie_veicolo (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patente_richiesta_id INT NOT NULL,
        note LONGTEXT,
        KEY fk_patente_richiesta (patente_richiesta_id)
    ) {$charset_collate} ENGINE=InnoDB;";

    dbDelta( $sql );

    // 3. Veicoli
    $sql = "CREATE TABLE {$prefix}gm_veicoli (
        id INT AUTO_INCREMENT PRIMARY KEY,
        targa VARCHAR(20) NOT NULL UNIQUE,
        marca VARCHAR(100) NOT NULL,
        modello VARCHAR(100) NOT NULL,
        anno INT,
        categoria_id INT NOT NULL,
        posti_totali INT NOT NULL DEFAULT 2,
        km_attuali INT NOT NULL DEFAULT 0,
        attivo INT NOT NULL DEFAULT 1,
        note LONGTEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        KEY fk_categoria (categoria_id)
    ) {$charset_collate} ENGINE=InnoDB;";

    dbDelta( $sql );

    // 4. Utenti Patenti
    $sql = "CREATE TABLE {$prefix}gm_utenti_patenti (
        utente_id INT NOT NULL,
        categoria_id INT NOT NULL,
        data_conseguimento DATE,
        data_scadenza DATE,
        PRIMARY KEY (utente_id, categoria_id),
        KEY fk_categoria_patente (categoria_id)
    ) {$charset_collate} ENGINE=InnoDB;";

    dbDelta( $sql );

    // 5. Fogli di Marcia
    $sql = "CREATE TABLE {$prefix}gm_fogli_di_marcia (
        id INT AUTO_INCREMENT PRIMARY KEY,
        numero INT NOT NULL,
        anno INT NOT NULL,
        conducente_id INT NOT NULL,
        veicolo_id INT NOT NULL,
        merci LONGTEXT,
        motivo LONGTEXT,
        richiesto_da LONGTEXT,
        richiesto_per LONGTEXT,
        data_inizio DATETIME,
        data_fine DATETIME,
        km_iniziali INT,
        km_finali INT,
        carburante_litri FLOAT,
        carburante_prezzo DECIMAL(10,2),
        olio_litri FLOAT,
        olio_prezzo DECIMAL(10,2),
        note LONGTEXT,
        stato VARCHAR(20) NOT NULL DEFAULT 'bozza',
        creato_da INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE (numero, anno),
        KEY fk_conducente (conducente_id),
        KEY fk_veicolo (veicolo_id),
        KEY fk_creato_da (creato_da),
        KEY idx_anno (anno),
        KEY idx_stato (stato)
    ) {$charset_collate} ENGINE=InnoDB;";

    dbDelta( $sql );

    // 6. Foglio Passeggeri
    $sql = "CREATE TABLE {$prefix}gm_foglio_passeggeri (
        foglio_id INT NOT NULL,
        utente_id INT NOT NULL,
        PRIMARY KEY (foglio_id, utente_id),
        KEY fk_utente_passeggero (utente_id)
    ) {$charset_collate} ENGINE=InnoDB;";

    dbDelta( $sql );

    // ON DELETE CASCADE separato: dbDelta non gestisce FOREIGN KEY
    $fk_exists = $wpdb->get_var( "
        SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = DATABASE()
        AND CONSTRAINT_NAME = 'fk_foglio_passeggeri'
        AND TABLE_NAME = '{$prefix}gm_foglio_passeggeri'
    " );
    if ( ! (int) $fk_exists ) {
        $wpdb->query( "
            ALTER TABLE {$prefix}gm_foglio_passeggeri
            ADD CONSTRAINT fk_foglio_passeggeri
            FOREIGN KEY (foglio_id) REFERENCES {$prefix}gm_fogli_di_marcia(id) ON DELETE CASCADE
        " );
    }

    // 7. Log Attività
    $sql = "CREATE TABLE {$prefix}gm_log_attivita (
        id INT AUTO_INCREMENT PRIMARY KEY,
        utente_id INT,
        azione VARCHAR(50) NOT NULL,
        tabella VARCHAR(100),
        record_id INT,
        dettaglio LONGTEXT,
        ip_address VARCHAR(45),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        KEY fk_utente_log (utente_id),
        KEY idx_tabella_record (tabella, record_id),
        KEY idx_created_at (created_at)
    ) {$charset_collate} ENGINE=InnoDB;";

    dbDelta( $sql );
}
