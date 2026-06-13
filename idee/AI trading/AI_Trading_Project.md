# 🚀 PROGETTO: AI AUTONOMOUS TRADING SYSTEM
## Kronos + DeepSeek + Kraken (Demo/Produzione)

**Data Creazione:** Giugno 2026  
**Status:** Planning & Development  
**Autore:** AI Trading Team  

---

## 📋 INDICE RAPIDO

1. [Overview Progetto](#overview-progetto)
2. [Architettura del Sistema](#architettura-del-sistema)
3. [Componenti Principali](#componenti-principali)
4. [Setup Infrastruttura](#setup-infrastruttura)
5. [Implementazione Dettagliata](#implementazione-dettagliata)
6. [Monitoraggio & Metriche](#monitoraggio--metriche)
7. [Timeline & Budget](#timeline--budget)
8. [Risk Management](#risk-management)

---

## 🎯 OVERVIEW PROGETTO

### Che Cosa Facciamo?
Creiamo un **sistema di trading completamente autonomo** che combina:
- **Kronos**: Foundation model specializzato per previsioni di candlestick (K-line) su 45+ exchange
- **DeepSeek V3.1**: LLM generalistico che prende decisioni di trading basate sui segnali di Kronos
- **Kraken**: Piattaforma di trading (demo per testing, reale per produzione)

### Perché Funziona?
```
Kronos          = "Cosa accadrà ai prezzi?" (predizione accurata)
     ↓
DeepSeek        = "Che posizioni apriamo?" (decision making autonomo)
     ↓
Kraken API      = "Esegui ordine" (trading autonomo)
     ↓
Monitoring      = "Come stiamo facendo?" (performance tracking)
```

### KPI Principale
Generare alpha (profitti) superiori a:
- ⚠️ DeepSeek in Alpha Arena: **+4.89% finale** (18 ott–3 nov 2025, secondo posto; Qwen3 Max vinse con +22.3%)
- ✅ Modelli generali (~10-15% annuo, con alto rischio)
- ✅ S&P 500 (~10% annuo medio)

**Target REALISTICO:** Sharpe Ratio **0.8–1.2** (già rispettabile in crypto); rendimento annuo positivo dopo costi.

> ⛔ **CORREZIONE CRITICA:** Il target precedente "25-40% annuo con Sharpe > 2.0" era **non realistico e fuorviante**.  
> Sharpe > 2.0 netto è da hedge fund quant d'élite con infrastruttura istituzionale. In crypto, un backtest a Sharpe 2.0 degrada tipicamente a 1.0–1.4 in produzione reale con costi inclusi. **Un Sharpe > 2.0 in backtest è più probabile segnale di overfitting che di alpha reale.**  
> Tratta questo progetto come **laboratorio R&D**, non come macchina da soldi.

---

## 🏗️ ARCHITETTURA DEL SISTEMA

```
┌─────────────────────────────────────────────────────────────────┐
│                   TRADING AI INFRASTRUCTURE                      │
└─────────────────────────────────────────────────────────────────┘

┌──────────────────────┐
│   DATA SOURCES       │
│  • Kraken REST API   │
│  • WebSocket feeds   │
│  • Historical OHLCV  │
└──────────┬───────────┘
           │ OHLCV Data (5min, 1h, 4h)
           ▼
┌──────────────────────────────────────────────────┐
│  LAYER 1: PREDIZIONE (KRONOS)                   │
│─────────────────────────────────────────────────│
│ • Load Model: Kronos-small (24.7M params)       │
│ • Input: K-line storiche (400-500 candele)      │
│ • Processing: Tokenizzazione → Transformer      │
│ • Output: Previsioni OHLCV (prossime 2-4h)      │
│ • Latency: ~500ms per batch                     │
└──────────┬───────────────────────────────────────┘
           │ Trading Signals
           │ (prezzo atteso, volatilità, trend)
           ▼
┌──────────────────────────────────────────────────┐
│  LAYER 2: DECISIONE (DEEPSEEK)                  │
│─────────────────────────────────────────────────│
│ • Input: Segnali da Kronos + Market metrics     │
│ • Analysis: Risk assessment, position sizing     │
│ • Output: Trading decision in JSON               │
│   {                                              │
│     "action": "LONG" | "SHORT" | "HOLD",        │
│     "position_size": 0.1,  # % of wallet        │
│     "entry_price": XXX,                         │
│     "stop_loss": XXX,                           │
│     "take_profit": XXX,                         │
│     "confidence": 0.85,                         │
│     "reasoning": "brief explanation"            │
│   }                                             │
│ • Latency: ~1-2s (API call)                     │
└──────────┬───────────────────────────────────────┘
           │ Trade Orders
           ▼
┌──────────────────────────────────────────────────┐
│  LAYER 3: ESECUZIONE (KRAKEN SANDBOX)           │
│─────────────────────────────────────────────────│
│ • Endpoint: https://demo-futures.kraken.com     │
│ • Execute limit orders                          │
│ • Manage positions (stop loss, take profit)      │
│ • Real money pero NO rischio reale (demo)        │
│ • Latency: ~100-200ms                           │
└──────────┬───────────────────────────────────────┘
           │
           ▼
┌──────────────────────────────────────────────────┐
│  LAYER 4: MONITORING & FEEDBACK                 │
│─────────────────────────────────────────────────│
│ • Dashboard: Win rate, PnL, Sharpe ratio        │
│ • Logging: Tutti gli ordini e segnali           │
│ • Alerts: Errori, liquidazioni, anomalie        │
│ • Feedback: Migliora modello con dati reali      │
└──────────────────────────────────────────────────┘
```

---

## 🧠 COMPONENTI PRINCIPALI

### 1. KRONOS - Foundation Model per Previsioni

#### Specs Tecniche
```
Modello: Kronos-small
├─ Parametri: 24.7M  (⚠️ "Kronos-mini" NON esiste nel paper ufficiale arXiv 2508.02739)
├─ Tokenizer: Kronos-Tokenizer-base
├─ Context Length: 512 token (K-line)
├─ VRAM Richiesto: 12-16GB
├─ Inference Latency: 400-600ms
├─ Metriche reali (paper): IR 1.42-1.65 su A-share cinesi (CSI300/800)
│  ⚠️ Accuratezza direzionale "65-72%" NON è nel paper — proviene da blog secondari,
│     non trattarla come dato verificato.
│     Studi indipendenti mostrano che i TSFM hanno accuratezza media ~50% su dati
│     finanziari orari (= lancio di moneta), coerente con efficienza debole.
└─ Supporta: OHLCV + volume + amount

Input Format:
├─ Open (float)
├─ High (float)
├─ Low (float)
├─ Close (float)
├─ Volume (float)
└─ Amount (float, optional)

Output Format:
├─ Predicted Open
├─ Predicted High
├─ Predicted Low
├─ Predicted Close
└─ Predicted Volume
```

#### Addestramento
- **Dati:** 12+ miliardi di record K-line da 45+ exchange globali
- **Validazione ufficiale:** Azioni cinesi A-share (CSI 300/800) via Qlib, strategia long-only top-k, costo transazione 0.15%
- **Performance paper (Kronos-large):** AER ≈ 21.9%, IR 1.42 su CSI300; AER media 20.8%, IR 1.65
- **⚠️ LIMITE CRITICO:** Il paper valida Kronos su **A-share daily**, non su **crypto perpetual intraday**. Il transfer su BTC/ETH futures non è garantito e va dimostrato empiricamente nel backtest.

#### Come Usarlo
```python
from model import Kronos, KronosTokenizer, KronosPredictor

# Load
tokenizer = KronosTokenizer.from_pretrained("NeoQuasar/Kronos-Tokenizer-base")
model = Kronos.from_pretrained("NeoQuasar/Kronos-small")
predictor = KronosPredictor(model, tokenizer, max_context=512)

# Predict
predictions = predictor.predict(
    df=historical_df,           # K-line historiche
    x_timestamp=x_ts,           # Timestamp storiche
    y_timestamp=y_ts,           # Timestamp future
    pred_len=120,               # N candele da predire
    T=1.0,                      # Temperature
    top_p=0.9,                  # Nucleus sampling
    sample_count=3              # Media 3 campioni
)
```

---

### 2. DEEPSEEK V3.1 - Decision Making Engine

#### Specs Tecniche
```
Modello: DeepSeek V3.1 Chat
├─ Tipo: Large Language Model (LLM)
├─ Context Window: 128K tokens
├─ Reasoning: Chain-of-thought
├─ API: Endpoint nativo OpenAI-compatibile (base_url="https://api.deepseek.com")
├─ Latency: 1-3 secondi (cloud); "thinking" mode più lenta
├─ Pricing: ~$0.15/1M token input, ~$0.75/1M output
└─ Alpha Arena (risultati FINALI 18 ott–3 nov 2025):
   ├─ Qwen3 Max: +22.3%    ← VINCITORE
   ├─ DeepSeek V3.1: +4.89% ← secondo (unici due in profitto)
   ├─ Claude Sonnet 4.5: -30.81%
   ├─ Grok 4: -45.3%
   ├─ Gemini 2.5 Pro: -56.71%
   └─ GPT-5: -62.66%

⚠️ DEPRECAZIONE: I nomi "deepseek-chat" e "deepseek-reasoner" saranno
   deprecati il 2026/07/24. Aggiornare a "deepseek-v4-flash" prima di tale data.

⚠️ NOTA CRITICA Alpha Arena: campione = 6 modelli, ~2 settimane, asset crypto
   ad altissima volatilità. NON è prova statistica di redditività. Nof1 stessa
   ammette "limited sample sizes / lack of statistical rigor". DeepSeek nello
   snapshot intermedio usava leva 12.9x — sopravvivenza fortunata su campione
   minimo, non edge reale.

Capabilities:
├─ Analizza segnali Kronos
├─ Calcola risk/reward ratio
├─ Determina position size (⚠️ vincolato a ¼ Kelly max — vedi Risk Management)
├─ Valuta market conditions
├─ Genera segnali di trading
└─ Spiega reasoning
```

#### Prompt Engineering
```
System Prompt:
"Sei un esperto trader quantitativo con esperienza in:
- Risk management e portfolio optimization
- Market microstructure
- Behavioral finance
- Statistical arbitrage

Il tuo job: Analizzare segnali di previsione dai modelli
e decidere autonomamente se/come tradare."

Input Template:
"PREVISIONI KRONOS (prossime 2 ore):
- Prezzo atteso: $XXX
- Range: $YYY - $ZZZ
- Volatilità: X.XX%
- Trend: UP/DOWN

DATI MERCATO ATTUALE:
- Prezzo spot: $XXX
- Volume 24h: $XX.XM
- RSI: XX
- MACD: (XX, XX, XX)

WALLET STATUS:
- Balance: $XXXXX
- Open Positions: N
- Max Drawdown Allowed: Y%

DECIDI: Action (LONG/SHORT/HOLD)?
Confidence? Position size? Entry/SL/TP?"
```

#### Configurazione
```yaml
provider: "DeepSeek"  # ⚠️ NON usare Anthropic SDK come "proxy" — sono due modelli diversi!
model: "deepseek-chat"  # ⚠️ DEPRECATO il 24/07/2026 → aggiornare a "deepseek-v4-flash"
temperature: 0.7
top_p: 0.95
max_tokens: 1000
timeout: 5s
retry_attempts: 3
```

---

### 3. KRAKEN - Trading Execution

#### Account Setup
```
Tipo: Futures (perpetual contracts)
Ambiente: Sandbox/Demo per testing
URL Demo: https://demo-futures.kraken.com
Autorizzazione: API Keys (read + trade)

API Features:
├─ REST API per ordini
├─ WebSocket per real-time updates
├─ Order Types: Market, Limit, Stop-loss
├─ Supported Pairs: BTCUSD, ETHUSD, ADAUSD, etc
├─ Leverage: Up to 50x (⚠️ non usare)
└─ Rate Limits: 15 req/sec
```

#### Configurazione API
```python
from kraken.futures import Trade, User, Market

# DEMO/SANDBOX
trade_client = Trade(
    key="your-demo-key",
    secret="your-demo-secret",
    sandbox=True  # ⚠️ IMPORTANTE!
)

# PRODUCTION (dopo testing)
trade_client = Trade(
    key="your-prod-key",
    secret="your-prod-secret",
    sandbox=False
)
```

#### Order Management
```python
# Limit Order (preferred)
order = trade_client.create_order(
    symbol="BTCUSD",
    side="buy",
    orderType="limit",
    size=0.1,              # 0.1 BTC
    limitPrice=45000.50,   # Entry price
    stopPrice=44000,       # Stop loss
    triggerSignal="mark"
)

# Close Position
trade_client.close_position(
    symbol="BTCUSD",
    orderType="market"
)
```

---

## 🖥️ SETUP INFRASTRUTTURA

### OPZIONE 1: RunPod (CONSIGLIATA) - Economica & Flessibile

```
┌─────────────────────────────────────┐
│ RunPod Community Cloud              │
├─────────────────────────────────────┤
│ GPU: RTX 4090 (24GB VRAM)           │
│ CPU: 8+ cores                       │
│ RAM: 32GB                           │
│ Storage: 100GB SSD                  │
├─────────────────────────────────────┤
│ Pricing: $0.44/hr                   │
│ Calc: $0.44 × 24h × 30 = €316/mese │
│ 12h/day = €158/mese                 │
├─────────────────────────────────────┤
│ Uptime: 99% (buono per testing)     │
│ Setup: 1 minuto                     │
│ Support: Community                  │
└─────────────────────────────────────┘

URL: https://www.runpod.io/
Template: "RunPod Pytorch"
```

**PRO:**
- ✅ Molto economico
- ✅ Setup ultra rapido
- ✅ Billing per-secondo
- ✅ RTX 4090 perfetto per Kronos
- ✅ Community ecosystem

**CONTRO:**
- ⚠️ Community Cloud = variabilità
- ⚠️ Host può disconnettere
- ❌ Non ideal per 24/7 produzione

**CONSIGLIATO PER:** Testing e pre-produzione (primi 2-3 mesi)

---

### OPZIONE 2: Lambda Labs - Professionale & Stabile

```
┌─────────────────────────────────────┐
│ Lambda Cloud                        │
├─────────────────────────────────────┤
│ GPU: A100 80GB (oppure H100)        │
│ CPU: 32+ cores                      │
│ RAM: 512GB                          │
│ Storage: 1TB SSD                    │
├─────────────────────────────────────┤
│ Pricing: $1.99/hr (A100)            │
│ Calc: $1.99 × 24h × 30 = €1,431/m  │
│ Reserved 1-year: -20% sconto        │
├─────────────────────────────────────┤
│ Uptime: 99.9% (SLA)                 │
│ Setup: 5 minuti                     │
│ Support: Enterprise                 │
│ Zero egress fees (importante!)       │
└─────────────────────────────────────┘

URL: https://lambdalabs.com/
Pre-configured: Lambda Stack (PyTorch + CUDA)
```

**PRO:**
- ✅ Enterprise-grade reliability
- ✅ Excellent support
- ✅ Pre-configured tutto
- ✅ Free egress (importante!)
- ✅ Ideal per 24/7 production

**CONTRO:**
- ❌ Più caro
- ⚠️ Meno GPU types
- ❌ Overkill per testing

**CONSIGLIATO PER:** Produzione stabile 24/7 dopo 2-3 mesi

---

### OPZIONE 3: DatabaseMart - Migliore Rapporto per 24/7

```
┌─────────────────────────────────────┐
│ DatabaseMart GPU VPS                │
├─────────────────────────────────────┤
│ GPU: RTX 4090 (dedicato)            │
│ CPU: 8 cores (dedicato)             │
│ RAM: 64GB (dedicato)                │
│ Storage: 500GB SSD                  │
├─────────────────────────────────────┤
│ Pricing: €350-450/mese (fisso)      │
│ Uptime: 99.5% (dedicated)           │
│ Setup: 15 minuti                    │
│ Support: 24/7 free                  │
│ No noisy neighbors                  │
└─────────────────────────────────────┘

URL: https://www.databasemart.com/gpu-server/vps
```

**PRO:**
- ✅ Prevedibile (monthly billing)
- ✅ Dedicated resources
- ✅ Buon supporto 24/7
- ✅ Affordable per 24/7
- ✅ Upgrade facile

**CONTRO:**
- ⚠️ Impegno mensile
- ❌ Meno flessibile

**CONSIGLIATO PER:** Produzione 24/7 con budget contenuto

---

### OPZIONE 4: Vast.ai - Cheapest (rischio)

```
┌─────────────────────────────────────┐
│ Vast.ai Marketplace                 │
├─────────────────────────────────────┤
│ GPU: Varia (RTX 4090, A100, etc)    │
│ Prezzo: $0.15-0.25/hr (best prices) │
│ Calc: $0.20 × 24h × 30 = €144/mese │
├─────────────────────────────────────┤
│ Uptime: Variabile (⚠️ 60-80%)       │
│ Setup: 5-10 minuti                  │
│ Support: Limited                    │
│ Stabilità: Bassa (P2P)              │
└─────────────────────────────────────┘

URL: https://www.vast.ai/
```

**PRO:**
- ✅ Prezzi bassissimi
- ✅ Grande varietà GPU

**CONTRO:**
- ❌ Hosting non affidabile
- ❌ Interruzioni frequenti
- ❌ Non per produzione seria

**CONSIGLIATO PER:** Solo test brevi, NON per trading

---

### RACCOMANDAZIONE PER FASI

```
FASE 1: TESTING (Settimane 1-2)
├─ Provider: RunPod Community
├─ GPU: RTX 4090
├─ Ore/giorno: 6-8
├─ Costo: ~€80-100/mese
└─ Scopo: Validare Kronos + DeepSeek

FASE 2: PRE-PRODUZIONE (Settimane 3-8)
├─ Provider: RunPod Community (upgrade)
├─ GPU: RTX 4090 oppure A100
├─ Ore/giorno: 12-16
├─ Costo: ~€200-350/mese
└─ Scopo: Raccogliere dati, fine-tune

FASE 3: PRODUZIONE (Settimana 9+)
├─ Provider: DatabaseMart o Lambda
├─ GPU: RTX 4090 (DatabaseMart) o A100 (Lambda)
├─ Ore/giorno: 24
├─ Costo: €400-600/mese
└─ Scopo: Trading autonomo stabile
```

---

## 💻 IMPLEMENTAZIONE DETTAGLIATA

### STEP 1: Setup VPS (30 minuti)

#### 1.1 Crea Account RunPod
```bash
# URL: https://www.runpod.io/
# 1. Sign up con email
# 2. Aggiungi credito ($20+)
# 3. Vai a "Community Cloud" → GPU (Community)
# 4. Seleziona "RTX 4090"
# 5. Scegli template: "RunPod Pytorch"
# 6. Click "RENT POD"
# 7. Aspetta 1-2 minuti
```

#### 1.2 Accedi via SSH
```bash
# RunPod ti fornisce:
# - IP address: XXX.XXX.XXX.XXX
# - Port: XXXXX
# - Password: XXXXXXX

ssh root@XXX.XXX.XXX.XXX -p XXXXX
# Inserisci password

# Verifica GPU
nvidia-smi

# Output atteso:
# +---------+------+-------+
# | RTX 4090| 100% | 24GB  |
# +---------+------+-------+
```

---

### STEP 2: Installare Dipendenze (15 minuti)

```bash
# Update system
apt update && apt upgrade -y

# Python 3.10+
python3 --version  # Dovrebbe essere 3.10+

# Clone repo o crea directory
mkdir ~/ai-trading
cd ~/ai-trading

# Crea virtual env
python3 -m venv venv
source venv/bin/activate

# Installa requirements
pip install --upgrade pip

# Dipendenze principali
pip install torch torchvision torchaudio --index-url https://download.pytorch.org/whl/cu118
pip install transformers huggingface-hub
pip install python-kraken-sdk
pip install openai          # ✅ per DeepSeek (OpenAI-compatibile)
# pip install anthropic    # ❌ NON installare per DeepSeek — è Claude, non DeepSeek
pip install pandas numpy matplotlib
pip install pyyaml python-dotenv
```

---

### STEP 3: Configurare Kronos (20 minuti)

```bash
# Download modelli da Hugging Face
python3 << 'EOF'
from transformers import AutoTokenizer, AutoModel
from huggingface_hub import snapshot_download

# Scarica tokenizer
snapshot_download(
    repo_id="NeoQuasar/Kronos-Tokenizer-base",
    local_dir="./models/kronos-tokenizer"
)

# Scarica modello
snapshot_download(
    repo_id="NeoQuasar/Kronos-small",
    local_dir="./models/kronos-small"
)

print("✅ Modelli scaricati!")
EOF

# Verifica file
ls -lh models/
```

#### File: `kronos_predictor.py`
```python
import torch
from transformers import AutoTokenizer, AutoModel
import pandas as pd
import numpy as np

class KronosPredictor:
    def __init__(self, model_path="./models/kronos-small", 
                 tokenizer_path="./models/kronos-tokenizer"):
        self.device = "cuda" if torch.cuda.is_available() else "cpu"
        
        # Load
        self.tokenizer = AutoTokenizer.from_pretrained(tokenizer_path)
        self.model = AutoModel.from_pretrained(model_path).to(self.device)
        self.model.eval()
        
        print(f"✅ Kronos loaded on {self.device}")
        print(f"   Memory: {torch.cuda.get_device_properties(0).total_memory / 1e9:.1f} GB")
    
    def predict(self, historical_df, lookback=400, pred_len=120):
        """
        historical_df: DataFrame con colonne [open, high, low, close, volume, amount]
        lookback: numero candele storiche da usare
        pred_len: numero candele da predire
        """
        
        # Normalizza
        x_df = historical_df.iloc[-lookback:].copy()
        means = x_df.mean()
        stds = x_df.std()
        x_norm = (x_df - means) / stds
        
        # Predict (simplified)
        with torch.no_grad():
            # Process through model
            predictions = self.model(x_norm)  # Dummy
        
        # Denormalizza
        predictions = predictions * stds + means
        
        return predictions

# Usage
predictor = KronosPredictor()
```

---

### STEP 4: Configurare DeepSeek (10 minuti)

#### File: `deepseek_trader.py`
```python
# ✅ CORRETTO: usa OpenAI SDK con base_url DeepSeek nativo
# ❌ SBAGLIATO (versione precedente): usava `import anthropic` — Claude ≠ DeepSeek!
#    L'SDK Anthropic interroga Claude, non DeepSeek. Non esiste alcun "proxy".

from openai import OpenAI
import json
import re

class DeepSeekTrader:
    def __init__(self, api_key):
        # Endpoint nativo DeepSeek — OpenAI-compatibile
        self.client = OpenAI(
            api_key=api_key,
            base_url="https://api.deepseek.com"
        )
        # ⚠️ Aggiornare a "deepseek-v4-flash" dopo il 24/07/2026
        self.model = "deepseek-chat"

    def generate_signal(self, kronos_predictions, market_data, account_status):
        """
        Ricevi segnali da Kronos e genera trading decision.
        ⚠️ Output vincolato a ¼ Kelly max per position sizing sicuro.
        """

        prompt = f"""Sei un trader quant esperto. Analizza e decidi:

PREVISIONI KRONOS (prossime 2 ore):
- Prezzo atteso: ${kronos_predictions['close_mean']:.2f}
- Range: ${kronos_predictions['low_min']:.2f} - ${kronos_predictions['high_max']:.2f}
- Volatilità: {kronos_predictions['volatility']:.4f}
- Trend: {kronos_predictions['trend']}

MERCATO ATTUALE:
- Spot: ${market_data['current_price']}
- RSI: {market_data['rsi']:.1f}
- MACD Signal: {market_data['macd']}
- Volume 24h: ${market_data['volume_24h']/1e6:.1f}M

ACCOUNT:
- Balance: ${account_status['balance']}
- Max Risk: {account_status['max_risk_pct']}%
- Open Positions: {account_status['open_positions']}

VINCOLI OBBLIGATORI:
- Leva effettiva massima: 2x
- Position size massima: 10% del wallet (¼ Kelly)
- Stop loss SEMPRE obbligatorio

DECIDE in JSON:
{{
    "action": "LONG|SHORT|HOLD",
    "confidence": 0.0-1.0,
    "position_size": 0.0-0.10,
    "entry_price": FLOAT,
    "stop_loss": FLOAT,
    "take_profit": FLOAT,
    "max_holding_hours": INT,
    "reasoning": "brief"
}}
"""

        response = self.client.chat.completions.create(
            model=self.model,
            messages=[
                {"role": "system", "content": "Rispondi SOLO con JSON valido, nessun testo extra."},
                {"role": "user", "content": prompt}
            ],
            max_tokens=500,
            temperature=0.7
        )

        text = response.choices[0].message.content
        json_match = re.search(r'\{.*\}', text, re.DOTALL)

        if json_match:
            signal = json.loads(json_match.group())
            # Hard cap position size indipendentemente da cosa dice il modello
            signal['position_size'] = min(signal.get('position_size', 0.05), 0.10)
            return signal

        return {"action": "HOLD", "confidence": 0.0}

# Usage
trader = DeepSeekTrader(api_key="your-deepseek-api-key")
```

---

### STEP 5: Configurare Kraken API (15 minuti)

#### File: `kraken_executor.py`
```python
from kraken.futures import Trade, User, Market
import logging

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class KrakenDemoExecutor:
    def __init__(self, api_key, api_secret, sandbox=True):
        self.trade = Trade(
            key=api_key,
            secret=api_secret,
            sandbox=sandbox
        )
        self.user = User(
            key=api_key,
            secret=api_secret,
            sandbox=sandbox
        )
        self.market = Market(sandbox=sandbox)
        self.sandbox = sandbox
        
        env = "DEMO" if sandbox else "PRODUCTION"
        logger.info(f"✅ Kraken {env} initialized")
    
    def execute_trade(self, signal, symbol="BTCUSD"):
        """
        Esegui trade basato su signal
        """
        
        if signal['action'] == 'HOLD':
            logger.info("Signal: HOLD - nessun ordine")
            return None
        
        try:
            # Get current price
            ticker = self.market.get_ticker(symbol=symbol)
            current_price = float(ticker['result']['BTCUSD']['last'])
            
            # Create order
            order_params = {
                "symbol": symbol,
                "side": "buy" if signal['action'] == 'LONG' else "sell",
                "orderType": "limit",
                "size": signal['position_size'],
                "limitPrice": signal['entry_price'],
                "stopPrice": signal['stop_loss'],
                "triggerSignal": "mark"
            }
            
            result = self.trade.create_order(**order_params)
            logger.info(f"✅ Order created: {result}")
            
            return {
                "status": "success",
                "order_id": result.get('orderId'),
                "signal": signal
            }
        
        except Exception as e:
            logger.error(f"❌ Error: {e}")
            return {"status": "error", "error": str(e)}
    
    def get_balance(self):
        return self.user.get_account_balance()
    
    def get_positions(self):
        return self.trade.get_open_positions()

# Usage
executor = KrakenDemoExecutor(
    api_key="your-demo-key",
    api_secret="your-demo-secret",
    sandbox=True  # IMPORTANTE!
)
```

---

### STEP 6: Orchestratore Principale (30 minuti)

#### File: `main_orchestrator.py`
```python
import asyncio
import pandas as pd
import json
from datetime import datetime
import logging

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('trading.log'),
        logging.StreamHandler()
    ]
)
logger = logging.getLogger(__name__)

from kronos_predictor import KronosPredictor
from deepseek_trader import DeepSeekTrader
from kraken_executor import KrakenDemoExecutor

class TradingOrchestrator:
    def __init__(self, kronos, deepseek, kraken):
        self.kronos = kronos
        self.deepseek = deepseek
        self.kraken = kraken
        
        self.trade_log = []
        self.metrics = {
            "total_trades": 0,
            "winning_trades": 0,
            "losing_trades": 0,
            "total_pnl": 0.0,
            "max_pnl": 0.0,
            "max_loss": 0.0
        }
    
    async def trading_loop(self, symbols=['BTCUSD'], interval_minutes=5, 
                          run_for_hours=None):
        """
        Main trading loop
        """
        logger.info(f"🚀 Trading loop started at {datetime.now()}")
        logger.info(f"   Symbols: {symbols}")
        logger.info(f"   Interval: {interval_minutes} min")
        logger.info(f"   Environment: {'DEMO' if self.kraken.sandbox else 'PRODUCTION'}")
        
        start_time = datetime.now()
        
        try:
            while True:
                # Check timeout
                if run_for_hours:
                    elapsed = (datetime.now() - start_time).total_seconds() / 3600
                    if elapsed > run_for_hours:
                        logger.info(f"⏰ Reached {run_for_hours} hours limit")
                        break
                
                for symbol in symbols:
                    logger.info(f"\n{'='*60}")
                    logger.info(f"CYCLE: {datetime.now()} | {symbol}")
                    logger.info(f"{'='*60}")
                    
                    try:
                        # STEP 1: Get data
                        logger.info("[1/4] Fetching historical data...")
                        hist_data = self._fetch_kraken_data(symbol)
                        
                        # STEP 2: Kronos predicts
                        logger.info("[2/4] Kronos analyzing...")
                        predictions = self.kronos.predict(hist_data)
                        logger.info(f"   Predicted Close: ${predictions['close_mean']:.2f}")
                        
                        # STEP 3: DeepSeek decides
                        logger.info("[3/4] DeepSeek deciding...")
                        market_data = self._get_market_metrics(hist_data)
                        account = self.kraken.get_balance()
                        
                        signal = self.deepseek.generate_signal(
                            predictions, 
                            market_data,
                            account
                        )
                        logger.info(f"   Signal: {signal['action']} (conf: {signal['confidence']})")
                        
                        # STEP 4: Execute
                        logger.info("[4/4] Executing on Kraken DEMO...")
                        result = self.kraken.execute_trade(signal, symbol)
                        
                        if result and result['status'] == 'success':
                            self.metrics['total_trades'] += 1
                            self.trade_log.append({
                                'timestamp': datetime.now(),
                                'symbol': symbol,
                                'signal': signal,
                                'result': result
                            })
                            logger.info(f"✅ Trade #{self.metrics['total_trades']} executed")
                    
                    except Exception as e:
                        logger.error(f"❌ Error in cycle: {e}", exc_info=True)
                
                # Print performance
                self._print_performance()
                
                # Wait
                logger.info(f"⏳ Waiting {interval_minutes} minutes...")
                await asyncio.sleep(interval_minutes * 60)
        
        except KeyboardInterrupt:
            logger.info("\n⛔ Trading loop stopped by user")
            self._save_results()
    
    def _fetch_kraken_data(self, symbol, lookback_hours=24):
        """Fetch OHLCV from Kraken"""
        # Implementazione semplificata
        import numpy as np
        
        # Dummy data (in produzione: API call)
        n_candles = int(lookback_hours * 60 / 5)
        data = {
            'open': np.random.normal(45000, 100, n_candles),
            'high': np.random.normal(45200, 100, n_candles),
            'low': np.random.normal(44800, 100, n_candles),
            'close': np.random.normal(45000, 100, n_candles),
            'volume': np.random.normal(100, 20, n_candles),
            'amount': np.random.normal(1000, 200, n_candles)
        }
        return pd.DataFrame(data)
    
    def _get_market_metrics(self, df):
        """Calculate RSI, MACD, etc"""
        return {
            'current_price': df['close'].iloc[-1],
            'rsi': 50.0,  # Dummy
            'macd': 0.0,
            'volume_24h': df['volume'].sum() * 45000
        }
    
    def _print_performance(self):
        """Print dashboard"""
        logger.info(f"\n📊 PERFORMANCE:")
        logger.info(f"   Total Trades: {self.metrics['total_trades']}")
        logger.info(f"   Win Rate: {(self.metrics['winning_trades'] / max(1, self.metrics['total_trades']) * 100):.1f}%")
        logger.info(f"   Total PnL: ${self.metrics['total_pnl']:+.2f}")
    
    def _save_results(self):
        """Save log"""
        df = pd.DataFrame(self.trade_log)
        filename = f"trading_log_{datetime.now().strftime('%Y%m%d_%H%M%S')}.csv"
        df.to_csv(filename, index=False)
        logger.info(f"✅ Results saved to {filename}")

# MAIN
if __name__ == "__main__":
    # Initialize
    kronos = KronosPredictor()
    deepseek = DeepSeekTrader(api_key="your-api-key")
    kraken = KrakenDemoExecutor(
        api_key="demo-key",
        api_secret="demo-secret",
        sandbox=True
    )
    
    # Create orchestrator
    orchestrator = TradingOrchestrator(kronos, deepseek, kraken)
    
    # Run (test per 4 ore)
    asyncio.run(orchestrator.trading_loop(
        symbols=['BTCUSD', 'ETHUSD'],
        interval_minutes=5,
        run_for_hours=4
    ))
```

---

### STEP 7: Configuration File

#### File: `config.yaml`
```yaml
# KRONOS CONFIG
kronos:
  model_path: "./models/kronos-small"
  tokenizer_path: "./models/kronos-tokenizer"
  device: "cuda"
  lookback_candles: 400
  pred_len: 120
  temperature: 1.0
  top_p: 0.9
  sample_count: 3

# DEEPSEEK CONFIG
deepseek:
  api_key: "${DEEPSEEK_API_KEY}"  # Load from env var
  model: "claude-3-5-sonnet-20241022"
  temperature: 0.7
  top_p: 0.95
  max_tokens: 1000
  timeout_seconds: 5

# KRAKEN CONFIG
kraken:
  demo:
    api_key: "${KRAKEN_DEMO_KEY}"
    api_secret: "${KRAKEN_DEMO_SECRET}"
    sandbox: true
  production:
    api_key: "${KRAKEN_PROD_KEY}"
    api_secret: "${KRAKEN_PROD_SECRET}"
    sandbox: false

# TRADING CONFIG
trading:
  symbols: ["BTCUSD", "ETHUSD"]
  interval_minutes: 5
  max_position_size: 0.1  # 10% of wallet
  max_drawdown_pct: 10    # Stop if loses 10%
  max_leverage: 2.0       # NO leverage, stay conservative
  stop_loss_offset_pct: 2
  take_profit_offset_pct: 5

# LOGGING
logging:
  level: "INFO"
  file: "trading.log"
  console: true
```

---

## 📊 MONITORAGGIO & METRICHE

### Dashboard Principale

```
┌─────────────────────────────────────────────────┐
│           TRADING PERFORMANCE DASHBOARD          │
├─────────────────────────────────────────────────┤
│                                                 │
│ 💰 ACCOUNT                                      │
│    Balance: $10,500.00 (+5.0%)                 │
│    Open Positions: 2                           │
│    Used Margin: 20%                            │
│                                                 │
│ 📈 TRADES                                       │
│    Total: 45                                    │
│    Win Rate: 62%                               │
│    Avg Win: +$150                              │
│    Avg Loss: -$95                              │
│    Profit Factor: 2.1                          │
│                                                 │
│ 🎯 RISK METRICS                                │
│    Sharpe Ratio: 2.3                           │
│    Max Drawdown: -8.5%                         │
│    Recovery Factor: 3.2                        │
│                                                 │
│ ⚙️ SYSTEM                                       │
│    Uptime: 99.7%                               │
│    Kronos Latency: 512ms avg                   │
│    DeepSeek Latency: 1.2s avg                  │
│    Order Fill Rate: 98.5%                      │
│                                                 │
└─────────────────────────────────────────────────┘
```

### Metriche Chiave

```python
metrics = {
    # Performance
    "total_return": (end_capital - start_capital) / start_capital,
    "annual_return": total_return ** (252 / num_trading_days) - 1,
    "monthly_return": monthly_pnl / start_capital,
    
    # Risk
    "sharpe_ratio": (mean_daily_return / std_daily_return) * sqrt(252),
    "sortino_ratio": (mean_daily_return / std_negative_returns) * sqrt(252),
    "max_drawdown": (peak - trough) / peak,
    "calmar_ratio": annual_return / abs(max_drawdown),
    
    # Trading Activity
    "total_trades": len(trades),
    "win_rate": winning_trades / total_trades,
    "profit_factor": gross_profit / abs(gross_loss),
    "expectancy": (win_rate * avg_win) - (loss_rate * avg_loss),
    
    # Execution Quality
    "avg_slippage": avg_execution_price - expected_price,
    "order_fill_rate": filled_orders / submitted_orders,
    "avg_latency_ms": mean([kronos_latency + deepseek_latency + kraken_latency])
}
```

### Logging Strategy

```python
# File: logger_setup.py

import logging
from logging.handlers import RotatingFileHandler

def setup_logger(name, log_file, level=logging.INFO):
    # File handler (rotating)
    file_handler = RotatingFileHandler(
        log_file, 
        maxBytes=10*1024*1024,  # 10MB
        backupCount=5
    )
    file_handler.setFormatter(logging.Formatter(
        '%(asctime)s - %(name)s - %(levelname)s - %(message)s'
    ))
    
    # Console handler
    console_handler = logging.StreamHandler()
    console_handler.setFormatter(logging.Formatter(
        '%(asctime)s - %(levelname)s - %(message)s'
    ))
    
    # Logger
    logger = logging.getLogger(name)
    logger.setLevel(level)
    logger.addHandler(file_handler)
    logger.addHandler(console_handler)
    
    return logger

# Usage
logger_trading = setup_logger('trading', 'logs/trading.log')
logger_signals = setup_logger('signals', 'logs/signals.log')
logger_errors = setup_logger('errors', 'logs/errors.log')
```

---

## ⏱️ TIMELINE & BUDGET

### Timeline di Implementazione

```
FASE 0: PRE-BACKTEST (Settimane 1-2) ← NUOVA, obbligatoria
├─ Giorno 1-3:  Setup RunPod, install Kronos + openai SDK
├─ Giorno 4-7:  Scarica almeno 2 anni dati storici BTC/ETH (5min/1h)
├─ Giorno 8-14: Costruisci pipeline walk-forward con purged CV
└─ DELIVERABLE: Backtest engine pronto, dati storici caricati

FASE 1: VALIDAZIONE STATISTICA (Settimane 3-10) ← IL CANCELLO DECISIVO
├─ Settimana 1-2: Kronos predictions su dati crypto storici (out-of-sample)
├─ Settimana 3-4: Walk-forward su 2 anni BTC/ETH, costi reali inclusi
├─ Settimana 5-6: Accumula 200+ trade simulati, calcola Deflated Sharpe
├─ Settimana 7-8: Confronto vs naive forecast — se Kronos non batte, STOP
├─ Settimana 9-10: Robustness check (Monte Carlo, parameter sensitivity)
└─ DELIVERABLE: Sharpe 0.8+ out-of-sample confermato, oppure progetto sospeso

FASE 2: DEMO INTEGRATO (Settimane 11-14)
├─ Settimana 1-2: Deploy sistema su Kraken demo, test ordini reali
├─ Settimana 3-4: Run integrato per 4 settimane (Kronos→DeepSeek→Kraken)
└─ DELIVERABLE: Performance live demo allineata al backtest (±20%)

FASE 3: PRODUZIONE (Settimana 15+) — SOLO se Fasi 0-2 superano i test
├─ Giorno 1:    Upgrade infrastruttura (DatabaseMart o Lambda)
├─ Giorno 2-7:  Deploy con capitale minimo (es. 5-10% del totale)
├─ Mese 1-3:    Monitoraggio intensivo, confronto live vs backtest
└─ DELIVERABLE: Sistema stabile, scalabile gradualmente
```

### Budget Dettagliato

```
FASE 1: TESTING (2 settimane)
├─ RunPod GPU (RTX 4090): €100
├─ API Keys (DeepSeek): €20 (pricing ~$0.15/1M token input)
├─ Data subscription (optional): €0
└─ TOTAL: ~€120

FASE 2: PRE-PRODUZIONE (6 settimane) — include validazione statistica
├─ RunPod GPU (upgrade): €400 (50-60h/week)
├─ API Keys DeepSeek: €100
└─ TOTAL: ~€500

FASE 3: PRODUZIONE (ongoing) — SOLO dopo walk-forward validato
├─ Option A (DatabaseMart, 24/7): €400-500/mese
├─ Option B (Lambda Labs, 24/7): €1,200+/mese
├─ API Keys: €200/mese
├─ Data feeds: €50-100/mese (optional)
└─ TOTAL: €650-1,500/mese

COSTO TOTALE PRIMI 2 MESI: ~€1,200-1,500
COSTO MENSILE PRODUZIONE: ~€650-1,500
```

---

## ⚠️ RISK MANAGEMENT

### Rischi Identificati

```
RISCHIO: Kronos su dominio sbagliato
├─ Probabilità: ALTA (validato su A-share, usato su crypto)
├─ Impatto: ALTO (alpha nullo o negativo)
└─ Mitigazione:
    ✓ Backtest esplicito su dati crypto (2+ anni BTC/ETH)
    ✓ Walk-forward out-of-sample obbligatorio
    ✓ Non assumere che IR 1.42 su CSI300 si trasferisca
    ✓ Confronto vs naive forecast (se Kronos non batte il naive, fermati)

RISCHIO: Overfitting / backtest ottimistico
├─ Probabilità: ALTA (comune in ML trading)
├─ Impatto: CRITICO (performance live << backtest)
└─ Mitigazione:
    ✓ Purged k-fold CV con embargo (Lopez de Prado)
    ✓ Walk-forward analysis su almeno 2 anni
    ✓ Deflated Sharpe Ratio (corregge per multiple testing)
    ✓ Min 200+ trade per significatività statistica
    ✓ Se Sharpe backtest > 2.0: segnale di overfitting, NON di alpha

RISCHIO: Leverage e liquidazione
├─ Probabilità: ALTA se non vincolata
├─ Impatto: CATASTROFICO (account blown)
└─ Mitigazione:
    ✓ Leva effettiva massima 2-3x (mai 50x Kraken)
    ✓ Position sizing: ¼ Kelly frazionario
    ✓ Volatility targeting (riduce size in alta volatilità)
    ✓ Monitor funding rate perpetual (erode P&L continuamente)

RISCHIO: Costi reali azzerano alpha
├─ Probabilità: ALTA se non modellati nel backtest
├─ Impatto: ALTO
└─ Mitigazione:
    ✓ Commissioni Kraken: ~0.05% taker per side (usa taker conservativo)
    ✓ Funding rate perpetual: pagato ogni ora, cap ±0.25%/ora
    ✓ Slippage: modellare esplicitamente nell'order book
    ✓ Edge per trade DEVE essere > costi per trade

RISCHIO: DeepSeek fa decisioni crazy (over-leverage)
├─ Probabilità: MEDIA (leva 12.9x nello snapshot Alpha Arena)
├─ Impatto: CATASTROFICO
└─ Mitigazione:
    ✓ Hard cap position_size nel codice (override risposta LLM)
    ✓ Max leva 2x hardcoded nel executor
    ✓ Kill switch su perdita giornaliera >5%

RISCHIO: Kraken API down / errori tecnici
├─ Probabilità: BASSA (99.9% uptime)
├─ Impatto: MEDIA (missed trades, ordini duplicati)
└─ Mitigazione:
    ✓ Retry con backoff esponenziale
    ✓ API key dedicata per ogni strategia (no nonce conflict)
    ✓ Riconciliazione periodica posizioni aperte
    ✓ Monitor status.kraken.com

RISCHIO: Tassazione italiana non considerata
├─ Probabilità: CERTA
├─ Impatto: ALTO (erode rendimenti netti)
└─ Mitigazione:
    ✓ Plusvalenze crypto: aliquota 33% dal 1/1/2026 (L. 207/2024)
    ✓ Quadro RW/W obbligatorio sempre (anche senza vendita)
    ✓ DAC8: exchange UE comunicano dati all'Agenzia Entrate dal 2026
    ✓ Derivati/perpetual: consulta commercialista esperto crypto
    ✓ Calcola rendimento NETTO includendo tasse prima di valutare profittabilità
```

### Safety Mechanisms

```python
# File: safety_monitor.py

class SafetyMonitor:
    def __init__(self, max_loss_pct=10, max_position_size=0.1):
        self.max_loss_pct = max_loss_pct
        self.max_position_size = max_position_size
        self.circuit_breaker = False
    
    def check_account_health(self, account_balance, start_balance):
        """Stop if losses exceed threshold"""
        loss_pct = (start_balance - account_balance) / start_balance * 100
        
        if loss_pct > self.max_loss_pct:
            logger.critical(f"⛔ MAX LOSS EXCEEDED: {loss_pct:.1f}%")
            self.circuit_breaker = True
            return False
        
        return True
    
    def check_signal(self, signal):
        """Validate signal before execution"""
        
        # Confidence too low
        if signal['confidence'] < 0.5:
            logger.warning(f"Signal confidence too low: {signal['confidence']}")
            return False
        
        # Position size too large
        if signal['position_size'] > self.max_position_size:
            logger.warning(f"Position size too large: {signal['position_size']}")
            signal['position_size'] = self.max_position_size
        
        # SL/TP not set
        if not signal.get('stop_loss') or not signal.get('take_profit'):
            logger.error("SL/TP not set - REJECT")
            return False
        
        return True
    
    def emergency_stop(self):
        """Close all positions immediately"""
        logger.critical("🚨 EMERGENCY STOP ACTIVATED")
        # Close all open positions
        # Disable trading
        self.circuit_breaker = True
```

---

## 🔬 VALIDAZIONE STATISTICA (OBBLIGATORIA PRIMA DEL GO-LIVE)

> Questa sezione non esisteva nella v1.0. È la **più importante dell'intero documento**: senza di essa qualsiasi risultato positivo è rumore statistico, non alpha.

### Il problema centrale
Un sistema ML/LLM può sembrare profittevole per puro caso o overfitting. I rischi sono massimi perché: (a) si testano molte configurazioni, (b) i mercati crypto sono non-stazionari, (c) le label sono forward-looking.

### Metodologia corretta

```
STEP 1: Walk-Forward Analysis
├─ Ogni decisione si basa SOLO su dati precedenti
├─ Gold standard per validazione out-of-sample
├─ Finestre rolling: train 12 mesi → test 3 mesi → avanza
└─ Ripeti su almeno 2 anni di dati BTC/ETH

STEP 2: Purged K-Fold CV con Embargo (Lopez de Prado)
├─ Elimina campioni training che si sovrappongono al test (purge)
├─ Aggiungi embargo dopo ogni fold (es. 5% delle barre)
├─ Previene data leakage da autocorrelazione
└─ Senza questo, Sharpe > 2.0 crollano a 1.0 una volta imposto

STEP 3: Quanti trade servono?
├─ MINIMO: 30 trade (base statistica)
├─ CONSIGLIATO: 200+ trade su più condizioni di mercato
├─ 13 vittorie su 20 (65%) → p-value > 0.2 (rumore!)
├─ 130 vittorie su 200 (65%) → p-value < 0.01 (edge reale)
└─ Formula MinTRL (Lopez de Prado) per track record minimo

STEP 4: Costi da includere SEMPRE
├─ Commissioni taker Kraken: ~0.05% per side
├─ Funding rate perpetual: ogni ora, cap ±0.25%/ora
├─ Slippage: modellare dall'order book
└─ (In Italia) Tasse: 33% sulle plusvalenze
```

### Metriche corrette da calcolare

```python
# Aggiungi al dashboard — queste mancavano nella v1.0

advanced_metrics = {
    # Standard
    "sharpe_ratio":   mean_daily_return / std_daily_return * sqrt(252),
    "sortino_ratio":  mean_daily_return / std_negative_returns * sqrt(252),
    "max_drawdown":   (peak - trough) / peak,
    "calmar_ratio":   annual_return / abs(max_drawdown),

    # Avanzate (obbligatorie)
    "deflated_sharpe": DSR(SR, T, skew, kurtosis, n_trials),
    # Corregge per selection bias, multiple testing e non-normalità
    # Ref: Bailey & Lopez de Prado 2014
    # https://www.davidhbailey.com/dhbpapers/deflated-sharpe.pdf

    "probabilistic_sharpe": PSR(SR_obs, SR_benchmark, T, skew, kurtosis),
    # Probabilità che lo Sharpe vero superi la soglia benchmark

    "profit_factor":  gross_profit / abs(gross_loss),
    # > 1.5 = buono; < 1.2 = marginale; < 1.0 = perdita

    "expectancy":     (win_rate * avg_win) - (loss_rate * avg_loss),
    # Deve essere > costo medio per trade
}
```

### Soglie realistiche per crypto

```
Sharpe Ratio netto (dopo costi):
├─ < 0.5  → Non procedere
├─ 0.5-0.8 → Marginale, dipende dalla robustezza
├─ 0.8-1.2 → Buono per crypto (già sopra media)
├─ 1.2-2.0 → Ottimo — verifica molto bene per overfitting
└─ > 2.0   → ⛔ Segnale di overfitting salvo prova contraria

⚠️ Un backtest Sharpe 2.0 degrada tipicamente a 1.0-1.4 in produzione
   una volta inclusi costi reali e mercato live.
```

---

## 🔑 KEY TAKEAWAYS

```
✅ SUCCESSO DIPENDE DA:

1. VALIDAZIONE STATISTICA RIGOROSA (priorità assoluta)
   └─ Walk-forward analysis + purged k-fold CV con embargo
   └─ Min 200+ trade out-of-sample prima di andare live
   └─ Deflated Sharpe Ratio (non il Sharpe grezzo)
   └─ Test su almeno 2 anni di dati crypto (2+ cicli di mercato)

2. KRONOS COME SEGNALE, NON ORACOLO
   └─ Edge statistico reale ma modesto (IR 1.42-1.65 su A-share)
   └─ Validare esplicitamente su crypto prima di usarlo
   └─ Se non batte il naive forecast su BTC/ETH: fermati
   └─ Accuratezza ≠ profittabilità (commissioni + funding possono azzerarlo)

3. DEEPSEEK: DECISION MAKER CON VINCOLI HARD
   └─ Leva effettiva massima 2-3x (hardcoded nel executor)
   └─ Position sizing: ¼ Kelly frazionario
   └─ Volatility targeting: riduce size in alta volatilità
   └─ Hard cap nel codice: override risposta LLM se supera limiti

4. COSTI REALISTICI NEL BACKTEST
   └─ Commissioni taker ~0.05% per side
   └─ Funding rate perpetual (ogni ora, può essere significativo)
   └─ Slippage modellato esplicitamente
   └─ Tasse: 33% sulle plusvalenze (Italia, dal 1/1/2026)

5. MONITORAGGIO & KILL SWITCH
   └─ Kill switch su perdita giornaliera >5%
   └─ Monitor funding e prezzo di liquidazione sempre
   └─ Riconciliazione posizioni ogni ciclo

❌ COSA EVITARE:

1. ❌ Target Sharpe > 2.0 in backtest
   └─ È segnale di overfitting, non di alpha
   └─ Soglia realistica: 0.8-1.2 in crypto

2. ❌ Assumere che Kronos funzioni su crypto senza test
   └─ Validato su A-share cinesi — mercato diverso
   └─ Dimostrarlo empiricamente prima di investire

3. ❌ Usare Anthropic SDK per chiamare DeepSeek
   └─ Sono due modelli diversi di aziende diverse
   └─ Usa OpenAI SDK con base_url="https://api.deepseek.com"

4. ❌ Leva > 2-3x
   └─ GPT-5 e Gemini in Alpha Arena: -62% e -56% per over-leverage
   └─ La matematica del recupero è impietosa: -50% richiede +100%

5. ❌ Backtest di pochi giorni come prova di profittabilità
   └─ Alpha Arena = 2 settimane su 6 modelli: è variance, non skill
   └─ Servono mesi di dati out-of-sample su più regimi di mercato

6. ❌ Ignorare la tassazione
   └─ 33% di plusvalenze azzera uno Sharpe mediocre
   └─ Quadro RW/W obbligatorio ogni anno
```

---

## 📞 CONTACTS & RESOURCES

```
MODELLI:
├─ Kronos repo: https://github.com/shiyu-coder/Kronos
├─ Kronos HuggingFace: https://huggingface.co/NeoQuasar
├─ Kronos paper (arXiv): https://arxiv.org/abs/2508.02739
├─ DeepSeek API: https://platform.deepseek.com
└─ DeepSeek docs: https://api-docs.deepseek.com

PIATTAFORME:
├─ Kraken Futures Demo: https://demo-futures.kraken.com
├─ Kraken API Docs: https://docs.kraken.com
├─ RunPod: https://www.runpod.io/
├─ Lambda Labs: https://lambdalabs.com/
└─ DatabaseMart: https://www.databasemart.com/

BENCHMARK & RICERCA:
├─ Alpha Arena (Nof1): https://nof1.ai/
├─ Lopez de Prado (MLFE): "Advances in Financial Machine Learning"
├─ Deflated Sharpe Ratio: https://www.davidhbailey.com/dhbpapers/deflated-sharpe.pdf
└─ CPCV / Walk-forward: https://arxiv.org/pdf/1910.05555

FISCALITÀ ITALIANA:
├─ Legge di Bilancio 2025 (L. 207/2024) — aliquota 33% crypto dal 2026
├─ Agenzia delle Entrate — Quadro RW/W
├─ DAC8 — scambio dati exchange UE dal 2027
└─ ⚠️ Consulta un commercialista esperto crypto per i derivati perpetual

COMUNITÀ:
├─ Reddit: r/algotrading, r/MachineLearning
├─ QuantLib / QuantConnect community
└─ Twitter/X: ricerca quant traders
```

---

## ✍️ NOTE IMPORTANTI

```
⚠️ DISCLAIMER:
- Questo è uno STUDIO/IDEA R&D, non financial advice
- Crypto trading è HIGH RISK — solo capitale che puoi perdere interamente
- Tratta il progetto come laboratorio, non come fonte di reddito
- NON andare live senza almeno 3 mesi di validazione statistica out-of-sample
- Un backtest profittevole di pochi giorni è rumore, non alpha

💸 FISCALITÀ ITALIANA (obbligatoria):
- Plusvalenze crypto: aliquota 33% dal 1/1/2026 (L. n. 207/2024)
- Franchigia 2.000€ abolita dal 1/1/2025 — ogni plusvalenza è tassata
- Quadro RW/W: obbligo di monitoraggio sempre, anche senza vendita
- DAC8: gli exchange UE comunicheranno i dati all'Agenzia delle Entrate
  (raccolta dal 2026, scambi cross-border dal 2027) — NON dichiarare è rischioso
- Sanzioni omessa RW: 3-15% del valore; infedele dichiarazione: fino al 240%
- I derivati/perpetual potrebbero avere trattamento specifico: consulta
  un commercialista esperto crypto prima di operare con soldi reali

📊 VALIDAZIONE STATISTICA (non saltare):
- Min 200+ trade out-of-sample per significatività
- Walk-forward analysis obbligatoria (non solo backtest singolo)
- Purged k-fold CV con embargo (Lopez de Prado) per evitare data leakage
- Calcola Deflated Sharpe Ratio, non solo Sharpe grezzo
- Testa su almeno 2 anni di dati crypto (minimo 2 cicli di mercato)
- Se Kronos non batte il naive forecast su BTC/ETH: non procedere

🎯 POSITION SIZING CORRETTO:
- Non usare Kelly pieno: sovrastima l'edge → drawdown 50% con prob. 50%
- ¼ Kelly frazionario come massimo
- Volatility targeting: riduci size quando vol aumenta
- Ricalcola la frazione ogni 20-50 trade

🔐 SICUREZZA:
- Mai condividere API keys
- Usa environment variables (.env file), mai hardcode
- API key dedicata per strategia (evita nonce conflicts)
- Rotate keys periodicamente
- Backup strategy & model weights

📚 LETTURA CONSIGLIATA:
- "Advances in Financial Machine Learning" — Marcos Lopez de Prado
- Paper Kronos: https://arxiv.org/abs/2508.02739
- Deflated Sharpe Ratio: Bailey & Lopez de Prado (2014)
- Alpha Arena blog post critico: https://nof1.ai/blog/TechPost1
```

---

**VERSIONE:** 2.0 (aggiornata con report tecnico-operativo)
**ULTIMO UPDATE:** Giugno 2026
**STATUS:** R&D — validazione statistica richiesta prima del go-live

> ⚠️ **NEXT STEP CORRETTO:** Prima di scrivere altro codice — costruisci il backtest walk-forward su dati storici BTC/ETH con costi reali inclusi. Solo se l'edge sopravvive a quella analisi, procedi.
