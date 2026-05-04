# Sviluppare con l'AI nel 2026

> Fonte: trascrizione video omonimo — appunti strutturati sul metodo di sviluppo AI-assisted.

---

## Il Problema di Base

Delegare il **pensiero** all'AI è l'errore principale. L'AI va trattata come una tastiera velocissima, non come un sostituto del ragionamento. Senza struttura, produce output a metà, inventa dettagli e riscrive cose che non doveva toccare.

---

## Il Framework a 3 Blocchi

Da applicare ogni volta che si assegna un task a un AI.

---

### 1. Obiettivo

Definisci **esattamente** cosa significa "task concluso". Non scrivere mai "fammi un'app", ma:

> *"Voglio ottenere questo risultato, con queste funzionalità, considerando finita la task solo quando succede X."*

**Regola:** Se non vincoli la definizione di "done", l'AI riempie i buchi inventando. Ogni buco lasciato = output da ripulire.

---

### 2. Contesto

Fornisci vincoli precisi sul progetto. Il 99% dei Vibe Coders lo dà per scontato.

Elementi obbligatori da includere:

| Elemento | Esempio |
|----------|---------|
| **Stack tecnologico** | React 18 + Vite + TypeScript + CSS vanilla |
| **Scopo dei file esistenti** | "storage.ts gestisce persistenza locale" |
| **Esempi input/output attesi** | "cella A1 contiene formula `=A2+A3`" |
| **Limiti di performance/memoria** | "prototipo, non scalare per 100k utenti" |

**Nota:** Senza sapere cosa stai chiedendo al computer, non puoi valutare se l'output ha senso. Le basi della programmazione servono proprio a questo.

---

### 3. Non Fare — Guard Rails

La parte più ignorata. Specifica **nero su bianco** cosa non deve toccare.

**Esempi di guard rails:**

```
- Non toccare mai i file X o Y
- Ottimizza unicamente questa funzione
- Non cambiare lo stack tecnologico
- Non introdurre dipendenze non richieste
- Se ti manca informazione, fermati e fammi domande invece di indovinare
```

**Guard rail anti-allucinazione (per task con documentazione):**

```
Utilizza solo le informazioni presenti nel contesto o nelle fonti esplicite
che ti ho fornito. Motiva sempre le tue risposte.
```

---

## Review Driven Development

Modalità collaborativa invece di delegare tutto:

1. L'AI genera un **piano** (task list + implementation plan) prima del codice
2. Tu **revisioni** il piano e lasci commenti su cosa non va
3. L'AI aggiorna il piano in base ai feedback
4. Solo dopo il tuo **OK**, l'AI procede a generare codice
5. Ogni comando eseguito richiede la tua **approvazione**

**Beneficio:** riduce drasticamente le allucinazioni e il codice inutilizzabile.

---

## Dimostrazione Pratica — Confronto

### Prompt vago (risultato: inutilizzabile)
```
Creami un'app Google Sheet.
```
Risultato: formule non funzionanti, UX incomprensibile, funzionalità inventate.

### Prompt strutturato (risultato: professionale)
```
Obiettivo: [definizione precisa di "done"]
Stack: React 18 + Vite + TypeScript + CSS vanilla
Funzionalità: [editing celle, motore formule, import/export CSV]
Vincoli: [no dipendenze extra, prototipo leggero]
Guard rails: [non ottimizzare funzioni non richieste, chiedi se manca info]
```
Risultato: UI pulita, formule corrette, gestione errori (es. divisione per 0), export CSV funzionante.

---

## Connessioni

- [[Skill MOC]] — framework operativi per l'uso degli strumenti AI
- [[Source Intake Protocol]] — per integrare nuove fonti nel vault
- [[knowledge/Knowledge MOC]] — context engineering e uso AI
