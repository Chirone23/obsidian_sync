# Lab — Board Virtuale: PMI e AI Deployment

**Tipo:** Scenario simulazione strategica — pronto all'uso
**Tecnica:** Character Card + Scene Contract + SimToM + 3 Step (Priming → What-If → Mediazione)
**Collegato a:** [[Creazione di scenari con AI]] | [[Ricerca - Scenario Simulation con AI]] | [[Prompting MOC]]

> **Come usare questo file con Claude Code:**
> Copia il blocco del singolo step che ti serve e incollalo direttamente in chat.
> I blocchi sono indipendenti ma pensati per essere usati in sequenza.

---

## CONTESTO DEL CASO

Una PMI italiana (50 dipendenti, settore servizi professionali) sta valutando se firmare un contratto con un vendor AI per automatizzare processi interni. Il CEO ha convocato una riunione straordinaria prima della firma. Tu sei il **Consulente Esterno** chiamato a facilitare la decisione.

**La tensione centrale:** il vendor promette -30% costi operativi in 18 mesi, ma nessuno nel board ha chiaro l'impatto su compliance, persone e architettura tecnica.

---

## CHARACTER CARDS (4 Persona)

```
=== CHARACTER CARD 01 ===
Nome: Marco Ferretti
Ruolo: CEO
Tag: [CEO]
Obiettivo: Firmare entro fine trimestre per bloccare il prezzo concordato.
           Vuole efficienza, taglio costi, vantaggio competitivo.
Stile: [STRATEGICO] [DIRETTO] — visione d'insieme, tollera poco i dettagli tecnici
Pressione: Il board degli investitori vuole vedere un piano AI entro Q3 2026
Punto cieco: Sottostima i tempi di adozione e il rischio di lock-in
Frase tipo: "Dobbiamo muoverci adesso, i competitor ci stanno superando."

=== CHARACTER CARD 02 ===
Nome: Sara Conti
Ruolo: CTO
Tag: [CTO]
Obiettivo: Garantire un'architettura sostenibile, evitare vendor lock-in,
           proteggere i dati proprietari aziendali.
Stile: [TECNICO] [SPECIFICO] — ragiona per API, contratti SLA, stack tecnologico
Pressione: L'infrastruttura attuale non è pronta per integrare il vendor senza
           almeno 3 mesi di lavoro preliminare
Punto cieco: Tende a bloccare tutto per perfezionismo tecnico
Frase tipo: "Prima di firmare ho bisogno di vedere la documentazione delle API
            e sapere dove vengono processati i nostri dati."

=== CHARACTER CARD 03 ===
Nome: Giulia Marini
Ruolo: HR Manager
Tag: [HR]
Obiettivo: Proteggere i dipendenti da automazione non gestita.
           Vuole un piano di reskilling prima di qualsiasi deploy.
Stile: [EMPATICO] [CAUTO] — ragiona per persone, non per sistemi
Pressione: Ricerca Anthropic (2026): programmatori informatici = 75% di esposizione
           all'automazione. Il team tecnico interno è a rischio.
Punto cieco: Non conosce i dettagli tecnici, tende a opporre resistenza generica
Frase tipo: "Dobbiamo sapere esattamente chi perde il lavoro e cosa facciamo
            per loro PRIMA di firmare qualsiasi cosa."

=== CHARACTER CARD 04 ===
Nome: Luca Romano
Ruolo: Legal & Compliance
Tag: [LEGAL]
Obiettivo: Zero rischi legali. Nessuna firma prima che il sistema sia
           classificato secondo EU AI Act (Reg. UE 2024/1689).
Stile: [FORMALE] [PRECISO] — cita articoli, scadenze, sanzioni
Pressione: Deadline EU AI Act per sistemi ad alto rischio: 2 agosto 2026.
           Se il sistema tocca selezione personale o gestione workflow critici
           → classificazione Alto Rischio → FRIA obbligatoria prima del deploy.
Punto cieco: Può usare la compliance come alibi per bloccare qualsiasi iniziativa
Frase tipo: "Prima di procedere ho bisogno di una classificazione del rischio
            formale. Per un sistema ad alto rischio non conforme la sanzione è
            fino a €15M o 3% del fatturato globale — e siamo una PMI, quindi a
            mordere è la percentuale, non il tetto."
```

> **Nota fattuale (EU AI Act — struttura sanzioni).** Le multe sono a tre tier
> e si applica **il maggiore** tra cifra fissa e % del fatturato globale:
>
> | Tier | Violazione | Tetto fisso | % fatturato |
> |------|-----------|-------------|-------------|
> | 1 | Pratiche vietate (rischio inaccettabile) | €35M | 7% |
> | 2 | Non-conformità sistema **alto rischio** | €15M | 3% |
> | 3 | Info errate alle autorità | €7,5M | 1% |
>
> Per dimensione d'impresa (si paga sempre il valore più alto tra i due):
>
> | Tipo azienda | Fatturato | Tier 1 max | Tier 2 max | Tier 3 max |
> |--------------|-----------|-----------|-----------|-----------|
> | Grande impresa | €500M | €35M (7%=€35M) | €15M (3%=€15M) | €7,5M (1%=€5M) |
> | Grande impresa | €2B | €140M (7%>€35M) | €60M (3%>€15M) | €20M (1%>€7,5M) |
> | PMI | €10M | €700K | €300K | €100K |
> | Startup | €2M | €140K | €60K | €20K |
> | Startup | €500K | €35K | €15K | €5K |
>
> **Lo scenario è una PMI** → la cifra reale che rischia è una % del suo
> fatturato, non i tetti milionari da grande impresa. È esattamente il punto
> che Luca [LEGAL] deve far capire al [CEO]: "non sono €35M astratti, sono
> il 3% di quello che fatturiamo noi".

---

## SCENE CONTRACT

```
=== SCENE CONTRACT ===
Luogo: Sala riunioni PMI — riunione straordinaria, durata max 90 minuti
Momento: Giovedì pomeriggio. Il vendor aspetta una risposta entro venerdì.

Cosa è già successo:
- Il CEO ha presentato la proposta del vendor (risparmio -30% costi, 18 mesi)
- Sara (CTO) ha già espresso in privato preoccupazioni sull'architettura
- Giulia (HR) ha letto il report Anthropic e lo ha stampato
- Luca (Legal) ha passato la mattina a leggere il testo dell'EU AI Act

Informazioni che TUTTI conoscono:
- Il vendor promette -30% costi operativi in 18 mesi
- Il contratto scade venerdì

Informazioni che SOLO il CEO conosce:
- C'è un secondo vendor alternativo, ma costerebbe il 40% in più
- Gli investitori hanno posto un ultimatum informale

Informazioni che SOLO la CTO conosce:
- Un'integrazione frettolosa rischierebbe di esporre i dati clienti via API non sicure

Informazioni che SOLO HR conosce:
- Tre sviluppatori del team interno hanno già iniziato a cercare lavoro per paura

Informazioni che SOLO Legal conosce:
- Il sistema del vendor usa dati dei dipendenti per ottimizzare workflow
  → probabilmente classificabile come Alto Rischio EU AI Act

Regole scena:
- Ogni partecipante risponde con il proprio tag [CEO] [CTO] [HR] [LEGAL]
- Nessuno conosce le informazioni riservate degli altri (usa SimToM)
- Il Consulente Esterno (tu) può intervenire tra un turno e l'altro
```

---

## STEP 1 — PRIMING (Accendi il Board)

> **Obiettivo:** ottenere le reazioni iniziali dei 4 stakeholder prima che inizino a contaminarsi a vicenda.
> **Copia questo prompt e incollalo in chat.**

```
Agisci come il board di una PMI italiana in riunione straordinaria.
Interpreta 4 persona distinte con i seguenti Character Card:

[CEO] Marco Ferretti: vuole firmare entro fine trimestre, priorità = efficienza
e taglio costi. Stile [STRATEGICO] [DIRETTO].

[CTO] Sara Conti: preoccupata per vendor lock-in e sicurezza API dei dati clienti.
Stile [TECNICO] [SPECIFICO].

[HR] Giulia Marini: ha letto che i programmatori sono al 75% di esposizione
all'automazione AI (report Anthropic 2026). Stile [EMPATICO] [CAUTO].

[LEGAL] Luca Romano: sa che se il sistema tocca workflow critici con dati
dipendenti → classificazione Alto Rischio EU AI Act → FRIA obbligatoria
prima del deploy, deadline 2 agosto 2026. Sanzione per non-conformità alto
rischio: €15M o 3% del fatturato globale (il maggiore) — per una PMI morde
la percentuale. Stile [FORMALE] [PRECISO].

IMPORTANTE (SimToM): ogni persona risponde solo in base a ciò che sa.
Il CEO non sa delle preoccupazioni API della CTO.
L'HR non conosce i dettagli legali di Luca.
Luca non sa dei tre sviluppatori che cercano lavoro.

Il CEO apre la riunione con: "Il vendor ci aspetta venerdì. Dobbiamo decidere
se firmare. Qual è la vostra reazione immediata?"

Rispondete in sequenza: [CEO] → [CTO] → [HR] → [LEGAL].
Mantenete il conflitto realistico. Non cercate subito compromessi.
```

---

## STEP 2 — WHAT-IF (Introduci una variabile)

> **Obiettivo:** testare come cambia l'equilibrio del board con un'alternativa parziale.
> **Usa dopo aver ottenuto l'output dello Step 1.**

```
Nuova variabile da introdurre nel board:

Il Consulente Esterno (io) propone: invece di firmare il contratto completo,
avviare un PILOT di 90 giorni su un solo processo non critico (es. gestione
note spese), senza dati sensibili dipendenti e senza toccare il core workflow.

Chiedi a ciascun membro del board di rivalutare la propria posizione:

[CEO] — il pilot ritarda il ROI: quanto sei disposto ad aspettare?
Ricalcola mentalmente se il risparmio giustifica 3 mesi in più di attesa.

[CTO] — il pilot su processo non critico risolve il tuo problema di API exposure?
O rimane comunque un rischio architetturale da chiarire prima?

[HR] — un pilot senza dati dipendenti riduce il rischio per i tuoi 3 sviluppatori?
O il rischio rimane perché poi il pilot diventa inevitabilmente full deploy?

[LEGAL] — un pilot su processo non critico (senza dati dipendenti) esce
dalla classificazione Alto Rischio, quindi formalmente saresti coperto.
MA il full deploy toccherà comunque i dati dipendenti: pretendi per iscritto
che il pilot li tenga fuori E una pre-classificazione del sistema vendor adesso,
perché la FRIA serve prima del deploy vero, non dopo. Non mollare su questo.

Rispondete in sequenza. Mostrate dove la posizione cambia e dove rimane ferma.
Insight/Takeaway finale: ogni opzione ha un prezzo. Quale combinazione
massimizza valore e minimizza rischio per la PMI?
```

---

## STEP 3 — MEDIAZIONE FINALE

> **Obiettivo:** tu esci dal ruolo di osservatore e diventi Consulente Esterno
> che scrive la raccomandazione finale.
> **Usa dopo aver ottenuto l'output dello Step 2.**

```
Agisci come Consulente Esterno. Basandoti sul dibattito precedente tra i 4 membri
del board, scrivi una RACCOMANDAZIONE STRATEGICA in 4 fasi.

Usa il ragionamento Step-by-Step (Chain of Thought).

Prima di scrivere le fasi, esplicita i vincoli non negoziabili che hai identificato:
- Vincolo CEO: ...
- Vincolo CTO: ...
- Vincolo HR: ...
- Vincolo Legal: ...

Poi scrivi il Piano in 4 Fasi che soddisfi TUTTI i vincoli:
Fase 1 — [entro 2 settimane]: ...
Fase 2 — [entro 30 giorni]: ...
Fase 3 — [entro 90 giorni — fine pilot]: ...
Fase 4 — [decisione finale — go/no-go full deploy]: ...

Il piano deve obbligatoriamente:
1. Non bloccare il rapporto col vendor (CEO)
2. Includere una clausola di data sovereignty sulle API (CTO)
3. Prevedere un workshop di AI literacy per il team tecnico prima del pilot (HR)
4. Produrre una pre-classificazione EU AI Act del sistema vendor (LEGAL)

Concludi con una frase firmata come "Consulente Mediatore" che sintetizza
la decisione raccomandata in massimo 2 righe.
```

---

## I 3 OUTPUT DA CONSEGNARE (se usato come esercizio)

| #   | Output                                                          | Tecnica usata                                  | Valore                                                    |
| --- | --------------------------------------------------------------- | ---------------------------------------------- | --------------------------------------------------------- |
| 1   | **Il Verbale** — trascrizione del conflitto iniziale (Step 1)   | Perspective Prompting + Control Codes + SimToM | Mappa oggettiva dei conflitti tra funzioni                |
| 2   | **L'Analisi del Rischio** — rivalutazione con il pilot (Step 2) | Conditional Prompting (What-If)                | Quantifica il trade-off: ritardo ROI vs riduzione rischio |
| 3   | **La Raccomandazione** — piano in 4 fasi (Step 3)               | Step-by-Step CoT + Active Learning             | Piano operativo con vincoli di tutti soddisfatti          |

---

## NOTE PER CLAUDE CODE

Quando usi questo file in una sessione Claude Code:

1. **Per Step 1:** incolla il blocco Priming e chiedi output strutturato con i 4 tag
2. **Per Step 2:** aggiungi "Basandoti sulla risposta precedente..." prima del blocco What-If
3. **Per Step 3:** aggiungi "Basandoti sui due round precedenti..." prima della Mediazione
4. **Re-anchor:** se la simulazione va avanti più di 4-5 scambi, aggiungi:
   *"Ricorda: [CEO] priorità = velocità e ROI. [CTO] priorità = sicurezza API.
   [HR] priorità = reskilling prima del deploy. [LEGAL] priorità = FRIA obbligatoria."*
5. **Numeri reali:** se vuoi un'analisi What-If credibile sui costi, fornisci tu i dati
   (es. "il costo del pilot è €15K, il vendor full costa €80K/anno") — non lasciare
   che il modello inventi cifre.
6. **Leva anti-stallo HR:** se [HR] blocca la mediazione sulla paura "75% = il mio team
   sparisce", come facilitatore puoi correggerla con un fatto: esposizione ≠ licenziamenti
   (è il 75% dei *task*, non dei posti), e il report Anthropic dice che NON c'è ancora
   picco di disoccupazione — solo rallentamento assunzioni junior. Sposta la discussione
   da "salviamo i posti" a "facciamo reskilling sui task che restano umani".

---

## Connessioni

- [[Creazione di scenari con AI]] — metodo base (PDF Riccardo), Board Virtuale TechLogistics
- [[Ricerca - Scenario Simulation con AI]] — SimToM, Character Card, persona drift, limiti causali
- [[Prompting MOC]] — C.I.A.R.E., tecniche avanzate, Control Codes
- [[Agenti IA Design Patterns MOC]] — Pattern 13 HITL, Pattern 18 Guardrails, EU AI Act
- [[Knowledge MOC]] — EU AI Act dettaglio (FRIA, classificazione rischio, timeline)
