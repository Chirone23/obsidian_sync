# Idea: agente AI per vendite "viste dall'alto"

## Il caso (venditore di piscine in Florida)
Un venditore di piscine usa un agente AI open source per trovare case senza piscina nella zona di Tampa e contattare i proprietari in modo automatico. Scrive le istruzioni, preme invio e va a dormire: l'agente lavora di notte da solo.

## Cosa fa l'agente, passo per passo
1. **Scansione satellitare** — parte dalle immagini satellitari e analizza i lotti uno per uno.
2. **Verifica urbanistica** — controlla i vincoli e decide se c'è spazio per una piscina.
3. **Rendering** — se il lotto è buono, genera un rendering del giardino visto dall'alto con la piscina già inserita.
4. **Calcolo economico** — stima il costo di costruzione e l'aumento di valore della casa.
   - Esempio: ~$48.500 di costruzione + ~$37.500 di valore aggiunto all'immobile.
5. **Ricerca proprietario** — cerca nei registri pubblici chi è il proprietario e l'agente immobiliare collegato.
6. **Cartolina fisica** — prepara un template con rendering aereo + QR code e lo spedisce via **Lob** (stampa e invio automatico). Arriva nella cassetta ~3 giorni dopo.
7. **Sito personalizzato** — crea un sito web dedicato solo a quel proprietario, raggiungibile dal QR code, con tutte le informazioni.

## Il punto chiave
Un singolo agente fa il lavoro di un team intero: analista immobiliare, grafico, copywriter, addetto spedizioni e venditore. Ogni proprietario riceve un messaggio unico, costruito sulla sua casa reale con numeri calcolati sul suo lotto. Costo: poche decine di euro di token/API.

## Generalizzazione
La stessa logica vale per qualsiasi prodotto **visibile dall'alto**:
- pannelli solari
- tetti / rifacimento coperture
- recinzioni
- giardinaggio e paesaggistica

Tutto vendibile "porta a porta" senza bussare.

## Morale
I primi a essere sostituiti dall'intelligenza artificiale sono quelli che non la usano.

---
## Nota tecnica su OpenClaw
OpenClaw **non è un modello AI**, è un **software open source**: una piattaforma/gateway per agenti AI con automazione del browser, memoria e scheduling. Gira sulla propria infrastruttura (tipicamente container Docker) e si collega a un modello esterno (Claude, OpenAI, ecc.) che fa da motore. La configurazione del comportamento dell'agente avviene tramite un file `SOUL.md`. Si integra con i canali di messaggistica già in uso (Slack, Telegram, WhatsApp, Teams).

In sintesi: OpenClaw = il software che orchestra l'agente; il modello AI = il "cervello" che gli si collega.
