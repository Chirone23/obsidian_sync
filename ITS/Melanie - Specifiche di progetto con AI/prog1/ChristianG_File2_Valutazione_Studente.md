# Christian G. — Valutazione Intermedia (Lezione 2 → Lezione 3)

**Progetto:** SpecterAI — AI Contract Analyzer for Non-Lawyers  
**Corso:** AI Projects Development — ITS ICT Academy Roma  
**Data:** 06/05/2026

---

## Punteggio: 95/100 — Eccellente

---

## Valutazione generale

Lavoro eccezionale sotto ogni aspetto. Il brainstorming è il più ampio ricevuto (7 idee, con ricerca di mercato e stress-test delle obiezioni via Perplexity), la specifica tecnica è una delle più complete e professionali della classe, e l'iterazione v1→v2 con changelog documentato è esattamente il processo richiesto dal corso.

Il progetto ha un perimetro chiuso e realistico, un problema reale ben identificato (freelance italiani che firmano contratti senza capirli), e uno stack lineare che non introduce complessità inutile. Il prompt di sistema completo con few-shot examples, la strategia di token optimization e la sezione Green AI nella v2 mostrano un livello di maturità progettuale che va oltre le aspettative del corso.

---

## Punti di forza

- **Ricerca di mercato autentica:** hai mappato competitor reali (Spellbook, Harvey AI, Docusign IAM, Mikeoss, Legora), verificato la saturazione del segmento enterprise, e identificato la sub-nicchia "non-lawyer SMB italiano" come spazio non occupato. La tabella di stress-test con Perplexity (obiezione → dato → verdetto) è un approccio critico eccellente.
- **Iterazione v1→v2 documentata:** il changelog con 7 modifiche specifiche è esattamente come si evolve una specifica. Le aggiunte nella v2 (competitive positioning, prompt completo, few-shot, edge case input, Green AI) mostrano riflessione reale tra una versione e l'altra.
- **Prompt di sistema completo:** Ruolo → Task → Formato → Vincoli → Esclusioni, con schema JSON esatto e 2 few-shot examples (clausola presente/assente). Questo prompt è pronto per essere usato in produzione.
- **Layer deterministico:** la scelta di estrarre date e importi con regex prima di invocare l'LLM mostra maturità — non tutto deve passare dall'AI.
- **Posizionamento AI Act:** hai identificato il rischio (analisi legale = potenzialmente alto rischio), proposto la mitigazione corretta (decision-support, disclaimer, HITL), e verificato con dati che il posizionamento funziona nel mercato.
- **Ambiente pronto:** la verifica PC con tutte le 8 dipendenze installate e l'API key confermata significa che puoi iniziare a costruire immediatamente alla Lezione 3.

---

## Cosa fare prima della Lezione 3

La tua specifica è completa e il tuo ambiente è pronto. Il lavoro da fare è preparare il terreno per costruire in modo efficiente.

### 1. Crea la struttura del progetto in Cursor

Apri Cursor e crea la struttura base del progetto:

```
specter-ai/
├── main.py              # FastAPI app, endpoint upload + analisi
├── pdf_processor.py     # PyMuPDF: estrazione testo da PDF
├── regex_layer.py       # Estrazione date, importi, scadenze
├── llm_client.py        # Chiamata Claude API + gestione retry
├── schemas.py           # Schema Pydantic per validazione JSON output
├── templates/
│   └── report.html      # Template Jinja2 per il report
├── prompts/
│   └── system_prompt.md # Il prompt di sistema (già scritto nella spec v2)
├── .env                 # ANTHROPIC_API_KEY (nel .gitignore)
├── .gitignore
├── requirements.txt
└── README.md
```

Non devi scrivere codice ora — solo la struttura. Alla Lezione 3 popolerai i file.

### 2. Testa il prompt di sistema in Claude.ai

Prima di codificare, verifica che il prompt funzioni. Prendi un contratto PDF reale (anche breve), estrai il testo manualmente, e incollalo in Claude.ai con il tuo system prompt. Verifica che:
- L'output sia JSON valido
- Tutte e 7 le categorie siano presenti
- I `raw_excerpt` siano citazioni reali dal testo
- I `risk_level` siano solo "low", "medium" o "high"

Se qualcosa non funziona, correggi il prompt ora — prima di scrivere codice è il momento migliore.

### 3. Scrivi lo schema Pydantic (schemas.py)

Questo è il primo file da scrivere perché tutto il resto dipende da lui:

```python
from pydantic import BaseModel
from typing import Literal

class CategoryAnalysis(BaseModel):
    present: bool
    raw_excerpt: str
    plain_language: str
    risk_level: Literal["low", "medium", "high"]
    question_to_ask: str

class ContractAnalysis(BaseModel):
    language_detected: Literal["italian", "english"]
    categories: dict[str, CategoryAnalysis]  # 7 categorie fisse
    top_3_risks: list[str]
    disclaimer: str
```

Testalo con un JSON di esempio per verificare che la validazione funzioni prima di collegarlo a Claude.

### 4. Prepara 3 contratti PDF di test

Trova 3 contratti PDF reali e diversi (servizi, NDA, fornitura) da usare come test set alla Lezione 3. Li hai già previsti nella sezione test — averli pronti ti fa risparmiare tempo.

---

## Materiali consegnati e mancanti

**Materiali obbligatori consegnati:** brainstorming, validazione dettagliata, specifica v1 e v2 con changelog. Tutto presente.

**Materiali extra consegnati:** Verifica_PC_-_personale.docx — completa e dettagliata (sistema, Python, Git, Cursor, 8 dipendenze installate, API key confermata).

**Materiali mancanti (non obbligatori):** PROMPT_LOG, INCIDENTS, studio PDF lezioni. I file di documentazione sono referenziati nella spec v2 (sezione 12) come file da mantenere durante lo sviluppo — coerente.

**Versionamento spec:** 2 versioni prodotte (v1 → v2) con changelog esplicito di 7 modifiche. Ottima pratica.

---

## Priorità per la Lezione 3

1. **Testa il prompt di sistema** in Claude.ai con un contratto reale — verifica che l'output JSON sia valido e conforme
2. **Crea la struttura del progetto** in Cursor (cartelle, file vuoti, .env, .gitignore)
3. **Scrivi schemas.py** (Pydantic) — è la base di validazione di tutto il sistema
4. **Costruisci pdf_processor.py** (PyMuPDF: estrai testo da PDF) — il primo modulo del flusso
5. **Costruisci regex_layer.py** — estrazione date e importi deterministici

---

*Valutazione intermedia — Corso AI Projects Development, ITS ICT Academy Roma*
