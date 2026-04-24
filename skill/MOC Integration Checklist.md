# MOC Integration Checklist

> Disciplina operativa per mantenere il vault **compoundante** e non accumulativo.
> Ogni modifica deve rispondere a: "dove si aggancia questo?"

---

## Regola Zero

**Nessuna nota nasce orfana.** Se non sai a quale MOC agganciarla, non scriverla ancora.

---

## Pre-Scrittura (5 controlli)

Prima di creare/modificare una nota:

- [ ] **Search first** — `obsidian_simple_search` con 3 keyword; leggi i primi 5 risultati
- [ ] **MOC target dichiarato** — *quale* MOC ospiterà o referenzierà questo contenuto?
- [ ] **Evita duplicati** — se il concetto esiste già, aggiorna; non creare versione parallela
- [ ] **Tipo di nota chiaro** — MOC, skill, meta-fonte, idea grezza, progetto?
- [ ] **Cartella giusta** — `moc/`, `skill/`, `idee/`, `knowladge/`, `ITS/`, `daily/`?

---

## Regole di Routing

| Cosa stai scrivendo | Destinazione |
|---------------------|--------------|
| Mappa di un dominio | `moc/` (nuovo MOC solo se 5+ note lo giustificano) |
| Procedura operativa | `skill/` |
| Riferimento/estratto fonte | `skill/` o `knowladge/` |
| Concetto trasversale | Aggiorna MOC esistente, non creare nota |
| Idea grezza | `idee/` → ma poi va promossa/cancellata entro 7 giorni |
| Log giornaliero | `daily/` |
| Progetto ITS | `ITS/{progetto}/` |

---

## Backlink Discipline

### Backlink **obbligatori** in ogni nota nuova

1. `[[MOC target]]` (almeno 1)
2. Eventuali note correlate trovate nel search
3. Meta-fonte se applicabile

### Backlink **obbligatori** nel MOC target

Dopo aver aggiornato o creato una nota, **torna nel MOC** e aggiungi il riferimento nella sezione giusta (non in fondo come stub).

### Anti-pattern backlink

- ❌ Link solo a `[[Index MOC]]` — troppo generico, non aiuta la navigazione
- ❌ Bullet list di link a fondo pagina senza contesto
- ❌ Creare una nota e non aggiornare il MOC padre nello stesso commit

---

## Update vs Create

**Aggiorna** quando:
- Esiste già una nota sullo stesso tema
- Il nuovo contenuto è un insight, non un dominio intero
- Il volume aggiunto è < 30% della nota esistente

**Crea** quando:
- Nessuna nota esistente copre il tema
- Il contenuto è un dominio autonomo con 5+ sotto-concetti
- Separare migliora la navigabilità (non la frammenta)

---

## Post-Scrittura (checklist pre-commit logico)

- [ ] La nota ha almeno 1 `[[backlink]]` a un MOC
- [ ] Il MOC referenziato è stato aggiornato nella sezione pertinente
- [ ] Nessun "TODO" / "da completare" lasciato come stub permanente
- [ ] Il nome file segue convenzione (Title Case, no underscore)
- [ ] Frontmatter se serve (tag, data, tipo)

---

## Quando Creare un Nuovo MOC

Soglia minima:
- 5+ note già esistenti sul tema
- Tema non è sotto-insieme di MOC esistente
- Si intravede crescita > 10 note nei prossimi mesi

Altrimenti: **sezione in MOC esistente**.

---

## Red Flags (stop and ask)

Ferma e chiedi all'utente se:
- Sovrapposizione con MOC esistente ambigua
- Stai per rinominare/spostare una nota con backlink multipli
- Il contenuto sembra sensibile/personale
- Non trovi il MOC target giusto dopo 3 ricerche

---

## Connessioni

- [[Source Intake Protocol]]
- [[Vault Audit]]
- [[Knowledge MOC]]
