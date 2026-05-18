---
tags: [claude-code, manutenzione, cleanup, triage]
data: 2026-05-18
---

# Triage Sessioni Claude Code

Procedura per ripulire `C:\Users\Chirone\.claude\projects\` quando si accumulano troppe sessioni `.jsonl` e non si sa più cosa tenere.

## Contesto

Le conversazioni di Claude Code vengono salvate come file `.jsonl` per ogni sessione, una sottocartella per ogni working directory. Crescono in modo invisibile finché non occupano centinaia di MB. Il problema: non si ricorda quali siano importanti e quali siano spazzatura (test, `/clear`, conn-test).

## Risultato run 2026-05-18

| Verdetto | Sessioni | Spazio |
|----------|----------|--------|
| **DELETE** | 29 | 0,3 MB (test, /clear, conn-test) |
| **ARCHIVE** | 43 | 35,9 MB (lavoro completato da preservare) |
| **KEEP** | 47 | 105,8 MB (attivo, recente, core) |
| **Totale** | **119** | **142 MB** |

## Metodologia (parallelizzazione con sub-agenti Haiku)

1. **Inventario** (PowerShell): scansiona ricorsivamente `*.jsonl`, estrae per ogni sessione `path`, `sizeKB`, `lastWrite`, `msgCount`, `firstUser` (primo prompt utente, 500 char), `lastMsg` (ultimo messaggio, 400 char). Output: `C:\tmp\sessions_inventory.json`.
2. **Split in chunk**: divisione modulare in 5 chunk JSON (uno per agente).
3. **Triage parallelo**: 5 sub-agenti `general-purpose` con `model: haiku` lanciati in parallelo (singolo messaggio, 5 tool call). Ogni agente legge il suo chunk e ritorna un JSON `{path, verdict, tag, reason}` inline nel reply finale (evita problemi di permessi Write).
4. **Aggregazione**: i 5 output vengono concatenati in `triage_all.json`.
5. **Report + script**: generazione `triage_report.md` (leggibile, raggruppato per verdetto) + `cleanup_sessions.ps1` (sposta DELETE/ARCHIVE in `.tmp\claude_sessions_triage\`, parte in `dryRun = $true`).

### Criteri di triage

- **DELETE**: `msgCount <= ~10`, path `conn-test`, sessioni con solo `/clear` o caveat-only, comandi sconosciuti, Q&A triviali senza follow-up.
- **ARCHIVE**: lavoro completato e documentato, `msgCount > ~30` ma `lastWrite > 30gg`, deliverable chiusi (commit pushato, doc finiti, env setup completato).
- **KEEP**: `lastWrite` entro ~30gg con `msgCount > 3`, progetti core (Secondo-Cervello, n8n, ITS coursework, biblio), problemi attivi/interrupted.

## Esecuzione cleanup

```powershell
# 1. Dry-run (vede solo cosa farebbe)
powershell C:\tmp\cleanup_sessions.ps1

# 2. Apri triage_report.md, sposta manualmente in ARCHIVE quello che non vuoi DELETE
# 3. Modifica $dryRun = $false nello script
# 4. Riesegui
powershell C:\tmp\cleanup_sessions.ps1
```

Lo script **non elimina nulla**: sposta in `C:\Users\Chirone\.tmp\claude_sessions_triage\{da_cancellare,da_archiviare}\`. La cancellazione finale è manuale dopo verifica.

## Artefatti

- `[[attachments/triage-sessioni-2026-05-18/cleanup_sessions.ps1]]` — script PowerShell di pulizia (dry-run di default)
- `[[attachments/triage-sessioni-2026-05-18/triage_all.json]]` — 119 verdetti grezzi (riferimento + base per script futuri)
- Report dettagliato qui sotto ⬇️

## Quando rieseguire

Quando `Get-ChildItem 'C:\Users\Chirone\.claude\projects' -Recurse | Measure-Object Length -Sum` supera ~150-200 MB, o quando si percepisce di nuovo accumulo di sessioni `/clear` e test.

Da automatizzare in futuro come hook settimanale (cfr. `[[Vault Audit]]` pattern).

## Lessons learned

- I sub-agenti **non possono usare Write senza permessi**: meglio farli dumpare l'output JSON direttamente nel reply finale (`Output the result ONLY as your final message, as a single fenced json code block`).
- `SendMessage` permette di **resumere** un agente esistente con feedback senza rilanciarlo a freddo (era l'approccio pulito mancato in questa run).
- 5 agenti Haiku in parallelo coprono 119 sessioni in ~30s. Singola wave > batch sequenziali.

## Connessioni

- [[Skill MOC]]
- [[Vault Audit]] — pattern simile applicato al vault Obsidian
- [[Triage Protocol]] — protocollo di triage delle richieste in entrata (concetto analogo, dominio diverso)

---

# Report dettagliato 2026-05-18

Totale sessioni analizzate: 119

## DELETE (29)

- [bash test] `C--Users-Chirone\56c45edb-490a-4d9f-86ac-7029f2afe89b.jsonl` (4 KB) — 9 msgs, comando bash fallito, throwaway
- [tool check] `C--Users-Chirone\98948e45-0a5b-4005-b264-d95a925772c3.jsonl` (12 KB) — 13 msgs, Q&A rapida Perplexity
- [conn-test] `C--Users-Chirone-AppData-Local-Temp-od-conn-test-2hGYac\14b2b770-...jsonl` (12.7 KB) — test path, throwaway
- [session setup] `...ITS-Melanie...\1d2de54d-...jsonl` (18.9 KB) — 11 msgs, setup minimale
- [clear] `C--Users-Chirone\0e248206-...jsonl` (2.1 KB) — 6 msgs, solo /clear
- [CC setup] `C--Users-Chirone\6eb87f2b-...jsonl` (25 KB) — 22 msgs, installazione completata
- [chat navigation] `C--Users-Chirone\9fa2c511-...jsonl` (14.5 KB) — 7 msgs, Q&A triviale
- [conn-test] `...od-conn-test-JJT2ju\0d7fa175-...jsonl` (12.3 KB) — test path, throwaway
- [clear] `...n8n-locale-workflow\6b0b4e48-...jsonl` (1.9 KB) — 4 msgs, solo /clear
- [file exploration] `c--Users-Chirone-Documents-rip\99cd7402-...jsonl` (17.4 KB) — 10 msgs, solo IDE opens
- [format Q] `...ITS-Melanie...prog1\5bbf8e68-...jsonl` (15.6 KB) — 7 msgs, Q&A incompleta
- [model-config] `C--Users-Chirone\3b331363-...jsonl` (2 KB) — 5 msgs, command caveat
- [clear] `C--Users-Chirone\58276c6f-...jsonl` (1.8 KB) — 4 msgs, solo /clear
- [bitwarden download] `C--Users-Chirone\7b591bd6-...jsonl` (48.9 KB) — 24 msgs, download singolo
- [pwd test] `C--Users-Chirone-Desktop\0b713234-...jsonl` (5.1 KB) — 13 msgs, command caveat
- [install test] `...n8n-locale-workflow\1c83efa0-...jsonl` (12.6 KB) — 12 msgs, comando sconosciuto
- [local cmd] `C--Programmi-miei-php\d5ecd892-...jsonl` (3.8 KB) — auto-generato
- [single Q] `C--Users-Chirone\13929ff3-...jsonl` (8.4 KB) — 9 msgs, una domanda isolata
- [model setting] `C--Users-Chirone\51e239c3-...jsonl` (2 KB) — 5 msgs, command locale
- [clear] `C--Users-Chirone\5875f0f6-...jsonl` (1.8 KB) — 4 msgs, /clear
- [clear] `...n8n-locale-workflow\91e40bbc-...jsonl` (6.2 KB) — 4 msgs, /clear
- [path error] `...Progetti-private-agent\f0893342-...jsonl` (30.4 KB) — 24 msgs, file not found
- [clear] `...Secondo-Cervello\6f01eb88-...jsonl` (2 KB) — 5 msgs, /clear
- [clear] `...Secondo-Cervello\b3b496f0-...jsonl` (1.9 KB) — 4 msgs, /clear
- [git status] `...ITS-Melanie...\903cde9c-...jsonl` (10.8 KB) — 7 msgs, query singola
- [setup triviale] `C--Users-Chirone\02e025ea-...jsonl` (25.1 KB) — caveat-only CLAUDE.md
- [clear] `C--Users-Chirone\ba93cf03-...jsonl` (1.8 KB) — 4 msgs, /clear
- [model setup] `...n8n-locale-workflow\4265f47b-...jsonl` (3.9 KB) — 9 msgs, caveat triviale
- [clear] `...Secondo-Cervello\7e6c1567-...jsonl` (2.1 KB) — 6 msgs, /clear

## ARCHIVE (43)

- [Meta-Review Multi-Agent] `C--Users-Chirone\07cabe7b-...jsonl` (382.9 KB) — 207 msgs, completato 11gg fa
- [FAST MOC] `C--Users-Chirone\31d980ef-...jsonl` (564.8 KB) — 234 msgs, knowledge extract completato
- [CV Rheinmetall] `C--Users-Chirone\467272f4-...jsonl` (617.9 KB) — 65 msgs, progetto CV chiuso
- [RAM diagnostics] `C--Users-Chirone\affb3f9e-...jsonl` (141 KB) — 88 msgs, analisi completata
- [CV supermercato] `C--Users-Chirone\c643713e-...jsonl` (1336.8 KB) — 605 msgs, progetto chiuso
- [n8n workflow] `...n8n-locale-workflow\14fc599f-...jsonl` (859 KB) — 118 msgs, test completati
- [n8n Instagram] `...n8n-locale-workflow\5fbffe7e-...jsonl` (1212.9 KB) — 571 msgs, conversione completata
- [n8n dir analysis] `...n8n-locale-workflow\6cc84def-...\subagents\agent-a3d40f9f...jsonl` (287 KB) — 55 msgs
- [contract analysis] `...Secondo-Cervello\3e3f0452-...jsonl` (2080.8 KB) — 34 msgs, completato
- [AI newsletter] `...Secondo-Cervello\6ae47485-...jsonl` (397.5 KB) — 200 msgs, ricerca completata
- [SpecterAI docs] `...Secondo-Cervello\815c46ec-...jsonl` (716.5 KB) — 272 msgs, docs push completato
- [Perplexity research] `...Secondo-Cervello\baff804d-...jsonl` (622.4 KB) — 309 msgs, ricerca completata
- [biblio audit] `...1e7401a8-...\subagents\agent-a8eeb6e9...jsonl` (182.9 KB) — 30 msgs, audit completato
- [MD to Word] `...ITS-Melanie...prog1\4084e314-...jsonl` (324.4 KB) — 109 msgs, conversione completata
- [SpecterAI validation] `C--Users-Chirone\b235bbe8-...jsonl` (165.8 KB) — 74 msgs, spec validata
- [PHP testing setup] `C--Users-Chirone\f655cb23-...jsonl` (179.6 KB) — 169 msgs, env setup completato
- [n8n schema] `...n8n-locale-workflow\1911a9f4-...jsonl` (660.3 KB) — 62 msgs, schema design completato
- [ITS progettistica] `...Progetti-its-m1\34e49649-...jsonl` (15.5 MB) — 67 msgs, MOC + 5 lesson notes
- [subagent docs] `...3edfc5d0-...\subagents\agent-ae2fc16f...jsonl` (32.5 KB) — 2 msgs, completato
- [privacy-filter] `C--Users-Chirone\11bc5be6-...jsonl` (718.5 KB) — 389 msgs, completato 12gg
- [obsidian-moc] `C--Users-Chirone\5088bb87-...jsonl` (714.4 KB) — 182 msgs, Knowledge Compiler setup chiuso
- [perplexity-agent] `C--Users-Chirone\b31d12af-...jsonl` (1942.1 KB) — 164 msgs, skill documentato
- [git-remote setup] `C--Users-Chirone\fc39dcb8-...jsonl` (902.9 KB) — 273 msgs, config verificato
- [n8n plan] `...n8n-locale-workflow\6cc84def-...jsonl` (448.3 KB) — 210 msgs, file committato
- [ITS research] `...Progetti-its-m1\4f1d3f95-...jsonl` (265.1 KB) — 141 msgs, prompt completato
- [EU-AI-Act MOC] `...Secondo-Cervello\1ee49ac6-...jsonl` (634.3 KB) — 297 msgs, commit pushato
- [biblio-template] `...Secondo-Cervello\6c1d7c2e-...jsonl` (631.6 KB) — 131 msgs, WP template chiuso
- [SpecterAI-doc] `...ITS-Melanie...\84ac8d2c-...jsonl` (252.5 KB) — 125 msgs, doc finiti
- [session-status] `...ITS-Melanie...prog1\8fc0b1d0-...jsonl` (38.8 KB) — 29 msgs, domanda risolta
- [SpecterAI review] `C--Users-Chirone\7d798edf-...jsonl` (31.8 KB) — 13 msgs, spec analisi
- [cleanup analysis] `C--Users-Chirone\aa8a6ef7-...jsonl` (93.4 KB) — 38 msgs, completato
- [SpecterAI spec] `C--Users-Chirone\b9c6e910-...jsonl` (110.3 KB) — 23 msgs, validazione completata
- [CV extraction] `...467272f4-...\subagents\agent-a2abbe5b...jsonl` (183.5 KB) — 26 msgs, completato
- [NotebookLM prompt] `...n8n-locale-workflow\334f0b4c-...jsonl` (1369 KB) — 224 msgs, completato 57gg
- [CLI info] `...ITS-Melanie...prog1\8fc0b1d0-...\subagents\agent-ad3cf7ae...jsonl` (44.8 KB) — 11 msgs, doc subagent
- [audio troubleshoot] `C--Users-Chirone\41eefb14-...jsonl` (519.2 KB) — 65 msgs, RemoteApp risolto
- [MCP setup] `C--Users-Chirone\5555f5f1-...jsonl` (23.4 KB) — 22 msgs, obsidian-mcp completato
- [JSON reformat] `C--Users-Chirone\5f26e743-...jsonl` (19 KB) — 20 msgs, task breve
- [memory setup] `C--Users-Chirone\8af74682-...jsonl` (186.3 KB) — 95 msgs, MEMORY.md committato
- [obsidian test] `C--Users-Chirone\ac6682a0-...jsonl` (287 KB) — 99 msgs, MCP test
- [ranking analysis] `...aa8a6ef7-...\subagents\agent-aa29e5a4...jsonl` (197.3 KB) — 80 msgs, score completato
- [workflow deployed] `...n8n-locale-workflow\97d1bca1-...jsonl` (664.6 KB) — 126 msgs, caricato su server
- [interrupted] `...Secondo-Cervello\f0e496a5-...jsonl` (140.1 KB) — 44 msgs, non concluso

## KEEP (47)

Sessioni mantenute: progetti core attivi (Secondo-Cervello, biblio-php, biblio-WP, SpecterAI, n8n, ITS Northwind/coursework). Vedi `triage_all.json` per la lista completa con path + tag + motivo.

