# OCR fallback — decisione e criteri

> Knowledge tecnica SpecterAI. Stato: **detector implementato**, OCR **in roadmap** (non ancora scelto).
> Progetto: [[Specifica Tecnica v4 - SpecterAI]] · [[Progettistica AI MOC]]
> Data: 2026-06-24

---

## Il problema

Alcuni PDF (es. `Consip_CondizioniGeneraliRelativeAllaFornituraDiProdottiAgosto2018-A.pdf`)
producono **testo estratto corrotto**: `Đoŵplessivo` invece di `complessivo`, `CoŶtƌatto`
invece di `Contratto`.

**Causa diagnosticata:** i font sono subset con encoding Identity e **tabella ToUnicode
rotta** (`Calibri-Identity-H`, `Calibri-OneByteIdentityH`). Il mapping carattere→unicode è
sbagliato in modo sistematico (c→Đ, n→Ŷ, m→ŵ, r→ƌ, q→Ƌ).

**Conseguenza:** nessuna libreria di **estrazione testo** lo risolve, perché leggono tutte
la stessa mappa rotta:
- `pymupdf`/`fitz` (estrattore attuale) → mojibake. Tutte le modalità (`text`/`blocks`/`words`) identiche.
- `markitdown` → peggio: produce `(cid:1005)(cid:1004)…` illeggibili.

L'unico modo robusto è **ignorare i font e leggere i glifi renderizzati → OCR**.

Non è un problema universale: NDA Polimi, co.co.co. Sapienza, locazione INPS si estraggono
**puliti**. È una minoranza patologica di PDF.

---

## Cosa è stato fatto: detector (✅ implementato)

In `pdf_processor.py`: misura la densità di caratteri Latin Extended-A/B (U+0100–U+024F),
di fatto assenti nell'italiano. Se supera la soglia → `extract_text` solleva `ValueError`
e SpecterAI **rifiuta onestamente** il PDF invece di citare spazzatura (coerente col
principio anti-allucinazione: niente testo non verificabile).

**Calibrazione su PDF reali:**

| PDF | ratio sospetti |
|---|---|
| Consip (corrotto) | 4,42% |
| NDA Polimi (pulito) | 0,00% |
| co.co.co. Sapienza (pulito) | 0,00% |
| Locazione INPS (pulito) | 0,00% |

Soglia: **`GARBLED_RATIO_THRESHOLD = 0.01`** (1%), nel mezzo della separazione netta.

Il punto in cui oggi solleva l'errore è **il seam dove si innesterà l'OCR fallback**.

---

## Cosa resta: scelta OCR (roadmap)

L'OCR va come **fallback dietro il detector**, NON come estrattore di default (sproporzionato
e lento su ogni contratto, quando serve solo a una minoranza di PDF).

### Vincolo bloccante #1 — deve girare in LOCALE
Il differenziatore di SpecterAI v4 è **privacy-first**: PII oscurate localmente *prima* del
cloud. Un OCR che manda le pagine a un'API cloud spedirebbe il contratto **integro con PII a
un terzo, prima della redazione** → distrugge l'architettura GDPR/privacy-by-design. Un OCR
non-locale è **scartato a prescindere dall'accuratezza**.

### Candidati

**Tesseract** (baseline da battere)
- ✅ Locale, maturo, gratis, italiano supportato, leggero (CPU).
- ✅ Pilotabile direttamente da pymupdf: `page.get_textpage_ocr()`.
- ⚠️ Richiede installazione binario di sistema (Tesseract) + `pytesseract` + `Pillow`
  (oggi **assenti** nella venv).

**baidu/Unlimited-OCR** (in valutazione — agente background 2026-06-24)
- Utente conferma: **gira in locale** ✅ (supera il vincolo #1).
- Architettura fallback: ok.
- **Da verificare** (criteri per cui varrebbe la complessità in più vs Tesseract):
  1. **Licenza** — permissiva (MIT/Apache) o copyleft/non-commerciale?
  2. **Peso** — GB di modello, serve GPU o gira su CPU in tempi decenti?
  3. **Italiano** — supportato o ottimizzato cinese/inglese?
  4. **Accuratezza vs Tesseract** su PDF italiani con font corrotti — deve essere
     **nettamente** superiore, altrimenti vince Tesseract per semplicità.

**Decisione:** Unlimited-OCR vale solo se (a) locale [confermato] **e** (b) batte nettamente
Tesseract sui PDF italiani. Altrimenti → Tesseract come fallback.

---

## Architettura target

```
PDF → fitz get_text() → detector testo corrotto
        ├─ pulito  → pipeline normale (redazione PII → LLM → report)
        └─ corrotto → [ROADMAP] OCR locale (Tesseract o Unlimited-OCR)
                         → redazione PII → LLM → report
```

---

## Connessioni
- [[Specifica Tecnica v4 - SpecterAI]] · [[Progettistica AI MOC]]
- [[INCIDENTS]] — candidato a nuova voce (diagnosi font ToUnicode + detector)
- [[parti importanti contratti]] — knowledge dominio clausole
