# Validazione Idea — Assistente AI "Voce & Forma" per Studio Commercialisti

**Progetto:** Assistente AI per revisione documenti (PROGETTO LORENZO)
**Data:** 2026-06-13
**Autore:** Chirone / Filippo
**Riferimento metodologico:** [[Brainstorming_Idea_Validation_Guide|Guida Validazione Idea — Melanie]]
**Fonte idea:** Lorenzo (cliente) — [[idea nel piatto]] · Report di fattibilità `AssistenteStudioCommercialisti_StudioFattibilita_v1`

> **Nota di metodo.** Diversamente dal flusso standard del corso, le idee iniziali **non sono generate da noi**: sono fornite dal committente (Lorenzo / lo Studio). Il nostro compito non è inventare alternative, ma **validare criticamente** l'idea ricevuta, delimitarne un MVP costruibile e trasferire le decisioni alla specifica tecnica.

---

## 1. Le opzioni sul tavolo (fornite dal committente)

La domanda di partenza dello Studio: *"si può ottenere lo stesso risultato di strumenti come Harvey/CoCounsel pagando molto meno?"* Da qui, tre strade considerate:

| Opzione | Cosa significa | Punti di forza | Perché tenuta/scartata |
|---|---|---|---|
| **A. Solo abbonamento** (comprare) | Normo.ai / One Fiscale AI / One LEGALE | Pronto subito, banche dati e aggiornamenti inclusi, basso costo | ❌ **Scartata come soluzione unica**: non scrive nello stile dello Studio, dipendenza dal fornitore, vincoli pluriennali |
| **B. Ibrida** *(scelta)* | Abbonamento per la ricerca **+** componente su misura "Voce & Forma" | Voce dello Studio di proprietà; fornitore di ricerca sostituibile; rischio basso e controllato | ✅ **Scelta**: copre ciò che nessun abbonamento vende (lo stile) lasciando la ricerca a chi ha le banche dati |
| **C. Nativa completa** (costruire tutto) | Software su misura che fa anche ricerca e citazioni | Tutto "vostro" | ❌ **Scartata**: costosa, richiede banche dati sentenze a pagamento, eredita il rischio peggiore (citazioni inventate), manutenzione continua |

**Cosa abbiamo imparato dal confronto.** Il valore difendibile non è la ricerca giuridica (già venduta meglio da altri), ma **la voce dello Studio**. Costruire la ricerca significherebbe rifare peggio ciò che esiste, ereditando il rischio di allucinazioni delle citazioni. La scelta ibrida isola il valore proprietario dal rischio.

---

## 2. Idea scelta

Un componente su misura — **"Voce & Forma"** — di proprietà dello Studio, che lavora su un singolo flusso:

> **bozza grezza in entrata → testo riscritto nello stile dello Studio + controllo della forma + segnalazione delle norme da verificare → gate umano**

La ricerca giurisprudenziale resta affidata a un abbonamento esterno (fuori dal nostro componente). L'AI **propone**, il professionista **valida e firma**.

---

## 3. Motivazione della scelta

- **Problema reale:** lo Studio scrive ogni giorno pareri, email e comunicazioni; uniformare stile e forma fa risparmiare tempo a parità di qualità.
- **Valore non comprabile altrove:** lo stile dello Studio è l'unica cosa che nessun abbonamento può fornire → è qui che il custom ha senso.
- **Rischio controllato:** tenendo fuori la generazione di citazioni si neutralizza il rischio che ha già portato a condanne (Siracusa 338/2026, ~30.000 €).
- **Indipendenza:** la voce vive in un artefatto dello Studio, non incollata a un singolo vendor.

---

## 4. Validazione sulle 5 dimensioni

> Per ogni dimensione: **giudizio · rischio principale · domanda critica aperta · riduzione di scope**. (L'AI mette alla prova l'idea, non la conferma.)

### 4.1 Fattibilità tecnica — 🟢 Forte
- **Giudizio:** riscrivere un testo in uno stile dato è il task in cui gli LLM eccellono; input/output chiari (bozza → bozza riscritta + forma + flag). L'AI aggiunge valore reale vs lavoro manuale di un junior.
- **Rischio principale:** "stile dello Studio" non è misurabile a occhio → rischio di output generico spacciato per riuscito.
- **Domanda critica aperta:** come dimostriamo che lo stile è *quello dello Studio* e non un registro neutro? Servono 3-5 documenti veri come riferimento.
- **Riduzione scope:** MVP su **un solo tipo di documento** (es. il parere) e su ~10 file campione di stile.

### 4.2 Sostenibilità economica — 🟡 Ribaltata (commerciale ok / didattica da gestire)
- **Giudizio:** commercialmente sostenibile (lo Studio paga ~€120-320/mese a regime). Per il **corso**, però, il criterio è "costo quasi zero": l'Enterprise non rientra.
- **Rischio principale:** confondere il costo del prodotto a regime con il costo della consegna didattica.
- **Domanda critica aperta:** la demo gira su Claude Pro/Code (€0-20) con dati anonimizzati, tenendo Claude Enterprise come *fase produzione*?
- **Riduzione scope:** **demo costo-zero** (Sonnet 4.6, dati anonimizzati); Enterprise → roadmap, non requisito MVP.

### 4.3 Complessità — 🟡 Media (alta se non si delimita)
- **Giudizio:** il prodotto a regime ha tre sistemi (ricerca + riscrittura + gate) + anonimizzazione + GDPR → troppo per un MVP.
- **Rischio principale:** voler portare tutto subito e non chiudere niente bene.
- **Domanda critica aperta:** il motore di ricerca è davvero necessario per dimostrare il valore?
- **Riduzione scope:** MVP = **solo il flusso Voce & Forma**. Il *motore* di ricerca (Perplexity/abbonamento) esce; la *segnalazione* delle norme (flag locale, non ricerca) resta perché è gratis e sicura.

### 4.4 Rischio e compliance — 🔴 Critica (punto più forte e più pericoloso)
- **Giudizio:** dominio legale, dati di terzi, output vicino alla consulenza → massima attenzione. Mitigazioni già forti: gate umano (L. 132/2025 art. 13), nessuna generazione di citazioni.
- **Rischio principale:** allucinazione di citazioni (caso Siracusa 338/2026) e trattamento dati clienti (GDPR / segreto professionale).
- **Domanda critica aperta:** nel progetto trattiamo dati clienti reali o solo documenti-modello anonimizzati?
- **Scelta presa:** **progettiamo come se trattassimo dati reali** (GDPR art. 28, anonimizzazione, modello UE/Enterprise in roadmap) — è l'ostacolo serio che dà valore al progetto — **ma la demo gira su dati anonimizzati/sintetici**. Così si prendono i punti compliance restando costo-zero.
- **Mitigazione hard:** lo strumento **non genera e non corregge mai** citazioni — le segnala soltanto, da verificare alla fonte.

### 4.5 Sostenibilità tecnologica — 🟢 Forte (per design)
- **Giudizio:** l'architettura ibrida è già la risposta giusta: la voce dello Studio è indipendente dal fornitore.
- **Rischio principale:** dipendenza da un solo provider (se cambia prezzi/termini, il sistema muore).
- **Domanda critica aperta:** se Claude raddoppia il prezzo o chiude, la "voce dello Studio" è portabile?
- **Mitigazione:** le **regole di stile vivono in un file `.md` portabile** (prompt/skill + esempi), non dentro un assistente custom legato a un vendor → si incollano su qualunque modello.

---

## 5. Decisione finale (ponte verso la Specifica Tecnica)

**Procediamo con l'idea ibrida (B)**, perché isola il valore proprietario (la voce dello Studio) dal rischio peggiore (le citazioni inventate, lasciate alla ricerca esterna + verifica umana).

**Il MVP per lunedì 16/06 INCLUDE:**
- Flusso **Voce & Forma** su un singolo documento: bozza grezza → riscrittura nello stile (da ~10 file campione) + controllo forma (grammatica, coerenza, struttura) + segnalazione norme da verificare
- **Gate umano** finale (l'AI propone, il professionista valida)
- Modello **Sonnet 4.6** · esecuzione su **dati anonimizzati/sintetici**
- Metrica di qualità: **rubrica a 3 assi** validata dal professionista → tono/registro · correttezza linguaggio tecnico · leggibilità "umana" (per non addetti)

**Il MVP ESCLUDE (fuori scope):**
- Motore di ricerca giurisprudenziale (Perplexity/abbonamento) → fase successiva
- Valutazione di merito legale del contenuto → territorio a rischio (consulenza)
- Generazione/correzione di citazioni → mai, per design
- Esecuzione su dati clienti reali → progettata ma non eseguita nella demo

**Decisioni da trasferire nella specifica tecnica:**
1. Architettura ibrida a 3 livelli, con la voce isolata in artefatto portabile (`.md`)
2. Hard rule: nessuna generazione di citazioni, solo segnalazione
3. GDPR art. 28 come vincolo di design (dati reali in roadmap, demo anonimizzata)
4. Metrica = rubrica 3 assi su test set di documenti veri
5. Sonnet 4.6 per la voce; Haiku 4.5 come ottimizzazione costi per i task ad alto volume/bassa sfumatura (es. solo forma)

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Template - Specifica Tecnica]] — prossimo deliverable
- [[idea nel piatto]] — briefing del committente
- [[Contract Analyzer - Validazione Idea]] · [[PrivateAgent - Validazione Idea]] — esempi di formato
