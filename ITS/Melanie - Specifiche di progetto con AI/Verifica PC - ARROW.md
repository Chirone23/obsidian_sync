# Verifica PC — ARROW

**Data:** 2026-05-04  
**Ambiente:** Casa / Academy (dedotto da: sviluppo SpecterAI)

---

## Sistema Operativo

- **OS Name:** Microsoft Windows 11 Pro
- **OS Version:** 10.0.26200
- **System Type:** x64-based PC
- **Total Physical Memory:** 16 GB (~16.44 GB esatti)

---

## Processore

- **Modello:** AMD Ryzen 5 PRO 5650U with Radeon Graphics
- **Core Fisici:** 6
- **Logical Processors:** 12

---

## Python

- **Versione:** 3.12.10
- **Percorso:** C:\Users\Chirone\AppData\Local\Programs\Python\Python312\python.exe

---

## Dipendenze Progetto (SpecterAI)

| Pacchetto | Versione | Stato |
|---|---|---|
| fastapi | - | ❌ mancante |
| uvicorn | - | ❌ mancante |
| pymupdf | - | ❌ mancante |
| anthropic | - | ❌ mancante |
| jinja2 | - | ❌ mancante |
| python-multipart | - | ❌ mancante |
| python-dotenv | 1.2.2 | ✅ installato |
| pydantic | 2.12.5 | ✅ installato |

---

## Git

- **Versione:** 2.53.0.windows.2

---

## Cursor

- **Installato:** ❌ No
- **Percorso:** Non trovato in `$env:LOCALAPPDATA\Programs\cursor\Cursor.exe`

---

## Variabili d'Ambiente

- **ANTHROPIC_API_KEY:** NOT SET ⚠️

---

## Note e Azioni Consigliate

### Pacchetti Mancanti (CRITICO)

Installare immediatamente le dipendenze mancanti per SpecterAI:

```powershell
pip install fastapi uvicorn pymupdf anthropic jinja2 python-multipart
```

### API Key Mancante

La variabile d'ambiente `ANTHROPIC_API_KEY` non è configurata. Prima di eseguire SpecterAI:

1. Aggiungere la chiave API di Anthropic a `.env` o come variabile d'ambiente globale
2. Verificare con: `[System.Environment]::GetEnvironmentVariable("ANTHROPIC_API_KEY")`

### Installazione Cursor (Opzionale)

Se desiderato, scaricare Cursor da: https://www.cursor.com/

### Riepilogo Readiness

- ✅ Sistema: OK (Windows 11, hardware adeguato)
- ✅ Python 3.12: OK
- ✅ Git: OK
- ❌ Dipendenze Python: **MANCANTI** → installa
- ❌ Variabili d'ambiente: **MANCANTI** → configura
- ⚠️ Cursor: Non installato (opzionale)

**Status:** ⚠️ **NON PRONTO** — installare dipendenze e configurare API key prima di avviare SpecterAI
