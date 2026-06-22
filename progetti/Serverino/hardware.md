
# Serverino — Hardware Specs

**Status:** ✅ Confermato  
**Data:** 2026-06-15  
**Scopo:** Portatile dev ultralight + server edge  

---

## CPU
- **Processore:** AMD A9-9420e (Carrizo, 7ª gen)
- **Caratteristiche:** x86-64, 2 core / 2 thread, TDP ~15W integrata
- **Implicazione:** Compilazione lenta, no HPC; ok per runtime Python/Node.js

## RAM
- **Memoria:** DDR4 4 GB
- **Vincolo critico:** Single-threaded workload, no Docker multi-container
- **Estrategie:**
  - Swap su SSD (essenziale)
  - Node.js/Python con `--max-old-space-size=2048`
  - SQLite over PostgreSQL (unless single-user)

## Storage ✅
- **SSD M.2 PCIe:** 128 GB (confermato)
- **Layout suggerito:**
  - OS (Windows/Linux): 30 GB
  - Tools dev (git, node, python): 15 GB
  - Progetti attivi: 50 GB
  - Buffer/temp: 33 GB
- **⚠️ Tight:** No bulk datasets; externe drive per archivi

## GPU
- **AMD Radeon integrata** (Carrizo graphics)
- **Use case:** Headless server, no GPU-accel ML

## Connettività
- **WiFi:** 802.11 AC 1x1 (single-stream ~65 Mbps max) — sufficiente per dev sync git
- **Ethernet:** Nessuno — **TODO:** valutare USB-to-Ethernet dongle per stabilità rete
- **USB 3.0:** 2x (mouse/keyboard/storage)
- **Bluetooth:** 4.2 (ok)

## Alimentazione
- **Adattatore:** 45 W
- **Batteria:** ~8h (mobile work)
- **Per server 24/7:** AC power required, thermal management

## Thermal
- **Dimensioni:** 327 × 235 × 19 mm (ultraleggero)
- **Ventilazione:** Limitata → throttling probabile su carico sostenuto
- **Mitigazione:** Fan riser, ventilazione intake

---

## Profilo di Utilizzo — Green/Red

| Carico | Fattibilità | Note |
|--------|-------------|------|
| **Node.js + SQLite server** | ✅ Sì | Express 4-6 concurrent ok |
| **Python Flask + spaCy** | ✅ Sì | NLP-lite, non lg models (→ timeout) |
| **PostgreSQL single-user** | ⚠️ Forse | RAM marginal, ok for dev, no prod |
| **Docker + 3+ containers** | ❌ No | 4GB RAM esaurita in minuti |
| **k8s / Swarm** | ❌ No | Overkill per HW, overhead non giustificato |
| **Rust compilation** | ⏳ Lento | 30-60 min per progetto medio, ok se paziente |

---

## Vincoli Operativi
1. **RAM is the bottleneck** — monitora con `top` / Task Manager
2. **SSD speed is critical** — fast build/test cycles
3. **Thermal throttling** — aspettarsi cali di performance sotto load sostenuto
4. **Single WiFi stream** — download lenti, usa Ethernet se possibile
5. **Power delivery** — 45W PSU ok per idle, hot CPU drains quick

---

## Prossimi step
- [ ] Installare SSD M.2 e verificare BIOS detect
- [ ] Scegliere OS (Windows 10 existing, o upgrade?)
- [ ] Setup swap partition (SSD-based)
- [ ] Valutare USB-Ethernet adapter per dev stabile
- [ ] Baseline thermal test (CPU-Z, stress test)

---

[[moc/Index MOC]] • [[skill/Serverino Setup Playbook]]
