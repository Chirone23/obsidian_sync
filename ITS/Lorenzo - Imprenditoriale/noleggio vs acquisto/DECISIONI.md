# Decisioni — Configuratore Acquisto vs Noleggio

> Registro append-only delle decisioni prese. Ogni voce: **data**, **decisione**, **motivazione**, **alternative scartate**.
> Le ipotesi in discussione stanno in [[BRAINSTORMING]].

---

## 2026-05-18 — Schema documentazione adottato

**Decisione:** Riusare il pattern docs del progetto Melanie/SpecterAI in versione ridotta (no PROMPT_LOG, no INCIDENTS).
**Motivazione:** Il progetto non è un'app AI con iterazioni prompt; serve solo tracciare contesto + brainstorming + decisioni + sessioni.
**Alternative scartate:** Schema completo Melanie (eccessivo per un configuratore).

---

## 2026-05-18 — Profilo cliente target per stime TCO

**Decisione:** Adottato un profilo "italiano medio realistico" come target unico per tutte le stime di costo (assicurazione, e in futuro svalutazione, manutenzione retail). Sostituisce il profilo "best case" usato nella v1 (era troppo ottimista).

**Profilo target ufficiale:**

| Campo | Valore |
|---|---|
| Età contraente | 42 anni |
| Anni patente | 22 |
| Classe di merito | 4 (non 1 — la media italiana) |
| Storia sinistri | 1 sinistro con colpa parziale 6 anni fa (fuori periodo osservazione) |
| Residenza base | Bologna (Centro-Nord, costo medio) |
| Residenze comparative | Milano (+10/12%), Roma (+14/18%) |
| Uso veicolo | Privato + commuting casa-lavoro |
| Percorrenza | 15.000 km/anno |
| Conducenti aggiuntivi | Coniuge 40 anni |
| Ricovero notturno | Strada pubblica (no box, no posto privato) |
| Scatola nera | NO (rifiutata — ~30% degli italiani la rifiuta) |
| Pagamento | Annuale (non rateale) |
| Auto | Nuova, immatricolata 2026, primo proprietario |

**Motivazione:** Il configuratore deve mostrare numeri credibili al cliente reale. Il profilo "best case" (classe 1 + scatola nera + box + zero sinistri) restituisce premi 200-300 € che chiunque in Italia sa essere irrealistici → il configuratore perderebbe credibilità → la leva "sottobanco" pro-noleggio diventa inefficace. Con il profilo realistico, il delta vs noleggio (che include tutto) è naturalmente più ampio e più persuasivo, senza bisogno di forzare i numeri.

**Ancora hard:** Premio medio RCA Italia dicembre 2025 = **629,24 €** (Euroborsa). Qualsiasi stima per segmento deve essere coerente con questo numero (city car leggermente sotto, SUV premium sopra).

**Alternative scartate:**
- Profilo "best case" (classe 1, scatola nera, Trento): troppo ottimista, non credibile.
- Profilo "worst case" (neopatentato Napoli): utile come scenario "stress test" opzionale, non come default.
- Profilo "B2B partita IVA": fuori scope (il progetto è B2C).

**Come applicare:** Quando serve una nuova stima costo (svalutazione, manutenzione retail, gomme, ecc.) per il configuratore, **partire sempre da questo profilo**. Se servono scenari diversi (giovane, Sud, neopatentato in famiglia), trattarli come "variabili sensibili" derivate dal profilo base, non come baseline alternativi.

---

## (prossime decisioni qui)
