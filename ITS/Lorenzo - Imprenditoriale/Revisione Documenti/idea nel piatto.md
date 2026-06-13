# 🤖 PROGETTO LORENZO — Assistente AI per Revisione Documenti

**Data:** Giugno 2026  
**Referente:** Lorenzo  
**Stato:** Presentazione demo (scadenza lunedì 16 giugno)  
**Status:** ⏳ In valutazione

---

## 🎯 Obiettivo Finale

Creare uno **strumento AI su misura** per lo Studio che revisiona e riscrive documenti commercialistici nello **stile dello Studio**, più economico e controllabile di soluzioni come Harvey.

**Cliente finale:** I commercialisti clienti dello Studio di Lorenzo

---

## 📌 Cosa Fa lo Strumento (Scope)

### ✅ Include:
- Riscrittura di documenti nel **registro e stile dello Studio**
- Controllo forma: grammatica, coerenza, struttura
- Preparazione di bozze a partire dai **modelli dello Studio** (pareri, email, comunicazioni)
- Segnalazione di norme/sentenze **da verificare sulla fonte** (non generazione)

### ❌ Esclude:
- Generazione autonoma di ricerche giurisprudenziali
- Invenzione o "correzione" di citazioni di leggi/sentenze
- Ricerca di "sentenze introvabili"

---

## 🏗️ Architettura (Ibrida — Confermata)

```
┌─────────────────────────────────────────────────┐
│ RICERCA GIURISPRUDENZA                          │
│ → Perplexity (€20/mese)                         │
│   • Deep Research: 50-100 fonti                 │
│   • Operatori: site:, after:, before:           │
│   • Sempre cita le fonti                        │
├─────────────────────────────────────────────────┤
│ VOCE & FORMA (Proprietà dello Studio)           │
│ → Da decidere: ChatGPT / Claude / DeepSeek      │
│   • Riscrittura nello stile vostro              │
│   • Skill personalizzate (Formato Studio)       │
│   • Controllo forma/grammatica                  │
├─────────────────────────────────────────────────┤
│ GATE UMANO (SEMPRE)                             │
│ → Professionista valida e firma                 │
│   • Verifica citazioni                          │
│   • Approva ogni documento                      │
└─────────────────────────────────────────────────┘
```

---

## 🔍 Progresso Attuale

| Elemento | Status | Note |
|----------|--------|------|
| Report di fattibilità | ✅ Completato | Documento "AssistenteStudioCommercialisti_StudiFattibilita_v1.md" |
| Demo con GPT | ✅ Pronta | Basata su materiale pubblico (~10 file) |
| Validazione dell'idea | ✅ Confermata | Report include business case e scenari di costo |
| Scelta ricerca | ✅ Definita | **Perplexity** (verticale, economico, fonti citate) |
| Scelta modello IA | 🟡 APERTA | Dipende dalla risposta sulla sensibilità dati |
| Setup privacy | 🟡 APERTA | Privacy Filter vs Claude Enterprise vs anonimizzazione manuale |
| Documenti-tipo vostri | ❌ Mancano | Necessari per il training finale |

---

## 💰 Scenari di Costo (Preliminari)

### Scenario LEGGERO (dati pubblici/anonimizzati)
- Perplexity Pro: €20/mese
- ChatGPT Pro: €20/mese
- Setup iniziale: da definire
- **Totale:** ~€40–60/mese + setup

### Scenario SOLIDO (dati veri, GDPR compliant)
- Perplexity Pro: €20/mese
- Claude Enterprise: €100–300/mese
- Contratto GDPR art. 28: incluso
- **Totale:** ~€120–320/mese

---

## ⚖️ Rischi Identificati & Mitigazioni

| Rischio | Impatto | Mitigazione |
|---------|--------|------------|
| **Citazioni inventate** | Condanne fino a €30k (caso Siracusa 2026) | AI segnala, professionista verifica sulla fonte |
| **Privacy clienti** | Violazione GDPR, danno reputazionale | Anonimizzazione + eventuale Claude Enterprise |
| **Dipendenza da fornitore** | Vincoli contrattuali, niente voce vostro | Approccio ibrida: voce vostra indipendente |
| **Normativa obsoleta** | Documenti con leggi vecchie | Perplexity aggiornato real-time; testi pubblici da Normattiva (gratis) |

---

## 📋 Cosa Lorenzo Chiede per Lunedì

Per il **preventivo definitivo**, Lorenzo vi farà queste domande:

### 1️⃣ **Scala di utilizzo**
- Quanti professionisti useranno lo strumento?
- Quanti clienti finali (commercialisti)?
- Volume mensile di documenti stimato?

### 2️⃣ **Fonte di ricerca giuridica**
Preferite:
- [ ] Abbonamento con AI inclusa (Normo.ai, One Fiscale AI)
- [ ] Banca dati classica (One LEGALE, De Jure)
- [ ] Combinazione (Perplexity + banca dati leggera)

### 3️⃣ **Documenti-tipo vostri**
- Portate 3–5 **pareri, email, comunicazioni reali** dello Studio
- Servono come modello per il training finale
- Il tool imparerà il vostro stile su questi

### 4️⃣ **Dati sensibili**
**CRITICISSIMO:** Passerete dati veri dei clienti all'AI, oppure solo testi pubblici/anonimizzati?
- Se **dati veri** → Claude Enterprise + contratto GDPR
- Se **anonimizzati** → ChatGPT Pro + anonimizzazione manuale

---

## 🎬 Timeline (da confermare con Lorenzo)

| Fase | Quando | Cosa |
|------|--------|------|
| Demo & Feedback | Lunedì 16 giugno | Lorenzo presenta; voi date risposte sopra |
| Preventivo | 17–18 giugno | Lorenzo prepara quote basate su vostre risposte |
| Negoziazione | 19–20 giugno | Definite scope e costi |
| Setup iniziale | Post-giugno | Setup skills, training su vostri documenti |
| Produzione | Luglio+ | Go-live con commercialisti |

---

## 📊 Vantaggi Rispetto ai Tool Esistenti

- ✅ **Proprietà intellettuale** — la vostra voce rimane vostra
- ✅ **Economico** — €40–320/mese vs Harvey €300–500+/utente
- ✅ **Flessibile** — cambiate fornitore ricerca quando volete (ibrida)
- ✅ **Controllato** — gate umano sempre; AI propone, professionista valida
- ✅ **Specifico** — impara il vostro stile

---

## ⚠️ Decisioni Ancora Aperte

### 1. **Modello IA per la redazione**
- **ChatGPT Pro** (€20/mese, se dati anonimizzati)
- **Claude Pro** (€20/mese, se dati pubblici/leggeri)
- **Claude Enterprise** (€100–300/mese, se dati veri + GDPR)
- ❌ **DeepSeek** (escluso — non GDPR per Italia)

**Dipende da:** Tipo di dati che passerete

### 2. **Privacy & Compliance**
- Anonimizzazione manuale a monte (vostro)?
- Privacy Filter OpenAI?
- Contratto GDPR art. 28 (Claude Enterprise)?

**Dipende da:** Sensibilità dati + budget

### 3. **Banca dati per giurisprudenza**
- Solo Perplexity?
- Perplexity + abbonamento leggero (One LEGALE ~€200/anno)?

**Dipende da:** Specializzazione materie e frequenza ricerca

---

## 📝 Checklist per Lunedì

- [ ] Leggere il report di fattibilità completamente
- [ ] Vedere la demo che Lorenzo vi mostrerà
- [ ] Rispondere alle 4 domande di Lorenzo (scala, ricerca, documenti, dati)
- [ ] Portare 3–5 documenti-tipo vostri
- [ ] Chiarire: dati veri o anonimizzati?
- [ ] Chiedere tempistiche e costi preliminari

---

## 📚 Riferimenti

- **Report principale:** AssistenteStudioCommercialisti_StudiFattibilita_v1.md
- **Ricerca:** Perplexity.ai
- **Normativa:** L. 132/2025 (AI come supporto, mai sostitutiva)
- **Caso di riferimento:** Sentenza Siracusa 338/2026 (allucinazioni citazioni)

---

## 📞 Contatti

**Referente:** Lorenzo  
**Scadenza decisione:** Lunedì 16 giugno 2026  
**Prossimo incontro:** Lunedì (demo + Q&A)

---

**Ultimo aggiornamento:** 13 giugno 2026  
**Versione:** Rivalutazione post-discussione

