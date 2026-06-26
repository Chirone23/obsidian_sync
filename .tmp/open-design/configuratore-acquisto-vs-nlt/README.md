# Configuratore Acquisto vs NLT — 4Vantage

File singolo HTML autosufficiente, deployabile su InfinityFree senza build.

## Deploy InfinityFree (FTP)
1. Login pannello InfinityFree → **Online File Manager** (o client FTP tipo FileZilla).
2. Vai in `htdocs/` (la root pubblica).
3. Carica `index.html` (rinominalo se vuoi servirlo su un sottopercorso, es. `configuratore.html`).
4. Apri `https://tuodominio.infinityfreeapp.com/index.html` — è online.
5. Per aggiornare i numeri: modifica gli oggetti `AUTO_DATA` e `CONFIG` dentro `<script>` e ricarica il file via FTP. Nessuna build, nessun deploy step.

## Tuning numeri
- **`AUTO_DATA`** — 5 auto con prezzo, canone, svalutazione, manutenzione, bollo.
- **`CONFIG`** — assicurazioni base per classe, moltiplicatori città/guidatore, TAEG, km inclusi NLT.

## Peso
< 60 KB total (no asset esterni se non Google Fonts via CDN).
