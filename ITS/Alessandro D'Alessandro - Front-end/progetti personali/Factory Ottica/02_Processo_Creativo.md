# 02 — Processo Creativo & Strategia LLM — Factory Ottica

**Versione:** 1.2
**Data:** 2026-05-21
**Status:** Attivo — da eseguire partendo da Fase 1

---

## Flusso del processo creativo

```
Discovery (completata v1.1)
  → 02_Specifica_Tecnica.md       ← Fase 1  (PROSSIMO)
    → 02_Design_Brief.md          ← Fase 3a  (Opus 4.7)
      → Esecuzione open-design    ← Fase 3a-bis (Haiku)
        → Port WordPress theme PHP ← Fase 3c  (Sonnet)
          → Deploy InfinityFree + catalogo demo
            → Pitch pack
```

**Collo di bottiglia creativo:** il passaggio `Design Brief → open-design → port PHP`. E' l'unico step dove l'output LLM diventa codice in produzione. Se va storto, slitta l'intero progetto.

---

## Strategia LLM per fase

### Fase 1 — Specifica Tecnica

| Parametro | Valore |
|---|---|
| **Modello** | `claude-sonnet-4-6` |
| **Think** | off |
| **Effort** | low-medium |

Task strutturato su template gia' esistente ([[Template - Specifica Tecnica]]). Dati gia' estratti dal Discovery, poco spazio creativo.

---

### Fase 3a — Scrittura Design Brief

| Parametro | Valore |
|---|---|
| **Modello** | `claude-opus-4-7` |
| **Think** | extended (`budget_tokens: 16000`) |
| **Effort** | max |

Task: scrivere `02_Design_Brief.md` partendo da §8.4-8.7 del Discovery.

**Perche' Opus + extended thinking:**
- 6 sezioni da progettare (Home, Shop, Single Product, Cart, Contatti, Servizi)
- 3 CTA gerarchiche per scheda prodotto
- Configuratore 3-step lenti
- Override template WooCommerce
- Vincoli palette viola brand (`#A98BD9` / `#8B5FBF`) + token design da §8.4 Discovery
- I vincoli si contraddicono a vicenda — il thinking risolve le tensioni prima di scrivere il brief

---

### Fase 3a-bis — Esecuzione open-design (generazione HTML/CSS)

| Parametro | Valore |
|---|---|
| **Modello** | `claude-haiku-4-5-20251001` |
| **Think** | off |
| **Effort** | medium |
| **Surface** | **Responsive web** (desktop + tablet + mobile) |
| **Design systems** | **Canva + Levels + Airbnb** |

**Motivazione design systems:**
- **Canva** — palette purple-blue mappa su `#A98BD9`/`#8B5FBF` gia' loro brand, friendly geometry, generous spacing
- **Levels** — architettura di conversione: CTA hierarchy, trust signals, friction removal per "Prenota controllo gratuito"
- **Airbnb** — photography-first + product discovery + rounded UI per le schede occhiali

**Motivazione surface:** la titolare deve poter navigare il demo dal telefono durante il pitch ("Account demo per farle cliccare dal telefono" — da [[00_Piano_Azione]] §Fase 5). Desktop web da solo taglierebbe meta' del pitch.

**Regola:** max 2-3 iterazioni del brief, poi fallback Astra+Elementor (da [[00_Piano_Azione]] §Rischi).

---

### Fase 3c — Port PHP WordPress theme

| Parametro | Valore |
|---|---|
| **Modello** | `claude-sonnet-4-6` |
| **Think** | off |
| **Effort** | high (tempo umano, non LLM) |

Conversione meccanica HTML → template PHP: `while have_posts()`, `wc_get_template_part`, enqueue CSS. Pattern ripetitivo. Budget 2x sul tempo come da Piano Azione.

---

## Insight chiave (driver di tutte le scelte)

1. **Hook principale = "Controllo visivo gratuito"** — non il prezzo. CTA primaria del sito.
2. **Palette viola/lavanda** — gia' brand FB reale, non cambiare.
3. **Tono educational** — continuare la voce FB, non sostituirla con copy commerciale.
4. **Acquisition engine** — newsletter + lead capture = priorita', non nice-to-have.

**Posizionamento:** *"Outlet curato + centro diagnostico"*

---

## Connessioni

- [[00_Piano_Azione]]
- [[01_Discovery]]
- [[Template - Specifica Tecnica]]
- [[Front-end MOC]]
