# Step 3 — Guida Sessione

**Obiettivo:** testare e correggere tutto il codice scritto nello Step 2/2.1. Tutti i file PHP sono pronti ma non sono mai stati eseguiti insieme. Questa sessione trasforma il codice in un sistema funzionante.

---

## Contesto

**Plugin:** `gestionale-mezzi` in `C:\xampp\htdocs\wp\wp-content\plugins\gestionale-mezzi`
**Stack:** WordPress + PHP 8.3 + MySQL 8.0 + Bootstrap 5 CDN + Vanilla JS
**Ambiente:** XAMPP locale (non InfinityFree — test in locale, deploy dopo)

**Utenti test (password: `test1234`):**

| Username          | Ruolo                                              |
| ----------------- | -------------------------------------------------- |
| `volontario_test` | `gm_volontario`                                    |
| `direttivo_test`  | `gm_direttivo`                                     |
| `amm_test`        | `gm_amministrazione`                               |
| `admin`           | `gm_amministrazione` (accesso wp-admin consentito) |

---

## Stato attuale

Tutti i file sono scritti. Nulla è stato testato in esecuzione.

**includes/:** `db-setup.php`, `roles.php`, `auth.php`, `impersonation.php`, `log.php`, `veicoli.php`, `users.php`, `fogli.php`, `pages.php`
**templates/:** `layout.php`, `dashboard.php`, `gestione-veicoli.php`, `gestione-utenti.php`, `nuovo-foglio.php`, `i-miei-fogli.php`, `tutti-i-fogli.php`, `log-attivita.php`, `error-403.php`
**assets/:** `style.css`, `app.js`

---

## Regola operativa

**Leggi sempre il file prima di modificarlo.** Quando un test fallisce: identifica il file responsabile → leggi → correggi → ritesta. Non modificare mai alla cieca.

---

## Ordine dei test

Segui esattamente `02_todo.md`. Non saltare step — ogni livello dipende dal precedente.
