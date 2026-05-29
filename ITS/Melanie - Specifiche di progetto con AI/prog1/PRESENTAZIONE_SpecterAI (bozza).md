# PRESENTAZIONE SpecterAI — BOZZA

**Stato:** 🚧 bozza — da riprendere e rifinire a fine progetto (Fase 6, ultimissimo step)
**Progetto:** SpecterAI — AI Contract Analyzer per Non-Avvocati
**Corso:** AI Projects Development · [[Progettistica AI MOC]]
**Spec di riferimento:** [[Specifica Tecnica v4 - SpecterAI]]

> Scaletta della presentazione totale di progetto. Non è ancora il copione finale: è l'ossatura da completare con screenshot, tempi e copione parlato quando il sistema è testato (post-Fase 4/5).

---

## 0. Struttura proposta (durata target ~8-10 min + demo)

| # | Sezione | Tempo | Obiettivo |
|---|---|---|---|
| 1 | Hook + problema | 1' | Far sentire il dolore reale |
| 2 | Soluzione + target | 1' | Cosa fa e per chi |
| 3 | Demo live | 2-3' | Mostrare il valore, non raccontarlo |
| 4 | Architettura | 1.5' | Come funziona (privacy-first) |
| 5 | Evoluzione spec v1→v4 | 1.5' | Il processo come valore |
| 6 | Qualità & compliance | 1' | Anti-allucinazione, GDPR, AI Act |
| 7 | Limiti dichiarati + roadmap | 1' | Onestà metodologica |
| 8 | Chiusura | 0.5' | Cosa portare a casa |

---

## 1. Hook + Problema

- **Apertura:** "Quante volte hai firmato un contratto che non hai capito del tutto?"
- Freelance, autonomi, piccoli imprenditori ricevono contratti e li firmano al buio.
- Gli strumenti esistenti (Spellbook, Harvey, Legora) sono per **avvocati e legal team enterprise**: linguaggio giuridico, pricing fuori portata, workflow complessi.
- **Gap:** nessuno serve il segmento *non-avvocato, italiano, SMB-budget*.

## 2. Soluzione + Target

- **SpecterAI:** carichi il PDF del contratto → ricevi un report in **italiano plain-language** con i punti critici da verificare prima di firmare.
- **Non dà consulenza legale:** evidenzia i rischi e suggerisce le domande da porre (decision-support, human-in-the-loop).
- **7 categorie di red flag:** pagamento, rinnovo automatico, penali, limitazione responsabilità, recesso, foro competente, proprietà intellettuale.
- **Target:** freelance/PMI italiane senza legal team (TAM ~2,7M, Statista 2019).
- **Differenziatore:** Italian-first + plain-language + **privacy-first**.

## 3. Demo live  *(da preparare)*

- 2-3 contratti scelti (es. un servizi IT con tutte le categorie, un NDA con categorie assenti, uno in inglese → output IT).
- Mostrare: upload → report con semafori risk_level + citazioni verbatim + domande da porre + disclaimer.
- **Punto da enfatizzare in demo:** "il contratto con i dati personali non è mai uscito in chiaro" (vedi §4).
- *TODO: screenshot/registrazione dopo Fase 4 (test plan) e Fase 5 (polish).*

## 4. Architettura — privacy-first

Pipeline:
```
PDF → estrazione testo → estrazione metadati (date/importi)
    → REDAZIONE PII (spaCy + regex IT) → LLM riceve SOLO il testo redatto
    → JSON validato (Pydantic) → gate lingua + verifica citazioni
    → ripristino PII negli excerpt → report
```
- **Il messaggio forte:** i dati personali (nomi, CF, P.IVA, IBAN…) vengono pseudonimizzati **localmente** prima di toccare il cloud. Il modello non li vede mai.
- Backend LLM **configurabile**: Claude Code CLI (dev, €0) o API Anthropic (deploy).
- Stateless: niente persistenza, niente database.

## 5. Evoluzione delle specifiche v1 → v4  *(il processo come valore)*

| Versione | Salto |
|---|---|
| **v1** | "Basta un system prompt + un LLM efficace" — pipeline minima |
| **v2** | Il prompt diventa ingegneria (C.I.A.R.E., few-shot, routing, Green AI) |
| **v3/v3.1** | Il sistema si indurisce (anti-allucinazione citazioni, gate lingua, AI Act limited-risk, GDPR esteso, test misurabili) — confermata prof 95/100 |
| **v4** | **Cambio architetturale: privacy-first** (redazione PII pre-LLM, GDPR Art. 5/25) + backend configurabile |

- **Da raccontare:** spec congelata → in fase di build emergono divergenze → tracciate in [[SPEC_ERRATA]] → due risultano *architetturali* → promozione disciplinata a v4, senza sovrascrivere le versioni precedenti.
- Questo dimostra **metodo**, non improvvisazione (audit trail completo).

## 6. Qualità & Compliance

- **Anti-allucinazione:** ogni citazione (`raw_excerpt`) verificata contro il testo (fuzzy match) + patch prompt anti-speculazione/anti-calcolo.
- **GDPR:** data minimization (Art. 5) + privacy by design (Art. 25) → redazione PII pre-cloud. Commercial Terms Anthropic (no-training) sul path API.
- **AI Act:** limited-risk (derogazioni Art. 6(3)), solo obblighi di trasparenza già implementati.
- **Processo documentato:** PROMPT_LOG (iterazioni prompt), INCIDENTS (errori→fix, incl. code review 13 bug), CHANGELOG_BUILD, SPEC_ERRATA.

## 7. Limiti dichiarati + roadmap  *(onestà metodologica)*

- **Validazione di mercato 3/5:** desk research, **zero user interview** con freelance reali (§11.bis). L'angolo "sub-niche indifesa" è basato su assenza di concorrenza, non su domanda dimostrata.
- **Limiti tecnici noti:** over-redaction del filtro privacy (INC-006); soglia fuzzy 0.92 da calibrare; single point of failure su Anthropic (mitigato dal backend configurabile).
- **Roadmap post-MVP:** interviste utenti, smoke test landing, beta 10 utenti; provider EU (Mistral Medium 3) per data residency.

## 8. Chiusura

- **One-liner:** "SpecterAI fa capire un contratto a chi non è avvocato — senza mai esporre i suoi dati."
- Cosa portare a casa: prodotto funzionante + processo rigoroso + onestà sui limiti.

---

## TODO prima della versione finale
- [ ] Eseguire il test plan §8 (T1-T15) e inserire numeri reali (recall/precision/latenza/zero-leak)
- [ ] Screenshot/registrazione demo (post-polish Fase 5)
- [ ] Decidere il formato finale: slide (es. via `/open-design`) o documento parlato
- [ ] Copione parlato + prove tempi
- [ ] Eventuale messaggio alla prof su v4 (gap GDPR) — testo in [[SPEC_ERRATA]] Parte 3

---

## Connessioni
- [[Specifica Tecnica v4 - SpecterAI]] · [[SPEC_ERRATA]] · [[Privacy Filter Integration]]
- [[CHANGELOG_BUILD_20-05]] · [[PROMPT_LOG]] · [[INCIDENTS]] · [[SESSION_HANDOFF]]
- [[Progettistica AI MOC]]
