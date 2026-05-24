# Direzione Teorica — Gestionale Mezzi

**Versione:** 1.0
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
- **Palette:** colori del sito istituzionale dell'ente (da applicare via CSS custom properties)
- **Logo:** inserito manualmente a tema/plugin pronto
- **Mobile-first:** form a step progressivi, bottoni touch-friendly, font leggibile su piccolo schermo

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
