# Bibliò — Punto di partenza

> Leggi questo file per primo. Ti dice cosa leggere dopo in base a cosa vuoi fare.

---

## Stato attuale

**Tema live:** `https://biblio.web1337.net/` — versione **v0.3.0**
**Sorgente locale:** `infinityfree/wp/wp-content/themes/biblio-theme/`
**Deploy:** file singolo via File Manager InfinityFree (no zip, no WP admin)

---

## Cosa leggere in base al task

### Qualsiasi cosa
→ [[CONTEXT_NUOVA_SESSIONE]] — stack, workflow deploy, struttura file, design system, vincoli InfinityFree

### Capire cosa manca e cosa fare dopo
→ [[BIBLIO_AUDIT_2026-05-14]] — 12 problemi identificati + piano in 6 fasi (Fase 1+2 già eseguite in v0.3.0)

### Lavorare sul chatbot MyBibliò
→ [[MyBibliò AI Implementation MOC]] — stack Groq, roadmap 4 fasi, vincoli non negoziabili

### Capire perché le cose sono come sono
→ [[02_Development/CHANGELOG_biblio-theme]] — storia completa dal concept React fino a v0.3.0

### Decisioni già chiuse (non riaprire senza motivo)
→ [[BIBLIO_PROJECT_MOC]] — stack confermato, decisioni bloccate, punti aperti

---

## Prossime fasi (dall'audit)

| Fase | Contenuto | Stato |
|---|---|---|
| 0 | Git snapshot baseline | ✅ |
| 1 | Cleanup CSS + dead code | ✅ v0.3.0 |
| 2 | CSS split in 4 file modulari | ✅ v0.3.0 |
| 3 | Icon set SVG + hamburger mobile + bottom nav | ⏳ |
| 4 | MyBibliò drawer + pagina `/mybiblio/` | ⏳ |
| 5 | Polish editoriale (microcopy, metriche reali) | ⏳ |
| 6 | Performance (self-host font, WP Super Cache) | ⏳ |

---

## Prompt di avvio consigliato

```
Leggi README.md e CONTEXT_NUOVA_SESSIONE.md,
poi voglio lavorare su [Fase N / descrizione task].
```
