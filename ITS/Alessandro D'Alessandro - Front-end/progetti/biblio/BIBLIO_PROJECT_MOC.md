# 📚 Biblio Project MOC

> **Mappa di contenuto** per il progetto Biblio (piattaforma di noleggio + vendita ebook)

---

## 📋 Documenti Principali

### Specifiche
- [[biblio_specs_funzionale_mvp|Spec Funzionale MVP]] — Requisiti business + use cases
- [[biblio_spec_tecnica_mvp_v0_1|Spec Tecnica MVP v0.1]] — Architettura tecnica MVP

### Template & Implementazione
- [[BIBLIO_WORDPRESS_TEMPLATE_2026|WordPress Template 2026 — Enterprise Edition]] ⭐ **START HERE**
  - Stack tecnico, security hardening, performance optimization
  - Database schema completo, API endpoints, digital products workflow
  - Backup strategy, disaster recovery, CI/CD
  - Best practices 2026 from WooCommerce, WordPress dev docs

- [[BIBLIO_SETUP_GUIDE|Setup Guide Completo]] — Istruzioni step-by-step per MVP

### Assets & Media
- `Bibliò_Neural_Reading_Ecosystem.pdf` — Concept doc AI/chatbot
- `biblio_spec_tecnica_mvp_v0_1.md.pdf` — Versione printabile spec tecnica

---

## 🎯 Decisioni Architetturali Bloccate

| Decisione | Status | Note |
|-----------|--------|------|
| WordPress + WooCommerce | ✅ Confermato | MVP commerciale proven stack |
| Custom tabelle Biblio | ✅ Confermato | Per accessi, noleggi, modalità |
| PDF viewer protetto | ✅ Confermato | Accesso server-side, no URL pubblici |
| MyBiblio (chatbot) | ✅ Confermato | Retrieval SQL + LLM per risposta naturale |
| Hosting managed | ✅ Confermato | PHP 8.3+, Redis, backup automatici |
| Opzione A: prodotto WC per modalità | ✅ Consigliato | Più semplice, checkout lineare |

---

## ❓ Punti Aperti (pre-implementazione)

1. **Mapping WooCommerce esatto** — Confermare se un prodotto per ogni modalità
2. **Regola rinnovo noleggio** — Estensione da scadenza o da pagamento?
3. **Gestione rimborsi** — Cosa succede agli accessi digitali?
4. **Storage PDF** — Media library standard vs percorso custom?
5. **Copertina immagini** — URL esterno vs media WordPress?

---

## 🚀 Flussi Principali Implementati

### 1. Acquisto Cartaceo
WooCommerce → Ordine → Admin spedizione manuale

### 2. Acquisto eBook Definitivo
WooCommerce → Ordine completato → Hook crea accesso perpetuo → Libreria digitale

### 3. Noleggio eBook
WooCommerce → Ordine completato → Hook crea accesso temporaneo (started_at + expires_at)

### 4. Scadenza Noleggio
Cron job orario → Aggiorna stato accessi → UI mostra "Rinnova" / "Acquista definitivo"

### 5. Conversione Noleggio → Acquisto Definitivo
Utente clicca "Acquista definitivo" → Calcolo upgrade → Nuovo checkout → Accesso perpetuo

### 6. MyBiblio (Chatbot)
Utente invia domanda → SQL retrieval catalogo → Prompt + libri candidati → LLM → Risposta naturale

---

## 🔧 Stack Tecnico Confermato

```
┌──────────────────────────────────────┐
│   BIBLIO 2026 TECH STACK             │
├──────────────────────────────────────┤
│ OS: Ubuntu 22.04 LTS                 │
│ Web: Nginx (fastcgi cache + reverse) │
│ PHP: 8.3+ (managed hosting)          │
│ Database: MySQL 8.0 + custom tables  │
│ Cache: Redis 7.0+                    │
│ CDN: Cloudflare (images + static)    │
│ WP Core: 6.6+                        │
│ WooCommerce: 9.1+                    │
│ Theme: Blocksy/Neve (FSE-ready)      │
│ Payment: WooPayments + Stripe        │
│ AI: OpenAI/Anthropic (via API)       │
│ Storage: S3-compatible (Wasabi)      │
└──────────────────────────────────────┘
```

---

## 📊 Database Schema Principale

### Custom Tables
1. **biblio_libri** — Metadata editoriale (title, author, ISBN, pages, etc.)
2. **biblio_modalita** — Modalità vendibili (cartaceo, ebook_acquisto, ebook_noleggio)
3. **biblio_piani_noleggio** — Piani temporali (7gg, 30gg, 90gg + prezzo)
4. **biblio_accessi_ebook** — Accessi utente (user_id, book_id, tipo, scadenza, stato)
5. **biblio_conversioni** — History noleggio → acquisto
6. **biblio_download_log** — Audit dei download (compliance)

---

## 📦 Plugin Stack MVP

### Must-Have
- **WooCommerce** — Core commerce
- **WP Rocket** — Caching + performance
- **Wordfence** — Security + firewall
- **Yoast SEO** — SEO optimization
- **Akismet** — Spam filtering

### Recommended
- **Advanced Custom Fields (ACF)** — Custom fields
- **UpdraftPlus** — Backup management
- **Mailchimp for WooCommerce** — Email automation
- **ShortPixel** — Image optimization
- **Query Monitor** — Debug + profiling

---

## 🔐 Security Checklist

- [ ] SSL/TLS (Let's Encrypt)
- [ ] 2FA for all admin accounts
- [ ] Wordfence security scanning
- [ ] File permissions hardened
- [ ] Database user with limited privileges
- [ ] wp-config.php secured (600)
- [ ] Backup strategy tested
- [ ] WAF Cloudflare enabled
- [ ] SSH hardened (no root, key-only)

---

## 📈 Performance Targets

| Metrica | Target |
|---------|--------|
| Lighthouse Performance | 90+ |
| Lighthouse Accessibility | 95+ |
| Lighthouse Best Practices | 95+ |
| Lighthouse SEO | 95+ |
| LCP (Largest Contentful Paint) | <2.5s |
| CLS (Cumulative Layout Shift) | <0.1 |
| FID/INP | <100ms |

---

## 🧪 Test Minimi Richiesti

### Funzionali
- [ ] Acquisto cartaceo → Ordine WC
- [ ] Acquisto ebook → Accesso permanente creato
- [ ] Noleggio ebook → Accesso temporaneo creato
- [ ] Scadenza noleggio → Stato aggiornato a "scaduto"
- [ ] Conversione noleggio → acquisto → Accesso permanente
- [ ] Blocco accesso non autorizzato al PDF

### Admin
- [ ] Import Excel titoli validi
- [ ] Import con referenze rotte → Errori segnalati
- [ ] Upload PDF → Collegamento al titolo

### Chatbot
- [ ] Domanda con match nel catalogo → Risultati rilevanti
- [ ] Domanda senza match → "Non ho risultati"

---

## 📞 Contatti & Ownership

- **Progetto** — Alessandro D'Alessandro (frontend lead)
- **Backend** — [Chi?]
- **Database** — [Chi?]
- **Security** — [Chi?]

---

## 📅 Timeline Consigliato

| Fase | Durata | Milestone |
|------|--------|-----------|
| Setup + Deploy staging | 1w | WP installato, MySQL pronto |
| Import catalogo + tabelle custom | 1-2w | Dati di test caricati |
| Plugin + theme child | 1w | Child theme + caching funzionante |
| Core flows (carrello → ordine → accesso) | 2w | MVP transazioni funzionante |
| PDF viewer + libreria digitale | 1-2w | Lettura ebook working |
| MyBiblio chatbot | 1-2w | Integration con LLM |
| Testing + security audit | 1w | Wordfence clean, tests passati |
| **Go-live** | - | 🚀 |

---

## 🎓 Learning Resources (Linked)

Vedi anche:
- [[skill/WordPress]] — Setup + best practices
- [[skill/WooCommerce]] — e-commerce specifico
- [[skill/Database Design]] — Schema + optimization
- [[skill/Security]] — Hardening checklist

---

*MOC versione: 1.0 (2026-04-24)*  
*Last updated: 2026-04-24*  
*Backlinks: [[ITS/]], [[Automation MOC]], [[Front-end MOC]]*
