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
Switch off entirely with `/automemoria off` (re-enable `/automemoria on`). When off,
NOA never modifies memory.md on its own — only `/ricorda` capture works.

The vault is under git sync → history is the backup, recovery is always possible.

### L1 — automatic + notify (non-semantic, reversible)
- Delete exact duplicates.
- Delete case/punctuation-only duplicates (same fact, different capitalization/spacing).
- Normalize format to `- [YYYY-MM-DD] fact`; trim whitespace; remove blank lines.
- Strip and refuse secrets/credentials accidentally present (delete + warn).

Each action → notification with **motivation + citation**, e.g.
`Rimosso "<fact>" — duplicato di "<existing fact>".`

### L2 — suggest → confirm (semantic changes)
NOA does NOT apply these. It records a suggestion (what it would change/delete, with
citation + reason) and sends it as a notification — **batched at end of day** (default)
or immediately (configurable). The owner confirms each one.
- Merge near-duplicates (same meaning, different wording).
- Mark a fact *superseded* when a newer one contradicts it.
- Shorten an overly verbose fact.

### L3 — owner-initiated only
- Delete a non-duplicate fact, rewrite the owner's wording, promote facts into
  MOC-linked thematic notes, bulk restructure.
- NOA never starts these. Only on the owner's explicit request.

**Rule of least action:** NOA always picks the lowest layer that solves the case.

## 3. Boundaries
- Capture is always propose → confirm. L1 is automatic + notify; L2 is
  suggest → confirm; L3 is owner-initiated only.
- NOA never invents facts. It never deletes anything it cannot cite.
- `memory.md` is a working scratchpad; the curated, lasting notes live in the
  files linked from the MOC.

## 4. Implementation notes (for code, not for NOA)
- Pending L2 suggestions must survive a restart → store in SQLite
  (table `memory_suggestions`), never in RAM. (This is the exact bug §8 avoided.)
- L2 batch delivery = a daily JobQueue job; immediate mode = send on detection.
- `/automemoria` state persisted (SQLite) so it survives restart.
