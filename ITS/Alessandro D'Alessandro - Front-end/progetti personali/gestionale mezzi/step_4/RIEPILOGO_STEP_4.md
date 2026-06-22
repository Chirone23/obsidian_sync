# RIEPILOGO STEP 4 — Consolidamento: ruoli, patenti, rendering, UX

**Periodo:** maggio 2026
**Obiettivo:** revisione architetturale dopo i test dello Step 3 — semplificazione ruoli,
ridisegno della logica patenti, fix del rendering WordPress e rifiniture UX/responsive.

> Questo step modifica decisioni prese negli Step 2/2.1/3. I changelog precedenti restano
> validi come **storia**, ma dove c'è conflitto **vale lo Step 4**. Riferimenti aggiornati:
> `schema_db.md` (v1.2) e il `CLAUDE.md` nella root del plugin.

---

## 1. Ruoli — eliminato `gm_amministrazione`, rimossa l'impersonazione

- Il ruolo custom **`gm_amministrazione` è stato eliminato**. Il livello "amministrazione"
  coincide ora con l'**`administrator` di WordPress**.
- Ruoli attuali: `gm_volontario`, `gm_direttivo`, `administrator`.
- `roles.php`: rimosso `add_role('gm_amministrazione')` e relativo `remove_role`. Aggiunto
  filtro `editable_roles` che **nasconde** i ruoli WP inutili (subscriber, contributor,
  author, editor) dal dropdown — non si eliminano, si nascondono.
- `auth.php`: `gm_block_admin_access` e `gm_hide_admin_bar` lasciano passare/vedere
  `administrator`; `gm_auth_cookie_expiration` estende la sessione (7gg) anche per admin.
- **Sistema impersonazione "Visualizza come" rimosso** dalla navbar. Le funzioni
  `gm_user_can_impersonated()` restano come gate RBAC: per l'admin **senza** impersonazione
  ritornano `true` (accesso pieno senza dover assegnare le capability `gm_*` al ruolo
  `administrator` nel DB).
- **Sicurezza creazione utenti:** un `gm_direttivo` **non** può creare/assegnare il ruolo
  `administrator`. Doppia barriera: l'opzione "Amministrazione" non compare nel dropdown se
  l'utente corrente non è admin, e `gm_validate_user_data()` rifiuta comunque il valore.

**File:** `includes/roles.php`, `includes/auth.php`, `includes/impersonation.php`,
`includes/users.php`, `templates/layout.php`, `templates/dashboard.php`,
`templates/gestione-utenti.php`, `gestionale-mezzi.php`.

---

## 2. Patenti — rimosse le date, introdotta la gerarchia come dato

### 2.1 Rimozione date patente
- Eliminate le colonne `data_conseguimento` e `data_scadenza` da `gm_utenti_patenti`.
  L'abilitazione è ora semplice possesso utente↔categoria.
- `db-setup.php`: schema senza le due colonne + migrazione idempotente
  (`SHOW COLUMNS ... LIKE` + `ALTER TABLE DROP COLUMN`) per le installazioni esistenti.
- `users.php`: `gm_get_user_patenti()` e `gm_sync_patenti()` non gestiscono più date.
- `gestione-utenti.php`: il form patenti è ora solo checkbox (niente campi data, niente JS
  di sync hidden); i badge patente non mostrano più lo stato scaduta/valida.
- **Rimossa** anche la logica "patente conseguita da almeno 3 anni" introdotta a inizio
  sessione: non più richiesta.

### 2.2 Gerarchia patenti come dato — `gm_patente_inclusione`
- Nuova tabella `gm_patente_inclusione (patente_id, include_patente_id)`, seedata con
  **C → B** e **MMT → B**: chi possiede C o MMT può guidare anche i veicoli che richiedono B.
- È **dato, non codice**: per cambiare la gerarchia si toccano le righe.
- Le 3 query di compatibilità in `fogli.php` considerano patente esatta **OPPURE** inclusioni:
  `gm_get_conducenti_list()`, `gm_get_conducenti_per_veicolo()`, `gm_get_veicoli_per_conducente()`.

**File:** `includes/db-setup.php`, `includes/users.php`, `includes/fogli.php`,
`templates/gestione-utenti.php`.

---

## 3. Nuovo Foglio di Marcia — logica invertita e validazioni

- **Inversione veicolo → conducente:** ora si seleziona prima il **veicolo** (lista completa
  dei veicoli attivi, sempre selezionabili). La scelta del veicolo filtra via AJAX
  (`gm_get_conducenti`) i **conducenti compatibili** con quel veicolo (patente + inclusioni).
  I conducenti partono comunque dalla lista completa; cambiando veicolo si filtra,
  preservando la selezione se ancora valida.
- **Km iniziali automatici:** allo `change` del veicolo si compila il campo readonly da
  `data-km`. Aggiunto **init al page-load**: se un veicolo è già selezionato (Firefox restore
  / modifica bozza) e il campo km è vuoto, lo compila — senza sovrascrivere lo snapshot
  salvato in modifica bozza.
- **Validazione km finali:** controllo JS con **debounce 250ms** mentre si digita — i km
  finali non possono essere inferiori ai km iniziali. Blocco anche sull'invio ("Invia
  foglio"); il "Salva come bozza" resta libero.
- **Elimina bozza:** in modifica bozza compare il bottone "Elimina bozza" (rosso, con
  conferma). Usa nonce/handler esistenti (`gm_delete_foglio`) tramite un form separato
  agganciato con l'attributo HTML5 `form="..."` (niente form annidati).

**File:** `includes/fogli.php` (funzione `gm_get_conducenti_per_veicolo` + AJAX
`gm_get_conducenti`), `templates/nuovo-foglio.php`.

> Nota: in `nuovo-foglio.php` sono presenti dei `console.log [GM DEBUG]` lasciati per
> diagnosticare il km auto-fill. Da rimuovere prima del deploy definitivo.

---

## 4. Rendering WordPress — fix import map e documento singolo

- **Causa del bug "import map duplicato":** ogni shortcode `[gm_*]` include `layout.php`, che è
  una pagina HTML completa (`wp_head`/`wp_footer`). Renderizzata dentro la pagina del block
  theme (Twenty Twenty-Five), produceva un **doppio documento** → doppio `wp_footer` →
  import map stampata due volte, oltre a HTML annidato e doppio Bootstrap.
- **Fix:** filtro `template_include` in `pages.php` → per le pagine `gestionale-*` usa
  `templates/page-gestionale.php`, che emette **solo** l'output dello shortcode
  (`do_shortcode( $post->post_content )`, niente `the_content`/wpautop). Risultato:
  un solo documento, un solo `wp_head`/`wp_footer`, niente wrapper del tema.
- Effetti collaterali positivi: niente doppio Bootstrap, sparisce il problema del padding del
  wrapper del tema. Le vecchie regole CSS che nascondevano header/footer del tema diventano
  superflue ma restano innocue.

**File:** `includes/pages.php`, `templates/page-gestionale.php`.

---

## 5. UI / Responsive

- **Navbar overflow dinamico:** rimosso il numero fisso di voci visibili. Un JS in
  `layout.php` misura lo spazio disponibile e sposta le voci eccedenti nel dropdown "Altro";
  se entrano tutte, "Altro" sparisce. Forzati `nowrap`/`flex-shrink:0` via JS
  (`setProperty(..., 'important')`) per battere gli override del tema. Sotto 992px → offcanvas.
- **Cache-busting CSS:** `style.css` enqueued con `filemtime()` (la cache di InfinityFree è
  aggressiva sui CSS).
- **Padding-top del main responsive:** classe `gm-main` → 72px desktop (≥992px), 63px mobile.
- **Sidebar offcanvas** larghezza max 180px (inline + CSS, `--bs-offcanvas-width`).

**File:** `templates/layout.php`, `assets/style.css`, `gestionale-mezzi.php`.

---

## 6. Documentazione / infrastruttura

- Creato **`CLAUDE.md`** nella root del plugin: contesto, vincoli InfinityFree, architettura
  a 3 strati, regole di sistema e di stile, workflow. Dichiara esplicitamente che il
  paradigma direttive/execution del `CLAUDE.md` globale **non** si applica qui.
- Creato **`TODO.md`**: task in sospeso (sqlmap, Google Fonts simboli).
- Workflow di lavoro (pre-lavoro → approvazione → una cosa alla volta) spostato nel
  `CLAUDE.md` **globale** (sezione "Modo di lavorare").
- `schema_db.md` aggiornato a **v1.2** (rimozione date, tabella `patente_inclusione`, nota WP).

---

## Recap file toccati (Step 4)

**includes/**
- `db-setup.php` — drop colonne date + tabella/seed `gm_patente_inclusione`
- `roles.php` — rimosso `gm_amministrazione` + filtro `editable_roles`
- `auth.php` — `administrator` al posto di `gm_amministrazione`
- `impersonation.php` — rimosso `gm_amministrazione`; admin senza impersonazione = accesso pieno
- `users.php` — patenti senza date; validazione ruolo `administrator`
- `fogli.php` — gerarchia patenti nelle 3 query; `gm_get_conducenti_per_veicolo` + AJAX `gm_get_conducenti`
- `pages.php` — filtro `template_include`
- `log.php` — `gm_log_azione_class()` spostata qui

**templates/**
- `layout.php` — navbar overflow dinamico, no impersonazione, padding/main responsive, sidebar 180px
- `page-gestionale.php` — render documento singolo (`do_shortcode`)
- `nuovo-foglio.php` — veicolo→conducente, km auto-fill + validazione, elimina bozza
- `gestione-utenti.php` — patenti senza date, ruolo admin gated
- `dashboard.php` — rilevamento ruolo aggiornato

**assets/** — `style.css` (override, padding responsive, sidebar)
**root** — `gestionale-mezzi.php` (cache-busting, `$gm_roles`), `CLAUDE.md`, `TODO.md`

---

## Deploy su InfinityFree

1. Caricare (sovrascrivere) i file toccati sopra.
2. Per le **modifiche DB** (drop colonne date + tabella `gm_patente_inclusione`):
   wp-admin → Plugin → **Disattiva → Riattiva** "Gestionale Mezzi".
   Le righe esistenti vanno **preservate** (non cancellare `gm_utenti_patenti` ecc.).
3. Hard reload (Ctrl+Shift+R) per la cache CSS.
4. Assegnare `administrator` agli utenti che facevano da amministrazione; riassegnare/eliminare
   eventuali utenti rimasti su ruoli WP inutili.

---

## In sospeso (coda Step 4)

- Rimuovere i `console.log [GM DEBUG]` da `nuovo-foglio.php`.
- 404 a `errors.infinityfree.net/errors/404/` da diagnosticare (risorsa mancante).
- Warning Firefox di *forced reflow* sulla navbar (avviare la misura solo su `load`).
