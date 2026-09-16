#!/usr/bin/env bash
# Arranque local (sem Docker): MySQL já deve estar a correr com as duas bases importadas.
set -euo pipefail
cd "$(dirname "$0")"

if [ ! -f .env ]; then
  cp .env.example .env
fi

if [ ! -d node_modules ]; then
  npm install
fi

echo "A arrancar a API Node.js em segundo plano (porta 3000)…"
npm start &
API_PID=$!
trap 'kill $API_PID 2>/dev/null || true' EXIT

echo "A arrancar o PHP em http://127.0.0.1:8080"
php -S 0.0.0.0:8080 -t php
