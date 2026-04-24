# Front-end MOC

Materia ITS — Sviluppo front-end, web architecture, design funzionale, AI.
**Docente:** Alessandro D'Alessandro
**Fonte:** `ITS/Alessandro D'Alessandro - Front-end/`
**NotebookLM:** https://notebooklm.google.com/notebook/14306a1e-2419-4608-9a47-a6c475c40505

---

## Web Architecture — Fondamenta

### Modello Client-Server

Il **client** (browser) è il richiedente che avvia la conversazione. Il **server** è il fornitore che ascolta, processa e risponde. Separare i due livelli permette di aggiornare il server senza toccare il client.

### Event Loop JavaScript

JavaScript è **single-threaded** — esegue una sola istruzione alla volta. Per non bloccare il thread principale (che causerebbe lag dell'interfaccia):

- Operazioni asincrone (timer, network) vengono **delegate ad altri sistemi del browser**
- Gli eventi arrivano in una **coda** e vengono gestiti uno alla volta
- `fetch()` restituisce una **Promise** — non blocca il thread, viene risolta dall'Event Loop in un secondo momento

### Pipeline Completa di una Richiesta HTTP

```
Click utente
  → DNS resolution      (nome dominio → IP numerico del server)
  → Connessione TCP/QUIC (trasporto affidabile)
  → TLS Handshake       (HTTPS: negozia chiavi crittografiche)
  → HTTP Request        (metodo + header + body)
  → Elaborazione server (routing, logica business, DB/cache)
  → HTTP Response       (status code + header + contenuto)
  → Rendering browser   (HTML/JSON → UI visibile)
```

### JSON → DOM (La Trasformazione)

- **JSON** = stringa testuale per scambiare dati via API ("lingua franca")
- **DOM** = albero in memoria del documento HTML, manipolabile da JavaScript
- **Flusso:** server invia JSON → `JSON.parse()` / `.json()` lo converte in oggetto JS → JavaScript crea elementi HTML → li inietta nel DOM → UI aggiornata

### API e REST

API = "contratto digitale" tra sistemi. REST usa metodi HTTP standard:

| Metodo | Azione |
|--------|--------|
| GET | Leggi risorsa |
| POST | Crea risorsa |
| PUT | Aggiorna risorsa |
| DELETE | Elimina risorsa |

### Sicurezza Web

**Same-Origin Policy (SOP):** blocca gli script dal leggere risposte di origini diverse (schema + host + porta diversi).

**CORS (Cross-Origin Resource Sharing):** permette deroghe controllate alla SOP.
- **Preflight (OPTIONS):** per richieste "non semplici" il browser chiede prima al server il permesso via `OPTIONS`. Solo se risponde con `Access-Control-Allow-Origin` corretto, procede con la richiesta reale.

---

## CSS — Come Funziona

**Specificità** — determina quale regola vince (ordine di priorità):
```
!important > Inline > ID > Classe > Tag
```

**Selettori:** base (tag, classe, ID), combinatori, attributi — mappano gli stili sul DOM.

---

## JavaScript & OOP

- **HTML5** — struttura semantica
- **CSS3** — presentazione
- **JavaScript** — comportamento, manipolazione DOM

**OOP (ES6+):** classi come "template", oggetti come istanze concrete. `constructor()` inizializza lo stato. `new` crea l'istanza. Ereditarietà tramite `extends`.

---

## Design Funzionale — "Design Before Code"

**Principio:** la progettazione decide COSA fare; il codice implementa COME farlo. La chiarezza funzionale riduce bug e refactoring più di qualsiasi framework.

### Triangolo Funzionale

Ogni funzione ha 3 elementi obbligatori:

```
Input (dati in entrata) → Processo (trasformazione) → Output (risultato)
```

Il flusso logico parte sempre dal **Bisogno utente** → definisce la **Funzione** → produce il **Risultato**.

### Workflow Design — Simboli Standard

| Simbolo | Significato |
|---------|-------------|
| **Rettangolo** | Azione da eseguire |
| **Rombo** | Decisione (Sì/No) |
| **Freccia** | Flusso / direzione |
| **Cerchio** | Inizio / Fine |

**Regola:** non iniziare mai a programmare senza aver mappato il rombo di decisione del workflow.

### 5 Stati Obbligatori di ogni Interfaccia

| Stato | Quando |
|-------|--------|
| **Idle** | In attesa di interazione |
| **Loading** | Caricamento in corso |
| **Success** | Operazione riuscita |
| **Empty** | Nessun dato da mostrare |
| **Error** | Qualcosa è andato storto |

### Abstractometer (Christoph Niemann)

L'equilibrio tra **troppo astratto** ("quadrato rosso") e **troppo realistico** ("cuore pulsante") per comunicare un'idea efficacemente.

**Shield of Technique:** la "corazza di tecnica" — metodo professionale per produrre risultati dignitosi **a comando**, indipendentemente dall'ispirazione. Non si aspetta la creatività, si usa la tecnica.

---

## Progetto Pratico: Movie Lab (TMDB API)

Costruire una mini-app che trasforma dati remoti in un'interfaccia cinematografica.

**Step-by-step:**

1. **Configurazione** — ottenere API Key da TMDB, inserirla nel codice (`TMDB_API_KEY`)
2. **Chiamata API** — `fetch()` verso endpoint (es. `/trending/movie/day`) con parametri lingua + chiave
3. **Gestione immagini** — `poster_path` è relativo (`/abc.jpg`) → concatenarlo a URL base (`https://image.tmdb.org/t/p/w500/`)
4. **Rendering** — iterare `results[]`, creare elementi HTML (`<article>`, `<img>`, `<h3>`) con `document.createElement()`
5. **Iniezione DOM** — `container.appendChild(card)`
6. **Extra** — ricerca (`/search/movie`), paginazione (parametro `page`), fallback per poster mancanti, gestione errori di rete

---

## AI — Evoluzione e Geopolitica (Contesto Culturale)

### Storia: da AlphaGo ad AGI

| Milestone | Dettaglio |
|-----------|-----------|
| **AlphaGo vs Lee Sedol** | "Momento Sputnik" dell'AI — reinforcement learning: milioni di partite contro se stesso |
| **Mossa 37** | Probabilità 1 su 10.000 per un umano → dimostra creatività artificiale non umana |
| **AlphaFold** | In un mese risolve il ripiegamento di 200 milioni di proteine — problema aperto da 50 anni → rivoluzione drug discovery |
| **DeepMind & AGI** | Obiettivo: sistema singolo capace di qualsiasi task cognitivo |

### Techno-Feudalesimo

I Big Tech operano come **entità sovranazionali** immuni alle leggi tradizionali:
- **Accordi circolari** (es. Microsoft/Nvidia) — controllo incrociato dell'infrastruttura
- **Nvidia** — monopolio quasi totale sui chip GPU necessari per AI
- **Energia** — Big Tech finanzia reattori nucleari, data center subacquei/orbitali
- **Dati** — strumento di previsione politica e sociale
- Nuovo ordine economico: chi controlla chip + energia controlla la società

---

## Progetti Pratici

- [[ITS/Alessandro D'Alessandro - Front-end/progetti/Bibliò MOC]] — Neural Reading Ecosystem

---

## Connessioni

- [[ITS MOC]]
- [[ITS/Back-end MOC]] — Server-side, API, database
- [[ITS/Automation MOC]] — Webhooks, API communication pattern
- [[Knowledge MOC]] — Context Engineering, AI frameworks
