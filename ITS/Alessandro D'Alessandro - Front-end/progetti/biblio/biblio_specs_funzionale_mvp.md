# SPEC MVP — Bibliò

## 1. Obiettivo del prodotto

1. Bibliò è una piattaforma e-commerce libraria che permette:
   1. acquisto di libri cartacei con spedizione
   2. acquisto definitivo di e-book leggibili in piattaforma
   3. noleggio temporaneo di e-book leggibili in piattaforma
   4. supporto alla scoperta del catalogo tramite chatbot AI MyBibliò

2. L’obiettivo dell’MVP è validare:
   1. interesse degli utenti per il modello ibrido cartaceo + e-book
   2. interesse per il noleggio digitale
   3. utilità del chatbot come assistente alla scelta
   4. sostenibilità operativa con stack WordPress/WooCommerce

---

## 2. Ambito MVP

1. L’MVP include:
   1. catalogo libri consultabile pubblicamente
   2. scheda libro unica per titolo
   3. registrazione e login con email + password
   4. acquisto cartaceo con spedizione semplice
   5. acquisto definitivo e-book con lettura in piattaforma
   6. noleggio e-book con piani multipli
   7. libreria digitale personale
   8. storico ordini cartacei
   9. backoffice admin per import, creazione e modifica libri
   10. chatbot MyBibliò disponibile ai soli utenti loggati

2. L’MVP non include:
   1. abbonamento Bibliò Plus
   2. login social
   3. gestione stock/magazzino cartaceo
   4. integrazioni corrieri e tracking avanzato
   5. DRM avanzato o protezioni anti-download evolute
   6. vector database
   7. chatbot generalista fuori catalogo
   8. ruolo Staff operativo
   9. recensioni/community
   10. memorie lunghe o profilazione AI avanzata

---

## 3. Ruoli utente

1. **Visitatore**
   1. può navigare il catalogo
   2. può vedere schede libro
   3. non può acquistare
   4. non può noleggiare
   5. non può usare MyBibliò

2. **Cliente registrato**
   1. può acquistare cartacei
   2. può acquistare e-book
   3. può noleggiare e-book
   4. può accedere alla libreria digitale
   5. può usare MyBibliò

3. **Admin**
   1. gestisce catalogo
   2. importa dati da Excel
   3. crea libri manualmente
   4. modifica modalità e piani di noleggio
   5. carica PDF e-book
   6. gestisce ordini e aggiornamento stati

---

## 4. Modello di business MVP

1. **Cartaceo**
   1. solo acquistabile
   2. spedizione fisica
   3. non leggibile in piattaforma

2. **E-book acquisto**
   1. acquistabile in modo definitivo
   2. leggibile solo dentro la piattaforma
   3. accesso attivato subito dopo pagamento riuscito

3. **E-book noleggio**
   1. noleggiabile per durata limitata
   2. leggibile solo dentro la piattaforma
   3. accesso attivato subito dopo pagamento riuscito
   4. alla scadenza l’accesso viene revocato
   5. l’utente può rinnovare
   6. l’utente può convertire in acquisto definitivo

4. **Conversione noleggio → acquisto**
   1. il cliente paga solo la differenza
   2. la differenza è calcolata tra:
      1. prezzo e-book definitivo
      2. totale già speso in quel percorso di noleggio

---

## 5. Modello catalogo

1. Ogni **titolo** ha una sola scheda libro.

2. Per ogni titolo possono esistere una o più modalità:
   1. cartaceo
   2. ebook_acquisto
   3. ebook_noleggio

3. Non tutti i titoli devono avere tutte le modalità.

4. Il titolo “solo cartaceo”:
   1. può essere acquistato
   2. non può essere letto in piattaforma

5. Il titolo con modalità digitali:
   1. usa un solo PDF associato al titolo
   2. quel PDF vale sia per acquisto definitivo sia per noleggio
   3. cambiano solo le regole di accesso

---

## 6. Scheda libro

1. La scheda libro è unica per titolo.

2. Deve mostrare almeno:
   1. copertina
   2. titolo
   3. autore
   4. ISBN
   5. descrizione
   6. categoria/genere
   7. numero pagine
   8. modalità disponibili

3. Le modalità devono essere selezionabili in pagina:
   1. cartaceo con prezzo
   2. e-book acquisto con prezzo
   3. e-book noleggio con elenco piani disponibili

4. Se il titolo non ha una modalità, quella modalità non deve comparire.

---

## 7. Regole di carrello e checkout

1. Un ordine può contenere solo elementi dello stesso tipo:
   1. solo cartacei
   2. oppure solo e-book acquisto
   3. oppure solo e-book noleggio

2. Non è consentito mescolare nello stesso ordine:
   1. cartaceo + e-book acquisto
   2. cartaceo + noleggio
   3. e-book acquisto + noleggio

3. Metodi di pagamento MVP:
   1. carta
   2. PayPal

4. Registrazione obbligatoria:
   1. il visitatore può navigare senza account
   2. per acquistare o noleggiare deve registrarsi/login

---

## 8. Regole di accesso agli e-book

1. **Acquisto definitivo e-book**
   1. accesso permanente
   2. attivazione automatica dopo pagamento riuscito

2. **Noleggio e-book**
   1. accesso temporaneo
   2. attivazione automatica dopo pagamento riuscito
   3. durata definita dal piano acquistato
   4. scadenza salvata a sistema
   5. accesso revocato automaticamente alla scadenza

3. **Rinnovo noleggio**
   1. consentito
   2. basato sui piani disponibili per quella modalità

4. **Conversione in acquisto definitivo**
   1. consentita
   2. disponibile dalla libreria digitale
   3. prezzo calcolato come differenza

5. **Reader MVP**
   1. PDF visualizzato nel browser
   2. protezione base
   3. accesso consentito solo a utenti autenticati con diritto attivo
   4. file non esposto pubblicamente in modo diretto

---

## 9. Area personale utente

1. L’area personale deve essere divisa in:
   1. **Libreria digitale**
   2. **Storico ordini cartacei**

2. La **Libreria digitale** contiene:
   1. e-book acquistati
   2. noleggi attivi
   3. noleggi scaduti

3. Per ogni card in libreria devono comparire:
   1. copertina
   2. titolo
   3. autore
   4. stato accesso
   5. data scadenza, se noleggio
   6. pulsante **Leggi**
   7. pulsante **Rinnova**, se noleggio attivo
   8. pulsante **Acquista definitivo**, se proviene da noleggio

4. Lo **Storico ordini cartacei** mostra:
   1. elenco ordini
   2. stato ordine
   3. dati essenziali dell’acquisto

---

## 10. Gestione spedizioni MVP

1. Spedizione solo per cartaceo.

2. Configurazione MVP:
   1. una o poche tariffe semplici
   2. nessuna integrazione con corrieri
   3. stato ordine aggiornato manualmente dall’admin

3. Non viene gestito stock di magazzino.

---

## 11. Backoffice admin MVP

1. L’admin può:
   1. importare catalogo da Excel
   2. creare un libro manualmente
   3. modificare un libro esistente
   4. configurare le modalità del titolo
   5. definire i prezzi di cartaceo ed e-book acquisto
   6. definire i piani di noleggio
   7. caricare il PDF del titolo
   8. pubblicare o aggiornare il titolo
   9. aggiornare gli stati degli ordini cartacei

2. Il backoffice deve prevedere validazioni minime:
   1. `book_id` univoco
   2. `modalita_id` univoco
   3. coerenza tra titolo e modalità
   4. presenza del PDF se esiste almeno una modalità digitale
   5. prezzo obbligatorio per cartaceo ed ebook_acquisto
   6. almeno un piano attivo se esiste ebook_noleggio

---

## 12. Import catalogo da Excel

1. Il formato ufficiale dell’MVP è:
   1. **un file Excel**
   2. con tre fogli:
      1. `libri`
      2. `modalita`
      3. `piani_noleggio`

2. **Foglio `libri`**
   1. una riga per titolo
   2. chiave primaria: `book_id`
   3. campi minimi consigliati:
      1. `book_id`
      2. `titolo`
      3. `autore`
      4. `isbn`
      5. `descrizione`
      6. `categoria`
      7. `copertina`
      8. `numero_pagine`
      9. `pdf_presente` o stato equivalente nel sistema
   4. il PDF non viene importato da Excel

3. **Foglio `modalita`**
   1. una riga per ogni modalità disponibile
   2. chiave primaria: `modalita_id`
   3. collegamento al titolo: `book_id`
   4. campi minimi:
      1. `modalita_id`
      2. `book_id`
      3. `tipo_modalita` (`cartaceo`, `ebook_acquisto`, `ebook_noleggio`)
      4. `prezzo` per `cartaceo` e `ebook_acquisto`
      5. `attivo`

4. **Foglio `piani_noleggio`**
   1. una riga per ogni piano
   2. campi minimi:
      1. `piano_id`
      2. `modalita_id`
      3. `durata_giorni`
      4. `prezzo`
      5. `attivo`

5. Associazione PDF:
   1. upload manuale da pannello admin
   2. collegamento al titolo
   3. obbligatorio se il titolo ha una modalità digitale

---

## 13. Chatbot MyBibliò MVP

1. MyBibliò è incluso nell’MVP.

2. È disponibile solo a utenti loggati.

3. Può parlare solo del catalogo Bibliò.

4. Non deve rispondere come consulente librario generale fuori catalogo.

5. Non usa vector DB.

6. Flusso consigliato:
   1. il sistema riceve la richiesta utente
   2. applica filtri controllati sul database
   3. recupera un sottoinsieme di libri coerenti
   4. passa quei risultati all’LLM
   5. l’LLM genera una risposta naturale
   6. la risposta include link o rimando alle schede libro reali

7. Dati usati dal chatbot nell’MVP:
   1. titolo
   2. autore
   3. descrizione
   4. categoria
   5. prezzo
   6. numero pagine

8. Cosa deve saper fare:
   1. consigliare libri del catalogo
   2. proporre opzioni coerenti al budget
   3. distinguere libri brevi/lunghi usando il numero pagine
   4. indirizzare verso la scheda libro

9. Cosa non deve fare:
   1. inventare libri non presenti
   2. parlare di cataloghi esterni
   3. gestire tracking ordini
   4. fornire supporto clienti avanzato
   5. mantenere memoria lunga tra sessioni

---

## 14. Architettura tecnica MVP

1. **CMS / Backoffice**
   1. WordPress

2. **E-commerce**
   1. WooCommerce

3. **Autenticazione**
   1. utenti WordPress/WooCommerce standard
   2. email + password
   3. recupero password standard

4. **Dati custom Bibliò**
   1. tabelle SQL custom per:
      1. modalità dei titoli
      2. piani di noleggio
      3. accessi e-book degli utenti
      4. stato/scadenza noleggi
      5. logica di conversione noleggio → acquisto

5. **Reader**
   1. PDF viewer in browser
   2. accesso protetto lato applicazione

6. **AI**
   1. integrazione LLM via API
   2. retrieval controllato da SQL
   3. nessun database vettoriale

---

## 15. Modello dati logico minimo

1. **Titolo**
   1. `book_id`
   2. metadati editoriali

2. **Modalità**
   1. `modalita_id`
   2. `book_id`
   3. `tipo_modalita`
   4. `prezzo`
   5. `attivo`

3. **Piano noleggio**
   1. `piano_id`
   2. `modalita_id`
   3. `durata_giorni`
   4. `prezzo`
   5. `attivo`

4. **Accesso e-book utente**
   1. utente
   2. titolo/modalità
   3. tipo accesso (`acquisto`, `noleggio`)
   4. data inizio
   5. data fine, se noleggio
   6. stato (`attivo`, `scaduto`, `convertito`)

5. **Storico conversione**
   1. importo già speso in noleggio
   2. prezzo e-book definitivo
   3. differenza dovuta

---

## 16. Automazioni MVP

1. **Pagamento e-book acquisto riuscito**
   1. crea accesso permanente

2. **Pagamento noleggio riuscito**
   1. crea accesso temporaneo
   2. imposta data inizio immediata
   3. imposta data fine in base al piano

3. **Scadenza noleggio**
   1. job schedulato revoca l’accesso
   2. stato aggiornato a scaduto

4. **Rinnovo**
   1. nuovo acquisto di un piano di noleggio compatibile
   2. aggiornamento accesso secondo regola da implementare

5. **Conversione**
   1. sistema calcola differenza
   2. genera checkout dedicato
   3. dopo pagamento aggiorna accesso a permanente

---

## 17. Fuori scope MVP

1. Bibliò Plus
2. recensioni utenti
3. notifiche avanzate prezzo/disponibilità
4. anti-piracy avanzato
5. app mobile
6. reader EPUB
7. magazzino e logistica avanzata
8. reporting avanzato
9. multi-admin con permessi granulari
10. AI con profilo utente persistente avanzato

---

## 18. Punti da confermare prima della specs tecnica definitiva

Questi punti non bloccano la spec funzionale, ma andranno chiusi per la fase tecnica esecutiva.

1. **Formato del campo copertina**
   1. URL
   2. media ID
   3. upload manuale

2. **Regola esatta del rinnovo**
   1. estende dalla scadenza corrente
   2. oppure riparte dalla data del nuovo pagamento

3. **Mappatura concreta tra entità Bibliò e prodotti WooCommerce**
   1. da definire in dettaglio tecnico
   2. è un punto importante della prossima fase

4. **Naming e schema definitivo delle tabelle custom SQL**
   1. da progettare nello step tecnico

---

## 19. Deliverable già pronti da derivare da questa spec

Da questa base possiamo già produrre in modo coerente:

1. **PRD / Product Requirements Document**
2. **spec tecnica backend**
3. **schema database**
4. **struttura Excel definitiva**
5. **user flow**
6. **wireframe delle pagine chiave**
7. **backlog MVP a sprint**
