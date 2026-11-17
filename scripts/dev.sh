#!/bin/sh
# Live development server (hot reload). It is the `dev` service in docker-compose.yml,
# so a plain `docker compose up -d` already starts it:
#   http://localhost:8001  (Laravel)   ·   Vite HMR on :5174
# This script (re)starts just that service and follows its logs. Ctrl+C stops the log
# view only; the server keeps running. Stop it with: docker compose stop dev
set -e
cd "$(dirname "$0")/.."
docker compose up -d dev
exec docker compose logs -f dev
