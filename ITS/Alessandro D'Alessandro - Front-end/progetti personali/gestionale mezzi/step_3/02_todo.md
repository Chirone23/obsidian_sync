# TODO — Test Step 3 (in ordine)

Segna ogni punto con ✅ quando passa o ❌ con nota quando fallisce.

---

## FASE 0 — Avvio plugin

- [ ] Attiva il plugin da wp-admin → Plugins (con utente `admin`)
- [ ] Nessun errore PHP fatale al caricamento
- [ ] Le 7 pagine gestionale esistono in wp-admin → Pagine
- [ ] I 3 ruoli esistono in wp-admin → Utenti → Aggiungi nuovo → dropdown Ruolo

---

## FASE 1 — Navigazione base (tutti i ruoli)

Per ogni utente test, esegui:

- [ ] Login → redirect automatico a `/gestionale-dashboard/`
- [ ] Admin bar WP nascosta
- [ ] Tema header/footer nascosto
- [ ] Navbar gestionale visibile con voci corrette per il ruolo
- [ ] Logout → redirect a `/wp-login.php`

**Voci navbar attese:**

| Voce | Volontario | Direttivo | Amministrazione |
|---|---|---|---|
| Dashboard | ✓ | ✓ | ✓ |
| Nuovo Foglio | ✓ | ✓ | ✓ |
| I Miei Fogli | ✓ | ✓ | ✓ |
| Tutti i Fogli | ✗ | ✓ | ✓ |
| Gestione Utenti | ✗ | ✓ | ✓ |
| Gestione Veicoli | ✗ | ✓ | ✓ |
| Log Attività | ✗ | ✗ | ✓ |

---

## FASE 2 — Controllo accessi (403)

- [ ] `volontario_test` → `/gestionale-tutti-i-fogli/` → pagina 403
- [ ] `volontario_test` → `/gestionale-gestione-utenti/` → pagina 403
- [ ] `volontario_test` → `/gestionale-gestione-veicoli/` → pagina 403
- [ ] `volontario_test` → `/gestionale-log-attivita/` → pagina 403
- [ ] `direttivo_test` → `/gestionale-log-attivita/` → pagina 403
- [ ] `direttivo_test` → `/gestionale-gestione-veicoli/` → accessibile ✓
- [ ] Utente non loggato → qualsiasi pagina gestionale → redirect login

---

## FASE 3 — Gestione Veicoli (con `direttivo_test`)

**Crea veicolo:**
- [ ] Form visibile e completo
- [ ] Submit senza targa → errore validazione
- [ ] Submit con targa duplicata → errore "targa già esistente"
- [ ] Submit valido → redirect con `?msg=created` → messaggio successo
- [ ] Veicolo appare in tabella
- [ ] Km formattati con punti (es. 1.234)

**Modifica veicolo:**
- [ ] Click icona matita → form pre-compilato con dati esistenti
- [ ] Modifica km → salva → log registra prima/dopo SOLO se km è cambiato
- [ ] Modifica altri campi → km_attuali nel log NON appare

**Toggle attivo/dismetti:**
- [ ] Click dismetti → confirm dialog → veicolo diventa grigio con badge "Dismesso"
- [ ] Click riattiva → veicolo torna attivo

---

## FASE 4 — Gestione Utenti (con `direttivo_test`)

**Crea utente:**
- [ ] Form visibile con checkbox patenti
- [ ] Checkbox patente → mostra campi data conseguimento/scadenza
- [ ] Deseleziona patente → campi date si nascondono
- [ ] Submit senza email → errore
- [ ] Submit valido → utente creato con ruolo corretto
- [ ] Utente appare in tabella con badge ruolo e patenti

**Modifica utente:**
- [ ] Click matita → form pre-compilato
- [ ] Patenti esistenti pre-selezionate con date
- [ ] Campo data_conseguimento: modifica e salva → data sopravvive al submit (hidden field)
- [ ] Password vuota in modifica → password non cambia

**Toggle disabilita/riabilita:**
- [ ] Disabilita → riga grigia, badge "Disabilitato"
- [ ] Utente disabilitato NON appare nel dropdown conducenti del form foglio

---

## FASE 5 — Nuovo Foglio di Marcia (con `volontario_test`)

**Prerequisiti:** almeno 1 veicolo attivo, almeno 1 utente con patente compatibile

**Dropdown dinamici:**
- [ ] Seleziona conducente → dropdown veicoli si aggiorna via AJAX
- [ ] Seleziona veicolo → km_iniziali si auto-compila (readonly)
- [ ] Seleziona veicolo → limite passeggeri si aggiorna

**Salva bozza:**
- [ ] Submit senza conducente → errore validazione
- [ ] Submit valido come bozza → redirect `/gestionale-i-miei-fogli/?msg=bozza`
- [ ] Foglio in tabella con badge "Bozza"
- [ ] km veicolo NON aggiornati dopo bozza
- [ ] Log registra `CREA` con dettaglio

**Invia foglio:**
- [ ] Submit come inviato senza km_finali → errore
- [ ] Submit valido come inviato → redirect `?msg=inviata`
- [ ] Foglio in tabella con badge "Inviata"
- [ ] km veicolo AGGIORNATI con km_finali
- [ ] Log registra `INVIA_SCHEDA`

**Modifica bozza:**
- [ ] Click matita su bozza → form pre-compilato con tutti i dati
- [ ] Passeggeri pre-selezionati
- [ ] Salva modifiche → dati aggiornati
- [ ] Log registra `MODIFICA`

**Invio bozza:**
- [ ] Modifica bozza → cambio stato a "inviata" → km veicolo aggiornati

---

## FASE 6 — I Miei Fogli

- [ ] Lista mostra solo fogli dell'utente corrente
- [ ] Bozze: bottoni modifica + elimina visibili
- [ ] Fogli inviati: nessun bottone azione (solo visualizzazione futura)
- [ ] Elimina bozza → confirm → foglio rimosso → log `ELIMINA`
- [ ] Elimina foglio inviato come `volontario_test` → non permesso (403 o errore)

---

## FASE 7 — Tutti i Fogli (con `direttivo_test`)

- [ ] Tabella mostra fogli di tutti gli utenti
- [ ] Filtro conducente → filtra correttamente
- [ ] Filtro veicolo → filtra correttamente
- [ ] Filtro anno → filtra correttamente
- [ ] Filtro stato → filtra correttamente
- [ ] Paginazione funziona (crea >25 fogli per testare, oppure abbassa `$per_page` temporaneamente)
- [ ] Reset filtri → mostra tutto

---

## FASE 8 — Log Attività (con `amm_test`)

- [ ] Tabella mostra tutte le voci
- [ ] Badge colorati per tipo azione (verde CREA, blu MODIFICA, rosso ELIMINA, ecc.)
- [ ] Filtro utente → filtra
- [ ] Filtro azione → filtra
- [ ] Filtro date → filtra
- [ ] Bottone "Dettagli ▼" → espande riga con JSON formattato
- [ ] Bottone "Dettagli ▲" → collassa riga

---

## FASE 9 — Impersonazione (con `admin`)

- [ ] Dropdown "Visualizza come" visibile solo per admin
- [ ] Switch a Volontario → menu si aggiorna, badge diventa giallo
- [ ] Switch a Direttivo → menu si aggiorna
- [ ] Switch a Amministrazione (me) → torna normale, badge blu
- [ ] Impersonando Volontario → accesso a log-attivita → 403

---

## FASE 10 — Verifica finale

- [ ] Nessun `console.error` in nessuna pagina
- [ ] Nessun warning PHP nei log di XAMPP (`C:\xampp\php\logs\php_error_log`)
- [ ] Tutti i redirect POST usano il PRG pattern (no risubmit al refresh)
