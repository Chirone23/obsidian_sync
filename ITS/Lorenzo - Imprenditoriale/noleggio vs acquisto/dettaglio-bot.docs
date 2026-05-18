# Contesto — Configuratore Acquisto vs Noleggio LTM

## Obiettivo
Configuratore B2C che confronta **acquisto auto** vs **noleggio a lungo termine**, mostrando in modo trasparente il più conveniente ma orientando "sottobanco" il cliente verso il **noleggio**.

Cliente finale: consumatore privato (non aziendale).
Azienda: progetto ITS di Lorenzo — presentazione imprenditoriale.

---

## Fonti dati disponibili

5 preventivi reali **ALD Automotive — contratto 4Vantage** (cartella corrente):

- `FIAT 500.pdf`
- `KIA PICANTO.pdf`
- `NISSAN JUKE.pdf`
- `VOLKSWAGEN T-CROSS.pdf`
- `VOLVO XC40 B3.pdf`
- `4moovy PPT_DEF.pptx` — slide presentazione del progetto

**Estrazione automatizzata:**
- Script: `execution/extract_noleggio_pdfs.py` (testo grezzo) + `execution/parse_noleggio_preventivi.py` (parsing strutturato)
- Output JSON: `.tmp/noleggio_extract/preventivi_noleggio.json`

### Sintesi numerica (tutti 48 mesi / 120.000 km / anticipo 0)

| Auto | Listino | Tot. veicolo | Canone puro | Servizi | Canone tot. | Costo tot. 48m |
|---|---:|---:|---:|---:|---:|---:|
| KIA Picanto | 17.400 | 18.300 | 218,87 | 127,69 | **346,56** | 16.635 |
| FIAT 500 Hybrid | 20.300 | 21.100 | 271,18 | 135,10 | **406,29** | 19.502 |
| VW T-Cross | 29.608 | 29.718 | 324,12 | 187,56 | **511,69** | 24.561 |
| Nissan Juke HEV | 30.527 | 31.727 | 343,39 | 210,88 | **554,29** | 26.606 |
| Volvo XC40 B3 | 42.956 | 44.366 | 493,88 | 284,27 | **778,17** | 37.352 |

**Km eccedenti**: 0,07–0,19 €/km entro 15%, 0,10–0,19 €/km oltre 15%.
**Quota servizi** sul canone totale: 33% (FIAT) → 38% (Volvo). Cresce con la fascia di prezzo.

---

## Voci del configuratore (da appunti presentazione)

### Lato Acquisto
- Prezzo di acquisto (input principale)
- Anticipo
- Canone finale (maxi rata / valore di riscatto se finanziato)
- Assicurazione
- Manutenzione
- *(da aggiungere: interessi finanziamento, bollo, svalutazione)*

### Lato Noleggio (LTM)
- Puro noleggio
- Interessi (variabili)
- Servizi
- Km annui / totali
- Durata (36 / 48 mesi)
- Sconto su canone
- Assicurazione
- Manutenzione (≈ 47% del canone secondo appunti — dai preventivi reali è 33–38%, dac verificare)

---

## Strategia "sottobanco" pro-noleggio (da definire)

Idee da discutere:
- Default sliders favorevoli al noleggio (km alti, durata 48m, manutenzione retail "completa" alta)
- Evidenziare costi nascosti dell'acquisto (svalutazione, imprevisti, immobilizzo capitale)
- "Costo mensile equivalente" sull'acquisto (rate + svalutazione + tutto) vs canone NLT secco
- Messaggi/microcopy che sottolineano serenità, prevedibilità, zero pensieri
- Output finale: due card affiancate con un "consigliato" sul noleggio anche in casi borderline

---

## Aperti / da decidere

- **Stack tecnico** del configuratore (web app? Calcolatore embed? PHP/WP, JS standalone, ecc.)
- **Fonti per i dati acquisto** (interessi, assicurazione retail, manutenzione media): input utente o tabelle medie di mercato?
- **Modello svalutazione auto** (% annua per fascia? valore residuo a 4 anni?)
- **Quante auto** nel configuratore: solo le 5 dei preventivi o catalogo più ampio?
- **Personalizzazione**: km/anno utente, anni di possesso desiderati, profilo guidatore
- **Output**: solo numeri o anche grafici / timeline costi cumulati?

---

## Status

- [x] Estrazione dati preventivi (5/5)
- [x] Parser strutturato → JSON
- [ ] Brainstorming modello dati + logica comparazione
- [ ] Definizione leve "sottobanco"
- [ ] Design configuratore
- [ ] Implementazione
