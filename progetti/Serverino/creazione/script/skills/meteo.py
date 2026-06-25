"""Skill meteo — Open-Meteo, NESSUNA API key (§8, prima skill no-OAuth).

Due chiamate httpx: geocoding (nome città → lat/lon) + forecast.
Restituisce una stringa pronta per Telegram. Errori → eccezione propagata:
il chiamante (scheduler/handler) gestisce notifica + log (no retry, §8).
"""
from __future__ import annotations

import httpx

GEOCODE_URL = "https://geocoding-api.open-meteo.com/v1/search"
FORECAST_URL = "https://api.open-meteo.com/v1/forecast"

# WMO weather codes → descrizione italiana (sottoinsieme utile).
WMO = {
    0: "sereno", 1: "prevalentemente sereno", 2: "parz. nuvoloso", 3: "coperto",
    45: "nebbia", 48: "nebbia con brina",
    51: "pioviggine debole", 53: "pioviggine", 55: "pioviggine intensa",
    61: "pioggia debole", 63: "pioggia", 65: "pioggia forte",
    71: "neve debole", 73: "neve", 75: "neve forte", 77: "nevischio",
    80: "rovesci deboli", 81: "rovesci", 82: "rovesci violenti",
    85: "rovesci di neve", 86: "rovesci di neve forti",
    95: "temporale", 96: "temporale con grandine", 99: "temporale forte con grandine",
}


async def get_meteo(citta: str, *, timeout: int = 10) -> str:
    """Meteo corrente + min/max di oggi per `citta`. Solleva ValueError se la
    città non viene trovata; propaga errori di rete/HTTP."""
    citta = citta.strip()
    if not citta:
        raise ValueError("Città mancante")
    async with httpx.AsyncClient(timeout=timeout) as http:
        g = await http.get(GEOCODE_URL,
                           params={"name": citta, "count": 1, "language": "it", "format": "json"})
        g.raise_for_status()
        results = g.json().get("results")
        if not results:
            raise ValueError(f"Città non trovata: {citta}")
        loc = results[0]
        lat, lon = loc["latitude"], loc["longitude"]
        nome = loc.get("name", citta)
        regione = loc.get("admin1", "")

        f = await http.get(FORECAST_URL, params={
            "latitude": lat, "longitude": lon,
            "current": "temperature_2m,weather_code,wind_speed_10m",
            "daily": "temperature_2m_max,temperature_2m_min",
            "timezone": "auto",
        })
        f.raise_for_status()
        data = f.json()

    cur = data["current"]
    daily = data["daily"]
    desc = WMO.get(cur["weather_code"], "—")
    luogo = f"{nome}" + (f" ({regione})" if regione else "")
    return (
        f"🌤 *{luogo}*\n"
        f"Ora: {cur['temperature_2m']}°C, {desc}, vento {cur['wind_speed_10m']} km/h\n"
        f"Oggi: min {daily['temperature_2m_min'][0]}°C / max {daily['temperature_2m_max'][0]}°C"
    )
