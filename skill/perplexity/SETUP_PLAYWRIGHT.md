# Perplexity + Playwright MCP Setup

## Cosa è

Uno script Python che automatizza il browser Playwright per interagire con Perplexity AI. Funziona con **qualsiasi metodo di login** (incluso Google OAuth).

## Installazione

```bash
pip install playwright
playwright install
```

## Come funziona

1. **Avvio**: apre il browser e naviga a perplexity.ai
2. **Login**: tu fai login manualmente una volta (il browser rimane aperto)
3. **Query**: invia query via MCP e scrapa il risultato
4. **Stabile**: usa l'interfaccia ufficiale, nessun reverse engineering

## Setup Claude Code

### 1. Copia lo script

Salva `playwright-mcp-server.py` in `~/.claude/`:

```bash
cp skill/perplexity/playwright-mcp-server.py ~/.claude/
```

### 2. Aggiorna `~/.claude/settings.json`

Aggiungi a `mcpServers`:

```json
"perplexity-playwright": {
  "command": "python",
  "args": [
    "C:\\Users\\Chirone\\.claude\\playwright-mcp-server.py"
  ]
}
```

(Nota: sostituisci il percorso se diverso)

### 3. Riavvia Claude Code

Dovrebbe comparire il tool `perplexity_search` globalmente disponibile.

## Primo avvio

1. Riavvia Claude Code
2. Il browser si apre automaticamente
3. Fai login su Perplexity (manualmente, una sola volta)
4. Puoi iniziare a usare `perplexity_search`

## Limitazioni

- Il browser rimane aperto (puoi minimizzarlo)
- Timeout: 30 secondi per query (aggiustabile in `playwright-mcp-server.py`)
- Una sola query alla volta

## Vantaggi

✅ Funziona con Google OAuth  
✅ Nessun token da rinnovare  
✅ Nessun reverse engineering  
✅ Usa l'interfaccia ufficiale  
✅ Gratis e senza limiti API  

## Troubleshooting

**"playwright not installed"**
```bash
pip install playwright && playwright install
```

**Browser non si apre**
Verifica il percorso dello script in settings.json

**Timeout su query**
Aumenta il timeout in `playwright-mcp-server.py` linea ~107
