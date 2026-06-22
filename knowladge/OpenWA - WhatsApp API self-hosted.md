# OpenWA — WhatsApp API self-hosted

**Fonte:** https://www.open-wa.org/
**Tipo:** Tool open source (MIT) — gateway HTTP WhatsApp self-hosted.

## Cos'è

API HTTP REST per WhatsApp, self-hosted: integri WhatsApp nelle tue app mantenendo controllo totale su infrastruttura e dati. *"Own your stack"* — niente fee di licenza, niente feature lock.

## Caratteristiche

- **REST API** multi-sessione + **webhook** real-time
- Dashboard web (9 lingue)
- Messaggi: testo, immagini, video, documenti, audio, reazioni
- Gestione gruppi e canali
- Rate limiting, audit log, IP whitelisting
- Architettura pluggable (DB, storage, cache intercambiabili)

## Stack

Node.js 22 · NestJS 11 · TypeScript 5 · React 19 · PostgreSQL · Redis · Docker.
Due engine WhatsApp: `whatsapp-web.js` (Puppeteer, default) o `baileys` (WebSocket).
SDK in Python e JS/TS.

## Perché interessa

Alternativa **self-hosted e gratuita** alla WhatsApp Business API ufficiale di Meta (vedi [[Automation MOC]] §3 — la Bus. API richiede Template pre-approvati e ha vincoli sui messaggi proattivi oltre 24h). Utile come trigger/canale per workflow di automazione e agenti.

> ⚠️ Nota: usa engine non ufficiali (browser/WebSocket) → non è la API ufficiale Meta, possibile rischio di ban account WhatsApp se usato in modo aggressivo. Per progetti production valutare i ToS.

## Connessioni

- [[Automation MOC]] — canale WhatsApp per automazioni e trigger
