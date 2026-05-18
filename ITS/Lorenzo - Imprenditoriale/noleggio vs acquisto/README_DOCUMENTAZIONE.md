# Documentazione — Configuratore Acquisto vs Noleggio LTM

**Progetto:** Configuratore B2C che confronta acquisto auto vs noleggio a lungo termine, con bias "sottobanco" verso il noleggio.
**Owner:** Lorenzo (ITS — Imprenditoriale)
**Fase corrente:** Brainstorming pre-design
**Ultimo aggiornamento indice:** 2026-05-18

---

## 1. Contesto e dettaglio progetto

| File | Scopo |
|------|-------|
| `dettaglio-bot.docs` | **Documento principale** — obiettivo, fonti dati, sintesi numerica preventivi, voci configuratore, strategia "sottobanco", aperti |
| [[Prezzi Auto MOC]] | Prezzi acquisto cliente B2C + confronto rapido con canoni NLT |

---

## 2. Brainstorming e decisioni

| File | Scopo |
|------|-------|
| [[BRAINSTORMING]] | Sessioni di brainstorming su modello dati, leve pro-noleggio, stack tecnico |
| [[DECISIONI]] | Decisioni prese (con data e motivazione) — append-only |

---

## 3. Diario sessioni

| File | Scopo |
|------|-------|
| [[SESSION_HANDOFF]] | Stato tra sessioni: ✅ completato · 🔄 in progress · ⏳ prossimi step · blocchi aperti |

---

## 4. Fonti dati

| File / Cartella | Contenuto |
|---|---|
| `FIAT 500.pdf`, `KIA PICANTO.pdf`, `NISSAN JUKE.pdf`, `VOLKSWAGEN T-CROSS.pdf`, `VOLVO XC40 B3.pdf` | Preventivi reali ALD Automotive 4Vantage (48m / 120.000 km) |
| `4moovy PPT_DEF.pptx` | Slide presentazione progetto |
| `../../../execution/extract_noleggio_pdfs.py` | Script estrazione testo da PDF |
| `../../../execution/parse_noleggio_preventivi.py` | Parser strutturato → JSON |
| `../../../.tmp/noleggio_extract/preventivi_noleggio.json` | Output JSON con campi chiave per ogni auto |

---

## 5. Spec tecnica (futuro)

Quando l'idea sarà validata, qui andrà:
- `Specifica Tecnica v1.md` (bozza iniziale)
- `spec precedenti/` (versioni superate)
- Eventuali review e gap analysis

---

## Connessioni

- [[ITS MOC]]
- [[Index MOC]]
