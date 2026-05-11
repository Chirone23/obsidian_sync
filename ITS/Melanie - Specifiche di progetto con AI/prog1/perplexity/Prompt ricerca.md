# Prompt ricerca contratti reali — Perplexity free

**Scopo:** trovare 5 PDF di contratti italiani reali per i test di SpecterAI (1 per tipologia).
**Versione:** ottimizzata per Perplexity **free** — 5 query brevi invece di 1 massiva, per ridurre allucinazioni e link inventati.
**Output target:** `prog1/specterai/tests/contratti/01..05_*.pdf`

> Storico: la versione precedente di questo file era pensata per Perplexity Pro (tabella unica + auto-valutazione). Sostituita il 2026-05-11 con il workflow free a 5 query.

---

## Workflow operativo

1. Apri Perplexity free (modalità web search standard).
2. Lancia **una query alla volta** dalla sezione sotto.
3. Clicca ogni URL **subito** per verificare che esista e sia un PDF.
4. Scarica il PDF valido in `prog1/specterai/tests/contratti/` con nome:
   `NN_tipologia_fonte.pdf` (es. `01_servizi_consip.pdf`).
5. Se Perplexity allucina un URL → annota in `INCIDENTS.md` e riformula chiedendo direttamente il sito istituzionale di riferimento (vedi colonna "Fallback").
6. Passa alla query successiva solo dopo aver chiuso la precedente.

---

## Blocco di contesto comune

Da incollare **prima** di ogni query qui sotto (o solo una volta se Perplexity mantiene il thread):

```
Sto sviluppando SpecterAI, un analizzatore di contratti italiani per il corso
"AI Projects Development" (ITS ICT Academy Roma). Mi servono PDF di contratti
reali in italiano per i test, NON template da compilare.

Regole valide per ogni mia richiesta:
- Solo PDF in italiano, link diretto al file (non pagina HTML).
- Fonti accettate in ordine di preferenza:
  1. PA italiane (.gov.it, ministeri, comuni, regioni)
  2. Consip, ANAC, MEF, Agenzia delle Entrate, INPS
  3. Camere di Commercio, ordini professionali
  4. Repository universitari italiani (.it accademici)
  5. Aziende grandi che pubblicano CGV ufficiali
- Fonti NON accettate: blog legali, generatori di template, forum, raccolte
  anonime, siti tipo "fac-simile contratti".
- Il PDF deve essere lungo almeno 3 pagine e contenere clausole reali su
  almeno 4 di queste 7 categorie: durata, recesso, penali, foro competente,
  riservatezza, responsabilità, pagamenti.
- NON inventare URL. Se non sei sicuro che il link esista, scrivi
  "URL da verificare" e indica il sito dove cercarlo.
- NON darmi modelli con campi vuoti tipo [INSERIRE NOME].
```

---

## Query 1 — Contratto di SERVIZI

```
Trovami 2 PDF di contratti di servizi reali in italiano (prestazione
professionale, consulenza, servizi IT), pubblicati da fonti istituzionali
italiane. Idealmente: contratti-tipo Consip per servizi ICT, capitolati
MEPA, contratti standard pubblicati da PA.

Per ognuno dammi:
1. URL diretto al PDF
2. Chi lo pubblica e perché è pubblico
3. Anno di pubblicazione
4. Quali clausole contiene (delle 7: durata, recesso, penali, foro,
   riservatezza, responsabilità, pagamenti)

Se trovi meno di 2 PDF affidabili, dimmelo esplicitamente.
```

**Fallback se Perplexity fallisce:**
`site:consip.it filetype:pdf contratto servizi` su Google → sezione "Documentazione" del portale acquistinretepa.it.

---

## Query 2 — NDA / Accordo di RISERVATEZZA

```
Trovami 2 PDF di NDA (accordi di riservatezza) reali in italiano, bilaterali
o unilaterali, pubblicati da fonti istituzionali italiane. Idealmente: modelli
NDA di uffici di trasferimento tecnologico universitari (Politecnico Milano,
Padova, ecc.) o camere di commercio italiane.

Per ognuno dammi:
1. URL diretto al PDF
2. Chi lo pubblica e perché è pubblico
3. Anno
4. Clausole presenti (delle 7 categorie SpecterAI)

Se trovi meno di 2 PDF affidabili, dimmelo esplicitamente.
```

**Fallback:** `site:polimi.it OR site:unipd.it filetype:pdf "accordo di riservatezza"` su Google.

---

## Query 3 — Contratto di FORNITURA

```
Trovami 2 PDF di contratti di fornitura reali in italiano (beni o servizi
continuativi), pubblicati da fonti istituzionali italiane. Idealmente:
contratti-tipo ANAC, capitolati Consip per fornitura, bandi-tipo per appalti
di beni.

Per ognuno dammi:
1. URL diretto al PDF
2. Chi lo pubblica e perché è pubblico
3. Anno
4. Clausole presenti (delle 7 categorie SpecterAI)

Se trovi meno di 2 PDF affidabili, dimmelo esplicitamente.
```

**Fallback:** `site:anticorruzione.it filetype:pdf bando tipo fornitura` su Google.

---

## Query 4 — Contratto di COLLABORAZIONE

```
Trovami 2 PDF di contratti di collaborazione coordinata e continuativa
(co.co.co.) reali in italiano, pubblicati da fonti istituzionali italiane.
Idealmente: modelli INPS, ispettorato del lavoro, ordini dei consulenti
del lavoro, contratti di collaborazione pubblicati da università.

Per ognuno dammi:
1. URL diretto al PDF
2. Chi lo pubblica e perché è pubblico
3. Anno
4. Clausole presenti (delle 7 categorie SpecterAI)

Se trovi meno di 2 PDF affidabili, dimmelo esplicitamente.
```

**Fallback:** `site:inps.it OR site:ispettorato.gov.it filetype:pdf collaborazione coordinata` su Google.

---

## Query 5 — Contratto di LOCAZIONE

```
Trovami 2 PDF di contratti di locazione reali in italiano (commerciale o
abitativa registrata), pubblicati da fonti istituzionali italiane.
Idealmente: modelli ufficiali dell'Agenzia delle Entrate (contratto-tipo
locazione 3+2, locazione commerciale 6+6).

Per ognuno dammi:
1. URL diretto al PDF
2. Chi lo pubblica e perché è pubblico
3. Anno
4. Clausole presenti (delle 7 categorie SpecterAI)

Se trovi meno di 2 PDF affidabili, dimmelo esplicitamente.
```

**Fallback:** `site:agenziaentrate.gov.it filetype:pdf contratto locazione` su Google.

---

## Piano B se Perplexity free è inaffidabile

Bypassa Perplexity e usa Google con ricerche `site:` mirate (vedi "Fallback" sotto ogni query). Fonti che pubblicano direttamente PDF di contratti reali:

- **agenziaentrate.gov.it** → modelli locazione ufficiali
- **consip.it** / **acquistinretepa.it** → contratti-tipo ICT e servizi
- **anticorruzione.it** (ANAC) → bandi e contratti tipo appalti
- **inps.it** / **ispettorato.gov.it** → collaborazioni
- **camcom.it** + camere di commercio locali → modelli vari
- **polimi.it**, **unipd.it**, **unibo.it** → uffici Knowledge Transfer (NDA, accordi ricerca)

10 minuti di Google mirato bastano per coprire tutte e 5 le tipologie.

---

## Connessioni

- [[Specifica Tecnica v3 - SpecterAI]] — §test plan, 7 categorie
- [[../valutazione 10-5/ChristianG_Promemoria_Lezione4]] — checklist pre-building (3 contratti di test minimi)
- [[../INCIDENTS]] — loggare qui ogni URL allucinato da Perplexity
