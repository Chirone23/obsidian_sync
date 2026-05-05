# SESSION_HANDOFF — SpecterAI

**Progetto:** SpecterAI (AI Contract Analyzer per Non-Avvocati in Italiano)  
**Ultima sessione:** 2026-05-04  
**Prossima sessione:** TBD  

---

## Completato

- ✅ Specifica Tecnica v1 — Prompt system completo, few-shot examples, 7 categorie di risk
- ✅ Specifica Tecnica v2 — Aggiunta Competitive Positioning (vs Mikeoss, Harvey, Legora), Green AI section, Multi-model routing
- ✅ PC Verification — ARROW environment check, API key confermato, Cursor setup completato
- ✅ Documentazione progetto — PROMPT_LOG.md, INCIDENTS.md, SESSION_HANDOFF.md creati

---

## In Progress

- 🔄 Building MVP SpecterAI in Cursor (NOT STARTED)

---

## Prossimi Step (Priorità)

### Fase 1: Progettazione
1. ✅ Specifica Tecnica v2 completata
2. ⏳ Review architecture con focus su:
   - PDF input validation layer
   - Text extraction robustness
   - JSON response validation
   - Claude API integration strategy

### Fase 2: PDF Input Layer
1. Implementare validation per file PDF
   - Dimensione max: TBD
   - Numero pagine max: TBD
   - Formati supportati: application/pdf only
2. Setup PyMuPDF per text extraction
3. Test su dataset iniziale (5-10 contratti vari)
4. Documentare incidents su INCIDENTS.md

### Fase 3: Regex/Deterministic Layer
1. Implementare detection deterministico delle 7 categorie
2. Clausole comuni da riconoscere (auto_renewal patterns, payment terms syntax, ecc.)
3. Fallback logic se Claude non risponde

### Fase 4: Claude API Integration
1. Implementare system prompt (da Specifica v2)
2. Few-shot examples integration
3. JSON parsing + validation
4. Error handling (timeout, malformed response, rate limits)
5. Temperature=0, max_tokens=2048

### Fase 5: Testing & Optimization
1. Dataset eterogeneo (20-30 contratti)
2. Misurazione accuracy su 7 categorie
3. Performance testing (latency, token usage)
4. Green AI optimization (token efficiency)

---

## Stack Tecnico Confermato

- **Runtime:** Python 3.11
- **PDF Parsing:** PyMuPDF (fitz)
- **Claude API:** claude-3-5-sonnet-20241022
- **Environment:** Cursor IDE + .env per API key
- **VCS:** Git (push dopo ogni sessione)

---

## Parametri Claude Confermati

```
model: claude-3-5-sonnet-20241022
temperature: 0
max_tokens: 2048
system_prompt: [Vedi Specifica v2 per testo completo]
```

---

## 7 Categorie di Risk (Verificare)

1. **payment_terms** — Termini di pagamento, clock handling, ritardi
2. **auto_renewal** — Rinnovo automatico, exit clauses, notice periods
3. **penalties** — Penalty clauses, breach consequences, limitazioni
4. **liability_limitation** — Cap di liability, esclusi danni
5. **termination** — Termination clauses, early exit, wind-down obligations
6. **governing_law** — Legge applicabile, giurisdizione, dispute resolution
7. **intellectual_property** — IP ownership, usage rights, derivative works

---

## File da Sincronizzare

- ✅ prog1/PROMPT_LOG.md
- ✅ prog1/INCIDENTS.md
- ✅ prog1/SESSION_HANDOFF.md
- ✅ prog1/Specifica Tecnica v2 - SpecterAI.docx (già committato)

---

## Blocchi / Domande in Sospeso

- [ ] Nessuno al momento — procedi con Fase 2

---

## Note per Prossima Sessione

- Se trovi un incident durante Fase 2+: aggiorna INCIDENTS.md immediatamente
- Se modifichi il prompt: aggiorna PROMPT_LOG.md con nuova versione
- Ogni modifica .md → git add -A && git commit -m "..." && git push (OBBLIGATORIO)
- Ricorda: "Agents read, humans write" — non aggiungere contenuto al vault senza fonte
