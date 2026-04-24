# Vault Audit

> Routine periodica per mantenere il vault **pulito, connesso e vivo**.
> Cadenza consigliata: **settimanale** (leggera) + **mensile** (approfondita).

---

## Audit Settimanale (5 min)

Obiettivo: niente cresce in silenzio, niente marcisce.

### Check 1 — Note Orfane

Note senza backlink in ingresso.

```
MCP: obsidian_list_files_in_vault → per ogni file:
     obsidian_complex_search su [[nome_file]]
     Se 0 risultati → ORFANA
```

**Azione:**
- Aggancia a MOC pertinente, OPPURE
- Sposta in `idee/` se ancora grezza, OPPURE
- Cancella se obsoleta

### Check 2 — Stub Permanenti

Note con `*(da aggiungere)*`, `TODO`, `*(da completare)*` vecchi di 14+ giorni.

**Azione:** completa o rimuovi lo stub (non lasciare falsi segnali).

### Check 3 — MOC non Toccati

MOC non modificati da 30+ giorni.

**Azione:** non necessariamente un problema — ma domandati se il dominio è morto o solo stabile. Se morto → archivia.

---

## Audit Mensile (20 min)

### Check 4 — Duplicati Semantici

Due note che trattano lo stesso tema con titoli diversi.

**Come trovarli:**
```
MCP: obsidian_simple_search su keyword core di ogni MOC
Cerca risultati multipli con overlap > 70%
```

**Azione:** merge nella nota più connessa, redirect con `[[nuovo-nome]]` dalla vecchia.

### Check 5 — Cartelle Fuori Struttura

File in root vault (non in `moc/`, `skill/`, `ITS/`, `idee/`, `knowladge/`, `daily/`).

**Azione:** sposta nella cartella giusta secondo [[MOC Integration Checklist]] § Routing.

### Check 6 — Naming Inconsistenze

- File con underscore invece di spazi
- MOC senza suffisso "MOC" / con suffisso ridondante
- Typo ricorrenti (es. `knowladge` vs `knowledge` — già noto nel vault)

**Azione:** rinomina in batch dopo aver verificato backlink con `obsidian_complex_search`.

### Check 7 — Backlink Morti

Link `[[nota inesistente]]` che puntano a note cancellate/rinominate.

**Come trovarli:** plugin Obsidian "Broken Links" o grep manuale.

**Azione:** correggi il target o rimuovi il link.

---

## Audit Trimestrale (1 h)

### Check 8 — Densità di Connessione

Per ogni MOC, conta i backlink:
- < 3 → MOC anemico, forse da fondere
- 3-15 → sano
- > 25 → da spezzare in sotto-MOC

### Check 9 — Coerenza con CLAUDE.md

Rileggi CLAUDE.md e verifica:
- Principi ancora aggiornati?
- Struttura cartelle ancora quella dichiarata?
- Workflow (Knowledge Compiler, NotebookLM) ancora validi?

**Azione:** aggiorna CLAUDE.md se il vault è evoluto.

### Check 10 — Skill Usate / Inutilizzate

Skill in `skill/` mai referenziate da 90+ giorni → forse obsolete o da promuovere in CLAUDE.md.

---

## Report Audit

Dopo ogni audit, crea nota `daily/audit-YYYY-MM-DD.md`:

```markdown
# Audit {data}

**Cadenza:** settimanale | mensile | trimestrale

## Trovato
- Orfane: N
- Stub: N
- Duplicati: N
- Backlink morti: N

## Azioni prese
- ...

## Decisioni rimandate
- ...

## Prossimo audit
- {data}
```

---

## Anti-Pattern

- ❌ Audit senza azione (solo "noto che c'è un problema")
- ❌ Cancellare in massa senza verificare backlink
- ❌ Trasformare l'audit in refactor totale → bloccare il lavoro quotidiano
- ❌ Ignorare il report: serve per vedere se il vault peggiora o migliora nel tempo

---

## Connessioni

- [[MOC Integration Checklist]]
- [[Daily Note Ritual]]
- [[Knowledge MOC]]
