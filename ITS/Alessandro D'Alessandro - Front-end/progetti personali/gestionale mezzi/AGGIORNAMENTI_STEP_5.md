# AGGIORNAMENTI STEP 5 — Registrazione, Gestione Utenti/Veicoli, Fogli

**Data:** 15 giugno 2026  
**Modifiche:** Rimozione verifica email SMTP, approvazione manuale utenti, offcanvas form, tab Dismessi/Disabilitati, modifica fogli altrui, cascata km

---

## 1. Registrazione — rimozione verifica email

**File:** `includes/registration.php`, `templates/registrazione.php`

- Rimosso l'intero flusso di verifica via email SMTP (`gm_send_verification_email`, `gm_verify_email_token`, handler GET token)
- Rimossa notifica email agli admin su nuova registrazione
- `gm_register_new_user` ora setta `gm_attivo=0` + `gm_pending=1` e basta
- Messaggio di conferma cambiato in "Richiesta inviata! Il direttivo la valuterà"
- Aggiunta icona visibilità password (Material Symbols) + toggle JS `gmTogglePass()`
- Validazione CF lato client (formato + carattere di controllo)

**Flusso nuovo:** registrazione → utente in pending → approvazione manuale da Gestione Utenti

---

## 2. Gestione Utenti — tab "Da verificare" + offcanvas form

**File:** `includes/users.php`, `templates/gestione-utenti.php`

### Logica (`includes/users.php`)
- `gm_get_gm_users_list()`: esclude utenti con `gm_pending=1`
- `gm_get_pending_users()`: restituisce solo utenti pending (include `gm_codice_fiscale`)
- `gm_validate_user_data()`: username opzionale, CF obbligatorio, forza password sicura + conferma
- `gm_create_gm_user()`: genera username automatico (`gm_generate_username()`), salva CF, costruisce `display_name`
- `gm_handle_approve_user()`: setta `gm_attivo=1`, cancella meta `gm_pending`
- `gm_handle_reject_user()`: log + `wp_delete_user()`
- `gm_handle_delete_user()`: admin-only, pulisce `gm_utenti_patenti` poi `wp_delete_user()`

### UI (`templates/gestione-utenti.php`)
- Offcanvas laterale `#gmUserOffcanvas` (larghezza 420px) per Nuovo Utente e Modifica
- X e Annulla usano `gmCloseUserPanel()` JS (nessun reload pagina, `history.replaceState` per pulire `edit_id` dall'URL)
- 3 tab: **Utenti** (attivi), **Da verificare** (pending — badge contatore), **Disabilitati** (admin-only)
- Tab Disabilitati: Riabilita + Elimina (cestino, cancellazione definitiva dal DB)
- ⚠️ **Asimmetria verificata nel codice:** il tab "Disabilitati" è renderizzato solo se `$is_admin`. Il **Direttivo può disabilitare** un utente (bottone presente nel tab Utenti) ma **non vede il tab Disabilitati**, quindi non può riabilitarlo né eliminarlo: serve un Amministratore.
- Bottone "Nuovo Utente" nel card header (lato opposto al titolo)
- Toggle visibilità password con Material Symbols (`visibility` / `visibility_off`)
- Utenti creati da Gestione Utenti sono già verificati (nessun `gm_pending`)

---

## 3. Gestione Veicoli — tab "Dismessi" + offcanvas form

**File:** `templates/gestione-veicoli.php`

- Stesso pattern offcanvas di Gestione Utenti (`#gmVeicoloOffcanvas`)
- 2 tab: **Veicoli** (attivi), **Dismessi** (admin-only — solo Riattiva, NO elimina per regola progetto)
- Bottone "Nuovo Veicolo" nel card header
- `gmCloseVeicoloPanel()` + `history.replaceState`
- ⚠️ **Stessa asimmetria di Gestione Utenti:** il Direttivo può dismettere un veicolo dal tab Veicoli ma non vede il tab Dismessi (admin-only) per riattivarlo.

---

## 4. Tutti i Fogli — modifica fogli altrui per admin/direttivo

**File:** `templates/tutti-i-fogli.php`, `templates/nuovo-foglio.php`, `includes/fogli.php`

- Admin e direttivo (`gm_edit_all`) vedono il bottone Modifica su **qualsiasi** foglio
- `$back_url` dinamico: torna a "Tutti i Fogli" se si modifica il foglio di qualcun altro, altrimenti "I Miei Fogli"
- Hidden field `_back_url` nel form → redirect corretto dopo salvataggio/eliminazione
- Whitelist URL per sicurezza: solo `i-miei-fogli` o `tutti-i-fogli`

---

## 5. I Miei Fogli — modifica fogli propri senza restrizione stato

**File:** `templates/i-miei-fogli.php`

- `$can_edit = $is_own || gm_user_can('gm_edit_all')` (rimosso vincolo `$is_bozza`)
- Il proprietario può modificare sia bozze che fogli inviati

---

## 6. Cascata km su modifica foglio

**File:** `includes/fogli.php` — funzione `gm_update_foglio()`

### Problema risolto
- Prima: `km_iniziali` veniva sovrascritta con `km_attuali` correnti del veicolo (bug su fogli storici)
- Prima: `km_attuali` veicolo aggiornata sempre, anche modificando fogli non recenti

### Logica nuova
- `km_iniziali` del foglio modificato **preservata dall'originale** (`$foglio->km_iniziali`)
- Se `km_finali` cambia → cerca il foglio successivo dello **stesso veicolo** (`veicolo_id = X AND id > Y ORDER BY id ASC LIMIT 1`) e aggiorna il suo `km_iniziali` al nuovo valore (nella stessa transazione)
- `km_attuali` veicolo aggiornata con i `km_finali` dell'**ultimo foglio inviato** per quel veicolo (`ORDER BY id DESC LIMIT 1`)
- La nota di cascata viene aggiunta al log: *"Km iniziali foglio #N aggiornati automaticamente a X"*

---

## 7. Fix allineamento colonna "Azioni"

**File:** tutti i template con tabelle (`i-miei-fogli.php`, `tutti-i-fogli.php`, `gestione-utenti.php`, `gestione-veicoli.php`)

- Aggiunto `align-middle` al `<th class="text-end align-middle">Azioni</th>`
- Bootstrap 5 applica `align-middle` alle celle tbody ma non agli `<th>` di thead per default

---

## Ruoli e capability coinvolte

| Capability | Chi ce l'ha (verificato in `roles.php`) |
|---|---|
| `gm_create_foglio` | `gm_volontario`, `gm_direttivo`, `administrator` |
| `gm_edit_own_foglio` | `gm_volontario`, `gm_direttivo`, `administrator` |
| `gm_read_all` | `gm_direttivo`, `administrator` |
| `gm_edit_all` | `gm_direttivo`, `administrator` |
| `gm_manage_users` | `gm_direttivo`, `administrator` |
| `gm_manage_veicoli` | `gm_direttivo`, `administrator` |
| `gm_delete_any` | solo `administrator` (via bypass `gm_user_can`) |
| `gm_view_log` | solo `administrator` (via bypass `gm_user_can`) |

**Note:**
- `gm_amministrazione` eliminato. `gm_pending` è usermeta (`'1'`), non un ruolo.
- `gm_delete_any` e `gm_view_log` **non sono assegnate a nessun ruolo** in `roles.php`: funzionano solo perché `gm_user_can()` (in `rbac.php`) restituisce `true` per gli `administrator`. Di conseguenza la voce di menu **"Log Attività" è visibile solo agli Amministratori**, non ai Direttivi.
