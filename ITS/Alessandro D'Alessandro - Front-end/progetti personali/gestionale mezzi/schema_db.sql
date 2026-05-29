-- ============================================================
--  SCHEMA DB — Sistema Schede Interventi / Fogli di Marcia
--  Versione: 1.1
--  Modifiche v1.1:
--    - Ruoli aggiornati: 'volontario', 'direttivo', 'amministrazione'
--    - Aggiunta tabella log_attivita per audit completo
-- ============================================================


-- ============================================================
--  1. CATEGORIE PATENTE
--  Rappresenta le abilitazioni di guida riconosciute.
--  I codici sono: B, C, MMT (Macchine Movimento Terra).
--
--  Regola di inclusione (gestita a livello applicativo):
--    - Patente C   → abilita anche i veicoli di categoria B
--    - Patente MMT → abilita anche i veicoli di categoria B
-- ============================================================
CREATE TABLE categorie_patente (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    codice      TEXT NOT NULL UNIQUE,   -- 'B', 'C', 'MMT'
    nome        TEXT NOT NULL,
    descrizione TEXT,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Dati iniziali fissi
INSERT INTO categorie_patente (codice, nome, descrizione) VALUES
    ('B',   'Patente B',
            'Abilitazione alla guida di autovetture e veicoli leggeri fino a 3.5t.'),
    ('C',   'Patente C',
            'Abilitazione alla guida di autocarri e veicoli pesanti oltre 3.5t. Include i veicoli di categoria B.'),
    ('MMT', 'Abilitazione Movimento Terra',
            'Patentino per la conduzione di macchine operatrici: escavatori, bobcat, pale meccaniche, ecc. (Accordo Stato-Regioni 22/02/2012). Include i veicoli di categoria B.');


-- ============================================================
--  2. CATEGORIE VEICOLO
--  Non descrive il "tipo" di veicolo (auto, furgone, ecc.)
--  ma esclusivamente quale patente è necessaria per guidarlo.
-- ============================================================
CREATE TABLE categorie_veicolo (
    id                    INTEGER PRIMARY KEY AUTOINCREMENT,
    patente_richiesta_id  INTEGER NOT NULL REFERENCES categorie_patente(id),
    note                  TEXT
);


-- ============================================================
--  3. VEICOLI
--  Anagrafica completa dei mezzi aziendali.
--  km_attuali è il dato "vivo": aggiornato ad ogni scheda
--  inviata (NON alle bozze).
-- ============================================================
CREATE TABLE veicoli (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    targa           TEXT NOT NULL UNIQUE,
    marca           TEXT NOT NULL,
    modello         TEXT NOT NULL,
    anno            INTEGER,
    categoria_id    INTEGER NOT NULL REFERENCES categorie_veicolo(id),
    posti_totali    INTEGER NOT NULL DEFAULT 2,
    -- I posti passeggeri nel form saranno: posti_totali - 1
    km_attuali      INTEGER NOT NULL DEFAULT 0,
    -- Aggiornato automaticamente all'invio definitivo di ogni scheda
    attivo          INTEGER NOT NULL DEFAULT 1,  -- 1 = attivo, 0 = dismesso
    note            TEXT,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP
);


-- ============================================================
--  4. UTENTI
--  Tutti i dipendenti hanno un account, indipendentemente
--  dal fatto che guidino o meno.
--
--  RUOLI:
--    'volontario'      → scrittura e modifica delle proprie schede, lettura
--    'direttivo'       → scrittura, modifica e lettura su tutto il sistema
--    'amministrazione' → accesso completo incluse cancellazioni e log
-- ============================================================
CREATE TABLE utenti (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    nome            TEXT NOT NULL,
    cognome         TEXT NOT NULL,
    email           TEXT NOT NULL UNIQUE,
    password_hash   TEXT NOT NULL,
    ruolo           TEXT NOT NULL DEFAULT 'volontario',
    -- Valori ammessi: 'volontario', 'direttivo', 'amministrazione'
    attivo          INTEGER NOT NULL DEFAULT 1,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP
);


-- ============================================================
--  5. UTENTI_PATENTI  (relazione many-to-many)
--  Collega ogni utente alle sue abilitazioni di guida.
--  Solo gli utenti con almeno una riga qui possono comparire
--  nel dropdown "Seleziona conducente" del form.
-- ============================================================
CREATE TABLE utenti_patenti (
    utente_id           INTEGER NOT NULL REFERENCES utenti(id),
    categoria_id        INTEGER NOT NULL REFERENCES categorie_patente(id),
    data_conseguimento  DATE,   -- opzionale ma utile per archivio
    data_scadenza       DATE,   -- utile soprattutto per il patentino MMT che scade
    PRIMARY KEY (utente_id, categoria_id)
);


-- ============================================================
--  6. FOGLI DI MARCIA
--  La tabella principale. Ogni riga è una scheda intervento.
--
--  Note importanti:
--    - numero + anno formano il codice visivo "46/2026"
--    - km_iniziali è copiato automaticamente da veicoli.km_attuali
--      al momento della creazione: è uno snapshot, non un input utente
--    - veicoli.km_attuali viene aggiornato solo al cambio di stato
--      da 'bozza' a 'inviata'
--    - creato_da può differire da conducente_id: un volontario
--      senza patente può compilare la scheda per un autista
-- ============================================================
CREATE TABLE fogli_di_marcia (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    numero              INTEGER NOT NULL,   -- sequenziale per anno (es: 46)
    anno                INTEGER NOT NULL,   -- es: 2026
    UNIQUE (numero, anno),                  -- garantisce unicità del codice "46/2026"

    -- Chi guida e quale mezzo
    conducente_id       INTEGER NOT NULL REFERENCES utenti(id),
    veicolo_id          INTEGER NOT NULL REFERENCES veicoli(id),

    -- Dettagli viaggio
    merci               TEXT,
    motivo              TEXT,
    richiesto_da        TEXT,
    richiesto_per       TEXT,

    -- Intervallo temporale
    data_inizio         DATETIME,
    data_fine           DATETIME,

    -- Chilometri
    -- km_iniziali: snapshot di veicoli.km_attuali alla creazione, non inserito dall'utente
    km_iniziali         INTEGER,
    km_finali           INTEGER,

    -- Consumi
    carburante_litri    REAL,
    carburante_prezzo   REAL,
    olio_litri          REAL,
    olio_prezzo         REAL,

    -- Metadati
    note                TEXT,
    stato               TEXT NOT NULL DEFAULT 'bozza', -- 'bozza' o 'inviata'
    creato_da           INTEGER NOT NULL REFERENCES utenti(id),
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP
);


-- ============================================================
--  7. FOGLIO_PASSEGGERI  (relazione many-to-many)
--  Collega ogni scheda ai suoi passeggeri.
--  Zero, uno o N passeggeri per scheda, fino a (posti_totali - 1).
--  La PRIMARY KEY composta impedisce duplicati sulla stessa scheda.
-- ============================================================
CREATE TABLE foglio_passeggeri (
    foglio_id       INTEGER NOT NULL REFERENCES fogli_di_marcia(id) ON DELETE CASCADE,
    -- ON DELETE CASCADE: cancellando una scheda si rimuovono anche i passeggeri collegati
    utente_id       INTEGER NOT NULL REFERENCES utenti(id),
    PRIMARY KEY (foglio_id, utente_id)
);


-- ============================================================
--  8. LOG_ATTIVITA
--  Registro immutabile di ogni azione rilevante nel sistema.
--  Popolato automaticamente dal backend — mai dall'utente.
--  Accessibile solo al ruolo 'amministrazione'.
--
--  Ogni riga risponde alle domande: Chi? Cosa? Su cosa? Quando?
--  Il campo 'dettaglio' può contenere un JSON con i valori
--  prima/dopo la modifica, utile per ricostruire la storia
--  completa di ogni record.
-- ============================================================
CREATE TABLE log_attivita (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    utente_id   INTEGER REFERENCES utenti(id),
    -- NULL se l'azione è di sistema (es: aggiornamento automatico km)
    azione      TEXT NOT NULL,
    -- Valori tipici: 'LOGIN', 'LOGOUT', 'CREA', 'MODIFICA', 'CANCELLA', 'INVIA_SCHEDA', 'SALVA_BOZZA'
    tabella     TEXT,
    -- La tabella coinvolta: 'fogli_di_marcia', 'utenti', 'veicoli', ecc.
    record_id   INTEGER,
    -- L'ID del record su cui è stata eseguita l'azione
    dettaglio   TEXT,
    -- JSON opzionale con snapshot dei dati prima/dopo la modifica
    ip_address  TEXT,
    -- Utile per rilevare accessi anomali o non autorizzati
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);


-- ============================================================
--  INDICI — migliorano la velocità delle query più frequenti
-- ============================================================

-- Ricerche per conducente (es: "tutte le schede di Mario Rossi")
CREATE INDEX idx_fogli_conducente ON fogli_di_marcia(conducente_id);

-- Ricerche per veicolo (es: "tutti i viaggi del furgone TargaXXX")
CREATE INDEX idx_fogli_veicolo    ON fogli_di_marcia(veicolo_id);

-- Ricerche per anno (es: "tutti i fogli del 2026")
CREATE INDEX idx_fogli_anno       ON fogli_di_marcia(anno);

-- Ricerche per stato (es: "tutte le bozze in sospeso")
CREATE INDEX idx_fogli_stato      ON fogli_di_marcia(stato);

-- Ricerche passeggeri per scheda
CREATE INDEX idx_passeggeri_foglio ON foglio_passeggeri(foglio_id);

-- Ricerche log per utente (es: "tutto ciò che ha fatto Mario nelle ultime 24h")
CREATE INDEX idx_log_utente       ON log_attivita(utente_id);

-- Ricerche log per tabella+record (es: "storia completa della scheda #46")
CREATE INDEX idx_log_tabella      ON log_attivita(tabella, record_id);

-- Ricerche log per data (es: "tutto ciò che è successo oggi")
CREATE INDEX idx_log_data         ON log_attivita(created_at);
