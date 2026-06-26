# Serverino — Skill Autonome (self-build & auto-attivazione)

**Data:** 2026-06-26
**Status:** 🟢 Design condiviso — esecuzione rimandata a sessione dedicata
**Scopo:** Permettere a NOA di creare, testare e attivare skill proprie in autonomia, restando dentro un confine di sicurezza imposto dall'esterno.

> Collega: [[progetti/Serverino/DEFINIZIONE_ASSISTENTE]] • [[progetti/Serverino/REALITY_CHECK]] • [[progetti/Serverino/SPECS]] • [[progetti/Serverino/bot-architecture]] • [[moc/Index MOC]]

---

## 0. Avvertenza di confine (rispetto a DEFINIZIONE_ASSISTENTE §5)

Questa feature **supera** consapevolmente il confine 2.5 → 3.0 su un punto: NOA genera codice e lo **auto-attiva** senza conferma umana. Decisione presa con cognizione il 2026-06-26. Il freno di conferma del §5 viene sostituito da un freno **tecnico** (sandbox imposta dal SO) invece che umano. Vale questo documento dove contraddice il §5 limitatamente alle skill self-build.

---

## 1. Cosa fa, in una frase

> NOA riconosce il bisogno di una skill, ne scrive il contratto, genera il codice, lo testa 3 volte in una sandbox, e se passa lo attiva da solo come nuovo comando — il tutto senza poter fare danni, perché ogni esecuzione gira dentro un recinto che NOA non controlla.

---

## 2. Ciclo di vita della skill

Macchina a stati, tabella `skills` accanto a `tasks`:

```
proposta → in_test → (3 tentativi) → attiva        se il test passa  → AUTO-ATTIVA
                                   → da_rifare      se fallisce 3 volte
attiva → attiva-fidata             dopo 5 esecuzioni riuscite "in prova"
```

| Stato | Significato |
|---|---|
| `proposta` | spec + contratto scritti, codice non ancora generato |
| `in_test` | dentro il loop dei 3 tentativi |
| `attiva` | passata, auto-attivata, registrata come comando — recinto stretto, "in prova" |
| `attiva-fidata` | superate le prime 5 esecuzioni → recinto allentato ai limiti dichiarati |
| `da_rifare` | 3 tentativi falliti → notifica + STOP, spec inoltrata a Claude |

---

## 3. Spec prima del codice (il contratto)

NOA non genera codice "a sentimento". Prima scrive un **contratto**:

- **nome** della skill
- **cosa fa** (una riga)
- **input di esempio** — almeno 2 casi, di cui 1 "scomodo" (input limite/malformato)
- **output atteso** per ciascun caso
- **capability dichiarate**: rete (sì/no), file da leggere (path espliciti), RAM richiesta

Vincolo anti-imbroglio: i casi di esempio si generano **prima** del codice, così il modello non può scrivere il test su misura del proprio bug. Il test è **comportamentale** (l'output corrisponde all'esempio?), non "exit code 0".

---

## 4. Generatore del codice — ibrido flash + fallback Claude

Decisione 2026-06-26: **"Flash genera, fallback a me (Claude)"**.

- I 3 tentativi li fa **v4-flash sul serverino**, in autonomia.
- Se la skill finisce in `da_rifare`, la spec+contratto vengono inoltrati a **Claude** (sessione con strumenti veri) per la versione buona.
- Autonomia di default, qualità come rete di sicurezza.

---

## 5. Loop a 3 tentativi (il freno)

Coerente con DEFINIZIONE §8.1 (niente retry infinito; fallimento = notifica+log+stop).

```
genera codice → esegui in sandbox → confronta output col contratto
  pass  → stato 'attiva' → AUTO-ATTIVA + registra come comando
  fail  → rimanda l'errore al modello, rigenera          [max 3 giri]
3 fail → stato 'da_rifare' + notifica + STOP → spec a Claude
```

Niente loop oltre i 3 — protegge la CPU 6W.

---

## 6. Sandbox — recinto imposto dal SO (NON Docker)

**Strumento:** `systemd-run --scope` (già presente; systemd è già usato per `Restart=always`). Niente Docker (buttato nel REALITY_CHECK: overkill su 4GB/6W), niente immagini, niente daemon. Solo limiti applicati dal kernel a un processo che parte comunque.

Esempio di lancio di una skill:
```
systemd-run --scope \
  -p MemoryMax=256M \                          # oltre soglia → kill (protegge i 4GB)
  -p CPUQuota=50% \                            # non satura la CPU 6W
  -p PrivateNetwork=yes \                      # rete spenta di default
  -p InaccessiblePaths=<path>/.env:<path>/vault \   # credenziali e vault invisibili
  python execution/skills/skill_meteo.py
```

### 6.1 Garanzie strutturali

- I permessi sono **dichiarati nel contratto alla creazione** (immutabili dopo) e **imposti dall'esterno** da systemd. NOA **non** sceglie i propri permessi a runtime né può allargarseli.
- Una skill che non ha dichiarato la rete non l'avrà **mai**, nemmeno da `attiva-fidata`.
- `.env` resta **sempre** invisibile, in ogni stato.
- Sandbox **effimera**: una creata a ogni lancio, sparisce a fine esecuzione. NOA non gestisce un parco di sandbox — avvolge ogni lancio nel wrapper, come un gesto, non come risorsa amministrata. Skill concorrenti = recinti separati.

### 6.2 Recinto che si allenta (non sparisce)

La tua scelta "solo test + prime 5 esecuzioni" è stata convertita in **recinto stretto → recinto allentato**, NON "recinto → niente recinto" (motivo: il momento più pericoloso è l'input nuovo che arriva *dopo* il periodo di prova; e il costo della sandbox è ~decine di ms, trascurabile):

- **In test + prime 5 run** (`attiva`): recinto stretto — 256 MB, rete spenta, `.env`/vault invisibili.
- **Dopo 5 run riuscite** (`attiva-fidata`): limiti allentati **ai soli valori dichiarati nel contratto** (se aveva chiesto rete, gliela dai; RAM più alta). `.env` resta invisibile. La promozione è una **regola fissa automatica**, non una decisione di NOA.

---

## 7. Autopersonalizzabile = registro dinamico

- Skill `attive` = righe in tabella `skills` + file in `execution/skills/`.
- Il dispatcher dei comandi le carica a **runtime**: una skill nuova diventa un comando **senza toccare il core**.
- Questo è il pezzo "scalabile": NOA acquisisce comandi nuovi senza riscrivere il bot.

---

## 8. Decisioni canoniche (2026-06-26)

| Tema | Scelta |
|---|---|
| Generatore | Flash genera i 3 tentativi · fallback a Claude su `da_rifare` |
| Attivazione | **Auto-attivazione** se passa il test (no conferma umana) |
| Freno | Tecnico (sandbox SO) al posto dell'umano; 3 tentativi poi `da_rifare` |
| Test | Comportamentale su contratto con ≥2 casi (1 scomodo), generato prima del codice |
| Sandbox | `systemd-run --scope`, effimera, imposta dall'esterno — NON Docker |
| Durata recinto | Sempre attivo; stretto in prova → allentato (ai limiti dichiarati) dopo 5 run |
| `.env`/vault | Invisibili in ogni stato |
| Capability | Dichiarate nel contratto alla creazione, immutabili, NOA non se le auto-assegna |

---

## 9. Prossimi passi (esecuzione — sessione dedicata)

> Da fare un file alla volta, con approvazione, come da metodo di lavoro.

1. **Schema tabella `skills` + macchina a stati** (fondamenta — tutto il resto ci si aggancia).
2. Modello del **contratto** (formato spec + casi di test).
3. **Loop a 3 tentativi** (generazione flash → test → esito).
4. **Wrapper sandbox** `systemd-run` (limiti da contratto + stato in-prova/fidata).
5. **Dispatcher dinamico** (registro skill → comando a runtime).
6. **Fallback a Claude** su `da_rifare`.

**Prerequisito da chiarire a inizio esecuzione:** dove vive il codice del bot (repo separata? cartella sul PC? da creare?). Il vault contiene solo i documenti.

---

[[progetti/Serverino/README]] • [[progetti/Serverino/DEFINIZIONE_ASSISTENTE]] • [[progetti/Serverino/REALITY_CHECK]] • [[progetti/Serverino/hardware]]
