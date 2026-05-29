# Schema Database — Sistema Schede Interventi / Fogli di Marcia

**Versione:** 1.1  
**Ultimo aggiornamento:** Maggio 2026

---

## Panoramica generale

Il database è strutturato attorno a tre aree concettuali distinte che collaborano tra loro. La prima area riguarda le **persone**: chi sono, che ruolo hanno nel sistema, e quali abilitazioni di guida possiedono. La seconda area riguarda i **mezzi**: i veicoli aziendali, la loro categoria e il chilometraggio aggiornato. La terza area è il **cuore operativo**: i fogli di marcia, i passeggeri associati, e il registro di tutte le attività del sistema.

---

## Ruoli utente e gerarchia dei permessi

Il sistema implementa un controllo degli accessi basato su tre ruoli gerarchici. È importante capire che il database si limita a *registrare* il ruolo di ciascun utente — è il codice applicativo (il backend) ad applicare concretamente le restrizioni. Questa separazione è intenzionale e corretta.

| Ruolo | Lettura | Scrittura | Modifica | Cancellazione | Accesso Log |
|:---|:---:|:---:|:---:|:---:|:---:|
| `volontario` | Propri dati | ✓ | Solo proprie bozze | ✗ | ✗ |
| `direttivo` | Tutto | ✓ | Tutto | ✗ | ✗ |
| `amministrazione` | Tutto | ✓ | Tutto | ✓ | ✓ |

Il **volontario** è il livello base: ogni dipendente — anche senza patente — parte da questo ruolo. Può creare schede, salvare bozze, modificarle finché non le invia, e leggere i propri dati storici.

Il **direttivo** ha visibilità e controllo su tutto il contenuto del sistema, ma *non può cancellare*. Questa restrizione è deliberata: protegge l'integrità storica dei dati. Un direttivo può correggere, ma non far sparire nulla.

L'**amministrazione** è il ruolo di controllo totale. In aggiunta a tutto il resto, ha accesso esclusivo alla tabella `log_attivita`, che registra ogni azione rilevante nel sistema.

---

## Regole di inclusione delle patenti

Le abilitazioni di guida seguono una gerarchia di inclusione gestita a livello applicativo. Il principio è semplice: chi ha una patente "superiore" può sempre guidare i veicoli che richiedono una patente "inferiore".

- La **Patente C** abilita alla guida di tutti i veicoli di categoria C *e* di tutti i veicoli di categoria B.
- Il **Patentino MMT** (Macchine Movimento Terra) abilita alla conduzione di macchine operatrici *e* di tutti i veicoli di categoria B.

Questo significa che nel dropdown "Seleziona il conducente", il sistema mostrerà automaticamente solo gli utenti che possiedono l'abilitazione compatibile con il veicolo selezionato, tenendo conto di queste inclusioni.

---

## Tabella 1 — `categorie_patente`

Contiene le tre tipologie di abilitazione riconosciute dal sistema. I dati sono fissi e pre-popolati: non cambieranno nel tempo. Il termine ufficiale per il patentino è **"Abilitazione alla conduzione di macchine movimento terra"**, regolamentato dall'Accordo Stato-Regioni del 22 febbraio 2012.

| Campo | Tipo | Note |
|:---|:---|:---|
| `id` | INTEGER PK | Auto-incrementale |
| `codice` | TEXT UNIQUE | `'B'`, `'C'`, `'MMT'` |
| `nome` | TEXT | Nome esteso per l'interfaccia utente |
| `descrizione` | TEXT | Descrizione opzionale |
| `created_at` | DATETIME | Timestamp di inserimento |

---

## Tabella 2 — `categorie_veicolo`

Questa tabella non classifica i veicoli per tipo (auto, furgone, camion) ma esclusivamente per **requisito di accesso**: quale patente serve per guidarli. È una distinzione concettuale importante. In futuro potrà essere arricchita con altri requisiti operativi senza stravolgere la struttura.

| Campo | Tipo | Note |
|:---|:---|:---|
| `id` | INTEGER PK | Auto-incrementale |
| `patente_richiesta_id` | INTEGER FK | Riferimento a `categorie_patente.id` |
| `note` | TEXT | Opzionale |

---

## Tabella 3 — `veicoli`

L'anagrafica completa di tutti i mezzi aziendali. Il campo `km_attuali` è il dato più "vivo" dell'intera tabella: viene aggiornato automaticamente dal sistema ogni volta che una scheda passa dallo stato `bozza` a `inviata`. Non viene mai aggiornato su una bozza, per evitare che dati provvisori alterino il chilometraggio reale del mezzo.

Il campo `posti_totali` alimenta la logica dei passeggeri nel form: il numero massimo di passeggeri selezionabili sarà sempre `posti_totali - 1`, sottraendo il posto del conducente.

| Campo          | Tipo        | Note                                 |
| :------------- | :---------- | :----------------------------------- |
| `id`           | INTEGER PK  | Auto-incrementale                    |
| `codice`       | TEXT UNIQUE | codice auto 'SKY N'                  |
| `targa`        | TEXT UNIQUE | Identificativo univoco del mezzo     |
| `marca`        | TEXT        | —                                    |
| `modello`      | TEXT        | —                                    |
| `anno`         | INTEGER     | Anno di immatricolazione             |
| `categoria_id` | INTEGER FK  | Riferimento a `categorie_veicolo.id` |
| `posti_totali` | INTEGER     | Posti compreso il conducente         |
| `km_attuali`   | INTEGER     | Aggiornato ad ogni scheda inviata    |
| `attivo`       | INTEGER     | `1` = in servizio, `0` = dismesso    |
| `note`         | TEXT        | Opzionale                            |
| `created_at`   | DATETIME    | —                                    |

---

## Tabella 4 — `utenti`

Tutti i dipendenti hanno un account, indipendentemente dal fatto che guidino o meno. Questo è il presupposto fondamentale del sistema: anche chi non ha patente può compilare schede per conto di un autista, e può comparire come passeggero in una scheda altrui.

| Campo | Tipo | Note |
|:---|:---|:---|
| `id` | INTEGER PK | Auto-incrementale |
| `nome` | TEXT | — |
| `cognome` | TEXT | — |
| `email` | TEXT UNIQUE | Usata per il login |
| `password_hash` | TEXT | La password non viene mai salvata in chiaro |
| `ruolo` | TEXT | `'volontario'`, `'direttivo'`, `'amministrazione'` |
| `attivo` | INTEGER | `1` = attivo, `0` = disabilitato (mai cancellare un utente) |
| `created_at` | DATETIME | — |

---

## Tabella 5 — `utenti_patenti`

Questa è una tabella di collegamento *many-to-many*: un utente può avere più abilitazioni, e ogni abilitazione può appartenere a più utenti. Solo gli utenti con almeno una riga in questa tabella possono comparire nel dropdown "Seleziona conducente" del form.

Il campo `data_scadenza` è particolarmente utile per il patentino MMT, che a differenza delle patenti B e C ha una validità limitata nel tempo e richiede rinnovo periodico.

| Campo | Tipo | Note |
|:---|:---|:---|
| `utente_id` | INTEGER FK | Riferimento a `utenti.id` |
| `categoria_id` | INTEGER FK | Riferimento a `categorie_patente.id` |
| `data_conseguimento` | DATE | Opzionale, per archivio |
| `data_scadenza` | DATE | Utile per il patentino MMT |
| PK composta | — | `(utente_id, categoria_id)` — impedisce duplicati |

---

## Tabella 6 — `fogli_di_marcia`

La tabella principale del sistema. Vale la pena soffermarsi su tre dettagli progettuali importanti.

Il **codice visivo** "46/2026" è ottenuto combinando i campi `numero` e `anno`. Il campo `numero` è sequenziale *per anno*, non globale: ogni primo gennaio riparte da 1. Il vincolo `UNIQUE(numero, anno)` garantisce che questo codice sia sempre univoco.

I **km iniziali** non vengono inseriti dall'utente: il sistema li copia automaticamente da `veicoli.km_attuali` nel momento in cui la scheda viene creata. Questo snapshot viene conservato nella scheda per sempre, così anche se il veicolo accumula altri chilometri in futuro, la ricostruzione storica del viaggio rimane accurata.

Il campo **`creato_da`** può essere diverso da `conducente_id`: un volontario senza patente può aprire il form, scegliere l'autista e il veicolo, e inviare la scheda per conto suo.

| Campo | Tipo | Note |
|:---|:---|:---|
| `id` | INTEGER PK | Auto-incrementale |
| `numero` | INTEGER | Sequenziale per anno |
| `anno` | INTEGER | Es: `2026` |
| `conducente_id` | INTEGER FK | Riferimento a `utenti.id` |
| `veicolo_id` | INTEGER FK | Riferimento a `veicoli.id` |
| `merci` | TEXT | Testo libero |
| `motivo` | TEXT | Testo libero |
| `richiesto_da` | TEXT | Testo libero |
| `richiesto_per` | TEXT | Testo libero |
| `data_inizio` | DATETIME | — |
| `data_fine` | DATETIME | — |
| `km_iniziali` | INTEGER | Snapshot automatico da `veicoli.km_attuali` |
| `km_finali` | INTEGER | Inserito dall'utente |
| `carburante_litri` | REAL | — |
| `carburante_prezzo` | REAL | In euro |
| `olio_litri` | REAL | — |
| `olio_prezzo` | REAL | In euro |
| `note` | TEXT | Testo libero |
| `stato` | TEXT | `'bozza'` o `'inviata'` |
| `creato_da` | INTEGER FK | Riferimento a `utenti.id` |
| `created_at` | DATETIME | — |
| `updated_at` | DATETIME | Aggiornato ad ogni modifica |

---

## Tabella 7 — `foglio_passeggeri`

Una tabella di collegamento *many-to-many* tra schede e utenti. Ogni riga rappresenta un singolo passeggero su una singola scheda. Se un foglio ha tre passeggeri, esistono esattamente tre righe con lo stesso `foglio_id` e tre `utente_id` diversi. La chiave primaria composta impedisce di aggiungere accidentalmente lo stesso passeggero due volte.

Il numero massimo di passeggeri selezionabili è imposto dal frontend (non dal database), che legge `veicoli.posti_totali` e ne sottrae uno.

La clausola `ON DELETE CASCADE` garantisce che se una scheda viene cancellata (operazione riservata all'amministrazione), tutte le righe dei passeggeri collegati vengano rimosse automaticamente, senza lasciare dati orfani.

| Campo | Tipo | Note |
|:---|:---|:---|
| `foglio_id` | INTEGER FK | Riferimento a `fogli_di_marcia.id` |
| `utente_id` | INTEGER FK | Riferimento a `utenti.id` |
| PK composta | — | `(foglio_id, utente_id)` — impedisce duplicati |

---

## Tabella 8 — `log_attivita`

Il registro immutabile di tutto ciò che accade nel sistema. Questa tabella è *write-only* dal punto di vista degli utenti: il backend la popola automaticamente, nessuno può modificarla o cancellarla manualmente. Solo il ruolo `amministrazione` può leggerla tramite l'interfaccia.

Il campo `dettaglio` è progettato per contenere un oggetto JSON con lo stato del record *prima* e *dopo* ogni modifica. Questo permette all'amministrazione di ricostruire la cronologia completa di qualsiasi scheda, come funziona la cronologia delle versioni in un documento collaborativo.

Il campo `utente_id` è nullable per gestire le azioni automatiche del sistema stesso (ad esempio, l'aggiornamento automatico dei km di un veicolo), che non sono compiute da nessun utente specifico.

| Campo | Tipo | Note |
|:---|:---|:---|
| `id` | INTEGER PK | Auto-incrementale |
| `utente_id` | INTEGER FK | `NULL` per azioni di sistema |
| `azione` | TEXT | Es: `'LOGIN'`, `'CREA'`, `'MODIFICA'`, `'CANCELLA'`, `'INVIA_SCHEDA'` |
| `tabella` | TEXT | La tabella coinvolta dall'azione |
| `record_id` | INTEGER | L'ID del record su cui è avvenuta l'azione |
| `dettaglio` | TEXT | JSON opzionale con snapshot prima/dopo |
| `ip_address` | TEXT | Per rilevare accessi anomali |
| `created_at` | DATETIME | — |

---

## Indici

Gli indici migliorano la velocità delle interrogazioni più frequenti senza modificare la struttura logica del database. Ogni indice è pensato per rispondere a una domanda operativa specifica.

| Indice | Tabella | Colonne | Domanda a cui risponde |
|:---|:---|:---|:---|
| `idx_fogli_conducente` | `fogli_di_marcia` | `conducente_id` | Tutte le schede di un autista specifico |
| `idx_fogli_veicolo` | `fogli_di_marcia` | `veicolo_id` | Tutti i viaggi di un veicolo specifico |
| `idx_fogli_anno` | `fogli_di_marcia` | `anno` | Tutti i fogli di un anno specifico |
| `idx_fogli_stato` | `fogli_di_marcia` | `stato` | Tutte le bozze in sospeso |
| `idx_passeggeri_foglio` | `foglio_passeggeri` | `foglio_id` | I passeggeri di una scheda specifica |
| `idx_log_utente` | `log_attivita` | `utente_id` | Tutto ciò che ha fatto un utente |
| `idx_log_tabella` | `log_attivita` | `tabella, record_id` | La storia completa di un record |
| `idx_log_data` | `log_attivita` | `created_at` | Tutte le azioni in un intervallo di tempo |

---

## Diagramma delle relazioni

```
categorie_patente ──< utenti_patenti >── utenti
        │                                  │  │
        │                             creato_da│
categorie_veicolo                    conducente_id
        │                                  │
      veicoli ──────────────────── fogli_di_marcia ──< foglio_passeggeri >── utenti
                                           │
                                     log_attivita (traccia tutto)
```
