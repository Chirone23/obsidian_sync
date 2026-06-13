# Specifica Tecnica — Assistente AI "Voce & Forma" per Studio Commercialisti

**Progetto:** Assistente AI per revisione documenti (PROGETTO LORENZO)
**Versione:** 1.0
**Data:** 2026-06-13
**Autore:** Chirone / Filippo
**Riferimento metodologico:** [[Template - Specifica Tecnica]] · [[Progettistica AI MOC]]
**Documento a monte:** [[Validazione Idea]]

> Questo documento è il riferimento vincolante per tutte le fasi di sviluppo. Ogni modifica deve essere versionata. Eredita le decisioni prese in [[Validazione Idea]].

---

## 1. Obiettivi e Perimetro

### Obiettivo principale
Trasformare una **bozza grezza** in un documento riscritto **nello stile dello Studio**, con controllo della forma e segnalazione delle norme da verificare — lasciando sempre l'ultima parola al professionista.

### Il sistema FA:
- Riscrive una bozza/appunti nel **registro e stile dello Studio** (la "voce")
- Controlla la **forma**: grammatica, coerenza dei termini, struttura del documento
- **Segnala** le norme/citazioni toccate dal testo, marcandole come *da verificare sulla fonte*
- Produce output con un **gate umano** esplicito (proposta, non versione finale)

### Il sistema NON FA:
- **Non genera né corregge** citazioni di leggi/sentenze (solo segnalazione) — hard rule
- Non esegue ricerca giurisprudenziale autonoma (è dell'abbonamento esterno, fuori MVP)
- Non valuta il **merito legale** del contenuto (territorio consulenza)
- Non tratta dati clienti reali nella demo (solo materiale anonimizzato/sintetico)

### Utenti target
Professionisti dello Studio (commercialisti), livello tecnico non informatico, in contesto di redazione quotidiana di pareri, email e comunicazioni. Uso assistito: l'utente fornisce la bozza, legge la proposta, valida e firma.

### Valore prodotto (misurabile)
Riduce il tempo di redazione/uniformazione di un documento mantenendo qualità ≥ a quella attuale, misurata via **rubrica a 3 assi** (vedi §3). Obiettivo demo: riscrittura di un parere-tipo in < 60 secondi con punteggio rubrica ≥ 4/5 medio validato dal professionista.

---

## 2. Input e Output

### Input accettati

| Campo | Tipo | Formato | Vincoli | Obbligatorio |
|---|---|---|---|---|
| `bozza` | string | testo libero | max ~6.000 parole | Sì |
| `tipo_documento` | enum | `parere` \| `email` \| `comunicazione` | uno dei valori | Sì |
| `note_contesto` | string | testo libero | max 500 char | No |
| `base_stile` | file[] | ~10 file `.md`/`.txt` campione | caricati a setup, non a runtime | Sì (setup) |

**Edge case da gestire:**
- **Input vuoto:** rifiuto con messaggio "fornisci una bozza".
- **Input malformato / non testo:** rifiuto, nessuna elaborazione parziale.
- **Input oltre i limiti (testo troppo lungo):** chunking o rifiuto con indicazione del limite (mai troncamento silenzioso).
- **Dato sensibile rilevato** (nome/CF/email cliente): warning + richiesta di anonimizzazione prima di procedere (vedi §5 GDPR).
- **Bozza che cita una norma:** la norma va nella sezione "da verificare", **mai riscritta come fatto certo**.

### Output attesi

| Campo | Tipo | Formato | Range accettabile |
|---|---|---|---|
| `testo_riscritto` | string | testo nello stile Studio | lunghezza ~ coerente con input |
| `note_forma` | list | elenco correzioni (grammatica/coerenza/struttura) | 0–N voci |
| `norme_da_verificare` | list | elenco riferimenti segnalati + rimando fonte | 0–N voci, **mai inventati** |
| `disclaimer_gate` | string | testo fisso "proposta da validare dal professionista" | costante |

---

## 3. Requisiti di Qualità

| Metrica | Valore minimo accettabile | Metodo di misurazione |
|---|---|---|
| **Aderenza stile — tono/registro** | ≥ 4/5 | Rubrica validata dal professionista su test set documenti veri |
| **Aderenza stile — linguaggio tecnico** | ≥ 4/5 | Idem (correttezza termini tecnici) |
| **Aderenza stile — leggibilità "umana"** | ≥ 4/5 | Idem (comprensibilità per non addetti) |
| **Citazioni inventate** | **0 (hard gate)** | Verifica manuale: ogni norma in output deve esistere o essere marcata "da verificare" |
| **Tasso di revisione umana** | tracciato (non bloccante) | % di output modificati dal professionista prima della firma |
| **Latenza media** | ≤ 60 s per documento | Misurata end-to-end nella demo |
| **Costo per output** | ≤ ~0,05 € (demo, Sonnet) | Costo API per singola generazione |

> La metrica chiave non è una % generica di "accuratezza", ma la **rubrica a 3 assi** su documenti reali + il **gate hard a 0 citazioni inventate**.

---

## 4. Architettura del Sistema

### Componenti principali (flusso Voce & Forma)

```
[Bozza grezza + tipo_documento]
            ↓
[Pre-processing: anonimizzazione / check dati sensibili]
            ↓
[LLM — Claude Sonnet 4.6]
   ↳ contesto = regole di stile + ~10 file campione (artefatto .md portabile)
   ↳ task: riscrittura stile + controllo forma + segnalazione norme
            ↓
[Post-processing / validatore output]
   ↳ separa: testo_riscritto | note_forma | norme_da_verificare
   ↳ enforce hard rule: nessuna citazione "certa", solo segnalata
            ↓
[GATE UMANO — il professionista valida e firma]
            ↓
[Documento finale]
```

### Architettura ibrida (3 livelli, a regime)

```
┌──────────────────────────────────────────────┐
│ RICERCA GIURISPRUDENZA (fuori MVP)            │
│ → Abbonamento esterno (Perplexity / One       │
│   Fiscale / Normo) — sostituibile             │
├──────────────────────────────────────────────┤
│ VOCE & FORMA (componente su misura, MVP)      │
│ → Regole stile in file .md portabile          │
│ → Claude Sonnet 4.6 (Haiku per task forma)    │
├──────────────────────────────────────────────┤
│ GATE UMANO (sempre)                           │
│ → Professionista valida e firma               │
└──────────────────────────────────────────────┘
```

### Stack tecnologico

| Layer | Tecnologia scelta | Motivazione |
|---|---|---|
| LLM (voce) | Claude **claude-sonnet-4-6** | Migliore fedeltà sulle sfumature di stile (tono + tecnico + umano) |
| LLM (forma, opzionale) | Claude **claude-haiku-4-5** | Ottimizzazione costi per task ad alto volume/bassa sfumatura |
| Artefatto stile | File `.md` (prompt/skill + ~10 esempi) | **Portabilità**: model-agnostic, la voce resta dello Studio |
| Interfaccia demo | Claude Code / chat | Costo-zero per la consegna |
| Esecuzione produzione | Modello UE / Claude Enterprise | Roadmap (GDPR art. 28) — non requisito MVP |

### Dipendenze esterne

| Servizio | Utilizzo | Alternativa se down |
|---|---|---|
| API Claude (Anthropic) | Riscrittura + forma | Regole stile portabili → altro modello (GPT/Gemini) senza riscrivere la voce |
| Abbonamento ricerca (a regime) | Giurisprudenza | Sostituibile (architettura ibrida) |
| Fonti gratuite (Normattiva, Agenzia Entrate) | Verifica norme segnalate | — (ridondanti fra loro) |

---

## 5. Rischi e Assunzioni

### Assunzioni esplicite
1. Lo Studio fornisce 3–5 documenti veri (o un campione di stile pubblico per la demo) come base della "voce".
2. La demo gira su materiale **anonimizzato/sintetico**, non su dati clienti reali.
3. Il professionista resta responsabile dell'output finale (gate umano obbligatorio, L. 132/2025 art. 13).

### Rischi identificati

| Rischio | Probabilità | Impatto | Strategia di mitigazione |
|---|---|---|---|
| **Citazioni/sentenze inventate** | Media | **Alto** (condanne, caso Siracusa 338/2026 ~30k€) | Hard rule: l'AI **segnala**, non genera/corregge; gate umano verifica alla fonte |
| **Output stile generico** (non "dello Studio") | Media | Medio | Rubrica 3 assi + ~10 file campione + iterazione prompt |
| **Privacy / segreto professionale** | Media | Alto | Demo anonimizzata; a regime modello UE/Enterprise + contratto art. 28 GDPR |
| **Dipendenza da un solo provider** | Bassa | Alto | Regole stile in file `.md` portabile → cambio modello senza riscrivere |
| **Cambio pricing / deprecazione modello** | Bassa | Medio | Architettura model-agnostic + monitoraggio costi |
| **Scope creep** (voler la ricerca subito) | Media | Medio | Confine MVP definito in [[Validazione Idea]]: ricerca fuori |

### Vincoli normativi
- **GDPR:** in produzione si trattano dati personali di clienti → base giuridica + **contratto art. 28** (responsabile esterno) + modello ospitato in UE/internamente. Nella demo: solo dati anonimizzati, nessun dato cliente in chatbot pubblici.
- **AI Act:** sistema di **supporto alla redazione** con supervisione umana obbligatoria; non decisionale autonomo. L. 132/2025 art. 13: AI mai sostitutiva del professionista.

---

## Changelog

| Versione | Data | Modifica |
|---|---|---|
| 1.0 | 2026-06-13 | Prima versione — eredita decisioni da [[Validazione Idea]] |

---

## Connessioni

- [[Validazione Idea]] — documento a monte (5 dimensioni + confine MVP)
- [[Progettistica AI MOC]]
- [[Template - Specifica Tecnica]]
- [[idea nel piatto]] — briefing del committente
