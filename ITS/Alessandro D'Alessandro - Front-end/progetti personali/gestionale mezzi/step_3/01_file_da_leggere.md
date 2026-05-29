# File da leggere — Priorità e perché

Leggi nell'ordine indicato. Non leggere tutto subito: leggi il file solo quando il test lo richiede.

---

## Priorità 1 — Prima di iniziare qualsiasi test

| File | Perché |
|---|---|
| `gestionale-mezzi.php` | Verifica ordine require e asset enqueue |
| `includes/db-setup.php` | Schema tabelle — capire struttura dati |
| `includes/roles.php` | Capabilities per ruolo — base di tutto il controllo accessi |
| `includes/auth.php` | Redirect, admin bar, body class |
| `includes/pages.php` | Come i shortcode caricano i template |

## Priorità 2 — Quando testi una pagina specifica

| Pagina | File da leggere prima |
|---|---|
| Gestione Veicoli | `includes/veicoli.php` + `templates/gestione-veicoli.php` |
| Gestione Utenti | `includes/users.php` + `templates/gestione-utenti.php` |
| Nuovo Foglio | `includes/fogli.php` + `templates/nuovo-foglio.php` |
| I Miei Fogli | `templates/i-miei-fogli.php` |
| Tutti i Fogli | `templates/tutti-i-fogli.php` |
| Log Attività | `includes/log.php` + `templates/log-attivita.php` |

## Priorità 3 — Solo se ci sono problemi CSS/JS

| File | Quando leggerlo |
|---|---|
| `assets/style.css` | Layout rotto, elementi visibili che non dovrebbero esserlo |
| `assets/app.js` | Sidebar non si chiude, role switch AJAX non funziona, dropdown veicoli non si aggiorna |

## Non leggere (già nel contesto o non modificabili)

- `schema_db.sql` — solo riferimento, le tabelle vengono create da `db-setup.php`
- `direzione_progetto.md` — roadmap, non serve durante i test
- File WordPress core
