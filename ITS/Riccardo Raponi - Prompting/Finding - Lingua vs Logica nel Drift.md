# Finding — Lingua vs Logica nel Persona Drift (IT vs EN)

**Tipo:** Esperimento originale (non presente nelle fonti)
**Data:** giugno 2026
**Setup:** stesso scenario [[Lab - Board Virtuale PMI AI Deployment]], eseguito in due lingue
**Collegato a:** [[Ricerca - Scenario Simulation con AI]] | [[Creazione di scenari con AI]] | [[Prompting MOC]]

---

## La domanda

Eseguendo la simulazione del Board Virtuale su 3 round in **italiano**, è emerso un pattern: la logica e i personaggi tenevano (anzi miglioravano), ma la **qualità linguistica degradava** progressivamente — anglicismi crescenti fino a un glitch vero (parola raddoppiata: *"compliance compliance"*).

**Ipotesi:** il modello è più forte in inglese. In italiano fa una traduzione continua a runtime; sotto carico (contesto lungo, molti turni) quel layer cede per primo. Il degrado sarebbe quindi **costo di traduzione, non collasso del ragionamento**.

**Test di controllo:** rieseguire lo Step 1 con prompt **interamente in inglese**, thread nuovo.

---

## I dati

| Round | IT — Logica/SimToM | IT — Lingua | EN — Logica/SimToM | EN — Lingua |
|---|---|---|---|---|
| 1 | solida | pulita | solida (HR dice "Three", carta CEO coperta) | pulita + regie sceniche |
| 2 | solida (CEO usa la carta nascosta) | **primi slip** | solida + calcolo costi esplicito | pulita |
| 3 | **ottima** (risolve via parallelizzazione FRIA) | **rotta + glitch** ("compliance compliance", "boardali") | **ottima** + economics exit clause | **pulita** (zero degrado) |

**Esperimento completo:** entrambe le versioni eseguite su 3 round identici. La logica regge in entrambe le lingue (anzi migliora coi round). La qualità di superficie **degrada solo in italiano, monotonicamente fino al collasso al round 3**; in inglese resta pulita per tutti e 3 i round.

---

## Esito: IPOTESI CONFERMATA

L'inglese è risultato **completamente idiomatico** (*"full stop"*, *"we'll hemorrhage exactly the people we need"*, *"north of €200,000"*), zero goffaggini — più pulito persino del round 1 italiano.

**Bonus non previsto:** in inglese il modello ha aggiunto **regie sceniche** spontanee (*"leans back, checks watch"*, *"removes glasses"*, *"jaw tightens"*) e ha ancorato la sanzione a un numero concreto (3% → *"north of €200,000"* → fatturato implicito ~€6,7M, plausibile per PMI 50 dipendenti). Segno che, lavorando nella lingua nativa, **si libera capacità cognitiva**: non spesa a tradurre, investita in qualità narrativa e grounding.

### Conclusione

Il degrado italiano era **costo di traduzione, non collasso del ragionamento**. Due insight generalizzabili:

1. **Il drift colpisce la superficie (lingua) prima del ragionamento (logica).** Il re-anchor sui *fatti* protegge i contenuti ma non lo *stile*.
2. **Operare nella lingua nativa del modello libera capacità** che si vede anche come ricchezza narrativa, non solo come correttezza.

---

## Esperimento 2 — La Strada B funziona? (vincolo di stile in italiano)

Testata anche la Strada B su 3 round: italiano col vincolo di stile esplicito + re-anchor dei fatti.

| Round | Anglicismi (nonostante il vincolo) | Fatti |
|-------|-----------------------------------|-------|
| 1 | 2 (*hardening*, *andare live*) | HR garbled ("tre colloqui falliti") |
| 2 | 4 (*trigger, roadmap, budget, question mark*) + 2 errori | HR **recuperato** → "3 sviluppatori" |
| 3 | 6-7 (*review, vs, feedback, assignment, timeline, remediation*, "no-X") | fedeli |

**Meccanismo scoperto — "acchiappa la talpa":** la lista nera esplicita **sopprime le parole vietate** (al round 3 niente *trigger/hardening/compliance/deadline*: usa *innesco/messa in sicurezza/conformità/scadenza*), MA la spinta verso l'inglese si **redistribuisce su anglicismi nuovi** non in lista. Il totale **aumenta comunque a ogni round**. Per fermarla servirebbe una lista nera infinita.

**Nota positiva:** il vincolo evita il collasso nel glitch (nessun *"compliance compliance"*), e il re-anchor dei **fatti** funziona (HR torna a "3"). La Strada B sta a metà: meglio del nudo, peggio dell'inglese.

---

## Implicazioni operative (per l'esame, che è in italiano)

| Strada | Come | Trade-off | Verdetto |
|--------|------|-----------|----------|
| **A — Prompt in inglese, traduci il deliverable** | Esegui la simulazione in EN, traduci solo l'output finale (breve, statico → non degrada) | Massima qualità; una passata di traduzione umana | ✅ **consigliata** |
| **B — Prompt in italiano + vincolo di stile** | Lista nera di anglicismi + re-anchor fatti a ogni round | Mitiga ma non risolve: anglicismi nuovi colano comunque, crescono coi round | ⚠️ usabile solo con editing pesante |

---

## Connessioni

- [[Lab - Board Virtuale PMI AI Deployment]] — lo scenario usato per l'esperimento
- [[Ricerca - Scenario Simulation con AI]] — persona drift, SimToM, re-anchor (contesto teorico)
- [[Prompting MOC]] — Control Codes, tecniche avanzate
