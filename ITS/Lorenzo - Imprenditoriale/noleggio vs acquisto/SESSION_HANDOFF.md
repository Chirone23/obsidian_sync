# Session Handoff — Configuratore Acquisto vs Noleggio

> Stato del progetto tra sessioni. Aggiornare all'inizio e fine di ogni sessione.

---

## Stato attuale (post-sessione 2026-05-18)

### ✅ Completato
- Estrazione testo da 5 PDF preventivi ALD → `.tmp/noleggio_extract/*.txt`
- Parsing strutturato → `.tmp/noleggio_extract/preventivi_noleggio.json`
- Script riutilizzabili in `execution/extract_noleggio_pdfs.py` + `execution/parse_noleggio_preventivi.py`
- Struttura documentazione progetto (README + dettaglio-bot + BRAINSTORMING + DECISIONI + SESSION_HANDOFF)

### 🔄 In progress
- Brainstorming modello dati e logica configuratore (sessione 1 aperta in [[BRAINSTORMING]])

### ⏳ Prossimi step
- Definire formula "costo totale di possesso" lato acquisto
- Decidere stack tecnico
- Decidere ampiezza catalogo auto
- Disegnare wireframe UI (card affiancate? timeline?)

### ❓ Blocchi / domande aperte
- Fonti per dati acquisto (interessi finanziamento, assicurazione retail, manutenzione media, svalutazione): input utente o tabelle medie di mercato?
- Git push bloccato per divergenza con remote (conflict su `moc/Claude Code Setup MOC.md` non legato a questo progetto) — da risolvere manualmente

### 📁 File modificati in questa sessione
- `ITS/Lorenzo - Imprenditoriale/noleggio vs acquisto/dettaglio-bot.md` (rinominato da `CONTESTO.md`)
- `ITS/Lorenzo - Imprenditoriale/noleggio vs acquisto/README_DOCUMENTAZIONE.md` (nuovo)
- `ITS/Lorenzo - Imprenditoriale/noleggio vs acquisto/BRAINSTORMING.md` (nuovo)
- `ITS/Lorenzo - Imprenditoriale/noleggio vs acquisto/DECISIONI.md` (nuovo)
- `ITS/Lorenzo - Imprenditoriale/noleggio vs acquisto/SESSION_HANDOFF.md` (nuovo)
- `execution/extract_noleggio_pdfs.py` (nuovo)
- `execution/parse_noleggio_preventivi.py` (nuovo)
