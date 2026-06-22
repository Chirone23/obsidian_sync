---
tags: [progetto, odysseus, claude-code, decisione, auth]
status: deciso
created: 2026-06-18
---

# DECISIONE — Auth Claude Code in Odysseus

> **Esito: versione SAFE (shell-out al binario `claude`).** Scartata l'integrazione OAuth nativa.

## Le due opzioni valutate

| | **A) SAFE — shell-out CLI** ✅ scelta | **B) Native OAuth** ❌ scartata |
|---|---|---|
| Come funziona | Odysseus lancia il binario `claude` come **stesso utente OS**; la CLI legge e rinnova da sola il login Pro da `~/.claude/.credentials.json` | Odysseus legge `accessToken` da `~/.claude/.credentials.json`, lo usa come Bearer + header beta OAuth, gestisce refresh con `refreshToken` |
| Gestione token | Nessuna — la fa `claude` | Manuale (lettura + refresh + scadenza) |
| Rischio ToS / account | Basso (uso sanzionato della CLI ufficiale) | **Alto** — uso del token `user:inference` fuori dalla CLI ufficiale, possibile flag/limit dell'account Pro |
| Complessità | Bassa | Alta |

## Perché SAFE

L'uso personale non giustifica il rischio sull'account Pro. Lo shell-out riusa il login ufficiale senza toccare i token, ed è già coperto dal design doc esistente ([[Design - IT]]).

## Come si concretizza

- Driver: `claude -p <task> --model <m> --output-format stream-json` → parse stream → bolle chat
- Auth: il processo `claude` gira come utente OS e legge `~/.claude` da solo (verificato: risponde con account Pro senza `ANTHROPIC_API_KEY`)
- **Attenzione env:** lanciare `claude` in ambiente PULITO, senza `ANTHROPIC_BASE_URL`/`ANTHROPIC_API_KEY` (lo switch DeepSeek globale le imposterebbe e scavalcherebbe il login Pro). Lo switch va tenuto OFF, o il driver deve azzerare quelle env per il sottoprocesso.

## Stato

- [x] Scelta auth: SAFE / shell-out
- [ ] Decisioni UI ancora aperte (interazione PTY vs chat, workspace) — vedi [[Design - IT]] §Stato
- [ ] Implementazione (eventuale sciame Octogent sul lavoro locale)

## Related

[[Design - IT]] • [[Design - EN]] • [[octogent]]
