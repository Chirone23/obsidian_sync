# Documentazione SpecterAI — Indice

**Progetto:** SpecterAI (AI Contract Analyzer per Non-Avvocati in Italiano)  
**Fase:** Building (Lezione 3)  
**Specifica:** [[Specifica Tecnica v2 - SpecterAI]]  

---

## File di Documentazione

Questi 3 file tracciamo il processo di building e iterazione del progetto:

### 1. [[PROMPT_LOG]]
**Scopo:** Diario delle iterazioni del prompt di sistema.  
**Cosa contiene:**
- Versioni del prompt (v1, v2, v3…)
- Problema riscontrato → Soluzione implementata
- Parametri Claude testati (temperature, max_tokens)
- Few-shot examples e loro evoluzione
- Risultati di ogni iterazione

**Quando aggiornare:** Dopo ogni test significativo del prompt.

---

### 2. [[INCIDENTS]]
**Scopo:** Registro strutturato di errori, bug e comportamenti inattesi.  
**Cosa contiene:**
- ID incident univoco (INC-001, INC-002…)
- Descrizione, severity, root cause
- Soluzione applicata e lezioni apprese
- Aggiornamenti alla Specifica v2 se necessario

**Quando aggiornare:** Ogni volta che un errore inatteso si verifica durante building.

---

### 3. [[SESSION_HANDOFF]]
**Scopo:** Stato del progetto tra sessioni (cosa è stato fatto, cosa rimane).  
**Cosa contiene:**
- ✅ Completato (task finiti)
- 🔄 In Progress (task in corso)
- ⏳ Prossimi Step (priorità)
- Blocchi/Domande aperte
- File modificati nella sessione

**Quando aggiornare:** All'inizio e alla fine di ogni sessione di lavoro.

---

## Relazione con la Specifica v2

I 3 file fanno parte della **valutazione del corso**. Documentare il processo di revisione (v1, v2, v3…) ha lo stesso peso del prodotto finale.

Vedi: [[Specifica Tecnica v2 - SpecterAI#12. Documentazione di Progetto]]

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Specifica Tecnica v2 - SpecterAI]]
- [[Lezione 3 - Building e Incident Management]]
