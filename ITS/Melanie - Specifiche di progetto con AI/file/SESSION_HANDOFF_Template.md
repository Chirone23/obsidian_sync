# Session_Handoff_template.md [Nome Progetto]

**Autrice:** Melanie Trucco — AI Projects Development, ITS ICT Academy Roma  
**Data:** Aprile 2026


## Come usare questo file

Una sessione, un passaggio di consegne (HANDOFF). Compila questo file dopo ogni sessione, anche se piccola, è il tuo diario di progetto, la memoria di quello che hai fatto e di quello che devi ancora fare. 
Ma è anche utile quando crei una nuova chat di Cursor senza memoria: così saprà cosa hai fatto, cosa è cambiato, cosa funziona, cosa è fallito e i prossimi passi immediati, senza dover riaprire i vecchi thread.
Compilalo tu stesso oppure: in Cursor, fai riferimento a @docs/spec.md e a questo file, e chiedi all'agente di compilare solo gli spazi vuoti basandosi sulla repository reale; poi tu revisiona attentamente ogni riga prima di fare il commit.

---

## Esempio compilato — dal progetto AI Social Agents della docente Melanie Trucco
(Puoi cancellare tutto e compilare nel modo che preferisci, io lo faccio fare all'agente e poi faccio la revisione di quello che ha scritto)

---

**Session ID:** SESSION-53 
**Data:** 2026-02-24 
**Luogo:** Work

---

**Repo state:** All three surfaces (Home Mac / GitHub origin / VPS) on `xxxxx`. Working tree clean. Service `segesit-agents` active on VPS. (SDK upgrade `xxxx`).

**Production generation path:** `api_direct` — code default in `src/config.py:18` is `api_direct` (Session E `xxxx`). VPS + Home Mac `.env` say `api_direct`. Work Mac `.env` still says `agent_sdk` (update next office visit). SDK is fallback only (reverse fallback chain in `pipeline.py`).

**Cost-per-post baseline:** api_direct path: **$0.11/batch** (Batch 48, clean single-call). Compare: SDK was $0.47-0.70/batch. Prompt caching active (cache_create=9035 tokens confirmed). At 25-client scale estimate ~$82/mo text-only (vs ~$350-525/mo SDK).

**Anthropic Tier 2 ACTIVE:** 450k ITPM / 1000 RPM / $500/mo cap.

**Claude model strings — ALL 4 LOCATIONS on Sonnet 4.6 `claude-sonnet-4-6`**: `src/creative/agent_api.py`, `src/approval/text_regen.py`, `src/approval/image_regen.py`, `scripts/agent_entrypoint.py`.

**Tavily research agent:** LIVE. Category rotation, Sonnet 4.6 query generation with `date.today()`, Tavily search, trending topic in all 3 variants, DB storage, Slack badge, dashboard display, manual toggle (extra batch + text regen). Weekly throttle for scheduled batches, `force=True` for manual triggers.

**Monthly maintenance policy locked:** hard-pin all deps + first-week-of-each-month audit (~45 min, read-only). Template + inaugural baseline both in `06-Resources/System/`. 

---

## ⚠️ FRESH CHAT? READ THIS PROJECT'S BRIEFING FIRST

**Start here:** Read `04-Projects/AI_Social_Media_Agents_Architecture_Discussion_8.md` Round 1 — consolidated TODO with everything remaining.

**For deep context:** Discussion 7 Rounds 1, 5–7, 10–13, 16–24 (canonical briefing + roadmap + audits).

---


*Documento creato per il corso AI Projects Development — ITS ICT Academy Roma*
