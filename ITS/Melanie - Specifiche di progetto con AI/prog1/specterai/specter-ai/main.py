from pathlib import Path

from fastapi import FastAPI, File, Request, UploadFile
from fastapi.responses import HTMLResponse
from fastapi.templating import Jinja2Templates

from llm_client import analyze
from pdf_processor import extract_text
from regex_layer import extract_metadata

app = FastAPI()
templates = Jinja2Templates(directory=str(Path(__file__).parent / "templates"))

MAX_SIZE = 10 * 1024 * 1024  # 10MB


@app.get("/", response_class=HTMLResponse)
async def index(request: Request):
    return templates.TemplateResponse("index.html", {"request": request})


@app.post("/analyze", response_class=HTMLResponse)
async def analyze_contract(request: Request, file: UploadFile = File(...)):
    if file.content_type != "application/pdf":
        return HTMLResponse(
            "<h2>Errore: carica un file PDF valido.</h2><a href='/'>Torna indietro</a>",
            status_code=422,
        )

    pdf_bytes = await file.read()

    if len(pdf_bytes) > MAX_SIZE:
        return HTMLResponse(
            "<h2>Errore: il file supera i 10MB.</h2><a href='/'>Torna indietro</a>",
            status_code=422,
        )

    try:
        contract_text = extract_text(pdf_bytes)
    except ValueError as e:
        return HTMLResponse(
            f"<h2>PDF non leggibile</h2><p>{e}</p><a href='/'>Torna indietro</a>",
            status_code=422,
        )

    metadata = extract_metadata(contract_text)

    try:
        result = analyze(contract_text, metadata)
    except RuntimeError:
        return HTMLResponse(
            "<h2>Servizio temporaneamente non disponibile.</h2><p>Riprova tra qualche minuto.</p><a href='/'>Torna indietro</a>",
            status_code=503,
            headers={"Retry-After": "30"},
        )
    except ValueError:
        return HTMLResponse(
            "<h2>Analisi non riuscita.</h2><p>Il documento potrebbe essere in un formato non supportato.</p><a href='/'>Torna indietro</a>",
            status_code=500,
        )

    return templates.TemplateResponse(
        "report.html",
        {"request": request, "analysis": result, "filename": file.filename},
    )
