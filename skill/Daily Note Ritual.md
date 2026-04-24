# Daily Note Ritual

> Le daily note non sono un diario — sono il **punto di cucitura** tra sessioni.
> Obiettivo: ogni mattina il secondo cervello riparte da dove si è fermato.

**Cartella:** `daily/`
**Formato nome file:** `YYYY-MM-DD.md`

---

## Template Daily

```markdown
# {YYYY-MM-DD}

## 🎯 Focus del giorno
- [priorità 1]
- [priorità 2]
- [priorità 3]

## 📚 Input (fonti toccate)
- [[fonte o link]]

## 🧠 Insight / Note raccolte
- ...

## 🔗 MOC impattati oggi
- [[MOC X]] — cosa è stato aggiornato
- [[MOC Y]] — ...

## ⏭ Open loops (da riprendere domani)
- [ ] ...

## 🗓 Log
- HH:MM — evento
```

---

## Apertura Giornata (2 min)

1. Apri la daily di oggi (crea se non esiste)
2. Copia dagli **Open loops** di ieri → `Focus del giorno`
3. Dichiara max 3 priorità
4. Se non hai priorità chiare → 1 domanda preventiva a te stesso: *"Cosa devo finire oggi per non bloccare domani?"*

---

## Durante la Giornata

Ogni volta che:
- **Aggiungi conoscenza a un MOC** → logga in `MOC impattati`
- **Incontri una fonte nuova** → logga in `Input`
- **Hai un'idea che non entra in un MOC esistente** → butta in `Insight` con tag `#da-promuovere`

**Non tenere tutto in testa.** La daily è il buffer.

---

## Chiusura Giornata (3 min)

1. Compila `Open loops` — tutto ciò che è rimasto a metà
2. Verifica che ogni insight sia stato **promosso al MOC** (vedi [[MOC Integration Checklist]])
3. Se un insight è ancora solo nella daily → è a rischio di perdita, promuovilo ora

---

## Review Settimanale (Domenica, 15 min)

Obiettivo: evitare che la daily diventi cimitero di idee non promosse.

- [ ] Scorri le 7 daily della settimana
- [ ] Per ogni `#da-promuovere` → decidi: promuovi a MOC, sposta in `idee/`, o cancella
- [ ] Conta MOC toccati: se solo 1-2 per tutta la settimana → stai lavorando in silos?
- [ ] Open loops vecchi > 7 giorni → chiudi, rimanda formalmente, o delega
- [ ] Crea/aggiorna una "Weekly Review" se emergono pattern (es. *"questa settimana ho toccato molto Bibliò"*)

---

## Review Mensile (ultimo giorno del mese, 30 min)

- [ ] Rileggi le Weekly Review
- [ ] Scrivi 3 domande aperte che vorresti risolvere il mese prossimo
- [ ] Archivia daily vecchie > 60 giorni in `daily/archive/{YYYY-MM}/` se la cartella diventa pesante

---

## Anti-Pattern

- ❌ Daily usata come diario emotivo — quello sta altrove
- ❌ Insight lasciati solo in daily, mai promossi → si perdono
- ❌ Daily vuote per giorni → sintomo di lavoro non riflessivo
- ❌ Open loops accumulati mese dopo mese senza pulizia

---

## Connessioni

- [[MOC Integration Checklist]]
- [[Vault Audit]] — check orfani include daily non promosse
