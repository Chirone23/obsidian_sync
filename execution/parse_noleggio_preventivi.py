"""Parser deterministico dei preventivi ALD Automotive 4Vantage.

Legge i .txt estratti da `extract_noleggio_pdfs.py` e produce
`preventivi_noleggio.json` con tutti i campi rilevanti per il
configuratore Acquisto vs Noleggio LTM.
"""
import json
import re
from pathlib import Path

SRC = Path(r"C:/Users/Chirone/Documents/Secondo_Cervello/.tmp/noleggio_extract")
OUT = SRC / "preventivi_noleggio.json"

NUM = r"([0-9]+(?:\.[0-9]+)?)"


def parse(txt: str) -> dict:
    d: dict = {}

    m = re.search(r"Veicolo\s*:\s*(.+?)(?:\n|$)", txt)
    d["veicolo"] = m.group(1).strip() if m else None

    # Riga listino: "20299.99 48 120000 € 271.18 € 135.1 € 406.29"
    m = re.search(
        rf"{NUM}\s+(\d+)\s+(\d+)\s+€\s*{NUM}\s+€\s*{NUM}\s+€\s*{NUM}",
        txt,
    )
    if m:
        d["listino_eur"] = float(m.group(1))
        d["durata_mesi"] = int(m.group(2))
        d["km_totali"] = int(m.group(3))
        d["canone_puro_noleggio"] = float(m.group(4))
        d["canone_servizi"] = float(m.group(5))
        d["canone_mensile_totale"] = float(m.group(6))

    m = re.search(rf"Totale optional\s*€\s*:\s*{NUM}", txt)
    d["totale_optional_eur"] = float(m.group(1)) if m else None

    m = re.search(rf"Totale veicolo\s*€\s*:\s*{NUM}", txt)
    d["totale_veicolo_eur"] = float(m.group(1)) if m else None

    m = re.search(rf"Anticipo \(iva inclusa\)\s*€\s*:\s*{NUM}", txt)
    d["anticipo_eur"] = float(m.group(1)) if m else None

    m = re.search(rf"Entro 15%\s*Km totali\s*{NUM}", txt)
    d["km_eccedente_entro_15pct"] = float(m.group(1)) if m else None
    m = re.search(rf"Oltre 15%\s*Km totali\s*{NUM}", txt)
    d["km_eccedente_oltre_15pct"] = float(m.group(1)) if m else None

    # Settimane di consegna
    m = re.search(r"Prevista consegna\s*:\s*(\d+)\s*Settimane", txt)
    d["consegna_settimane"] = int(m.group(1)) if m else None

    # Motorizzazione / CO2
    m = re.search(r"Motorizzazione:\s*([^\n]+?)\s+(?:6E\)|6D\))?\s*Cilindrata", txt)
    if m:
        d["motorizzazione"] = m.group(1).strip()
    m = re.search(r"Emissioni CO2:\s*(\d+)", txt)
    d["co2_g_km"] = int(m.group(1)) if m else None
    m = re.search(rf"Cilindrata:\s*(\d+)", txt)
    d["cilindrata_cc"] = int(m.group(1)) if m else None
    m = re.search(rf"Kw:\s*(\d+)", txt)
    d["potenza_kw"] = int(m.group(1)) if m else None

    # Servizi inclusi (sezione finale)
    servizi = {}
    for key in [
        "Veicolo Sostitutivo",
        "Fuel card",
        "Servizio pagamento tasse auto",
        "Immatricolazione",
        "Manutenzione Ordinaria e Straordinaria",
        "Ald Automotive Assistance",
        "Danni al veicolo",
        "Furto",
        "Pneumatici",
        "Gestione Sinistri",
        "Rinotifica Contravvenzioni",
        "Telematica",
    ]:
        mm = re.search(rf"{re.escape(key)}\s+([A-Za-z]+)", txt)
        if mm:
            servizi[key] = mm.group(1)
    d["servizi_inclusi"] = servizi

    # Quota massimali assicurazioni
    m = re.search(r"RCA[^€]*€\s*([\d\.]+)", txt)
    d["rca_massimale_eur"] = m.group(1) if m else None

    return d


def main() -> None:
    out = {}
    for txt_path in sorted(SRC.glob("*.txt")):
        out[txt_path.stem] = parse(txt_path.read_text(encoding="utf-8"))
    OUT.write_text(json.dumps(out, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"Scritto {OUT}")
    for name, row in out.items():
        print(
            f"  {name:25s} listino={row.get('listino_eur')!s:>10s} "
            f"canone={row.get('canone_mensile_totale')!s:>7s} "
            f"durata={row.get('durata_mesi')}m km={row.get('km_totali')}"
        )


if __name__ == "__main__":
    main()
