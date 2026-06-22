# C.I.A.R.E. — Framework di Riccardo Raponi

**Tipo:** Struttura di contenuto per prompt efficaci  
**Fonte:** `ITS/Riccardo Raponi - Prompting/Prompting MOC.md`  
**Applicabilità:** Trasversale (LLM-agnostic)

---

## Definizione

C.I.A.R.E. è un framework a 5 elementi per strutturare il **contenuto** di un prompt prima di inviarlo a qualsiasi LLM.

| Lettera | Elemento | Cosa fare |
|---------|----------|-----------|
| **C** | **Contesto** | Background, situazione di partenza, prerequisiti che l'IA deve conoscere |
| **I** | **Intento** | Obiettivo finale, uso dell'output (non il task generico, ma *perché*) |
| **A** | **Audience + Output** | Chi leggerà il risultato + formato (tabella, JSON, lista, prosa) |
| **R** | **Regole** | Vincoli di stile, lunghezza, tono, cosa evitare |
| **E** | **Esempi** | Modelli Few-Shot da imitare (input/output di riferimento) |

---

## Esempio: Validazione di Fonti (Prompt Library 01)

```markdown
**C — Contesto**
Ho una lista di newsletter/siti trovati tramite ricerca AI.
Devo verificarne la qualità prima di affidarmi come fonte di aggiornamento.

**I — Intento**
Classificare ogni fonte per affidabilità, in modo da scegliere solo le migliori
per la mia osservazione settimanale.

**A — Audience + Output**
Destinatario: me (ricercatore indipendente)
Formato: tabella con righe = fonti, colonne = criterio di valutazione
+ ranking top 3 finale in formato narrativo

**R — Regole**
- Non inventare informazioni: solo dati verificabili tramite web search
- Tono: fact-based, niente opinioni
- Lunghezza per fonte: max 2 righe
- Se un link è morto, segnalarlo esplicitamente

**E — Esempi**
Riferimento (row di tabella):
| Nome | Link | Attiva? | Ultimo aggiornamento | Verdict |
|------|------|---------|---------------------|---------|
| TechCrunch | techcrunch.com | ✅ | 22 giu 2026 | ✅ Tieni |
```

---

## I 5 Pilastri della Padronanza (da Riccardo)

1. **Dare Direzione** — ruolo/persona specifico → sblocca vocabolario tecnico
2. **Specificare il Formato** — lunghezza, struttura Markdown, tono
3. **Fornire Esempi (Few-Shot)** — mostra input/output attesi
4. **Valutare la Qualità** — chiedi all'IA di auto-valutarsi (score 1-5)
5. **Suddividere il Lavoro** — scomponi task complessi in sotto-compiti sequenziali

---

## Checklist 10 Controlli Pre-Invio

- [ ] **Ruolo** — persona/expertise è dichiarata?
- [ ] **Obiettivo** — cosa deve produrre l'output?
- [ ] **Contesto** — background e situazione sono presenti?
- [ ] **Destinatario** — chi leggerà il risultato?
- [ ] **Formato** — struttura, lunghezza, tipo (tabella/JSON/prosa)?
- [ ] **Vincoli** — limiti di stile, tono, tempo?
- [ ] **Specificità** — sono stati eliminati termini vaghi?
- [ ] **Esempi** — c'è almeno uno shot di riferimento?
- [ ] **Esclusioni** — cosa NON deve fare?
- [ ] **Chiarezza** — un estraneo capirebbe la richiesta?

---

## Matrice delle Strategie Complementari

| Strategia | Quando usarla | Interazione con C.I.A.R.E. |
|-----------|---|---|
| **Zero-Shot** | Task comuni, nessun esempio | Manca E (esempi) — rischioso su output specifici |
| **One-Shot / Few-Shot** | Output con formato preciso | E (esempi) è critico — allinea lo stile |
| **Role-Based** | Serve vocabolario di dominio | Enhance C (contesto) con ruolo dichiarato |
| **Chain-of-Thought (CoT)** | Logica/matematica multi-step | Parte di I (intento) — chiedi "pensiamo passo dopo passo" |
| **RAG** | Risposte ancorate a fonti | C (contesto) = upload file di riferimento |

---

## Edge Cases — Quando C.I.A.R.E. Fallisce

| Edge case | Sintomo | Mitigazione |
|-----------|---------|------------|
| Soggettività interpretativa | Termini come "interessante", "professionale" non univoci | Vincoli misurabili in R (regole) |
| Mancanza di ground truth | Intento non ancorato a documenti → allucinazioni | Allegare fonti in C (contesto) |
| Contesto ambiguo | Background contraddittorio → output incoerente | Esplicitare priorità in C |
| Sovrapposizione info | Troppe regole/esempi contrastanti | Limitare R a 3-5 max; E coerenti |

**Regola operativa:** dopo 5-6 round di raffinamento senza miglioramento, ripartire da prompt pulito basato sui feedback raccolti.

---

## Connessioni nel Vault

- [[Prompting MOC]] — fondamenti e tecnici
- [[Prompt Library]] — prompt pronti strutturati con C.I.A.R.E.
- [[Lab - Board Virtuale PMI AI Deployment]] — esempio complesso di C.I.A.R.E. applicato a scenario multi-personaggio
- [[Creazione di scenari con AI]] — C.I.A.R.E. + Character Card + Scene Contract
