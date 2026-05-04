# Lezione 4 - UI/UX e Validazione

**Corso:** AI Projects Development · [[Progettistica AI MOC]]
**Tema:** Interfaccia utente, validazione sistematica, quality control avanzato

---

## Contenuti

### Interfaccia UI/UX
L'interfaccia è il punto di contatto tra il sistema AI e l'utente finale. Nel caso del progetto:
- Deve includere un **meccanismo di approvazione umana** (supervisione critica in azione)
- Deve rendere visibili gli output del sistema per consentire la validazione
- Semplicità > complessità: meglio un'interfaccia funzionale e validata che una sofisticata e fragile

---

## Validazione Sistematica

### Cosa Validare
1. **Correttezza dell'output** — rispetta la specifica? I valori sono nel range atteso?
2. **Allucinazioni** — il sistema produce informazioni non fondate o inventate?
3. **Bias** — il sistema favorisce sistematicamente certe risposte?
4. **Non conformità normative** — il sistema rispetta GDPR, AI Act?
5. **Consistenza** — a input simili corrispondono output simili (quando atteso)?

### Framework di Quality Control

```
Input di test → Sistema AI → Output
                                ↓
                         Validatore automatico
                                ↓
                    Conforme alla specifica?
                       /              \
                     SÌ               NO
                      ↓                ↓
                  Registra         INCIDENTS.md
                  successo         + fix + retest
```

### Metriche da Misurare
- % output conformi alla specifica
- Latenza media e P95
- Tasso di errori / fallimenti
- Costo per output generato

---

## AI Sustainability

Progettare pensando alla sostenibilità a lungo termine:
- **Indipendenza dai provider** — non legarsi a un singolo LLM o API
- **Costi di inference** — monitorare e ottimizzare il costo per chiamata
- **Fallback strategies** — cosa succede se un servizio va down?
- **Deprecation risk** — il modello usato oggi potrebbe non esistere tra 12 mesi

---

## Checklist Validazione

- [ ] Definito un set di test cases dalla specifica (sezione input/output)
- [ ] Implementato almeno un validatore automatico sull'output
- [ ] Testato su almeno 5 input reali rappresentativi
- [ ] Documentati i risultati dei test
- [ ] Identificate e registrate almeno 2 anomalie su INCIDENTS.md
- [ ] Verificata la conformità con i requisiti di qualità della specifica

---

## Note Personali

*(spazio per appunti durante la lezione)*

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Template - INCIDENTS]]
- [[Lezione 3 - Costruire il Sistema]]
- [[Lezione 5 - Deploy e Presentazioni]]
