# 02 — Processo Creativo & Strategia LLM — Factory Ottica

**Versione:** 1.0
**Data:** 2026-05-21
**Status:** 🔵 Attivo — da eseguire partendo da Fase 1

---

## Flusso del processo creativo

```
Discovery (✅ v1.1)
  → 02_Specifica_Tecnica.md       ← Fase 1  (PROSSIMO)
    → 02_Design_Brief.md          ← Fase 3a
      → Generazione HTML/CSS      ← open-design skill  ⬅ CUORE TECNICO
        → Port WordPress theme PHP ← Fase 3c
          → Deploy InfinityFree + catalogo demo
            → Pitch pack
```

**Collo di bottiglia creativo:** il passaggio `Design Brief → open-design → port PHP`. È l'unico step dove l'output LLM diventa codice in produzione. Se va storto, slitta l'intero progetto.

---

## Strategia LLM per fase

### Fase 1 — `02_Specifica_Tecnica.md`

| Parametro | Valore |
|---|---|
| **Modello** | `claude-sonnet-4-6` |
| **Think** | off |
| **Effort** | low-medium |

Task strutturato su template già esistente ([[Template - Specifica Tecnica]]). Dati già estratti dal Discovery, poco spazio creativo.

---

### Fase 3a — Design Brief + generazione open-design ⚡

| Parametro | Valore |
|---|---|
| **Modello** | `claude-opus-4-7` |
| **Think** | **extended** (`budget_tokens: 16000`) |
| **Effort** | **max** |

**Perché Opus + extended thinking:**
- Il brief copre 6 sezioni (Home, Shop, Single Product, Cart, Contatti, Servizi)
- 3 CTA gerarchiche per scheda prodotto
- Configuratore 3-step lenti
- Override template WooCommerce
- Vincoli di palette viola brand (`#A98BD9` / `#8B5FBF`) + token design da §8.4 del Discovery
- Questi vincoli si contraddicono a vicenda → il thinking risolve le tensioni prima di scrivere CSS

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
2. **Palette viola/lavanda** — già brand FB reale, non cambiare.
3. **Tono educational** — continuare la voce FB, non sostituirla con copy commerciale.
4. **Acquisition engine** — newsletter + lead capture = priorità, non nice-to-have.

**Posizionamento:** *"Outlet curato + centro diagnostico"*

---

## Connessioni

- [[00_Piano_Azione]]
- [[01_Discovery]]
- [[Template - Specifica Tecnica]]
- [[Front-end MOC]]
