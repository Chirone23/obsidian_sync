C:\Users\Chirone\Downloads\biblio-theme.zip (22 KB, 18 file) 
### Come usarlo 
1. WordPress admin → Aspetto → Temi → Aggiungi nuovo → Carica tema → seleziona lo zip → Attiva. 
2. Impostazioni → Permalink → "Nome articolo" → Salva (registra le rewrite del CPT book). 
3. Aspetto → Menu → crea menu "Menu principale" (es. Catalogo, Plus, Noleggio vs Acquisto). 
4. Libri → Aggiungi libro → compila box "Dettagli libro" (autore, prezzo, ISBN, ecc.) + assegna un Genere. 
5. Crea pagine statiche /plus/, /noleggio-vs-acquisto/, /mybiblio/, /contatti/. 
### Cosa fa il tema 
- Design system Bibliò 1:1 (colori, tipografia Playfair/Lora/Inter, ombre, radius, motion). - CPT book + tassonomia book_genre + meta box nativo (no ACF, niente plugin extra → rispetta inode). 
- Home (front-page.php): hero con 3 copertine flottanti, trust strip, Selezione settimana, Categorie, Plus banner, Novità. 
- Catalogo (archive-book.php): sidebar filtri (categoria, formato, ordina per prezzo/rating), ricerca, paginazione. 
- Dettaglio libro (single-book.php): card buy/rent doppia, copertina grande, breadcrumb, related. 
- Copertine generate via CSS gradient (6 stili) se manca l'immagine in evidenza → zero asset extra. 
- WooCommerce-ready (add_theme_support + nav carrello condizionato), ma non obbligatorio. 
- Ottimizzazioni Infinity Free: heartbeat 60s, emoji/oembed/wlwmanifest rimossi, CSS unico, JS minimale. Cosa NON è incluso (intenzionalmente, per stare nei tempi) 
- Pagine Plus/Account/Checkout statiche (le crei da admin) 
- Chat MyBibliò reale (il pulsante porta a /mybiblio/) 
- Integrazione WC sul CPT book (PHASE 2, come da brief) Tutti i dettagli + checklist di setup sono nel README.md dentro lo zip.