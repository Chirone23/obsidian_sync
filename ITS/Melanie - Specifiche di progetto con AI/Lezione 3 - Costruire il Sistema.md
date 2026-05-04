# Lezione 3 - Costruire il Sistema

**Corso:** AI Projects Development · [[Progettistica AI MOC]]
**Tema:** Stack tecnologico, primo sviluppo, quality control iniziale

---

## Contenuti

### AI come Co-Sviluppatore
L'AI genera codice, architetture e soluzioni, ma **non può valutare autonomamente la propria adeguatezza al contesto**.

Il Pilastro III — **Supervisione umana critica** — significa:
- Riconoscere gli errori dell'AI
- Indirizzarla verso soluzioni migliori
- Mantenere la responsabilità decisionale su ogni scelta critica

> La supervisione non è un controllo passivo, ma una partecipazione attiva al processo di sviluppo.

---

## Stack Tecnologico Consigliato

| Categoria | Strumenti | Accessibilità |
|---|---|---|
| LLM | OpenAI, Claude, Gemini, Mistral, Llama | API tier gratuito/basso costo |
| AI code editor | Cursor | Piano gratuito disponibile |
| Linguaggio | Python 3.12+ | Gratuito / open source |
| Web framework | FastAPI (consigliato), Flask | Gratuito / open source |
| Database | SQLite (consigliato) | Integrato in Python |
| ORM + validazione | SQLAlchemy, Pydantic | Gratuito / open source |
| Client HTTP | httpx | Gratuito / open source |
| Version control | Git + GitHub | Gratuito |
| Deploy | Locale, Render, Railway, PythonAnywhere, Hetzner VPS | Gratuito → ~5-8€/mese |

---

## Primo Sviluppo: Approccio

1. **Partire dalla specifica** — il codice implementa la specifica, non la sostituisce
2. **Sviluppo modulare** — un componente alla volta, testato prima di passare al successivo
3. **Documentare ogni iterazione** → PROMPT_LOG.md
4. **Documentare ogni errore** → INCIDENTS.md

---

## Quality Control — Prima Integrazione

Il quality control non è un'ispezione conclusiva, è una **disciplina progettuale che attraversa ogni fase**.

### In questa lezione:
- Testare ogni componente in isolamento
- Verificare che gli output rispettino la specifica (input/output definiti)
- Identificare le prime allucinazioni o output inattesi
- Registrare su INCIDENTS.md

### Validatori da implementare:
- [ ] Validazione del formato dell'output (Pydantic)
- [ ] Test del caso limite più critico
- [ ] Verifica che il sistema fallisca in modo "graceful" su input errati

---

## Gestione Strutturata degli Errori

Quando qualcosa si rompe:
1. Leggere il messaggio di errore e lo stack trace
2. Capire la causa radice (non coprire il sintomo)
3. Correggere e testare
4. Documentare su INCIDENTS.md
5. Aggiornare la specifica se necessario

---

## Note Personali

*(spazio per appunti durante la lezione)*

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Template - INCIDENTS]]
- [[Template - PROMPT_LOG]]
- [[Lezione 2 - Specifica Tecnica e Prompt Engineering]]
- [[Lezione 4 - UI UX e Validazione]]
