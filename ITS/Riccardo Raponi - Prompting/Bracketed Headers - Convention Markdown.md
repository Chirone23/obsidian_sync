# Bracketed Headers — Convention Markdown per Prompt #

**Tipo:** Convenzione di formattazione prompt
**Materia:** [[Prompting MOC]]
**Tag:** #prompt #formatting #convention

---

## Cosa è ##

Convenzione di scrittura per i titoli nei prompt strutturati: ogni heading Markdown viene **chiuso** con lo stesso numero di `#` con cui è aperto, anziché lasciato aperto come da specifica CommonMark standard.

## Esempio ##

❌ Markdown standard:
```
# Titolo principale
## Sottosezione
### Dettaglio
```

✅ Bracketed headers (questa convenzione):
```
# Titolo principale #
## Sottosezione ##
### Dettaglio ###
```

## Perché funziona ##

- **Delimitazione visiva netta:** ogni blocco ha un'apertura e una chiusura esplicite → l'LLM percepisce meglio il confine semantico tra sezioni.
- **Riduce sanguinamento di contesto:** istruzioni di una sezione tendono meno a "contaminare" la sezione successiva.
- **Parsing più stabile:** utile quando il prompt viene processato da pipeline o salvato in librerie di prompt riutilizzabili.
- **Leggibilità umana:** in prompt lunghi, scorrere e individuare l'inizio/fine di un blocco è immediato.

## Quando applicarla ##

| Scenario | Applicare? |
|----------|------------|
| Prompt critici/business strutturati con C.I.A.R.E. | ✅ Sì |
| Master Prompt multi-sezione (Persona + Contesto + Output + Regole) | ✅ Sì |
| Prompt brevi conversazionali | ❌ No, overhead inutile |
| Documenti Markdown standard (note, README) | ❌ No, rompe rendering |

## Integrazione con C.I.A.R.E. ##

Da combinare con il framework [[Prompting MOC#Framework C.I.A.R.E.]]: ogni lettera del framework diventa una sezione `## Contesto (C) ##`, `## Intento (I) ##`, ecc.

## Connessioni ##

- [[Prompting MOC]] — framework C.I.A.R.E. e checklist 10 controlli
- [[Skill MOC]] — convenzioni operative per prompt riutilizzabili
