# 📚 Bibliò MVP — Guida completa di setup
### WordPress + WooCommerce + InfinityFree MySQL

---

## INDICE

1. [Struttura del database custom](#step-1--struttura-del-database-custom)
2. [5 Libri di prova — dati completi](#step-2--5-libri-di-prova)
3. [Plugin WordPress necessari](#step-3--plugin-necessari)
4. [Configurazione WooCommerce](#step-4--configurazione-woocommerce)
5. [Prodotti WooCommerce per i 5 libri](#step-5--prodotti-woocommerce)
6. [Theme child + CSS stile Bibliò](#step-6--theme-e-stile-bibliò)
7. [Pagina Catalogo (shortcode custom)](#step-7--pagina-catalogo)
8. [Pagina Singolo Libro](#step-8--pagina-singolo-libro)
9. [Libreria digitale utente](#step-9--libreria-digitale-utente)
10. [Chatbot MyBibliò](#step-10--chatbot-mybibliò)
11. [Automazioni e cron job](#step-11--automazioni)
12. [Checklist finale](#step-12--checklist-finale)

---

## STEP 1 — Struttura del database custom

InfinityFree fornisce un database MySQL già configurato. Devi solo creare le tabelle custom di Bibliò **in aggiunta** a quelle WordPress/WooCommerce.

### Come accedere al database
1. Vai su **InfinityFree Control Panel → MySQL Databases**
2. Prendi nota di: host, nome database, utente, password
3. Apri **phpMyAdmin** (link nel pannello InfinityFree)
4. Seleziona il tuo database, clicca su **SQL** e incolla le query qui sotto

### Query SQL — Tabelle custom Bibliò

```sql
-- Tabella modalità disponibili per ogni titolo
CREATE TABLE IF NOT EXISTS biblio_modalita (
  modalita_id    VARCHAR(50)  NOT NULL PRIMARY KEY,
  book_id        VARCHAR(50)  NOT NULL,
  tipo_modalita  ENUM('cartaceo','ebook_acquisto','ebook_noleggio') NOT NULL,
  prezzo         DECIMAL(10,2) DEFAULT NULL,
  attivo         TINYINT(1)   NOT NULL DEFAULT 1,
  woo_product_id BIGINT       DEFAULT NULL,
  created_at     DATETIME     DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_book_id (book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabella piani di noleggio
CREATE TABLE IF NOT EXISTS biblio_piani_noleggio (
  piano_id      VARCHAR(50)   NOT NULL PRIMARY KEY,
  modalita_id   VARCHAR(50)   NOT NULL,
  durata_giorni INT           NOT NULL,
  prezzo        DECIMAL(10,2) NOT NULL,
  attivo        TINYINT(1)    NOT NULL DEFAULT 1,
  created_at    DATETIME      DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_modalita_id (modalita_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabella accessi e-book per utente
CREATE TABLE IF NOT EXISTS biblio_accessi_ebook (
  accesso_id    BIGINT        NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id       BIGINT        NOT NULL,
  book_id       VARCHAR(50)   NOT NULL,
  modalita_id   VARCHAR(50)   NOT NULL,
  piano_id      VARCHAR(50)   DEFAULT NULL,
  tipo_accesso  ENUM('acquisto','noleggio') NOT NULL,
  data_inizio   DATETIME      NOT NULL,
  data_fine     DATETIME      DEFAULT NULL,
  stato         ENUM('attivo','scaduto','convertito') NOT NULL DEFAULT 'attivo',
  order_id      BIGINT        DEFAULT NULL,
  created_at    DATETIME      DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user_id (user_id),
  INDEX idx_book_id (book_id),
  INDEX idx_stato (stato)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabella storico conversioni noleggio → acquisto
CREATE TABLE IF NOT EXISTS biblio_conversioni (
  conversione_id     BIGINT        NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id            BIGINT        NOT NULL,
  book_id            VARCHAR(50)   NOT NULL,
  totale_noleggi     DECIMAL(10,2) NOT NULL DEFAULT 0,
  prezzo_acquisto    DECIMAL(10,2) NOT NULL,
  differenza_pagata  DECIMAL(10,2) NOT NULL,
  order_id_finale    BIGINT        DEFAULT NULL,
  created_at         DATETIME      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabella metadati libri (supplementa i post WordPress)
CREATE TABLE IF NOT EXISTS biblio_libri (
  book_id        VARCHAR(50)   NOT NULL PRIMARY KEY,
  wp_post_id     BIGINT        DEFAULT NULL,
  isbn           VARCHAR(20)   DEFAULT NULL,
  numero_pagine  INT           DEFAULT NULL,
  pdf_path       VARCHAR(500)  DEFAULT NULL,
  pdf_presente   TINYINT(1)    DEFAULT 0,
  created_at     DATETIME      DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_wp_post_id (wp_post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## STEP 2 — 5 Libri di prova

Questi sono i 5 libri inventati con tutti i dati della spec. Prima li aggiungi come **Custom Post Type** in WordPress (vedi Step 3), poi inserisci i dati SQL qui sotto.

### I 5 libri

| # | Titolo | Autore | Categoria | Pagine | ISBN |
|---|--------|--------|-----------|--------|------|
| 1 | L'algoritmo del silenzio | Elena Martori | Narrativa italiana | 312 | 978-88-001-0001-1 |
| 2 | Il confine delle maree | Marco Serafini | Romanzo storico | 448 | 978-88-001-0002-2 |
| 3 | Mappe per nessun luogo | Giulia Ferrante | Poesia contemporanea | 128 | 978-88-001-0003-3 |
| 4 | La fisica dei sogni | Alessandro Conti | Saggistica scientifica | 256 | 978-88-001-0004-4 |
| 5 | Tutto quello che brucia | Sara Vinci | Thriller | 384 | 978-88-001-0005-5 |

### SQL — Inserimento dati custom (esegui DOPO aver creato i post WordPress)

> Nota: i `wp_post_id` qui sotto sono placeholder — sostituiscili con gli ID reali dei post dopo averli creati.

```sql
-- Metadati libri
INSERT INTO biblio_libri (book_id, isbn, numero_pagine, pdf_presente) VALUES
('BLIB-001', '978-88-001-0001-1', 312,  0),
('BLIB-002', '978-88-001-0002-2', 448,  0),
('BLIB-003', '978-88-001-0003-3', 128,  0),
('BLIB-004', '978-88-001-0004-4', 256,  0),
('BLIB-005', '978-88-001-0005-5', 384,  0);

-- Modalità disponibili per ogni libro
-- BLIB-001: tutte e 3 le modalità
INSERT INTO biblio_modalita (modalita_id, book_id, tipo_modalita, prezzo, attivo) VALUES
('MOD-001-C',  'BLIB-001', 'cartaceo',        18.90, 1),
('MOD-001-EA', 'BLIB-001', 'ebook_acquisto',   9.99, 1),
('MOD-001-EN', 'BLIB-001', 'ebook_noleggio',   NULL, 1),
-- BLIB-002: cartaceo + ebook acquisto
('MOD-002-C',  'BLIB-002', 'cartaceo',        22.00, 1),
('MOD-002-EA', 'BLIB-002', 'ebook_acquisto',  11.99, 1),
-- BLIB-003: solo ebook (acquisto + noleggio)
('MOD-003-EA', 'BLIB-003', 'ebook_acquisto',   7.99, 1),
('MOD-003-EN', 'BLIB-003', 'ebook_noleggio',   NULL, 1),
-- BLIB-004: tutte e 3 le modalità
('MOD-004-C',  'BLIB-004', 'cartaceo',        20.00, 1),
('MOD-004-EA', 'BLIB-004', 'ebook_acquisto',   9.99, 1),
('MOD-004-EN', 'BLIB-004', 'ebook_noleggio',   NULL, 1),
-- BLIB-005: solo cartaceo e ebook acquisto
('MOD-005-C',  'BLIB-005', 'cartaceo',        16.90, 1),
('MOD-005-EA', 'BLIB-005', 'ebook_acquisto',   8.99, 1);

-- Piani di noleggio (per le modalità ebook_noleggio)
INSERT INTO biblio_piani_noleggio (piano_id, modalita_id, durata_giorni, prezzo, attivo) VALUES
-- Piani per BLIB-001
('PIANO-001-7',  'MOD-001-EN', 7,  1.99, 1),
('PIANO-001-30', 'MOD-001-EN', 30, 4.99, 1),
('PIANO-001-90', 'MOD-001-EN', 90, 9.99, 1),
-- Piani per BLIB-003
('PIANO-003-7',  'MOD-003-EN', 7,  1.49, 1),
('PIANO-003-30', 'MOD-003-EN', 30, 3.99, 1),
-- Piani per BLIB-004
('PIANO-004-7',  'MOD-004-EN', 7,  1.99, 1),
('PIANO-004-30', 'MOD-004-EN', 30, 4.99, 1),
('PIANO-004-90', 'MOD-004-EN', 90, 9.49, 1);
```

---

## STEP 3 — Plugin necessari

Installa tutti questi plugin da **WordPress Admin → Plugin → Aggiungi nuovo**:

### Plugin obbligatori (tutti gratuiti)

| Plugin | Scopo | Note |
|--------|-------|-------|
| **WooCommerce** | E-commerce base | Già installato ✓ |
| **Custom Post Type UI** (CPT UI) | Crea tipo "libro" | Cerca "CPT UI" |
| **Advanced Custom Fields (ACF)** | Campi custom per libri | Versione free OK |
| **WP PDF Viewer** o **PDF Embedder** | Reader PDF in browser | Cerca "PDF Embedder" |
| **WooCommerce PDF Invoices** | (opzionale) Fatture | Opzionale MVP |

### Plugin consigliati

| Plugin | Scopo |
|--------|-------|
| **Smash Balloon** o nessuno | — |
| **Wordfence** | Sicurezza base |
| **WP Crontrol** | Gestire cron job scadenza noleggi |

---

## STEP 4 — Configurazione WooCommerce

### 4.1 Impostazioni generali
1. Vai su **WooCommerce → Impostazioni → Generali**
2. Imposta valuta: **EUR (€)**
3. Posizione negozio: **Italia**

### 4.2 Metodi di pagamento
1. **WooCommerce → Impostazioni → Pagamenti**
2. Attiva **Stripe** (per carte) — installa plugin gratuito "WooCommerce Stripe Payment Gateway"
3. Attiva **PayPal** — già incluso in WooCommerce, configura con le credenziali sandbox prima

### 4.3 Spedizioni (solo cartaceo)
1. **WooCommerce → Impostazioni → Spedizioni**
2. Crea zona: **Italia**
3. Aggiungi metodo: **Tariffa fissa** → es. €4,90
4. Aggiungi metodo: **Spedizione gratuita** sopra €30

### 4.4 Categorie prodotto WooCommerce
Crea queste categorie in **Prodotti → Categorie**:
- `cartaceo`
- `ebook-acquisto`
- `ebook-noleggio`

---

## STEP 5 — Custom Post Type "libro" + ACF

### 5.1 Crea il CPT con CPT UI
1. Vai su **CPT UI → Aggiungi/Modifica tipi di articolo**
2. Slug: `libro`
3. Etichetta singolare: `Libro`, plurale: `Libri`
4. Supporta: Titolo, Editor, Miniatura, Estratto
5. **Salva tipo di articolo**

### 5.2 Crea i campi ACF per il CPT "libro"
1. **ACF → Gruppi di campi → Aggiungi nuovo**
2. Nome gruppo: `Metadati Libro`
3. Regola: mostra se **Tipo di articolo = libro**

Aggiungi questi campi:

| Nome campo | Tipo | Chiave |
|------------|------|--------|
| book_id | Testo | `book_id` |
| isbn | Testo | `isbn` |
| autore | Testo | `autore` |
| categoria | Testo | `categoria` |
| numero_pagine | Numero | `numero_pagine` |
| copertina | Immagine | `copertina` |
| descrizione_breve | Area di testo | `descrizione_breve` |
| ha_cartaceo | Vero/Falso | `ha_cartaceo` |
| ha_ebook_acquisto | Vero/Falso | `ha_ebook_acquisto` |
| ha_ebook_noleggio | Vero/Falso | `ha_ebook_noleggio` |
| prezzo_cartaceo | Numero | `prezzo_cartaceo` |
| prezzo_ebook_acquisto | Numero | `prezzo_ebook_acquisto` |
| woo_id_cartaceo | Numero | `woo_id_cartaceo` |
| woo_id_ebook_acquisto | Numero | `woo_id_ebook_acquisto` |

4. Pubblica il gruppo campi

### 5.3 Crea i 5 libri come post WordPress

Vai su **Libri → Aggiungi nuovo** e crea un post per ogni libro:

**Libro 1 — L'algoritmo del silenzio**
- Titolo: `L'algoritmo del silenzio`
- Contenuto (descrizione): *"In un futuro prossimo, Elena scopre che ogni decisione della sua vita è stata guidata da un algoritmo segreto. Un romanzo che interroga il confine tra libero arbitrio e determinismo digitale."*
- Campi ACF: book_id=`BLIB-001`, isbn=`978-88-001-0001-1`, autore=`Elena Martori`, categoria=`Narrativa italiana`, numero_pagine=`312`, ha_cartaceo=✓, ha_ebook_acquisto=✓, ha_ebook_noleggio=✓, prezzo_cartaceo=`18.90`, prezzo_ebook_acquisto=`9.99`

**Libro 2 — Il confine delle maree**
- Contenuto: *"Venezia, 1848. Tre famiglie di diverse origini si trovano unite dalla stessa rivoluzione e separate dallo stesso segreto. Un affresco storico potente e cinematografico."*
- ACF: book_id=`BLIB-002`, isbn=`978-88-001-0002-2`, autore=`Marco Serafini`, categoria=`Romanzo storico`, numero_pagine=`448`, ha_cartaceo=✓, ha_ebook_acquisto=✓, prezzo_cartaceo=`22.00`, prezzo_ebook_acquisto=`11.99`

**Libro 3 — Mappe per nessun luogo**
- Contenuto: *"Una raccolta di poesie che esplora la topografia dell'assenza. Ogni componimento è una mappa di un luogo che non esiste, ma che tutti abbiamo vissuto almeno una volta."*
- ACF: book_id=`BLIB-003`, isbn=`978-88-001-0003-3`, autore=`Giulia Ferrante`, categoria=`Poesia contemporanea`, numero_pagine=`128`, ha_ebook_acquisto=✓, ha_ebook_noleggio=✓, prezzo_ebook_acquisto=`7.99`

**Libro 4 — La fisica dei sogni**
- Contenuto: *"Cosa succederebbe se i sogni obbedissero alle leggi della termodinamica? Un neuroscienziato e una scrittrice esplorano le frontiere della coscienza con rigore scientifico e poetico."*
- ACF: book_id=`BLIB-004`, isbn=`978-88-001-0004-4`, autore=`Alessandro Conti`, categoria=`Saggistica scientifica`, numero_pagine=`256`, ha_cartaceo=✓, ha_ebook_acquisto=✓, ha_ebook_noleggio=✓, prezzo_cartaceo=`20.00`, prezzo_ebook_acquisto=`9.99`

**Libro 5 — Tutto quello che brucia**
- Contenuto: *"Una detective con un passato segnato indaga su una serie di incendi dolosi in una piccola città costiera. Il fuoco come metafora di tutto quello che non siamo riusciti a dire."*
- ACF: book_id=`BLIB-005`, isbn=`978-88-001-0005-5`, autore=`Sara Vinci`, categoria=`Thriller`, numero_pagine=`384`, ha_cartaceo=✓, ha_ebook_acquisto=✓, prezzo_cartaceo=`16.90`, prezzo_ebook_acquisto=`8.99`

### 5.4 Crea prodotti WooCommerce corrispondenti

Per ogni libro, crea i relativi **prodotti WooCommerce**:

**Esempio per Libro 1:**
- **Prodotto 1**: Nome=`L'algoritmo del silenzio — Cartaceo`, categoria=`cartaceo`, prezzo=`18.90`, Fisico=✓, aggiungi in descrizione `[BLIB-001]`
- **Prodotto 2**: Nome=`L'algoritmo del silenzio — eBook`, categoria=`ebook-acquisto`, prezzo=`9.99`, Virtuale=✓
- **Prodotto 3**: Nome=`L'algoritmo del silenzio — Noleggio 7gg`, categoria=`ebook-noleggio`, prezzo=`1.99`, Virtuale=✓
- **Prodotto 4**: Nome=`L'algoritmo del silenzio — Noleggio 30gg`, categoria=`ebook-noleggio`, prezzo=`4.99`, Virtuale=✓
- **Prodotto 5**: Nome=`L'algoritmo del silenzio — Noleggio 90gg`, categoria=`ebook-noleggio`, prezzo=`9.99`, Virtuale=✓

Dopo la creazione, aggiorna la tabella `biblio_modalita` con i `woo_product_id` reali:

```sql
UPDATE biblio_modalita SET woo_product_id = [ID_WOO] WHERE modalita_id = 'MOD-001-C';
```

---

## STEP 6 — Theme e stile Bibliò

### Stile scelto: "Editorial Contemporaneo"
Ispirato ai migliori bookshop europei: palette terracotta/avorio/inchiostro, tipografia serif elegante, layout editoriale con griglia asimmetrica. Più raffinato di IBS, più moderno di Feltrinelli, più pulito di Libraccio.

### 6.1 Installa un tema base
1. Vai su **Aspetto → Temi**
2. Installa tema: **Astra** (leggero e personalizzabile, gratuito)
3. Attivalo

### 6.2 Crea un tema figlio (child theme)

Vai su **Aspetto → Editor di file del tema** (oppure via FTP/File Manager InfinityFree):

Crea la cartella `/wp-content/themes/astra-biblio/` con questi file:

**`style.css`**
```css
/*
 Theme Name: Bibliò Child
 Template: astra
 Version: 1.0
*/

/* ========================================
   BIBLIÒ — Design System
   Palette: Carta + Inchiostro + Terracotta
   ======================================== */

@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap');

:root {
  --color-carta:      #F5F0E8;
  --color-avorio:     #FDFAF4;
  --color-inchiostro: #1C1C1E;
  --color-terracotta: #C4614A;
  --color-terra-dark: #9E4836;
  --color-grigio-caldo:#7A7469;
  --color-sabbia:     #E8E0D0;
  --color-bianco:     #FFFFFF;

  --font-display: 'Playfair Display', Georgia, serif;
  --font-body:    'Lato', system-ui, sans-serif;

  --radius-card: 4px;
  --shadow-card: 0 2px 12px rgba(28,28,30,0.08);
  --shadow-hover: 0 8px 30px rgba(28,28,30,0.16);
  --transition: 0.25s cubic-bezier(0.4,0,0.2,1);
}

body {
  background: var(--color-avorio);
  color: var(--color-inchiostro);
  font-family: var(--font-body);
  font-size: 16px;
  line-height: 1.65;
}

/* ---- HEADER ---- */
.site-header {
  background: var(--color-inchiostro) !important;
  padding: 0 !important;
}

.biblio-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 40px;
  max-width: 1280px;
  margin: 0 auto;
}

.biblio-logo {
  font-family: var(--font-display);
  font-size: 28px;
  font-weight: 700;
  color: var(--color-avorio) !important;
  text-decoration: none;
  letter-spacing: -0.5px;
}

.biblio-logo span {
  color: var(--color-terracotta);
}

.biblio-nav a {
  color: var(--color-sabbia) !important;
  font-size: 14px;
  font-weight: 400;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  margin-left: 32px;
  text-decoration: none;
  transition: color var(--transition);
}

.biblio-nav a:hover {
  color: var(--color-terracotta) !important;
}

/* ---- HERO CATALOGO ---- */
.biblio-hero {
  background: var(--color-inchiostro);
  color: var(--color-avorio);
  padding: 80px 40px 64px;
  text-align: center;
}

.biblio-hero h1 {
  font-family: var(--font-display);
  font-size: clamp(36px, 6vw, 72px);
  font-weight: 700;
  font-style: italic;
  line-height: 1.1;
  margin-bottom: 16px;
  color: var(--color-avorio);
}

.biblio-hero p {
  font-size: 18px;
  color: var(--color-grigio-caldo);
  max-width: 560px;
  margin: 0 auto 32px;
}

/* ---- GRIGLIA CATALOGO ---- */
.biblio-catalogo {
  max-width: 1280px;
  margin: 0 auto;
  padding: 48px 40px;
}

.biblio-filtri {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 40px;
  padding-bottom: 24px;
  border-bottom: 1px solid var(--color-sabbia);
}

.biblio-filtro-btn {
  background: transparent;
  border: 1.5px solid var(--color-sabbia);
  color: var(--color-grigio-caldo);
  padding: 8px 20px;
  border-radius: 24px;
  font-family: var(--font-body);
  font-size: 13px;
  cursor: pointer;
  transition: all var(--transition);
}

.biblio-filtro-btn:hover,
.biblio-filtro-btn.active {
  background: var(--color-terracotta);
  border-color: var(--color-terracotta);
  color: white;
}

.biblio-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 32px;
}

/* ---- CARD LIBRO ---- */
.biblio-card {
  background: var(--color-bianco);
  border-radius: var(--radius-card);
  overflow: hidden;
  box-shadow: var(--shadow-card);
  transition: transform var(--transition), box-shadow var(--transition);
  cursor: pointer;
  text-decoration: none;
  display: block;
  color: inherit;
}

.biblio-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-hover);
}

.biblio-card-cover {
  width: 100%;
  aspect-ratio: 2/3;
  object-fit: cover;
  background: var(--color-sabbia);
  display: block;
}

.biblio-card-cover-placeholder {
  width: 100%;
  aspect-ratio: 2/3;
  background: linear-gradient(135deg, var(--color-sabbia) 0%, var(--color-carta) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  text-align: center;
}

.biblio-card-cover-placeholder span {
  font-family: var(--font-display);
  font-size: 14px;
  color: var(--color-grigio-caldo);
  font-style: italic;
  line-height: 1.4;
}

.biblio-card-body {
  padding: 16px;
}

.biblio-card-categoria {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: var(--color-terracotta);
  font-weight: 700;
  margin-bottom: 4px;
}

.biblio-card-titolo {
  font-family: var(--font-display);
  font-size: 16px;
  font-weight: 700;
  line-height: 1.3;
  margin-bottom: 4px;
  color: var(--color-inchiostro);
}

.biblio-card-autore {
  font-size: 13px;
  color: var(--color-grigio-caldo);
  margin-bottom: 12px;
}

.biblio-card-badges {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  margin-bottom: 10px;
}

.biblio-badge {
  font-size: 10px;
  padding: 3px 8px;
  border-radius: 3px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.biblio-badge-cartaceo   { background: #E8F4E8; color: #2D6A2D; }
.biblio-badge-ebook      { background: #E8EEF8; color: #2D4A8A; }
.biblio-badge-noleggio   { background: #FFF0E8; color: #8A4A2D; }

.biblio-card-prezzo {
  font-family: var(--font-display);
  font-size: 18px;
  font-weight: 700;
  color: var(--color-terracotta);
}

.biblio-card-prezzo small {
  font-size: 12px;
  font-weight: 400;
  color: var(--color-grigio-caldo);
  font-family: var(--font-body);
}

/* ---- PAGINA SINGOLO LIBRO ---- */
.biblio-singolo {
  max-width: 1100px;
  margin: 0 auto;
  padding: 60px 40px;
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 60px;
}

.biblio-singolo-cover {
  width: 100%;
  border-radius: var(--radius-card);
  box-shadow: var(--shadow-hover);
}

.biblio-singolo-categoria {
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: var(--color-terracotta);
  font-weight: 700;
  margin-bottom: 8px;
}

.biblio-singolo-titolo {
  font-family: var(--font-display);
  font-size: 36px;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 8px;
  color: var(--color-inchiostro);
}

.biblio-singolo-autore {
  font-size: 18px;
  color: var(--color-grigio-caldo);
  margin-bottom: 24px;
}

.biblio-singolo-meta {
  display: flex;
  gap: 24px;
  margin-bottom: 24px;
  padding: 16px 0;
  border-top: 1px solid var(--color-sabbia);
  border-bottom: 1px solid var(--color-sabbia);
  font-size: 13px;
  color: var(--color-grigio-caldo);
}

.biblio-singolo-meta strong {
  display: block;
  color: var(--color-inchiostro);
  font-size: 14px;
}

.biblio-singolo-descrizione {
  font-size: 16px;
  line-height: 1.75;
  color: var(--color-inchiostro);
  margin-bottom: 32px;
}

/* Selettore modalità */
.biblio-modalita-selector {
  margin-bottom: 32px;
}

.biblio-modalita-selector h3 {
  font-family: var(--font-display);
  font-size: 20px;
  margin-bottom: 16px;
}

.biblio-modalita-tabs {
  display: flex;
  gap: 0;
  border-radius: 4px;
  overflow: hidden;
  border: 1.5px solid var(--color-sabbia);
  width: fit-content;
  margin-bottom: 24px;
}

.biblio-modalita-tab {
  padding: 10px 20px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 700;
  background: white;
  border: none;
  color: var(--color-grigio-caldo);
  transition: all var(--transition);
}

.biblio-modalita-tab.active {
  background: var(--color-inchiostro);
  color: white;
}

.biblio-modalita-panel {
  display: none;
}

.biblio-modalita-panel.active {
  display: block;
}

.biblio-piani-noleggio {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 20px;
}

.biblio-piano {
  border: 1.5px solid var(--color-sabbia);
  border-radius: 4px;
  padding: 14px 20px;
  cursor: pointer;
  transition: all var(--transition);
  text-align: center;
  min-width: 120px;
}

.biblio-piano:hover,
.biblio-piano.selected {
  border-color: var(--color-terracotta);
  background: #FFF5F3;
}

.biblio-piano-durata {
  font-family: var(--font-display);
  font-size: 20px;
  font-weight: 700;
  display: block;
}

.biblio-piano-giorni {
  font-size: 12px;
  color: var(--color-grigio-caldo);
  display: block;
  margin-bottom: 8px;
}

.biblio-piano-prezzo {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-terracotta);
}

/* Bottone aggiungi al carrello */
.biblio-btn-acquista {
  background: var(--color-terracotta);
  color: white;
  border: none;
  padding: 16px 40px;
  font-size: 16px;
  font-weight: 700;
  font-family: var(--font-body);
  border-radius: 4px;
  cursor: pointer;
  transition: background var(--transition);
  text-decoration: none;
  display: inline-block;
  width: 100%;
  text-align: center;
  margin-bottom: 12px;
}

.biblio-btn-acquista:hover {
  background: var(--color-terra-dark);
  color: white;
}

/* ---- LIBRERIA DIGITALE ---- */
.biblio-libreria {
  max-width: 1100px;
  margin: 0 auto;
  padding: 48px 40px;
}

.biblio-libreria-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 24px;
}

.biblio-accesso-card {
  background: white;
  border-radius: var(--radius-card);
  padding: 20px;
  box-shadow: var(--shadow-card);
  display: flex;
  gap: 16px;
}

.biblio-accesso-cover {
  width: 70px;
  min-width: 70px;
  aspect-ratio: 2/3;
  object-fit: cover;
  border-radius: 2px;
  background: var(--color-sabbia);
}

.biblio-accesso-info h4 {
  font-family: var(--font-display);
  font-size: 15px;
  margin-bottom: 4px;
}

.biblio-accesso-stato {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 2px 8px;
  border-radius: 3px;
  display: inline-block;
  margin-bottom: 8px;
}

.stato-attivo   { background: #E8F4E8; color: #2D6A2D; }
.stato-scaduto  { background: #F4E8E8; color: #6A2D2D; }

.biblio-accesso-actions {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-top: 10px;
}

.biblio-btn-sm {
  padding: 7px 14px;
  font-size: 12px;
  font-weight: 700;
  border: none;
  border-radius: 3px;
  cursor: pointer;
  text-align: center;
  text-decoration: none;
  display: block;
}

.btn-leggi     { background: var(--color-inchiostro); color: white; }
.btn-rinnova   { background: var(--color-sabbia); color: var(--color-inchiostro); }
.btn-acquista  { background: var(--color-terracotta); color: white; }

/* ---- CHATBOT ---- */
.biblio-chatbot-fab {
  position: fixed;
  bottom: 32px;
  right: 32px;
  width: 56px;
  height: 56px;
  background: var(--color-inchiostro);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 20px rgba(28,28,30,0.3);
  transition: transform var(--transition);
  z-index: 9999;
  border: none;
  font-size: 24px;
}

.biblio-chatbot-fab:hover {
  transform: scale(1.1);
}

.biblio-chatbot-window {
  position: fixed;
  bottom: 100px;
  right: 32px;
  width: 360px;
  max-height: 520px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 12px 48px rgba(28,28,30,0.2);
  display: flex;
  flex-direction: column;
  z-index: 9998;
  overflow: hidden;
  transition: all var(--transition);
}

.biblio-chatbot-window.hidden {
  display: none;
}

.biblio-chatbot-header {
  background: var(--color-inchiostro);
  color: white;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.biblio-chatbot-header h4 {
  font-family: var(--font-display);
  font-size: 16px;
  font-weight: 700;
  margin: 0;
  color: white;
}

.biblio-chatbot-header span {
  font-size: 12px;
  color: var(--color-grigio-caldo);
}

.biblio-chatbot-messages {
  flex: 1;
  padding: 16px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 12px;
  background: var(--color-avorio);
}

.biblio-msg {
  max-width: 80%;
  padding: 10px 14px;
  border-radius: 12px;
  font-size: 14px;
  line-height: 1.5;
}

.biblio-msg-bot {
  background: white;
  color: var(--color-inchiostro);
  border-radius: 12px 12px 12px 0;
  box-shadow: var(--shadow-card);
  align-self: flex-start;
}

.biblio-msg-user {
  background: var(--color-terracotta);
  color: white;
  border-radius: 12px 12px 0 12px;
  align-self: flex-end;
}

.biblio-chatbot-input {
  display: flex;
  padding: 12px;
  border-top: 1px solid var(--color-sabbia);
  gap: 8px;
  background: white;
}

.biblio-chatbot-input input {
  flex: 1;
  border: 1.5px solid var(--color-sabbia);
  border-radius: 24px;
  padding: 10px 16px;
  font-size: 14px;
  font-family: var(--font-body);
  outline: none;
  transition: border-color var(--transition);
}

.biblio-chatbot-input input:focus {
  border-color: var(--color-terracotta);
}

.biblio-chatbot-input button {
  background: var(--color-terracotta);
  color: white;
  border: none;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  cursor: pointer;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background var(--transition);
}

.biblio-chatbot-input button:hover {
  background: var(--color-terra-dark);
}

/* ---- RESPONSIVE ---- */
@media (max-width: 768px) {
  .biblio-header { padding: 14px 20px; }
  .biblio-nav { display: none; }
  .biblio-hero { padding: 48px 20px; }
  .biblio-catalogo { padding: 32px 20px; }
  .biblio-singolo {
    grid-template-columns: 1fr;
    padding: 32px 20px;
    gap: 32px;
  }
  .biblio-singolo-cover { max-width: 200px; }
  .biblio-chatbot-window { width: calc(100vw - 32px); right: 16px; }
}
```

**`functions.php`**
```php
<?php
// Bibliò Child Theme - functions.php

// Eredita stili dal tema padre Astra
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('biblio-style', get_stylesheet_directory_uri() . '/style.css', ['parent-style']);
});

// Registra lo script del chatbot
add_action('wp_enqueue_scripts', function() {
    if (is_user_logged_in()) {
        wp_enqueue_script('biblio-chatbot', get_stylesheet_directory_uri() . '/js/chatbot.js', ['jquery'], '1.0', true);
        wp_localize_script('biblio-chatbot', 'biblioChatbot', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('biblio_chatbot'),
        ]);
    }
});
```

Crea anche la cartella `/wp-content/themes/astra-biblio/js/` per i file JavaScript.

---

## STEP 7 — Pagina Catalogo

### 7.1 Crea la pagina
1. **Pagine → Aggiungi nuova**
2. Titolo: `Catalogo`
3. Imposta come pagina statica del sito: **Impostazioni → Lettura → Home page statica → scegli questa pagina** (oppure aggiungi link nel menu)

### 7.2 Aggiungi questo codice nel tuo plugin custom

Vai su **Plugin → Editor plugin** (o crea un file `biblio-mvp.php` via file manager InfinityFree):

Percorso: `/wp-content/plugins/biblio-mvp/biblio-mvp.php`

```php
<?php
/**
 * Plugin Name: Bibliò MVP
 * Description: Funzionalità core per l'e-commerce librario Bibliò
 * Version: 1.0
 */

if (!defined('ABSPATH')) exit;

// ========================
// SHORTCODE: Catalogo libri
// ========================
add_shortcode('biblio_catalogo', function() {
    $libri = get_posts([
        'post_type'      => 'libro',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ]);

    ob_start();
    ?>
    <div class="biblio-hero">
        <h1>Il nostro catalogo</h1>
        <p>Scopri i titoli disponibili, acquista o noleggia il tuo prossimo libro</p>
    </div>

    <div class="biblio-catalogo">
        <div class="biblio-filtri">
            <button class="biblio-filtro-btn active" data-filter="tutti">Tutti</button>
            <button class="biblio-filtro-btn" data-filter="cartaceo">📦 Cartaceo</button>
            <button class="biblio-filtro-btn" data-filter="ebook">📱 eBook</button>
            <button class="biblio-filtro-btn" data-filter="noleggio">⏱ Noleggio</button>
        </div>

        <div class="biblio-grid">
        <?php foreach ($libri as $libro):
            $ha_cartaceo  = get_field('ha_cartaceo', $libro->ID);
            $ha_ebook_acq = get_field('ha_ebook_acquisto', $libro->ID);
            $ha_noleggio  = get_field('ha_ebook_noleggio', $libro->ID);
            $autore       = get_field('autore', $libro->ID);
            $categoria    = get_field('categoria', $libro->ID);
            $prezzo_c     = get_field('prezzo_cartaceo', $libro->ID);
            $prezzo_e     = get_field('prezzo_ebook_acquisto', $libro->ID);
            $copertina    = get_field('copertina', $libro->ID);
            
            // Prezzo più basso da mostrare
            $prezzi = array_filter([$prezzo_c, $prezzo_e]);
            $prezzo_min = !empty($prezzi) ? min($prezzi) : null;

            $data_filter = [];
            if ($ha_cartaceo)  $data_filter[] = 'cartaceo';
            if ($ha_ebook_acq || $ha_noleggio) $data_filter[] = 'ebook';
            if ($ha_noleggio)  $data_filter[] = 'noleggio';
        ?>
            <a href="<?= get_permalink($libro->ID) ?>" class="biblio-card" data-filter="<?= implode(' ', $data_filter) ?>">
                <?php if ($copertina): ?>
                    <img class="biblio-card-cover" src="<?= esc_url($copertina['url']) ?>" alt="<?= esc_attr($libro->post_title) ?>">
                <?php else: ?>
                    <div class="biblio-card-cover-placeholder">
                        <span><?= esc_html($libro->post_title) ?></span>
                    </div>
                <?php endif; ?>
                <div class="biblio-card-body">
                    <div class="biblio-card-categoria"><?= esc_html($categoria) ?></div>
                    <div class="biblio-card-titolo"><?= esc_html($libro->post_title) ?></div>
                    <div class="biblio-card-autore"><?= esc_html($autore) ?></div>
                    <div class="biblio-card-badges">
                        <?php if ($ha_cartaceo):  ?><span class="biblio-badge biblio-badge-cartaceo">Cartaceo</span><?php endif; ?>
                        <?php if ($ha_ebook_acq): ?><span class="biblio-badge biblio-badge-ebook">eBook</span><?php endif; ?>
                        <?php if ($ha_noleggio):  ?><span class="biblio-badge biblio-badge-noleggio">Noleggio</span><?php endif; ?>
                    </div>
                    <?php if ($prezzo_min): ?>
                    <div class="biblio-card-prezzo">
                        da €<?= number_format($prezzo_min, 2, ',', '.') ?>
                    </div>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
        </div>
    </div>

    <script>
    document.querySelectorAll('.biblio-filtro-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.biblio-filtro-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            document.querySelectorAll('.biblio-card').forEach(card => {
                if (filter === 'tutti' || card.dataset.filter.includes(filter)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
});
```

### 7.3 Usa lo shortcode nella pagina catalogo
Nel contenuto della pagina **Catalogo**, aggiungi: `[biblio_catalogo]`

---

## STEP 8 — Pagina Singolo Libro

### 8.1 Template singolo libro

Crea il file `/wp-content/themes/astra-biblio/single-libro.php`:

```php
<?php get_header(); ?>

<?php if (have_posts()): while (have_posts()): the_post();
    $book_id      = get_field('book_id');
    $autore       = get_field('autore');
    $categoria    = get_field('categoria');
    $isbn         = get_field('isbn');
    $pagine       = get_field('numero_pagine');
    $copertina    = get_field('copertina');
    $ha_cartaceo  = get_field('ha_cartaceo');
    $ha_ebook_acq = get_field('ha_ebook_acquisto');
    $ha_noleggio  = get_field('ha_ebook_noleggio');
    $prezzo_c     = get_field('prezzo_cartaceo');
    $prezzo_e     = get_field('prezzo_ebook_acquisto');
    $woo_c        = get_field('woo_id_cartaceo');
    $woo_e        = get_field('woo_id_ebook_acquisto');

    // Recupera piani noleggio dal DB
    global $wpdb;
    $piani = [];
    if ($ha_noleggio && $book_id) {
        $piani = $wpdb->get_results($wpdb->prepare("
            SELECT pn.* FROM biblio_piani_noleggio pn
            JOIN biblio_modalita m ON pn.modalita_id = m.modalita_id
            WHERE m.book_id = %s AND m.tipo_modalita = 'ebook_noleggio' AND pn.attivo = 1
            ORDER BY pn.durata_giorni ASC
        ", $book_id));
    }
?>

<div class="biblio-singolo">
    <!-- Colonna sinistra: copertina -->
    <div>
        <?php if ($copertina): ?>
            <img class="biblio-singolo-cover" src="<?= esc_url($copertina['url']) ?>" alt="<?= esc_attr(get_the_title()) ?>">
        <?php else: ?>
            <div style="width:100%;aspect-ratio:2/3;background:var(--color-sabbia);border-radius:4px;display:flex;align-items:center;justify-content:center;padding:20px;text-align:center;">
                <span style="font-family:var(--font-display);font-style:italic;color:var(--color-grigio-caldo);"><?= get_the_title() ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Colonna destra: info + acquisto -->
    <div>
        <div class="biblio-singolo-categoria"><?= esc_html($categoria) ?></div>
        <h1 class="biblio-singolo-titolo"><?= get_the_title() ?></h1>
        <div class="biblio-singolo-autore">di <?= esc_html($autore) ?></div>

        <div class="biblio-singolo-meta">
            <?php if ($isbn): ?><div><strong><?= esc_html($isbn) ?></strong>ISBN</div><?php endif; ?>
            <?php if ($pagine): ?><div><strong><?= esc_html($pagine) ?></strong>pagine</div><?php endif; ?>
            <div><strong><?= esc_html($categoria) ?></strong>categoria</div>
        </div>

        <div class="biblio-singolo-descrizione">
            <?= wpautop(get_the_content()) ?>
        </div>

        <!-- Selettore modalità -->
        <div class="biblio-modalita-selector">
            <h3>Scegli come vuoi leggerlo</h3>

            <div class="biblio-modalita-tabs">
                <?php if ($ha_cartaceo):  ?><button class="biblio-modalita-tab active" data-tab="cartaceo">📦 Cartaceo</button><?php endif; ?>
                <?php if ($ha_ebook_acq): ?><button class="biblio-modalita-tab <?= !$ha_cartaceo ? 'active' : '' ?>" data-tab="ebook">📱 eBook</button><?php endif; ?>
                <?php if ($ha_noleggio):  ?><button class="biblio-modalita-tab" data-tab="noleggio">⏱ Noleggio</button><?php endif; ?>
            </div>

            <!-- Panel Cartaceo -->
            <?php if ($ha_cartaceo): ?>
            <div class="biblio-modalita-panel active" id="panel-cartaceo">
                <p style="color:var(--color-grigio-caldo);font-size:14px;margin-bottom:16px;">Libro fisico con spedizione in Italia</p>
                <div style="font-family:var(--font-display);font-size:32px;font-weight:700;color:var(--color-terracotta);margin-bottom:20px;">
                    €<?= number_format($prezzo_c, 2, ',', '.') ?>
                </div>
                <?php if (is_user_logged_in() && $woo_c): ?>
                    <a href="<?= esc_url(wc_get_cart_url() . '?add-to-cart=' . $woo_c) ?>" class="biblio-btn-acquista">
                        Aggiungi al carrello
                    </a>
                <?php elseif (!is_user_logged_in()): ?>
                    <a href="<?= wp_login_url(get_permalink()) ?>" class="biblio-btn-acquista">
                        Accedi per acquistare
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Panel eBook acquisto -->
            <?php if ($ha_ebook_acq): ?>
            <div class="biblio-modalita-panel <?= !$ha_cartaceo ? 'active' : '' ?>" id="panel-ebook">
                <p style="color:var(--color-grigio-caldo);font-size:14px;margin-bottom:16px;">Accesso permanente, leggibile nella piattaforma</p>
                <div style="font-family:var(--font-display);font-size:32px;font-weight:700;color:var(--color-terracotta);margin-bottom:20px;">
                    €<?= number_format($prezzo_e, 2, ',', '.') ?>
                </div>
                <?php if (is_user_logged_in() && $woo_e): ?>
                    <a href="<?= esc_url(wc_get_cart_url() . '?add-to-cart=' . $woo_e) ?>" class="biblio-btn-acquista">
                        Acquista eBook
                    </a>
                <?php elseif (!is_user_logged_in()): ?>
                    <a href="<?= wp_login_url(get_permalink()) ?>" class="biblio-btn-acquista">
                        Accedi per acquistare
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Panel Noleggio -->
            <?php if ($ha_noleggio && !empty($piani)): ?>
            <div class="biblio-modalita-panel" id="panel-noleggio">
                <p style="color:var(--color-grigio-caldo);font-size:14px;margin-bottom:16px;">Accesso temporaneo — scegli la durata</p>
                <div class="biblio-piani-noleggio">
                    <?php foreach ($piani as $i => $piano): ?>
                    <div class="biblio-piano <?= $i === 0 ? 'selected' : '' ?>"
                         data-piano-id="<?= esc_attr($piano->piano_id) ?>"
                         data-prezzo="<?= esc_attr($piano->prezzo) ?>">
                        <span class="biblio-piano-durata"><?= $piano->durata_giorni ?></span>
                        <span class="biblio-piano-giorni">giorni</span>
                        <span class="biblio-piano-prezzo">€<?= number_format($piano->prezzo, 2, ',', '.') ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if (is_user_logged_in()): ?>
                    <button class="biblio-btn-acquista" id="btn-noleggia" data-book="<?= esc_attr($book_id) ?>">
                        Noleggia ora
                    </button>
                <?php else: ?>
                    <a href="<?= wp_login_url(get_permalink()) ?>" class="biblio-btn-acquista">
                        Accedi per noleggiare
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div><!-- fine modalita-selector -->
    </div>
</div>

<script>
// Tab switcher
document.querySelectorAll('.biblio-modalita-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.biblio-modalita-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.biblio-modalita-panel').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('panel-' + this.dataset.tab).classList.add('active');
    });
});

// Selezione piano noleggio
document.querySelectorAll('.biblio-piano').forEach(piano => {
    piano.addEventListener('click', function() {
        document.querySelectorAll('.biblio-piano').forEach(p => p.classList.remove('selected'));
        this.classList.add('selected');
    });
});
</script>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
```

---

## STEP 9 — Libreria digitale utente

### 9.1 Crea la pagina "La mia libreria"

1. **Pagine → Aggiungi nuova**, titolo: `La mia libreria`
2. Contenuto: `[biblio_libreria]`

### 9.2 Shortcode libreria nel plugin `biblio-mvp.php`

```php
// ========================
// SHORTCODE: Libreria digitale
// ========================
add_shortcode('biblio_libreria', function() {
    if (!is_user_logged_in()) {
        return '<p>Devi essere <a href="' . wp_login_url() . '">loggato</a> per vedere la tua libreria.</p>';
    }

    global $wpdb;
    $user_id = get_current_user_id();

    $accessi = $wpdb->get_results($wpdb->prepare("
        SELECT a.*, b.isbn, b.numero_pagine, b.pdf_path
        FROM biblio_accessi_ebook a
        LEFT JOIN biblio_libri b ON a.book_id = b.book_id
        WHERE a.user_id = %d
        ORDER BY a.data_inizio DESC
    ", $user_id));

    ob_start();
    ?>
    <div class="biblio-libreria">
        <h2 style="font-family:var(--font-display);font-size:32px;margin-bottom:32px;">La mia libreria</h2>

        <?php if (empty($accessi)): ?>
            <p style="color:var(--color-grigio-caldo);">La tua libreria è ancora vuota. <a href="/catalogo">Scopri il catalogo!</a></p>
        <?php else: ?>
        <div class="biblio-libreria-grid">
            <?php foreach ($accessi as $accesso):
                // Recupera dati libro dal CPT WordPress
                $args = [
                    'post_type'  => 'libro',
                    'meta_query' => [['key' => 'book_id', 'value' => $accesso->book_id]],
                    'posts_per_page' => 1,
                ];
                $libri = get_posts($args);
                $libro = !empty($libri) ? $libri[0] : null;
                $copertina = $libro ? get_field('copertina', $libro->ID) : null;
                $autore    = $libro ? get_field('autore', $libro->ID) : '';
                $titolo    = $libro ? $libro->post_title : $accesso->book_id;

                // Stato accesso
                $is_scaduto = ($accesso->tipo_accesso === 'noleggio' && $accesso->data_fine && strtotime($accesso->data_fine) < time());
                $stato_label = $is_scaduto ? 'Scaduto' : 'Attivo';
                $stato_class = $is_scaduto ? 'stato-scaduto' : 'stato-attivo';
            ?>
            <div class="biblio-accesso-card">
                <?php if ($copertina): ?>
                    <img class="biblio-accesso-cover" src="<?= esc_url($copertina['url']) ?>" alt="">
                <?php else: ?>
                    <div class="biblio-accesso-cover" style="background:var(--color-sabbia);border-radius:2px;"></div>
                <?php endif; ?>
                <div class="biblio-accesso-info">
                    <h4><?= esc_html($titolo) ?></h4>
                    <small style="color:var(--color-grigio-caldo);"><?= esc_html($autore) ?></small><br><br>
                    <span class="biblio-accesso-stato <?= $stato_class ?>"><?= $stato_label ?></span>
                    <?php if ($accesso->tipo_accesso === 'noleggio' && $accesso->data_fine): ?>
                        <div style="font-size:12px;color:var(--color-grigio-caldo);margin-top:4px;">
                            <?= $is_scaduto ? 'Scaduto il' : 'Scade il' ?> <?= date('d/m/Y', strtotime($accesso->data_fine)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="biblio-accesso-actions">
                        <?php if (!$is_scaduto && $accesso->pdf_path): ?>
                            <a href="/leggi?accesso=<?= $accesso->accesso_id ?>" class="biblio-btn-sm btn-leggi">📖 Leggi</a>
                        <?php endif; ?>
                        <?php if ($accesso->tipo_accesso === 'noleggio' && !$is_scaduto): ?>
                            <a href="/rinnova?book=<?= esc_attr($accesso->book_id) ?>" class="biblio-btn-sm btn-rinnova">🔄 Rinnova</a>
                        <?php endif; ?>
                        <?php if ($accesso->tipo_accesso === 'noleggio' && $accesso->stato !== 'convertito'): ?>
                            <a href="/converti?accesso=<?= $accesso->accesso_id ?>" class="biblio-btn-sm btn-acquista">⭐ Acquista definitivo</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
});
```

### 9.3 Automazione WooCommerce: attivazione accesso dopo pagamento

Nel plugin `biblio-mvp.php`, aggiungi:

```php
// ========================
// HOOK: Ordine completato → attiva accesso ebook
// ========================
add_action('woocommerce_order_status_completed', function($order_id) {
    global $wpdb;
    $order = wc_get_order($order_id);
    $user_id = $order->get_user_id();

    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        
        // Cerca se questo prodotto è un ebook o noleggio in biblio_modalita
        $modalita = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM biblio_modalita WHERE woo_product_id = %d AND attivo = 1",
            $product_id
        ));

        if (!$modalita) continue;

        if ($modalita->tipo_modalita === 'ebook_acquisto') {
            $wpdb->insert('biblio_accessi_ebook', [
                'user_id'      => $user_id,
                'book_id'      => $modalita->book_id,
                'modalita_id'  => $modalita->modalita_id,
                'tipo_accesso' => 'acquisto',
                'data_inizio'  => current_time('mysql'),
                'stato'        => 'attivo',
                'order_id'     => $order_id,
            ]);
        }

        if ($modalita->tipo_modalita === 'ebook_noleggio') {
            // Recupera piano dal meta dell'item (devi salvarlo al checkout)
            $piano_id = $item->get_meta('piano_id');
            $piano = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM biblio_piani_noleggio WHERE piano_id = %s",
                $piano_id
            ));
            
            if ($piano) {
                $data_fine = date('Y-m-d H:i:s', strtotime('+' . $piano->durata_giorni . ' days'));
                $wpdb->insert('biblio_accessi_ebook', [
                    'user_id'      => $user_id,
                    'book_id'      => $modalita->book_id,
                    'modalita_id'  => $modalita->modalita_id,
                    'piano_id'     => $piano_id,
                    'tipo_accesso' => 'noleggio',
                    'data_inizio'  => current_time('mysql'),
                    'data_fine'    => $data_fine,
                    'stato'        => 'attivo',
                    'order_id'     => $order_id,
                ]);
            }
        }
    }
});
```

---

## STEP 10 — Chatbot MyBibliò

### 10.1 Quale LLM usare (free/low-cost)
Per l'MVP usa **Google Gemini API** (ha un piano gratuito generoso — 1M token/mese):
1. Vai su [aistudio.google.com](https://aistudio.google.com) → Get API key → Copia la chiave
2. In alternativa: **OpenAI API** (a consumo) o **Groq** (velocissimo, piano free)

### 10.2 Endpoint AJAX nel plugin

Nel plugin `biblio-mvp.php`:

```php
// ========================
// CHATBOT MyBibliò
// ========================

// Salva la chiave API nelle opzioni WordPress
// Vai su Impostazioni → (aggiungi manualmente per ora)
// oppure aggiungi questo campo temporaneamente:
// add_option('biblio_gemini_api_key', 'LA_TUA_CHIAVE_QUI');

add_action('wp_ajax_biblio_chat', function() {
    check_ajax_referer('biblio_chatbot', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Non autenticato');
    }

    $messaggio = sanitize_text_field($_POST['message'] ?? '');
    if (empty($messaggio)) {
        wp_send_json_error('Messaggio vuoto');
    }

    global $wpdb;

    // 1. Recupera catalogo dal DB (filtro semplice per parole chiave)
    $keyword = '%' . $wpdb->esc_like($messaggio) . '%';
    $libri_raw = get_posts([
        'post_type'      => 'libro',
        'posts_per_page' => 8,
        'post_status'    => 'publish',
        's'              => $messaggio, // ricerca nel titolo/contenuto
    ]);

    // Costruisci contesto catalogo
    $contesto_libri = [];
    foreach ($libri_raw as $libro) {
        $contesto_libri[] = [
            'titolo'         => $libro->post_title,
            'autore'         => get_field('autore', $libro->ID),
            'categoria'      => get_field('categoria', $libro->ID),
            'descrizione'    => wp_trim_words(get_the_content(null, false, $libro), 50),
            'numero_pagine'  => get_field('numero_pagine', $libro->ID),
            'prezzo_cartaceo'      => get_field('prezzo_cartaceo', $libro->ID),
            'prezzo_ebook'         => get_field('prezzo_ebook_acquisto', $libro->ID),
            'ha_noleggio'          => get_field('ha_ebook_noleggio', $libro->ID) ? 'sì' : 'no',
            'url'            => get_permalink($libro->ID),
        ];
    }

    // Se nessun risultato dalla ricerca, prendi tutti
    if (empty($contesto_libri)) {
        $tutti = get_posts(['post_type' => 'libro', 'posts_per_page' => 20, 'post_status' => 'publish']);
        foreach ($tutti as $libro) {
            $contesto_libri[] = [
                'titolo'        => $libro->post_title,
                'autore'        => get_field('autore', $libro->ID),
                'categoria'     => get_field('categoria', $libro->ID),
                'numero_pagine' => get_field('numero_pagine', $libro->ID),
                'prezzo_cartaceo'    => get_field('prezzo_cartaceo', $libro->ID),
                'prezzo_ebook'       => get_field('prezzo_ebook_acquisto', $libro->ID),
                'ha_noleggio'        => get_field('ha_ebook_noleggio', $libro->ID) ? 'sì' : 'no',
                'url'           => get_permalink($libro->ID),
            ];
        }
    }

    $catalogo_json = json_encode($contesto_libri, JSON_UNESCAPED_UNICODE);

    // 2. Chiama Gemini API
    $api_key = get_option('biblio_gemini_api_key', '');
    $prompt_sistema = "Sei MyBibliò, l'assistente librario della piattaforma Bibliò. 
Puoi SOLO consigliare libri presenti nel catalogo qui sotto. Non inventare titoli.
Non rispondere a domande fuori dal catalogo Bibliò.
Quando consigli un libro, includi sempre il link alla scheda.
Rispondi in italiano, in modo cordiale e appassionato.

CATALOGO DISPONIBILE:
{$catalogo_json}";

    $payload = json_encode([
        'contents' => [
            ['role' => 'user', 'parts' => [
                ['text' => $prompt_sistema . "\n\nDomanda utente: " . $messaggio]
            ]]
        ],
        'generationConfig' => ['maxOutputTokens' => 500, 'temperature' => 0.7],
    ]);

    $response = wp_remote_post(
        "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$api_key}",
        [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => $payload,
            'timeout' => 30,
        ]
    );

    if (is_wp_error($response)) {
        wp_send_json_error('Errore connessione AI');
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    $risposta = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Non riesco a rispondere in questo momento.';

    wp_send_json_success(['reply' => $risposta]);
});
```

### 10.3 JavaScript chatbot

Crea `/wp-content/themes/astra-biblio/js/chatbot.js`:

```javascript
// Bibliò Chatbot — MyBibliò
(function($) {
    // Inject HTML del chatbot nel footer
    const chatHTML = `
    <button class="biblio-chatbot-fab" id="biblioChatFab" title="Chatta con MyBibliò">📚</button>
    <div class="biblio-chatbot-window hidden" id="biblioChatWindow">
        <div class="biblio-chatbot-header">
            <span style="font-size:24px;">📚</span>
            <div>
                <h4>MyBibliò</h4>
                <span>Il tuo assistente librario</span>
            </div>
        </div>
        <div class="biblio-chatbot-messages" id="biblioChatMessages">
            <div class="biblio-msg biblio-msg-bot">
                Ciao! Sono MyBibliò 📚 Dimmi che tipo di libro stai cercando e ti aiuto a trovarlo nel nostro catalogo!
            </div>
        </div>
        <div class="biblio-chatbot-input">
            <input type="text" id="biblioChatInput" placeholder="Cerca un libro...">
            <button id="biblioChatSend">➤</button>
        </div>
    </div>`;

    $('body').append(chatHTML);

    const $fab     = $('#biblioChatFab');
    const $window  = $('#biblioChatWindow');
    const $messages = $('#biblioChatMessages');
    const $input   = $('#biblioChatInput');
    const $send    = $('#biblioChatSend');

    // Toggle finestra
    $fab.on('click', function() {
        $window.toggleClass('hidden');
        if (!$window.hasClass('hidden')) $input.focus();
    });

    function addMessage(text, type) {
        const cls = type === 'bot' ? 'biblio-msg-bot' : 'biblio-msg-user';
        const $msg = $(`<div class="biblio-msg ${cls}">${text}</div>`);
        $messages.append($msg);
        $messages.scrollTop($messages[0].scrollHeight);
    }

    function sendMessage() {
        const msg = $input.val().trim();
        if (!msg) return;
        
        addMessage(msg, 'user');
        $input.val('');
        addMessage('⏳ Sto cercando nel catalogo...', 'bot');

        $.ajax({
            url: biblioChatbot.ajaxUrl,
            method: 'POST',
            data: {
                action: 'biblio_chat',
                nonce:  biblioChatbot.nonce,
                message: msg,
            },
            success: function(res) {
                // Rimuovi messaggio "sto cercando"
                $messages.find('.biblio-msg-bot:last').remove();
                if (res.success) {
                    addMessage(res.data.reply, 'bot');
                } else {
                    addMessage('Mi dispiace, si è verificato un errore. Riprova tra poco.', 'bot');
                }
            },
            error: function() {
                $messages.find('.biblio-msg-bot:last').remove();
                addMessage('Errore di connessione. Controlla la tua rete.', 'bot');
            }
        });
    }

    $send.on('click', sendMessage);
    $input.on('keypress', function(e) {
        if (e.which === 13) sendMessage();
    });

})(jQuery);
```

### 10.4 Salva la chiave API Gemini

Vai su **Strumenti → Esegui PHP** (plugin "WPCode" o simile) oppure aggiungi temporaneamente nel plugin:

```php
// Esegui UNA VOLTA, poi rimuovi
add_action('init', function() {
    update_option('biblio_gemini_api_key', 'INSERISCI_QUI_LA_TUA_API_KEY');
});
```

---

## STEP 11 — Automazioni

### Cron job per scadenza noleggi

Nel plugin `biblio-mvp.php`:

```php
// Registra cron giornaliero
register_activation_hook(__FILE__, function() {
    if (!wp_next_scheduled('biblio_check_scadenze')) {
        wp_schedule_event(time(), 'daily', 'biblio_check_scadenze');
    }
});

register_deactivation_hook(__FILE__, function() {
    wp_clear_scheduled_hook('biblio_check_scadenze');
});

add_action('biblio_check_scadenze', function() {
    global $wpdb;
    // Revoca accessi scaduti
    $wpdb->query("
        UPDATE biblio_accessi_ebook
        SET stato = 'scaduto'
        WHERE tipo_accesso = 'noleggio'
          AND data_fine < NOW()
          AND stato = 'attivo'
    ");
});
```

Per far girare i cron di WordPress su InfinityFree (che ha limitazioni), vai su **WP Crontrol** (plugin) e verifica che il cron sia schedulato.

---

## STEP 12 — Checklist finale

Segui questo ordine:

- [ ] **Database** — Esegui le 4 query SQL in phpMyAdmin
- [ ] **Plugin** — Installa CPT UI, ACF, PDF Embedder
- [ ] **CPT** — Crea tipo "libro" con CPT UI
- [ ] **ACF** — Crea gruppo campi "Metadati Libro"
- [ ] **5 Libri** — Crea i 5 post con tutti i campi
- [ ] **SQL libri** — Esegui INSERT in biblio_libri e biblio_modalita
- [ ] **WooCommerce** — Configura valuta, spedizioni, pagamenti
- [ ] **Prodotti WooCommerce** — Crea prodotti per ogni modalità
- [ ] **Aggiorna SQL** — Inserisci i woo_product_id reali nelle tabelle
- [ ] **Child Theme** — Crea astra-biblio con style.css + functions.php
- [ ] **Plugin custom** — Crea biblio-mvp.php con tutti i codici
- [ ] **Pagina Catalogo** — Crea pagina con [biblio_catalogo]
- [ ] **Pagina Libreria** — Crea pagina con [biblio_libreria]
- [ ] **Template libro** — Crea single-libro.php nel child theme
- [ ] **Chatbot JS** — Crea chatbot.js nel child theme
- [ ] **Gemini API** — Ottieni chiave e salvala in WordPress
- [ ] **Test acquisto** — Acquista un libro in modalità sandbox
- [ ] **Test noleggio** — Verifica che accesso venga creato dopo ordine
- [ ] **Test chatbot** — Chiedi un consiglio a MyBibliò

---

## Applicativi free consigliati per le parti scoperte

| Esigenza | Soluzione gratuita | Note |
|----------|-------------------|------|
| Email transazionali (conferma ordine) | **WP Mail SMTP** + Gmail SMTP | 500 email/giorno |
| Backup database | **UpdraftPlus** free | Backup su Google Drive |
| PDF reader protetto | **PDF Embedder** free | Visualizzazione in browser |
| Immagini copertine placeholder | **placehold.co** o Unsplash | Per i 5 libri test |
| LLM Chatbot | **Google Gemini 1.5 Flash** | 1M token/mese free |
| Stripe (pagamenti) | **WooCommerce Stripe** gratuito | Commissioni solo su transazione |
| Ottimizzazione immagini | **Smush** free | Compressione copertine |
| Cache | **W3 Total Cache** o **LiteSpeed Cache** | InfinityFree supporta LiteSpeed |

---

*Guida generata per Bibliò MVP v1.0 — Aprile 2026*
