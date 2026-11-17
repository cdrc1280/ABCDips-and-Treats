#!/usr/bin/env bash
# Screenshot audit of the prod-like app (:8000) in the local Playwright image.
#   scripts/ui-audit/run.sh pages   -> 13 pages, 375 + 1440, light + dark, contact sheets
#   scripts/ui-audit/run.sh forms   -> all forms, dialogs, POS at 375 (rings up ONE real POS sale per theme)
# Rebuild first if code changed: docker compose up -d --build app
# Output: scripts/ui-audit/out/ (gitignored). Flags page overflow, clipped controls, dialog overflow, page errors.
set -euo pipefail
cd "$(dirname "$0")"; mkdir -p out/shots out/forms; rm -f out/shots/* out/forms/*
IMG=mcr.microsoft.com/playwright:v1.49.0-noble
[ -d node_modules/playwright ] || docker run --rm -v "$PWD":/out -w /out $IMG npm i --silent --no-save playwright@1.49.0 >/dev/null
q() { docker compose exec -T db psql -U abcdips_user -d abcdips -tAc "select id from $1 limit 1"; }
if [ "${1:-pages}" = pages ]; then
  docker run --rm --network host -v "$PWD":/out -w /out $IMG sh -c 'node shoot.mjs; node sheet.mjs'
else
  IDS="{$(for t in products product_categories employees suppliers orders roles users; do printf '"%s":"%s",' $t "$(q $t)"; done | sed 's/,$//')}"
  for th in light dark; do docker run --rm --network host -e IDS="$IDS" -e THEME=$th -v "$PWD":/out -w /out $IMG node forms.mjs; done
  docker run --rm -v "$PWD":/out -w /out $IMG node sheet2.mjs
fi
echo "Screens: scripts/ui-audit/out/"
