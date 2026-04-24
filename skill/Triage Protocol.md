# Triage Protocol

> Prima di agire su una richiesta, classificala. Evita di partire veloce su un task critico
> o di bloccarsi con domande inutili su un task creativo.

---

## Fase 1 — Classificazione Istantanea

### Task CRITICO / Business
Trigger:
- Coinvolge decisioni irreversibili (delete, push, invio email, impegni)
- Output andrà a terzi (cliente, docente, esame, PR)
- Numeri, date, impegni legali/fiscali
- Progetti ITS consegnabili (es. Bibliò MVP)
- Modifica CLAUDE.md o direttive di sistema

**Comportamento:** **domande preventive obbligatorie** prima di iniziare. No assunzioni.

### Task CREATIVO / Personale
Trigger:
- Brainstorming, esplorazione idee
- Note personali, daily, riflessioni
- Estrazione/organizzazione di fonti già fornite
- Refactor di note senza cambi semantici

**Comportamento:** **procedi**, chiedi solo chiarimenti di stile/formato se emergono.

---

## Fase 2 — Master Prompt (solo task CRITICI)

Quando identificato critico, costruisci mentalmente (o esplicitamente) un Master Prompt con:

| Componente | Domanda da farsi |
|-----------|------------------|
| **Persona** | Quale ruolo/expertise mi serve attivare? |
| **Contesto** | Quali note del vault devo leggere prima? |
| **Vincoli formato** | Output: Markdown? Tabella? Lista? Lunghezza? |
| **Few-Shot** | Ho un esempio simile già risolto nel vault? |
| **Edge cases** | Cosa può andare storto? Cosa NON deve fare? |
| **Chain-of-Thought** | Serve ragionamento step-by-step esplicito? |

Se uno di questi è indefinito → **domanda preventiva all'utente**.

---

## Fase 3 — Score Confidence

Prima di produrre l'output finale, auto-valuta:

| Score | Significato | Azione |
|-------|-------------|--------|
| **5** | Istruzioni cristalline, risorse tutte lette | Procedi |
| **4** | Una piccola ambiguità tollerabile | Procedi, flagga l'assunzione fatta |
| **3** | Ambiguità rilevanti | Fai 1-2 domande prima |
| **2** | Contesto insufficiente | Stop, chiedi chiarimenti |
| **1** | Non so cosa sta chiedendo | Riformula la richiesta con l'utente |

Soglia minima per partire su task critico: **4**.

---

## Domande Preventive Tipiche

Per un task critico, di solito 1-2 di queste sono sufficienti:

- *"L'output va a [chi]? Serve tono formale/informale?"*
- *"Devo sovrascrivere o creare versione parallela?"*
- *"Quali note esistenti devono restare immutate?"*
- *"Il deliverable è per oggi o posso proporre alternative?"*
- *"Preferisci velocità o completezza?"*

---

## Anti-Pattern

- ❌ Fare 5 domande quando ne bastava 1 (trigger: task creativo trattato come critico)
- ❌ Partire a scrivere senza leggere il vault (trigger: task critico trattato come creativo)
- ❌ Master Prompt senza edge cases → output che rompe su input atipici
- ❌ Chiedere conferma dopo aver già fatto (deve essere *preventiva*)

---

## Casi Edge

**Task ibrido** (creativo ma con output pubblico): applica Master Prompt leggero, 1 sola domanda di verifica.

**Task urgente dichiarato**: puoi abbassare la soglia a score 3 ma flagga esplicitamente le assunzioni.

**Task ripetitivo già fatto**: score 5 automatico se il pattern è nel vault — cerca prima in `skill/`.

---

## Connessioni

- [[Prompting MOC]] — framework C.I.A.R.E. e 5 pilastri
- [[MOC Integration Checklist]]
