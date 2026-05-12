# Prompt Library

Raccolta di prompt pronti all'uso, strutturati secondo il framework **C.I.A.R.E.** di Riccardo Raponi.
Ogni prompt include: contesto d'uso, il testo da copiare, e note operative.

---

## Come usare questa libreria

1. Trova il prompt per il tuo caso d'uso
2. Copia il blocco di testo
3. Sostituisci le parti in `[MAIUSCOLO]` con i tuoi dati
4. Incolla su Perplexity / ChatGPT / Claude

---

## PROMPT 01 — Validazione Lista di Fonti/Link

**Caso d'uso:** Hai una lista di siti, newsletter o canali trovati tramite Perplexity o consigliati da qualcuno. Vuoi verificare che siano reali, attivi, aggiornati e che coprano davvero quello che dicono.

**Dove usarlo:** Perplexity (modalità ricerca web attiva), ChatGPT con web browsing, Claude con strumenti web.

---

### Prompt

```
Agisci come un fact-checker specializzato in risorse digitali e media.

**CONTESTO**
Ho una lista di [TIPO DI FONTE: newsletter / siti / canali YouTube / account X] 
sull'argomento [ARGOMENTO]. Le ho trovate tramite una ricerca AI e devo 
verificarne la qualità prima di affidarmi a loro come fonti di aggiornamento.

**LISTA DA VALIDARE**
[INCOLLA QUI LA LISTA — una fonte per riga, con nome e link]

**COSA VERIFICARE per ogni fonte**
Per ciascuna, cerca e dimmi:
1. **Esiste davvero?** — Il link è raggiungibile e la fonte è attiva
2. **È aggiornata?** — Data dell'ultimo contenuto pubblicato
3. **Copre davvero [ARGOMENTO]?** — Descrivi brevemente cosa tratta effettivamente
4. **Qualità del segnale** — È di nicchia e autorevole, o è generalista e superficiale?
5. **Frequenza reale** — Quanto spesso pubblica (giornaliera / settimanale / irregolare)?
6. **Verdict** — ✅ Tieni / ⚠️ Verifica tu / ❌ Scarta (con motivazione)

**REGOLE**
- Se un link è irraggiungibile o non trovi dati recenti, segnalalo esplicitamente — 
  non inventare informazioni
- Usa solo dati verificabili, non la tua conoscenza pregressa se non confermata
- Se una fonte è stata rinominata o spostata, indica il nuovo link corretto
- Concludi con una classifica: le 3 fonti migliori per qualità e affidabilità

**OUTPUT**
Tabella con colonne: Nome | Link | Attiva? | Ultimo aggiornamento | Copre davvero X? | Frequenza | Verdict
Poi: classifica top 3 con motivazione sintetica.
```

---

**Note operative:**
- Funziona meglio su Perplexity perché fa ricerca web in tempo reale
- Se la lista è lunga (+10 fonti), spezzala in due richieste da 5
- Il `[ARGOMENTO]` deve essere specifico: non "AI" ma "novità modelli AI e piattaforme asiatiche"

---

<!-- AGGIUNGI NUOVI PROMPT QUI SOTTO seguendo lo stesso formato -->
