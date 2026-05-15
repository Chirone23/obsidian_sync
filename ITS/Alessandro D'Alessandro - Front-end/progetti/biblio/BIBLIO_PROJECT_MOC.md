# Biblio Project MOC

> Mappa di contenuto per il progetto **Biblio** (piattaforma noleggio + vendita ebook).
> Target deploy: **InfinityFree** (hosting gratuito) + WordPress + WooCommerce.

---

## Documenti principali

### Specifiche
- [[biblio_specs_funzionale_mvp]] — Requisiti funzionali MVP
- [[biblio_spec_tecnica_mvp_v0_1]] — Architettura tecnica MVP (decisioni + punti aperti)

### Setup & template operativi
- [[BIBLIO_SETUP_GUIDE]] — Setup iniziale: DB custom, prodotti di prova, plugin, WooCommerce
- [[BIBLIO_WORDPRESS_TEMPLATE_2026]] — Template WordPress + child theme Astra + workaround InfinityFree

### Audit & plan tema attuale
- [[BIBLIO_AUDIT_2026-05-14]] — Audit completo `biblio-theme` + plan 6 fasi (cleanup, refactor CSS, icons, chat MyBibliò, polish, perf)

### Storia delle modifiche
- [[02_Development/CHANGELOG_biblio-theme]] — Racconto sessione per sessione dei cambiamenti al tema (bug fix, refactor, nuove feature)

### Asset
- `Bibliò_Neural_Reading_Ecosystem.pdf` — Concept MyBibliò (chatbot AI)

---

## Stack confermato

| Componente | Scelta |
|---|---|
| Hosting | **InfinityFree** (gratuito) |
| CMS | WordPress 6.6+ |
| E-commerce | WooCommerce 9.x |
| Tema | `biblio-theme` standalone (v0.3.0) |
| Cache | WP Super Cache (file-based) |
| Backup | UpdraftPlus → Google Drive |
| Email | WP Mail SMTP + Brevo (300 email/giorno gratis) |
| Cron | cron-job.org esterno (wp-cron disabilitato) |
| Pagamento MVP | Bonifico bancario (WooCommerce integrato) |

---

## Decisioni bloccate

| Decisione | Status | Note |
|---|---|---|
| Stack WordPress + WooCommerce | ✅ | Da spec tecnica §2 |
| Tabelle custom (modalità, piani, accessi) | ✅ | Definite in `BIBLIO_SETUP_GUIDE` |
| PDF viewer server-side (no URL pubblici) | ✅ | Requisito da spec tecnica §10, §14 |
| MyBibliò = retrieval SQL + LLM (no vector DB) | ✅ | Da spec tecnica §15 |
| Un prodotto WooCommerce per modalità | ✅ | Opzione A raccomandata in spec tecnica §8.2 |
| Regola carrello di un solo tipo | ✅ | Implementata in `functions.php` del child theme |

---

## Punti ancora aperti (vedi spec tecnica §22)

1. **Regola rinnovo noleggio** — estensione da `expires_at` o da `now`?
2. **Gestione rimborsi** — cosa succede agli accessi in caso di rimborso WooCommerce?
3. **Storage PDF** — media library vs cartella protetta custom?
4. **Copertina libro** — URL esterno vs media library?
5. **Pagamento online reale** — Stripe non funziona su InfinityFree; MVP usa bonifico. Migrare a hosting cURL-libero quando serve accettare carte?

---

## Limiti noti InfinityFree da tenere a mente

- ❌ cURL outbound bloccato → niente Stripe/Mailchimp/webhook esterni
- ❌ No cron reali → wp-cron via traffico o cron-job.org
- ❌ Memory limit 256 MB, execution time ~10s → evitare plugin pesanti
- ❌ Upload max 10 MB dal WP admin → PDF grandi via FTP
- ❌ DB max ~400 MB → indici necessari sulle custom tables
- ❌ Email `mail()` inaffidabile → SMTP esterno obbligatorio

Se il progetto deve andare in produzione reale con clienti paganti, **migrare a hosting con cURL libero** (es. SiteGround, Keliweb, IONOS).

---

## Flussi implementati nel template

| Flusso | Dove |
|---|---|
| Acquisto cartaceo | WooCommerce standard, nessun accesso digitale creato |
| Acquisto ebook definitivo | Hook `woocommerce_order_status_completed` in `functions.php` → riga in `biblio_accessi_ebook` |
| Noleggio ebook | Stesso hook, con `data_fine` calcolata dal piano |
| Scadenza noleggio | `biblio_aggiorna_scadenze()` via cron-job.org |
| Blocco carrello misto | Filter `woocommerce_add_to_cart_validation` |
| Libreria utente | Template `page-libreria.php` (richiede login) |
| Catalogo | Template `page-catalogo.php` + filtri JS |
| Conversione noleggio → acquisto | ⏳ Non ancora implementato (vedi spec §12.6) |
| MyBibliò chatbot | ✅ MVP in v0.3.0 — `inc/chatbot.php` + Groq llama-3.1-8b |
| PDF viewer protetto | ⏳ Da sviluppare — endpoint `/reader?accesso=...` |

---

## Custom tables (nomi reali in uso)

Definite in `BIBLIO_SETUP_GUIDE` → STEP 1. Allineati ai nomi del setup guide (non a quelli della spec tecnica v0.1 che proponeva `biblio_books` / `biblio_user_accesses`).

- `biblio_libri` — metadati editoriali
- `biblio_modalita` — cartaceo / ebook_acquisto / ebook_noleggio (link a `woo_product_id`)
- `biblio_piani_noleggio` — durata + prezzo per ogni modalità noleggio
- `biblio_accessi_ebook` — accessi utente (attivo / scaduto / convertito)
- `biblio_conversioni` — storico noleggio → acquisto

> **Nota di coerenza:** i nomi tabelle in `BIBLIO_SETUP_GUIDE` e quelli proposti nella `biblio_spec_tecnica_mvp_v0_1` §7 divergono. Il template `BIBLIO_WORDPRESS_TEMPLATE_2026` segue il setup guide. Decidere quale diventa la source of truth prima dell'implementazione definitiva.

---

## Prossimi passi consigliati

1. Allineare nomi tabelle tra spec tecnica e setup guide
2. Chiudere i 5 punti aperti (§22 della spec tecnica)
3. Deploy iniziale su InfinityFree seguendo `BIBLIO_WORDPRESS_TEMPLATE_2026`
4. Caricare i 5 libri di prova (`BIBLIO_SETUP_GUIDE` STEP 2)
5. Testare i 3 flussi: acquisto cartaceo, acquisto ebook, noleggio ebook
6. Implementare PDF viewer protetto
7. Solo dopo: affrontare MyBibliò (chatbot AI)

---

*MOC v1.1 — allineato a target hosting InfinityFree*
