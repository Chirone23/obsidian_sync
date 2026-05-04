# Lezione 5 - Deploy e Presentazioni

**Corso:** AI Projects Development · [[Progettistica AI MOC]]
**Tema:** Messa in produzione, monitoring, ciclo di vita del sistema, presentazioni finali

---

## Contenuti

### Deploy

| Opzione | Costo | Quando usarla |
|---|---|---|
| Locale | Gratuito | Sviluppo / demo personale |
| Render | Gratuito (con limiti) | MVP pubblico a basso traffico |
| Railway | ~5€/mese | MVP con DB e background jobs |
| PythonAnywhere | ~5€/mese | Progetti Python semplici |
| Hetzner VPS | ~5-8€/mese | Controllo totale, più scalabile |

**Checklist pre-deploy:**
- [ ] Variabili d'ambiente configurate (non hard-coded)
- [ ] README aggiornato con istruzioni di avvio
- [ ] Dipendenze esportate (`requirements.txt` o `pyproject.toml`)
- [ ] Gestione errori in produzione (Sentry o equivalente)
- [ ] Test smoke sul sistema deployato

---

## Monitoring e Manutenzione

### Cosa Monitorare in Produzione
- **Disponibilità** — il sistema risponde?
- **Latenza** — i tempi di risposta restano accettabili?
- **Qualità degli output** — la qualità degrada nel tempo?
- **Costi** — i costi di inference restano nel budget?
- **Errori** — frequenza e tipologia degli errori

### Ciclo di Vita del Sistema
```
Produzione
    ↓
Monitor (metriche + errori)
    ↓
Degrado rilevato?
   /        \
  NO         SÌ
  ↓           ↓
Continua    Diagnosi → Fix → Test → Redeploy
```

---

## La Presentazione Finale

### Struttura Consigliata (20-30 min)
1. **Il problema** — quale problema risolve il sistema e per chi (2 min)
2. **La specifica** — le scelte principali e perché (3 min)
3. **L'architettura** — componenti, flusso, stack (3 min)
4. **Demo live** — il sistema funzionante (5-10 min)
5. **Errori e lezioni** — INCIDENTS.md: cosa è andato storto e come risolto (5 min)
6. **Limiti e evoluzioni** — cosa non fa ancora e perché (2 min)

### Cosa Dimostra la Presentazione
- Comprensione del problema (non solo del codice)
- Capacità di supervisione critica sull'AI
- Consapevolezza dei limiti del sistema
- Metodo replicabile

---

## Materiali da Consegnare

- [ ] Repository GitHub con README completo
- [ ] Specifica tecnica — versione finale
- [ ] PROMPT_LOG.md — iterazioni documentate
- [ ] INCIDENTS.md — minimo 2 voci complete
- [ ] Presentazione con demo del sistema funzionante

---

## Note Personali

*(spazio per appunti durante la lezione)*

---

## Connessioni

- [[Progettistica AI MOC]]
- [[Template - INCIDENTS]]
- [[Template - PROMPT_LOG]]
- [[Template - Specifica Tecnica]]
- [[Lezione 4 - UI UX e Validazione]]
