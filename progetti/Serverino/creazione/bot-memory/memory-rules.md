# NOA — memory rules

Rules NOA follows for long-term memory (memory.md). Two separate flows:
**capture** (the owner decides) and **maintenance** (layered autonomy).

## 1. Capture — what to remember (owner decides)
- Triggered by `/ricorda`. The owner decides WHAT matters; NOA only helps structure it.
- On `/ricorda`, NOA analyzes the recent conversation and proposes a DETAILED SCHEMA
  of everything worth keeping: grouped by topic, one atomic fact per line.
- Nothing is written until the owner confirms. He can edit, drop, or keep items.
- NOA records only what was actually said — never its own inferences or guesses.
- Saved-fact format: `- [YYYY-MM-DD] <atomic fact>`.
- Never write secrets, credentials, tokens, passwords. Refuse and warn.

## 2. Maintenance — modify / delete (layered autonomy)
Every automatic action is reported by notification with a **motivation** and a
**citation** of the affected text. The vault is under git sync → history is the
backup, recovery is always possible.

| Layer | Action | Autonomy |
|---|---|---|
| **L1** | Delete EXACT duplicates | Automatic + notify |
| **L2** | Merge near-duplicates; mark a fact *superseded* when a newer one contradicts it (keep the old line marked, never silently lose it) | Automatic + notify |
| **L3** | Delete a non-duplicate fact; rewrite the owner's wording; promote facts into MOC-linked thematic notes; any bulk restructuring | Requires `/conferma` |

Notification examples:
- L1 → `Rimosso "<fact>" — duplicato di "<existing fact>".`
- L2 → `Aggiornato "<old>" → superato da "<new>".`

**Rule of least action:** NOA always picks the lowest layer that solves the case.

## 3. Boundaries
- Capture is always propose → confirm. Maintenance L1/L2 is automatic + notify;
  L3 needs `/conferma`.
- NOA never invents facts. It never deletes anything it cannot cite.
- `memory.md` is a working scratchpad; the curated, lasting notes live in the
  files linked from the MOC.
