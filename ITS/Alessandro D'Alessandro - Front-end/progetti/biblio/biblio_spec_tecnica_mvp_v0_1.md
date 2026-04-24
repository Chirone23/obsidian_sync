
# Bibliò — Spec Tecnica MVP v0.1

## 1. Scopo del documento

Questo documento traduce la **spec funzionale MVP** in una **prima specifica tecnica** orientata allo sviluppo.

L'obiettivo è definire:

1. architettura applicativa MVP
2. responsabilità dei componenti
3. modello dati logico e proposta di persistenza
4. mapping tra WordPress / WooCommerce e dati custom
5. flussi tecnici principali
6. requisiti minimi di sicurezza e controllo accessi
7. decisioni già chiuse e punti ancora aperti

> Nota: dove una decisione non è ancora stata chiusa in modo definitivo, viene indicata come **punto aperto** e non come verità già fissata.

---

## 2. Stack tecnico MVP

## 2.1 Componenti principali

1. **WordPress**
   - CMS
   - gestione utenti
   - amministrazione contenuti
   - base di estensione plugin

2. **WooCommerce**
   - catalogo commerciale
   - carrello
   - checkout
   - ordini
   - pagamenti
   - gestione stato ordine

3. **Plugin custom Bibliò**
   - logica dominio specifica non coperta bene da WooCommerce
   - import Excel
   - modalità del titolo
   - piani di noleggio
   - accessi e-book
   - scadenze noleggio
   - libreria digitale
   - integrazione MyBibliò

4. **Database MySQL/MariaDB**
   - tabelle core WordPress
   - tabelle WooCommerce
   - tabelle custom Bibliò

5. **PDF Viewer in browser**
   - rendering PDF lato client
   - accesso mediato da endpoint protetti

6. **Provider AI via API**
   - usato da MyBibliò
   - input limitato a risultati filtrati da SQL
   - nessun vector DB nell'MVP

---

## 3. Architettura logica MVP

## 3.1 Livelli

### A. Livello CMS / e-commerce
Gestito da WordPress + WooCommerce.

Responsabilità:
1. utenti
2. login / registrazione
3. checkout
4. pagamenti
5. ordini
6. backoffice base

### B. Livello dominio Bibliò
Gestito dal plugin custom.

Responsabilità:
1. modello titolo / modalità / piani noleggio
2. accessi a e-book acquistati
3. accessi a e-book noleggiati
4. calcolo scadenze
5. conversione noleggio → acquisto
6. libreria digitale utente
7. import catalogo da Excel
8. guardrail e retrieval per MyBibliò

### C. Livello integrazioni esterne
Responsabilità:
1. gateway pagamento
2. provider AI
3. eventuali servizi email/transazionali

---

## 4. Principi architetturali

1. **MVP-first**
   - preferire semplicità implementativa
   - evitare soluzioni enterprise premature

2. **Separazione tra commercio e fruizione**
   - WooCommerce gestisce la transazione
   - il plugin Bibliò gestisce il diritto di accesso ai PDF

3. **Titolo come entità editoriale principale**
   - un titolo può avere più modalità commerciali

4. **Dati digitali sotto controllo applicativo**
   - nessun accesso al PDF basato su URL pubblici

5. **Chatbot controllato**
   - SQL retrieval prima del modello
   - LLM solo come generatore di risposta naturale

---

## 5. Mapping funzionale → tecnico

## 5.1 Concetti di dominio

### Titolo
Entità editoriale principale.

Campi minimi:
1. `book_id`
2. titolo
3. autore
4. isbn
5. descrizione
6. categoria
7. copertina
8. numero_pagine
9. stato PDF associato

### Modalità
Rappresenta una forma commerciale disponibile per un titolo.

Tipi previsti:
1. `cartaceo`
2. `ebook_acquisto`
3. `ebook_noleggio`

Campi minimi:
1. `modalita_id`
2. `book_id`
3. `tipo_modalita`
4. `prezzo` (solo per cartaceo e ebook_acquisto)
5. `attivo`

### Piano di noleggio
Disponibile solo per modalità `ebook_noleggio`.

Campi minimi:
1. `piano_id`
2. `modalita_id`
3. `durata_giorni`
4. `prezzo`
5. `attivo`

### Accesso digitale
Diritto dell'utente a leggere un PDF.

Tipi:
1. acquisto definitivo
2. noleggio temporaneo

Stati minimi:
1. `attivo`
2. `scaduto`
3. `convertito`
4. `revocato` (opzionale ma utile internamente)

---

## 6. Strategia di persistenza

## 6.1 Cosa resta in WooCommerce

WooCommerce deve restare responsabile di:
1. prodotti/varianti commerciali o riferimenti commerciali
2. carrello
3. ordini
4. pagamenti
5. clienti
6. stati ordine

## 6.2 Cosa va in tabelle custom Bibliò

Le seguenti aree vanno meglio in tabelle custom:

1. relazione titolo → modalità
2. piani di noleggio
3. accessi digitali utente
4. cronologia noleggio / rinnovi / conversioni
5. associazione titolo ↔ asset PDF
6. logica di libreria digitale

Motivo:
- WooCommerce non modella in modo pulito accessi temporanei e regole di lettura.
- Forzare tutto in post meta renderebbe query e manutenzione più fragili.

---

## 7. Proposta tabelle custom SQL

Di seguito una proposta logica. I nomi possono essere affinati nella fase di implementazione.

## 7.1 `biblio_books`

Contiene i dati editoriali del titolo.

Campi proposti:
- `id` PK autoincrement
- `book_id` varchar unique
- `title`
- `author`
- `isbn`
- `description`
- `category`
- `cover_url` o riferimento media
- `page_count`
- `pdf_media_id` nullable
- `created_at`
- `updated_at`

Vincoli:
- `book_id` univoco
- `isbn` obbligatorio, non necessariamente univoco a livello applicativo se in futuro cambiano edizioni

## 7.2 `biblio_book_modes`

Modalità disponibili per titolo.

Campi proposti:
- `id` PK
- `modalita_id` varchar unique
- `book_id` FK logica verso `biblio_books.book_id`
- `mode_type` enum(`cartaceo`,`ebook_acquisto`,`ebook_noleggio`)
- `price_amount` decimal nullable
- `currency` default `EUR`
- `is_active` tinyint
- `wc_product_id` nullable
- `created_at`
- `updated_at`

Vincoli:
- `modalita_id` univoco
- per `cartaceo` e `ebook_acquisto`, `price_amount` obbligatorio
- per `ebook_noleggio`, `price_amount` nullo

## 7.3 `biblio_rental_plans`

Piani di noleggio.

Campi proposti:
- `id` PK
- `piano_id` varchar unique
- `modalita_id` FK logica verso `biblio_book_modes.modalita_id`
- `duration_days` int
- `price_amount` decimal
- `currency` default `EUR`
- `is_active` tinyint
- `created_at`
- `updated_at`

Vincoli:
- `piano_id` univoco
- `duration_days > 0`
- `price_amount >= 0`

## 7.4 `biblio_user_accesses`

Accessi utente agli e-book.

Campi proposti:
- `id` PK
- `user_id` bigint
- `book_id` varchar
- `modalita_id` varchar nullable
- `access_type` enum(`ebook_acquisto`,`ebook_noleggio`)
- `source_order_id` bigint
- `source_order_item_id` bigint nullable
- `rental_plan_id` varchar nullable
- `started_at` datetime
- `expires_at` datetime nullable
- `status` enum(`attivo`,`scaduto`,`convertito`,`revocato`)
- `created_at`
- `updated_at`

Vincoli:
- per `ebook_acquisto`, `expires_at` nullo
- per `ebook_noleggio`, `expires_at` obbligatorio

## 7.5 `biblio_rental_events`

Storico delle operazioni sui noleggi.

Campi proposti:
- `id` PK
- `user_access_id` bigint
- `event_type` enum(`attivazione`,`rinnovo`,`scadenza`,`conversione`)
- `amount` decimal nullable
- `related_order_id` bigint nullable
- `notes` text nullable
- `created_at` datetime

Utilità:
- audit tecnico
- ricostruzione cronologia
- calcolo conversione più leggibile

## 7.6 `biblio_chat_logs` (opzionale MVP)
Può essere introdotta subito o rimandata.

Campi minimi:
- `id`
- `user_id`
- `prompt_user`
- `filters_json`
- `books_payload_json`
- `model_response`
- `created_at`

---

## 8. Relazione con WooCommerce

## 8.1 Strategia consigliata

Per l'MVP conviene mantenere **WooCommerce come motore transazionale**, ma non come unica verità del dominio.

### Modello raccomandato
1. ogni modalità vendibile ha un riferimento commerciale in WooCommerce
2. il plugin Bibliò mantiene il collegamento con il modello editoriale

### Esempio
Per un titolo con tre modalità:
1. cartaceo → prodotto WooCommerce A
2. ebook_acquisto → prodotto WooCommerce B
3. ebook_noleggio → prodotto WooCommerce C oppure gestione via piano

## 8.2 Nodo tecnico ancora da chiudere
C'è una decisione implementativa da prendere:

### Opzione A — un prodotto WooCommerce per ogni modalità
- più lineare lato checkout
- più semplice lato ordine
- più immediato da capire

### Opzione B — prodotto principale + logica custom attorno
- più elegante lato dominio
- ma più complesso lato WooCommerce

**Raccomandazione tecnica MVP:** Opzione A.

Motivo:
1. più semplice da integrare con carrello e ordini
2. più facile gestire regola "ordine di un solo tipo"
3. meno logica custom nelle fasi delicate del checkout

> Questo è un punto tecnico importante che suggerisco di bloccare nello step successivo.

---

## 9. Import catalogo da Excel

## 9.1 Formato sorgente

Un file Excel con tre fogli:
1. `libri`
2. `modalita`
3. `piani_noleggio`

## 9.2 Workflow tecnico import

1. upload file Excel da backoffice
2. parsing dei fogli
3. validazione strutturale
4. validazione referenziale
5. preview errori / warning
6. conferma import
7. upsert nel database custom
8. eventuale sincronizzazione con entità WooCommerce

## 9.3 Validazioni minime

### Foglio `libri`
- `book_id` obbligatorio e univoco nel file
- `titolo` obbligatorio
- `autore` obbligatorio
- `isbn` obbligatorio
- `descrizione` obbligatoria
- `categoria` obbligatoria
- `copertina` obbligatoria come dato catalogo
- `numero_pagine` obbligatorio

### Foglio `modalita`
- `modalita_id` obbligatorio e univoco
- `book_id` deve esistere in `libri`
- `tipo_modalita` ammesso solo tra i valori previsti
- `prezzo` obbligatorio per `cartaceo` e `ebook_acquisto`
- `prezzo` nullo per `ebook_noleggio`

### Foglio `piani_noleggio`
- `piano_id` obbligatorio e univoco
- `modalita_id` deve esistere
- `modalita_id` deve riferirsi a `ebook_noleggio`
- `durata_giorni` obbligatorio e > 0
- `prezzo` obbligatorio e >= 0

## 9.4 Gestione errori import
L'import non deve fallire in modo silenzioso.

Output minimo:
1. righe valide
2. righe con errori bloccanti
3. righe con warning
4. messaggio finale di import riuscito / parziale / fallito

---

## 10. Gestione asset PDF

## 10.1 Regola confermata
Il PDF:
1. è associato al titolo
2. non arriva da Excel
3. viene caricato manualmente dall'admin
4. è obbligatorio se il titolo ha almeno una modalità digitale

## 10.2 Strategia tecnica consigliata

1. upload del PDF nella media library WordPress oppure area protetta
2. salvataggio del riferimento nel record `biblio_books`
3. accesso al PDF mediato da endpoint applicativo

## 10.3 Evitare nell'MVP
1. URL pubblici diretti
2. file linkabili senza controllo
3. embedding del PDF da path statico pubblico

## 10.4 Flusso di lettura
1. utente apre "Leggi"
2. endpoint verifica login
3. endpoint verifica diritto attivo in `biblio_user_accesses`
4. se autorizzato, genera risposta viewer
5. il viewer carica il PDF tramite canale protetto o stream controllato

---

## 11. Libreria digitale

## 11.1 Query logica
La libreria digitale non deve dipendere dai soli ordini WooCommerce.

Fonte primaria:
- `biblio_user_accesses`

Dati arricchiti da:
- `biblio_books`
- eventualmente WooCommerce per riferimenti ordine

## 11.2 Dati necessari per card libreria
1. copertina
2. titolo
3. autore
4. stato accesso
5. data scadenza se presente
6. CTA coerenti:
   - `Leggi`
   - `Rinnova`
   - `Acquista definitivo`

## 11.3 Stati minimi UI
1. acquistato
2. noleggio attivo
3. noleggio scaduto

---

## 12. Flussi tecnici principali

## 12.1 Acquisto cartaceo

1. utente loggato aggiunge cartaceo al carrello
2. checkout WooCommerce
3. pagamento riuscito
4. ordine marcato secondo stato WooCommerce
5. nessun accesso digitale creato
6. admin aggiorna manualmente stato spedizione

## 12.2 Acquisto e-book definitivo

1. utente loggato aggiunge prodotto `ebook_acquisto`
2. checkout WooCommerce
3. pagamento riuscito
4. hook WooCommerce intercetta ordine completato/pagato
5. plugin Bibliò crea record in `biblio_user_accesses`
6. accesso permanente immediatamente disponibile

## 12.3 Noleggio e-book

1. utente loggato sceglie un piano
2. checkout WooCommerce
3. pagamento riuscito
4. hook crea record accesso con:
   - `started_at = now`
   - `expires_at = now + durata`
   - `status = attivo`
5. titolo visibile in libreria digitale

## 12.4 Scadenza noleggio

1. job schedulato periodico controlla accessi scaduti
2. aggiorna status a `scaduto`
3. il viewer nega l'apertura del PDF
4. UI mostra CTA di rinnovo o acquisto definitivo

## 12.5 Rinnovo noleggio
Questo punto non è ancora definito in modo formale.

### Decisione aperta
Va chiarito se il rinnovo:
1. estende dalla data di scadenza attuale
2. riparte dalla data di nuovo pagamento

### Raccomandazione tecnica
Estendere dalla data più favorevole tra:
- `expires_at` attuale se ancora futuro
- `now` se già scaduto

È una proposta, non una decisione già chiusa.

## 12.6 Conversione noleggio → acquisto definitivo

1. utente clicca `Acquista definitivo`
2. sistema recupera:
   - prezzo e-book definitivo
   - totale speso nel noleggio rilevante
3. calcola differenza
4. genera checkout dedicato
5. a pagamento riuscito:
   - crea o aggiorna accesso permanente
   - stato noleggio precedente può diventare `convertito`
   - accesso definitivo diventa `attivo`

---

## 13. Regola "ordine di un solo tipo"

## 13.1 Obiettivo
Evitare ordini misti tra:
1. cartaceo
2. ebook_acquisto
3. ebook_noleggio

## 13.2 Implementazione consigliata
Aggiungere una validazione custom in WooCommerce:

1. ogni prodotto/modalità ha un metadato `biblio_order_type`
2. all'aggiunta al carrello:
   - se carrello vuoto, ok
   - se carrello contiene tipo diverso, bloccare aggiunta
3. messaggio utente chiaro:
   - "Per completare questo acquisto devi svuotare il carrello o concludere separatamente."

Questa logica è semplice e adatta all'MVP.

---

## 14. Access control PDF

## 14.1 Requisiti minimi

1. utente autenticato
2. accesso attivo per il titolo
3. PDF non esposto pubblicamente
4. controllo server-side prima della lettura

## 14.2 Strategia consigliata MVP

### Possibile approccio
1. route custom WordPress tipo `/reader/{book_id}`
2. controller PHP verifica diritto
3. rendering pagina viewer
4. viewer chiama endpoint protetto per il file o stream

## 14.3 Rischi accettati nell'MVP
Con protezione base:
1. non si elimina del tutto il rischio di estrazione
2. si riduce l'accesso casuale/non autorizzato
3. non è un DRM forte

---

## 15. MyBibliò — specifica tecnica MVP

## 15.1 Regola di base
MyBibliò risponde **solo** su libri presenti nel catalogo Bibliò.

## 15.2 Pipeline tecnica consigliata

1. utente loggato invia messaggio
2. backend applica parsing leggero dell'intento
3. backend costruisce filtri SQL semplici su:
   - categoria
   - autore
   - prezzo
   - numero pagine
   - presenza modalità
4. backend recupera un set limitato di libri
5. backend costruisce prompt con:
   - contesto sistema
   - richiesta utente
   - lista libri candidati
6. provider AI genera risposta naturale
7. sistema restituisce risposta + link alle schede reali

## 15.3 Requisiti di controllo
1. se il retrieval non trova libri, il modello non deve inventare risultati
2. la risposta deve esplicitare quando non ci sono match sufficienti
3. il prompt deve vietare titoli fuori catalogo
4. il numero di libri candidati deve essere limitato

## 15.4 Dati passati al modello
Solo campi base:
1. titolo
2. autore
3. descrizione
4. categoria
5. prezzo
6. numero pagine

## 15.5 Endpoint logico
Possibile endpoint custom REST o AJAX protetto da sessione utente.

## 15.6 Rischi tecnici principali
1. risposta troppo generica se i filtri sono deboli
2. inventiva del modello se il prompt non è stretto
3. costi API se non si limita il volume

---

## 16. Eventi e hook WooCommerce richiesti

## 16.1 Hook o eventi da intercettare
A livello concettuale serviranno hook per:

1. creazione/aggiornamento ordine pagato
2. completamento pagamento
3. annullamento/rimborso ordine
4. aggiunta al carrello
5. visualizzazione area account

## 16.2 Effetti attesi
- creare accessi digitali
- bloccare carrelli misti
- aggiornare libreria
- invalidare accessi in caso di annullamenti rilevanti

> La lista concreta degli hook WordPress/WooCommerce andrà definita nella fase di implementazione dettagliata.

---

## 17. Backoffice admin — specifica tecnica

## 17.1 Funzioni richieste
1. import Excel
2. lista titoli
3. creazione titolo manuale
4. modifica titolo
5. gestione modalità
6. gestione piani noleggio
7. upload PDF
8. pubblicazione / attivazione / disattivazione

## 17.2 UX minima consigliata
Per l'MVP è preferibile evitare un pannello dispersivo.

### Struttura minima
1. schermata elenco titoli
2. form titolo
3. sezione modalità
4. sezione piani noleggio
5. sezione PDF
6. tool import Excel

## 17.3 Validazioni lato admin
1. non pubblicare titolo digitale senza PDF
2. non attivare `ebook_noleggio` senza almeno un piano attivo
3. non attivare `ebook_acquisto` senza prezzo
4. non attivare `cartaceo` senza prezzo

---

## 18. Sicurezza minima MVP

## 18.1 Requisiti minimi
1. controllo capability admin per tutte le funzioni di backoffice
2. nonce WordPress per form sensibili
3. sanificazione input
4. prepared statements sulle query custom
5. escaping output
6. access control server-side per libreria e reader
7. rate limit semplice per chatbot o almeno throttling applicativo

## 18.2 Privacy minima
1. non inviare all'AI più dati utente del necessario
2. non includere dati sensibili nei prompt
3. log tecnici minimizzati
4. policy privacy da allineare in seguito

---

## 19. Performance minima MVP

1. query indicizzate su:
   - `book_id`
   - `modalita_id`
   - `user_id`
   - `status`
   - `expires_at`

2. libreria digitale:
   - paginazione o lazy loading se necessario

3. chatbot:
   - limitare numero risultati passati al modello
   - limitare dimensione prompt

4. PDF:
   - evitare file serviti senza controllo
   - valutare caching solo dove non indebolisce i controlli

---

## 20. Logging e osservabilità minimi

## 20.1 Da tracciare
1. import catalogo
2. creazione accesso e-book
3. rinnovo noleggio
4. conversione noleggio → acquisto
5. revoche accesso da scadenza
6. errori viewer PDF
7. errori chatbot

## 20.2 Livelli minimi
1. info
2. warning
3. error

---

## 21. Test minimi richiesti

## 21.1 Test funzionali tecnici
1. import titolo valido
2. import con riferimenti rotti
3. acquisto cartaceo
4. acquisto e-book definitivo
5. noleggio e-book
6. scadenza noleggio
7. blocco carrello misto
8. accesso viewer con diritto valido
9. blocco viewer senza diritto
10. conversione noleggio → acquisto

## 21.2 Test admin
1. creazione manuale titolo
2. attivazione modalità
3. caricamento PDF
4. blocco pubblicazione incoerente

## 21.3 Test chatbot
1. match catalogo presente
2. nessun risultato coerente
3. richiesta ambigua
4. richiesta fuori catalogo

---

## 22. Punti aperti prima della build tecnica definitiva

Questi punti sono ancora da chiudere in modo esplicito:

1. **mapping preciso con WooCommerce**
   - confermare se usare un prodotto WooCommerce per ogni modalità

2. **regola rinnovo noleggio**
   - estensione da scadenza o da pagamento

3. **gestione rimborsi**
   - cosa succede agli accessi digitali in caso di rimborso

4. **gestione copertina**
   - URL esterno vs media library WordPress

5. **storage PDF**
   - media library standard vs percorso protetto custom

6. **logica ordine completato/pagato**
   - su quale stato WooCommerce scatta l'attivazione definitiva degli accessi

---

## 23. Raccomandazioni pratiche per il prossimo step

Prima di scrivere il codice, il passo più utile è produrre tre artefatti tecnici:

1. **schema database SQL v1**
2. **mappatura WooCommerce ↔ entità Bibliò**
3. **specifica del file Excel definitiva con colonne esatte**

Questi tre deliverable abbassano molto il rischio di refactor successivi.

---

## 24. Conclusione

La soluzione tecnica MVP consigliata per Bibliò è:

1. **WordPress + WooCommerce come base commerciale**
2. **plugin custom Bibliò come cuore della logica dominio**
3. **tabelle SQL custom per accessi, noleggi e modalità**
4. **PDF viewer protetto con accesso server-side**
5. **MyBibliò controllato da retrieval SQL senza vector DB**

Questa architettura è coerente con l'MVP deciso finora:
- evita complessità inutili
- non forza WooCommerce oltre il necessario
- lascia spazio a evoluzioni future
- permette di partire con uno sviluppo realistico
