# Stesso runtime del Serverino: Python 3.11 su base Debian slim.
FROM python:3.11-slim

# git serve per il pull del vault (contesto da clone locale).
RUN apt-get update \
    && apt-get install -y --no-install-recommends git \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Layer dipendenze separato → cache efficiente sui rebuild.
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

# storage/ e logs/ creati a runtime (montati o volume).
RUN mkdir -p /app/storage /app/logs

CMD ["python", "-u", "main.py"]
