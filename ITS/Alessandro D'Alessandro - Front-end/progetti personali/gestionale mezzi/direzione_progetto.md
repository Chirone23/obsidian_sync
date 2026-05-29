# Direzione Teorica — Gestionale Mezzi

**Versione:** 1.1
**Data:** 2026-05-24
**Status:** Definita — pronta per implementazione

---

## Contesto

Gestionale veicoli per un ente di **protezione civile / volontariato**. Fa parte di un **sito istituzionale WordPress** con area pubblica (a cura del cliente) e area riservata (il gestionale). Hosted su **InfinityFree** (PHP 8.3, MySQL 8.0, ~128MB RAM, ~30k inode limit).

---

## Stack

| Layer | Scelta |
|---|---|
| Hosting | InfinityFree (free tier) |
| CMS | WordPress 7.x |
| Backend | PHP 8.3 — custom plugin `gestionale-mezzi` |
| Database | MySQL 8.0 (InnoDB) — tabelle `wp_gm_*` |
| Frontend | Vanilla JS + CSS (no build step) |
| Auth | WP native — sessioni 1 settimana via `auth_cookie_expiration` |

---

## Architettura: WordPress Plugin

Il gestionale vive come **plugin WP** (`/wp-content/plugins/gestionale-mezzi/`). Vantaggi:

- Login unico: le credenziali WP sono le credenziali del gestionale
- Il cliente gestisce la parte pubblica del sito via WP admin in autonomia
- Plugin manutenibile e aggiornabile indipendentemente dal tema
- WP gestisce CSRF (nonces), sessioni, hashing password

### Ruoli WP custom

Tre ruoli registrati all'attivazione del plugin:

| Ruolo WP | Equivalente schema |
|---|---|
| `gm_volontario` | `volontario` |
| `gm_direttivo` | `direttivo` |
| `gm_amministrazione` | `amministrazione` |

**Utenti test (password: `test1234`):**

| Username          | Email                 | Nome visualizzato    | Ruolo                |
| ----------------- | --------------------- | -------------------- | -------------------- |
| `volontario_test` | volontario@test.local | Volontario Test      | `gm_volontario`      |
| `direttivo_test`  | direttivo@test.local  | Direttivo Test       | `gm_direttivo`       |
| `amm_test`        | amm@test.local        | Amministrazione Test | `gm_amministrazione` |

---

## Database

La tabella `utenti` dello schema originale **non esiste**: al suo posto si usa `wp_users`. I dati extra (patente assegnata, stato attivo/disabilitato) vivono in `wp_usermeta`.

Le restanti 7 tabelle si rinominano con prefisso `wp_gm_`:

| Tabella originale | Tabella MySQL |
|---|---|
| `categorie_patente` | `wp_gm_categorie_patente` |
| `categorie_veicolo` | `wp_gm_categorie_veicolo` |
| `veicoli` | `wp_gm_veicoli` |
| `utenti_patenti` | `wp_gm_utenti_patenti` |
| `fogli_di_marcia` | `wp_gm_fogli_di_marcia` |
| `foglio_passeggeri` | `wp_gm_foglio_passeggeri` |
| `log_attivita` | `wp_gm_log_attivita` |

Il campo `utente_id` / `conducente_id` / `creato_da` punta a `wp_users.ID`.

> **Nota SQL:** lo schema esistente usa sintassi SQLite. Per MySQL: `AUTOINCREMENT` → `AUTO_INCREMENT`, apici → backtick, `INTEGER` → `INT`. Le foreign key InnoDB sono standard e funzionano su InfinityFree.

---

## Sessioni

```php
add_filter('auth_cookie_expiration', fn() => WEEK_IN_SECONDS);
```

Sessioni attive per 7 giorni. Zero complessità aggiuntiva.

---

## Gestione utenti

- Gli account li crea l'**admin** manualmente tramite un pannello dedicato nel plugin
- Esiste anche un **form di registrazione** (opzionale, visibile solo all'admin) per velocizzare l'inserimento massivo
- Gli account non si cancellano mai: campo `attivo = 0` per disabilitare (già nel design del DB)

---

## Design

- **Stile:** flat, card-based, bordi netti — riferimento visivo: gabiodvzagarolo.lovable.app
- **Logo:** inserito manualmente a tema/plugin pronto
- **Mobile-first:** form a step progressivi, bottoni touch-friendly, font leggibile su piccolo schermo
- **Navigazione:** sidebar drawer (hamburger → menu laterale da sinistra)
- **Login:** pagina WP default `/wp-login.php` — customizzazione rimandata al tema

### Palette

Estratta da gabiodvzagarolo.lovable.app, mappata su CSS custom properties:

```css
--color-primary:      #2563eb;   /* blu — nav, bottoni principali */
--color-primary-dark: #575ecf;   /* indigo — hover, focus */
--color-accent:       #fe7b02;   /* arancione — azioni secondarie, badge */
--color-bg:           #fcfbf8;   /* crema — sfondo pagine */
--color-surface:      #ffffff;   /* bianco — card, form, sidebar */
--color-border:       #e5e7eb;   /* grigio chiaro — bordi, divisori */
--color-text:         #1b1b1b;   /* quasi nero — testo principale */
--color-text-muted:   #9ca3af;   /* grigio — testo secondario, label */
--color-danger:       #dc2626;   /* rosso — elimina, errori */
--color-warning:      #faa938;   /* ambra — avvisi, bozze in sospeso */
--color-success:      #16a34a;   /* verde — stato "inviato", conferme */
```

---

## Inventario schermate

**Comuni (tutti i ruoli)**
- Dashboard — riepilogo: ultime bozze, ultimi fogli inviati, km veicoli
- Nuovo foglio di marcia — form di creazione
- I miei fogli — lista fogli propri (bozze + inviati, pulsante modifica + visualizza)
- Dettaglio foglio — visualizzazione e modifica singolo foglio (anche dopo invio)

**Direttivo + Amministrazione**
- Tutti i fogli — lista globale con filtri (conducente, veicolo, anno, stato)
- Gestione utenti — lista + form crea/modifica/disabilita
- Gestione veicoli — lista + form crea/modifica/dismetti

**Solo Amministrazione**
- Log attività — tabella log con filtri (conducente, azione, intervallo date) + riga espandibile per dettaglio JSON prima/dopo

---

## Ordine di costruzione

```
1. Plugin bootstrap + DB setup
2. Sidebar drawer + layout base
3. Dashboard
4. Gestione veicoli
5. Gestione utenti
6. Nuovo foglio di marcia
7. I miei fogli + Dettaglio foglio
8. Tutti i fogli
9. Log attività
```

---

## Roadmap a fasi

### Fase 1 — Utenti & Patenti *(prerequisito di tutto)*
- Registrazione ruoli WP custom all'attivazione plugin
- Pannello admin: crea/modifica/disabilita utenti
- Assegnazione patenti (B, C, MMT) con data scadenza
- Form di registrazione opzionale (admin-only)

### Fase 2 — Anagrafica Veicoli *(prerequisito del form)*
- CRUD mezzi: targa, marca, modello, anno, categoria, posti, km attuali
- Attivazione/dismissione veicolo (flag `attivo`)

### Fase 3 — Fogli di Marcia *(core operativo)*
- Form creazione: seleziona conducente (filtrato per patente compatibile), veicolo, passeggeri, dati viaggio
- Salvataggio bozza / invio definitivo
- `km_iniziali` copiato automaticamente da `veicoli.km_attuali` alla creazione
- Aggiornamento `km_attuali` solo all'invio (non alla bozza)
- Vista storico per ogni utente

### Fase 4 — Dashboard & Log *(visibilità direttivo/admin)*
- Dashboard: fogli in bozza in sospeso, ultimi viaggi, km per veicolo
- Accesso log attività (solo `gm_amministrazione`)
- Filtri per conducente, veicolo, anno

#### Pagina Log Attività — Specifiche UI (2026-05-27)

**Layout:** tabella con filtri in cima + paginazione 25 righe.

**Colonne:** ID | Data/Ora | Utente | Azione | [Dettagli ▼]

**Filtri:** dropdown Utente (solo chi ha log), dropdown Azione, Da data, A data.

**Riga espandibile:** bottone "Dettagli ▼" aggiunge un `<tr>` inline sotto la riga con il JSON prima/dopo. Toggle via JS vanilla, JSON già nel DOM come attributo `data-dettaglio` (no AJAX).

**Query principali:**
```sql
-- Lista con filtri + paginazione
SELECT l.*, u.display_name
FROM wp_gm_log_attivita l
LEFT JOIN wp_users u ON l.utente_id = u.ID
WHERE (l.utente_id = %d OR %d = 0)
  AND (l.azione = %s OR %s = '')
  AND (l.created_at >= %s OR %s = '')
  AND (l.created_at <= %s OR %s = '')
ORDER BY l.created_at DESC
LIMIT 25 OFFSET %d

-- Count per paginazione
SELECT COUNT(*) FROM wp_gm_log_attivita l WHERE ...

-- Dropdown utenti (solo chi ha almeno un log)
SELECT DISTINCT u.ID, u.display_name
FROM wp_users u
INNER JOIN wp_gm_log_attivita l ON u.ID = l.utente_id
ORDER BY u.display_name ASC
```

**Export CSV:** non previsto nello Step 3, rimandato a Fase 5.

### Fase 3.5 — includes/fogli.php *(logica business fogli)*

File `includes/fogli.php` — controller+model per i fogli di marcia, nessun HTML.

**Funzioni di lettura:**
- `gm_get_conducenti_list()` — utenti con patente valida (escluse scadute) compatibile con almeno un veicolo attivo
- `gm_get_veicoli_per_conducente($id)` — veicoli compatibili con le patenti del conducente
- `gm_get_fogli_utente($utente_id)` — lista fogli dell'utente
- `gm_get_foglio_by_id($id)` — singolo foglio con join veicolo/conducente
- `gm_get_foglio_passeggeri($foglio_id)` — IDs passeggeri di un foglio

**CRUD (con transazione MySQL — tutto o niente):**
- `gm_save_foglio($data, $stato)` — INSERT foglio + passeggeri + log + aggiorna km se `inviata`
- `gm_update_foglio($id, $data, $stato)` — UPDATE bozza + sostituzione passeggeri
- `gm_delete_foglio($id)` — DELETE con controllo permessi

**AJAX handlers:**
- `wp_ajax_gm_get_veicoli` — JSON veicoli per conducente (dropdown dinamico)
- `wp_ajax_gm_get_km_veicolo` — km_attuali + posti_totali veicolo (auto-fill form)

**Hook template_redirect:**
- `gm_handle_foglio_form()` — processa POST form (PRG: valida → salva → redirect)
- `gm_handle_foglio_delete()` — processa POST cancellazione

**Flusso creazione foglio:**
```
nuovo-foglio.php (form) → POST → gm_handle_foglio_form() → gm_save_foglio() → redirect i-miei-fogli.php
```

**Nota patenti scadute:** `gm_get_conducenti_list()` esclude patenti con `data_scadenza < CURDATE()` per la compatibilità veicolo.

---

### includes/log.php *(2026-05-27)*

Unica funzione `gm_log_attivita($azione, $tabella, $record_id, $dettaglio = '')`.

- `$dettaglio` accetta array (convertito in JSON) o stringa — **non** usare `sanitize_textarea_field` che corrompe il JSON
- `utente_id` nullable per azioni di sistema
- IP loggato da `$_SERVER['REMOTE_ADDR']`

**Azioni previste:** `LOGIN`, `CREA`, `MODIFICA`, `CANCELLA`, `INVIA_SCHEDA`, `SALVA_BOZZA`

---

### Fase 5 — Export *(dopo MVP)*
- PDF stampabile per ogni foglio di marcia
- Export Excel/CSV per report mensili/annuali

### Fase 6 — Possibili migliorie *(future, non pianificate)*
- Notifiche email scadenza patentino MMT
- Alert km veicolo (manutenzione programmata)
- Statistiche avanzate consumi carburante/olio

---

## Vincoli InfinityFree

| Vincolo | Soluzione nel plugin |
|---|---|
| CPU/RAM ~128MB | `get_transient()` per cache lista veicoli e utenti (TTL 1h) |
| Inode ~30k | Plugin target < 30 file PHP; CSS/JS minificati in un file ciascuno |
| No heavy JS | Vanilla JS per logica dropdown (filtro conducente per patente) |
| Memory WP | `define('WP_MEMORY_LIMIT', '128M')` in `wp-config.php` |

---

## Struttura plugin (target)

```
gestionale-mezzi/
├── gestionale-mezzi.php        (bootstrap, registra ruoli, attiva tabelle)
├── includes/
│   ├── db-setup.php            (CREATE TABLE al primo avvio)
│   ├── roles.php               (registra gm_volontario/direttivo/amministrazione)
│   ├── auth.php                (filtro sessioni 7gg, controlli accesso)
│   ├── users.php               (gestione utenti + patenti)
│   ├── veicoli.php             (CRUD veicoli)
│   ├── fogli.php               (logica fogli di marcia)
│   └── log.php                 (scrittura log attività)
├── templates/
│   ├── dashboard.php
│   ├── form-foglio.php
│   ├── lista-fogli.php
│   ├── admin-utenti.php
│   └── admin-veicoli.php
└── assets/
    ├── style.css
    └── app.js
```

---

*Collegato a: [[schema_db]]*
