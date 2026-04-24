# NotebookLM Query Playbook

> Modello base di richieste per estrarre **tutto ciò che serve** da un notebook online,
> evitando estrazioni superficiali o incomplete.

**Contesto d'uso:** quando Chirone carica un notebook con N fonti miste (PDF, slide, immagini, articoli) e vuole integrarle in un MOC del vault.

---

## Principi Operativi

1. **Specifica sempre il numero di fonti** (es. *"14 elementi di cui 5 foto"*) — il modello tende a sintetizzare e saltare elementi se non glielo imponi.
2. **Chiedi estrazione esplicita dalle immagini/slide** — altrimenti vengono ignorate.
3. **Richiedi formato strutturato** (lista numerata, tabelle) — no prosa discorsiva.
4. **Itera con follow-up mirati** — una sola query non basta mai. Pianifica 2-4 round.
5. **Ogni query è indipendente** (nuova sessione browser) → includi sempre il contesto necessario.

---

## Sequenza Base (4 Query)

### Query 1 — Mappa Completa (Discovery)

Obiettivo: inventario di tutte le fonti, niente escluso.

```
Fornisci un overview completo e dettagliato di TUTTI i {N} elementi/fonti
presenti in questo notebook. Per ciascuna fonte indica:
1) titolo/nome
2) tipo (PDF, foto/slide, articolo, ecc.)
3) argomento principale
4) concetti chiave trattati

Sii esaustivo: voglio una mappa completa di tutti i contenuti,
senza escludere niente. Se ci sono foto/slide di lezione estrai il testo visibile e i concetti mostrati.
```

**Output atteso:** lista numerata 1…N con bullet strutturati per fonte.

---

### Query 2 — Deep Dive Tematico

Obiettivo: approfondire i concetti trasversali, non le fonti singole.

```
Dal contenuto dell'intero notebook, estrai e organizza in modo strutturato:

1) FRAMEWORK E MODELLI — tutti i framework, acronimi, modelli a N passi/livelli
   citati nelle fonti (es. C.I.A.R.E., 5 pilastri, matrice X/Y).
   Per ognuno: nome, componenti, quando si applica.

2) TECNICHE OPERATIVE — tutte le tecniche pratiche nominate
   (formula, uso, output atteso).

3) CHECKLIST E CRITERI — liste di controllo, criteri di qualità,
   regole d'oro citate.

4) ANTI-PATTERN — cosa NON fare, rischi, errori comuni segnalati.

Formato: tabelle Markdown dove possibile.
```

---

### Query 3 — Esempi e Casi Pratici

Obiettivo: materiale applicato, non teorico.

```
Dal notebook, estrai TUTTI gli esempi pratici, casi studio, template e
prompt di esempio presenti nelle fonti. Per ciascuno:
- dominio/settore (legale, marketing, PM, ecc.)
- testo integrale dell'esempio
- fonte di provenienza (quale documento)
- quale tecnica illustra

Non riassumere: riporta il testo letterale degli esempi quando possibile.
```

---

### Query 4 — Contraddizioni e Profondità

Obiettivo: rilevare conflitti tra fonti e dettagli tecnici.

```
Analizza criticamente il notebook e rispondi:

1) CONTRADDIZIONI — ci sono fonti che dicono cose diverse sullo stesso tema?
   (es. numero diverso di livelli, definizioni divergenti)

2) DEFINIZIONI TECNICHE — elenca i termini tecnici definiti nelle fonti
   (token, context window, RAG, agentic, ecc.) con la definizione data.

3) DATI NUMERICI — estrai tutti i numeri significativi citati
   (percentuali, metriche, benchmark, date).

4) RIFERIMENTI ESTERNI — URL, paper, libri, autori citati come fonti
   secondarie all'interno dei documenti.
```

---

## Query Opzionali (usare solo se rilevanti)

### Gap Analysis contro nota esistente

```
Tieni conto che nella mia nota attuale ho già questi contenuti:
{incolla la struttura della nota esistente, solo i titoli/headings}

Dal notebook, identifica SOLO i contenuti rilevanti che NON sono
già coperti dalla mia nota. Elenca in modo puntuale cosa manca,
con riferimento alla fonte del notebook.
```

### Estrazione Slide-per-Slide

Quando una fonte è una presentazione densa:

```
Concentrati sul file "{nome_file.pdf}". Procedi slide per slide:
per ogni slide riporta titolo, testo principale, eventuali schemi
o tabelle. Se una slide contiene solo un'immagine, descrivi cosa
mostra e cosa insegna.
```

### Glossario

```
Produci un glossario alfabetico di tutti i termini tecnici,
acronimi e concetti specialistici citati nel notebook.
Formato: Termine — Definizione (Fonte).
```

---

## Checklist Post-Estrazione

Prima di considerare completo il lavoro:

- [ ] Ogni fonte del notebook compare almeno una volta nell'estratto
- [ ] Le immagini/slide hanno testo estratto, non solo "è un'immagine di X"
- [ ] Framework nominati hanno tutti i loro componenti elencati
- [ ] Gli esempi pratici sono letterali, non parafrasati
- [ ] Il contenuto è integrato nel MOC esistente con `[[backlink]]`
- [ ] Nessuna nota isolata creata — la conoscenza compounda

---

## Lesson Learned (da sessione 2026-04-23)

**Caso:** notebook "Prompting" con 14 elementi.
Un modello precedente aveva estratto **solo un link** (`obsidian://...`) invece del contenuto.

**Causa:** query troppo generica tipo *"cosa c'è nel notebook?"* — NotebookLM aveva risposto con un pointer invece che col contenuto.

**Fix:** query esplicita con *"Per ciascuna fonte indica: tipo, argomento, concetti chiave"* + menzione del numero esatto di elementi.

**Risultato:** mappa completa delle 14 fonti + integrazione nel [[Prompting MOC]] con sezioni nuove (checklist 10 punti, matrice RAG, Human-in-the-Loop, multimodalità).

---

## Connessioni

- [[Prompting MOC]] — applicazione diretta dei framework C.I.A.R.E. / 5 Pilastri alle query NotebookLM
- [[Knowledge MOC]] — workflow Knowledge Compiler (Setup 04)
