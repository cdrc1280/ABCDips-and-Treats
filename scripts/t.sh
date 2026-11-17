#!/bin/sh
# Run a command inside the dev toolbox with the app (src/) mounted at /app.
#   scripts/t.sh php artisan test
#   scripts/t.sh npx tsc --noEmit
set -e
cd "$(dirname "$0")/.."
docker image inspect abcdips-tools >/dev/null 2>&1 || docker build -q -t abcdips-tools -f docker/tools.Dockerfile docker >/dev/null
# Join the compose network when PostgreSQL is running, so tests and artisan reach it as "db".
NET="$(docker inspect abcdips_db --format '{{range $k, $v := .NetworkSettings.Networks}}{{$k}}{{end}}' 2>/dev/null || true)"
exec docker run --rm -u "$(id -u):$(id -g)" -e HOME=/tmp ${NET:+--network "$NET" -e DB_HOST=db} \
    -e APP_KEY=base64:dGVzdGtleXRlc3RrZXl0ZXN0a2V5dGVzdGtleTEyMzQ= \
    -v "$PWD/src":/app abcdips-tools "$@"
