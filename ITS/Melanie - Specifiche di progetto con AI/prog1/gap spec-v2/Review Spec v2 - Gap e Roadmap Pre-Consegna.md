# Review Spec v2 — Gap e Roadmap Pre-Consegna

**Progetto:** SpecterAI — AI Contract Analyzer for Non-Lawyers
**Oggetto review:** [[Specifica Tecnica v2 - SpecterAI]]
**Data:** 2026-05-07
**Corso:** [[Progettistica AI MOC]]
**Stato:** ⚠️ Da rivedere — pronto consegna dopo 4-6h di fix mirati

---

## 1. Sintesi Esecutiva

Il progetto è metodologicamente solido: validazione di mercato fatta, AI Act gestito con posizionamento corretto, prompt engineering completo (Ruolo+Task+Formato+Vincoli+Esclusioni+few-shot), v1→v2 con changelog tracciato. Mancano però: **piano di test concreto con criteri di successo numerici**, **gestione esplicita del rischio "Claude allucina raw_excerpt"**, e **stima costi/budget API per il MVP**. Il pacchetto documentale è sopra la media corso, ma 3 GAP misurabili lo separano dal "pronto consegna".

---

## 2. Score Multi-Prospettiva

| Dimensione | Score | Motivazione |
|---|---|---|
| Coerenza interna (v1↔v2, idea↔specifica) | **5/5** | Changelog v1→v2 esplicito. Le 7 categorie di [[Contract Analyzer - Validazione Idea]] coincidono con `v2 §6`. Posizionamento AI Act coerente in tutti i file. |
| Completezza documentale | **4/5** | Specifica copre tutta la checklist corso (`v2 §11`). Manca **Test Plan eseguibile** (cosa, come, criterio pass/fail) e **stima costi MVP** con scenari numerici (es. 100 analisi/mese). |
| Fattibilità tecnica (stack, scope, tempi) | **4/5** | Stack minimale e provato (FastAPI+PyMuPDF+Anthropic). Scope chiuso (`v2 §2`). **Rischio:** la validazione "raw_excerpt verbatim" via Pydantic non è risolta — Pydantic valida la struttura, non se la stringa è davvero presente nel PDF. |
| Validazione di mercato (target, problema, competitor) | **4/5** | Stress-test Perplexity rigoroso con dichiarazione "no data" esplicita. Competitor mappati (Spellbook, Harvey, Legora, Mikeoss). **Debole:** zero user research diretta. |

---

## 3. Top 5 Problemi Critici

### Problema 1 — Validazione anti-allucinazione di `raw_excerpt` insufficiente
- **Impatto:** Alto — single point of failure dell'AI Act positioning
- **Dettaglio:** `v2 §11` dichiara "Pydantic validation + raw_excerpt testuale obbligatorio" come mitigazione, ma Pydantic non verifica che la stringa esista davvero nel testo del contratto. Claude può inventare una citazione plausibile e Pydantic la accetta.
- **Azione:** Aggiungere in `§8` un check deterministico post-LLM: `assert raw_excerpt in contract_text` (con normalizzazione whitespace). Se fallisce → retry o flag "citazione non verificata".
- **Fonte:** `Specifica Tecnica v2 §11 Rischi identificati`

### Problema 2 — Test Plan elencato ma non eseguibile
- **Impatto:** Medio-Alto — Melanie chiederà "come dimostri che funziona?"
- **Dettaglio:** `v2 §8` elenca 6 test ma senza criteri pass/fail misurabili. Nessun dataset definito.
- **Azione:** Tabella `[Test ID | Input | Output atteso | Criterio pass]`. Specificare i 5 contratti reali (anche fittizi ma fissati).
- **Fonte:** `Specifica Tecnica v2 §8 Test pianificati`

### Problema 3 — Stima costi MVP assente
- **Impatto:** Medio — buco nella dimensione "Economica" del framework 5D
- **Dettaglio:** `v2 §7` cita "<0,02 €/analisi" ma manca lo scenario operativo: budget demo corso, MVP testing, MVP pubblico/mese.
- **Azione:** Tabella scenario: Demo (20 analisi=0,40€), Testing (200=4€), Pubblico/mese (1000=20€).
- **Fonte:** `Specifica Tecnica v2 §7 Stima consumo`

### Problema 4 — Assunzione "10-20 contratti/anno" mai verificata
- **Impatto:** Medio — Melanie chiederà "hai parlato con utenti reali?"
- **Dettaglio:** Dichiarata in [[Contract Analyzer - Validazione Idea]] e `v2 §11` — nessuna verifica empirica (nemmeno 3 interviste).
- **Azione:** Aggiungere `§11.bis` "Limiti della validazione: zero user interview condotte; assunzione da testare in fase post-MVP con N=5 freelance".
- **Fonte:** `Contract Analyzer - Validazione Idea §Assunzione`

### Problema 5 — Gestione lingua incoerente nel flusso
- **Impatto:** Basso-Medio — edge case ma incoerenza visibile in review
- **Dettaglio:** `v2 §3` dice "rilevamento lingua heuristica, no LLM" e usa `langdetect`. Ma `§3 edge case` "lingua non IT/EN → elaborazione comunque, avviso" — comportamento non specificato (prompt in inglese, output deve essere italiano: cosa succede a un contratto in tedesco?).
- **Azione:** Decidere: blocco esplicito (errore "solo IT/EN supportati") o fallback documentato (output IT con warning "qualità ridotta").
- **Fonte:** `Specifica Tecnica v2 §3` vs `§6 Multi-model routing`

---

## 4. Domande che Melanie probabilmente farà

| # | Domanda | Risposta presente? | Dove |
|---|---|---|---|
| 1 | Come verifichi che `raw_excerpt` non sia un'allucinazione? | ❌ NO | Vedi Problema 1 |
| 2 | Quanti contratti reali hai testato e con quali risultati? | ❌ NO | `v2 §8` solo pianificati |
| 3 | Hai parlato con freelance reali per validare il problema? | ❌ NO | Vedi Problema 4 |
| 4 | Quanto costa girare il MVP per un mese? | ⚠️ Parziale | `v2 §7` senza scenari |
| 5 | Cosa cambia tecnicamente tra v1 e v2 oltre al prompt? | ✅ SÌ | `v2 §Changelog` |
| 6 | Perché temperature=0 e non 0.2 per leggere meglio le sfumature? | ✅ SÌ ma laconica | `v2 §6 Nota temperatura` |
| 7 | Il troncamento a 40k char come fai a sapere che non taglia clausola critica? | ⚠️ Parziale | `§11` dichiara rischio, mitigazione "futura" |
| 8 | Pydantic valida la struttura — chi valida la semantica? | ❌ NO | Riformulazione di Problema 1 |

---

## 5. Verdetto Finale + Roadmap di Fix

**Pronto dopo 4-6 ore di lavoro, in questo ordine:**

| # | Fix | Tempo | Sezione spec |
|---|---|---|---|
| 1 | Verifica `raw_excerpt in contract_text` (single point of failure AI Act) | 45 min | `§8 + §11` |
| 2 | Tabella Test eseguibile con criteri pass/fail e dataset 5 contratti | 60 min | `§8` |
| 3 | Tabella scenari costo (Demo/Testing/Pubblico) | 20 min | `§7` |
| 4 | Sezione `§11.bis` "Limiti validazione" — zero user research dichiarata | 15 min | `§11` |
| 5 | Decidere comportamento per lingue non IT/EN | 15 min | `§3 + §6` |
| 6 | Eseguire 1-2 test reali e riportare in [[PROMPT_LOG]] | 30 min | PROMPT_LOG |
| 7 | Frase in `§1` su "user research zero, validazione post-MVP" | 10 min | `§1` |

---

## Confidence dell'analisi

**4/5.** Letti i 5 file richiesti. Limite: non ho letto `PROMPT_LOG.md`, `INCIDENTS.md`, `SESSION_HANDOFF.md` (esclusi da brief) — se contengono test già eseguiti, Problema 2 può essere parzialmente coperto. Le "domande probabili" sono inferite dalla checklist del corso visibile in `v2 §11`, non da criteri di valutazione interni di Melanie.

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Specifica Tecnica v2 - SpecterAI]]
- [[Specifica Tecnica v1 - SpecterAI]]
- [[Contract Analyzer - Validazione Idea]]
- [[Brainstorming - Validazione Idea]]
