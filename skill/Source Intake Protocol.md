# Source Intake Protocol

> Flusso standard per trasformare una fonte esterna (articolo, PDF, video, notebook, screenshot)
> in conoscenza integrata nel vault.

**Principio guida:** ogni fonte deve uscire dal triage con **almeno un MOC aggiornato** e zero note orfane.

---

## Passo 0 — Classificazione Fonte

| Tipo fonte | Sotto-playbook |
|------------|---------------|
| NotebookLM | [[NotebookLM Query Playbook]] |
| PDF locale | § PDF qui sotto |
| Articolo web | § Web qui sotto |
| Video YouTube | § Video qui sotto |
| Screenshot / foto | § Immagine qui sotto |
| Audio / podcast | § Audio qui sotto |

---

## Passo 1 — Discovery nel Vault (obbligatorio)

Prima di leggere la fonte, cerca nel vault:

```
- MCP: obsidian_simple_search con 3-5 keyword dal titolo fonte
- MCP: obsidian_list_files_in_dir su moc/ e cartelle probabili
- Identifica il MOC target (quello che ospiterà gli insight)
```

**Output atteso:** lista di 1-5 note correlate + 1 MOC target dichiarato.

---

## Passo 2 — Estrazione (variabile per tipo)

### PDF
1. Read tool sul PDF (usare `pages` se >10)
2. Estrai: titolo, autore, data, tesi centrale, framework, numeri chiave, citazioni estratte
3. Se è slide → ogni slide = punto con titolo + contenuto

### Web
1. WebFetch con prompt: *"Estrai tesi, argomenti a supporto, esempi, contro-argomenti, link citati"*
2. Salva URL originale come fonte

### Video YouTube
1. Richiedi trascrizione all'utente o usa tool dedicato
2. Estrai timestamp dei momenti chiave (hh:mm concetto)

### NotebookLM
→ Segui [[NotebookLM Query Playbook]] (4 query: Discovery / Deep Dive / Esempi / Contraddizioni)

### Immagine (screenshot, foto slide)
1. Read tool sull'immagine
2. Trascrivi testo visibile verbatim
3. Descrivi schemi/diagrammi con struttura (nodi, frecce, relazioni)

### Audio
1. Chiedi trascrizione all'utente
2. Procedi come per video

---

## Passo 3 — Integrazione nel MOC

Segui [[MOC Integration Checklist]]:

1. Identifica le sezioni del MOC target impattate
2. Aggiungi contenuto **nei punti giusti** (non in fondo a caso)
3. Crea `[[backlink]]` bidirezionali
4. Se emergono temi nuovi trasversali → valuta se serve nuovo MOC (di solito NO)

---

## Passo 4 — Meta-Nota della Fonte (opzionale)

Crea una nota in `skill/` o `knowladge/` **solo se** la fonte è un testo di riferimento che verrà citato più volte. Altrimenti il contenuto vive solo nel MOC.

Template meta-nota:

```markdown
# {Titolo fonte}

**Tipo:** PDF | Web | Video | Notebook
**Autore:** ...
**Data fonte:** ...
**URL/Path:** ...
**MOC target:** [[...]]

## Tesi centrale
...

## Estratti utili (verbatim)
...

## Applicazioni nel vault
- [[MOC X]] § sezione Y — aggiunto concetto Z
```

---

## Anti-Pattern

- ❌ Creare una nota isolata in `idee/` senza toccare il MOC
- ❌ Parafrasare tutto senza conservare citazioni verbatim utili
- ❌ Generare contenuto nel vault senza fonte ("Agents read, humans write")
- ❌ Una sola query su NotebookLM / una sola passata su PDF lungo

---

## Connessioni

- [[MOC Integration Checklist]]
- [[NotebookLM Query Playbook]]
- [[Knowledge MOC]]
