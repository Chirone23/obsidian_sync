# Serverino — Tool da integrare

Strumenti candidati per estendere il [[README|Serverino Bot]] (Phase 2+). Tutti pensati per la pipeline **ingestion documenti → contesto per il bot**.

Collegato a: [[progetti/Serverino/README]] · [[progetti/Serverino/SPECS]]

---

## OpenWA — canale WhatsApp self-hosted

**Link:** https://www.open-wa.org/ · [[knowladge/OpenWA - WhatsApp API self-hosted]]
**Licenza:** MIT · self-hosted (Docker)

API HTTP REST per WhatsApp con webhook real-time. Aggiungerebbe un **secondo canale** oltre a Telegram (oggi unico canale del bot). Stack Node/NestJS, engine `whatsapp-web.js` o `baileys`.
⚠️ Engine non ufficiali → rischio ban account WhatsApp se usato aggressivamente. Non è la Business API di Meta.

## Tesseract OCR — estrazione testo da immagini

**Link:** https://github.com/tesseract-ocr/tesseract
**Licenza:** Apache 2.0 · CLI + libreria `libtesseract`

Motore OCR (LSTM, >100 lingue, incluso italiano). Estrae testo da PNG/JPEG/TIFF → output text/PDF/hOCR. Abilita il bot a leggere **immagini/scan** inviati via chat.
```bash
tesseract immagine.png output -l ita
```

## MarkItDown — conversione file → Markdown per LLM

**Link:** https://github.com/microsoft/markitdown
**Licenza:** MIT · Python 3.10+

Utility Microsoft che converte PDF, Word, Excel, PowerPoint, immagini (con OCR), audio (con trascrizione), HTML, CSV/JSON/XML, EPub → **Markdown pulito per LLM**, preservando heading/liste/tabelle. Tassello ideale per dare al bot documenti eterogenei come contesto.

---

## Pipeline ipotizzata

`Allegato (Telegram/WhatsApp) → MarkItDown (o Tesseract se immagine) → .md → contesto DeepSeek`
