---
tags: [ai, llm, reasoning, inference-time, mcmc, insight]
tipo: nota-di-sintesi
fonte-primaria: "[[Reasoning with Sampling - Your Base Model is Smarter Than You Think (arXiv 2510.14901)]]"
arxiv: "2510.14901v1"
codice: https://github.com/aakaran/reasoning-with-sampling
---

# Power Sampling — reasoning training-free dal modello base

**Paper:** *Reasoning with Sampling: Your Base Model is Smarter Than You Think* — Aayush Karan, Yilun Du (Harvard), ott. 2025.
**Testo integrale:** [[Reasoning with Sampling - Your Base Model is Smarter Than You Think (arXiv 2510.14901)]]

> [!abstract] Tesi in una riga
> Un algoritmo di sampling **training-free, dataset-free, verifier-free** estrae dal *modello base* capacità di reasoning quasi pari (e spesso superiori fuori dominio) a quelle ottenute con RL/GRPO — spostando il costo dal training all'inferenza.

## Il meccanismo reale (≠ divulgazione)

Non è "controllo i blocchi a bassa confidenza e li rigenero". È campionamento dalla **power distribution** `p^α`: una versione "affilata" (sharpened) della distribuzione del modello base, che sovrappesa le sequenze ad alta verosimiglianza.

- **Perché `p^α` e non low-temperature?** Sono distribuzioni diverse (Proposizione 1 del paper). La power distribution è "somma di esponenti" → favorisce token con **pochi ma ad alta likelihood** percorsi futuri (evita i *pivotal token* / *critical window* che intrappolano il ragionamento). Il low-temperature è "esponente di somme" → media avidamente i futuri, cadendo in trappole.
- **Come si campiona:** `p^α` non è normalizzabile in modo trattabile → si usa **MCMC Metropolis-Hastings**. Si genera per blocchi (block size B), si ricampionano sottosequenze da un indice casuale, e si accetta/rifiuta la variante secondo la **regola di accettazione MH** basata sulle likelihood del modello stesso — non "scelgo la migliore".
- **È single-shot:** più chiamate di inferenza, ma il risultato è *una* sequenza campionata da `p^α`. Nuovo asse di **test-time scaling**.

## Risultati (Table 1)

Su Qwen2.5-Math-7B / Qwen2.5-7B / Phi-3.5-mini, benchmark MATH500, HumanEval, GPQA, AlpacaEval 2.0:

- **In-dominio (MATH500):** alla pari con GRPO (es. Qwen-Math: 0.748 vs 0.785).
- **Fuori dominio:** spesso **batte** GRPO — HumanEval +59.8% su Phi-3.5, AlpacaEval consistentemente sopra.
- **Diversità:** a differenza dell'RL, **non collassa** — pass@k resta alto (best of both worlds: single-shot forte + multi-shot preservato).
- **Lunghezza tracce:** ~679 token in media, simile a GRPO, *senza* incentivo esplicito a generare lungo.

## Iperparametri chiave

- `α = 4.0` ottimale (robusto per `α ≥ 2.0`); `α=1` = modello base, `α→∞` = accetta ogni variante che aumenta la likelihood.
- `N_MCMC`: salto grosso da 0 a ≥2 step (+3-4%), poi plateau oltre 10 step.
- Proposal LLM = modello base a temperatura `1/α`.

## Il vero costo (che la divulgazione omette)

Non è gratis: con i parametri del paper (`N_MCMC=10`, `T=679`, `B=192`) l'inferenza costa **~8.84× i token** rispetto al sampling standard. Sposti il costo dal training all'inferenza — comparabile a ~1 epoca di GRPO, ma da pagare *ogni volta* che campioni.

## Perché conta

- Suggerisce che le capacità dei modelli base sono **sottoutilizzate a sampling-time**, non assenti — argomento a favore della tesi del *distribution sharpening* (l'RL "affila", non crea capacità nuove).
- Applicabile **oltre i domini verificabili** (nessun verifier richiesto) → utile dove non c'è reward automatico.
- Codice disponibile: https://github.com/aakaran/reasoning-with-sampling

## Connessioni

- [[../moc/Agenti IA Design Patterns MOC]] — Pattern 17 *Reasoning Techniques* + Pattern 16 *Resource-Aware Optimization* (test-time compute)
- [[Knowledge MOC]] — Ricerca AI
- [[Petri - AI Alignment Testing]] — altra ricerca su valutazione/comportamento LLM
