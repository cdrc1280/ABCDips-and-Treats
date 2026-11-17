#!/bin/sh
# One-time setup (and later schema updates) for the hosted production Postgres (Neon).
#   scripts/prod-db.sh          -> migrate, seed roles/settings/admin, set the admin password
#   scripts/prod-db.sh migrate  -> migrations only (after a deploy that adds migrations)
#
# The connection string is read with a hidden prompt, passed to the container through the
# environment only, and never written to disk or shell history. Use Neon's *unpooled* string
# (Vercel → Storage → your database → .env.local tab → DATABASE_URL_UNPOOLED).
set -eu
cd "$(dirname "$0")/.."

printf 'Neon connection string (input hidden): '
stty -echo; read -r DB_URL; stty echo; printf '\n'
[ -n "$DB_URL" ] || { echo 'No connection string given.'; exit 1; }
export DB_URL

run() {
    docker run --rm -i -u "$(id -u):$(id -g)" -e HOME=/tmp \
        -e DB_URL -e DB_CONNECTION=pgsql -e DB_SSLMODE=require -e APP_ENV=production \
        -e APP_KEY=base64:dGVzdGtleXRlc3RrZXl0ZXN0a2V5dGVzdGtleTEyMzQ= \
        -e ADMIN_PASSWORD="${ADMIN_PASSWORD:-}" \
        -v "$PWD/src":/app abcdips-tools "$@"
}

run php artisan migrate --force

if [ "${1:-}" = migrate ]; then
    echo 'Migrations applied.'
    exit 0
fi

run php artisan db:seed --force

printf 'New password for admin@abcdips.test (min 12 chars, input hidden): '
stty -echo; read -r ADMIN_PASSWORD; stty echo; printf '\n'
[ "${#ADMIN_PASSWORD}" -ge 12 ] || { echo 'Too short; the admin keeps the seeded default. Re-run to set it.'; exit 1; }
export ADMIN_PASSWORD
run php artisan tinker --execute='App\Domains\User\Models\User::where("email", "admin@abcdips.test")->firstOrFail()->forceFill(["password" => Illuminate\Support\Facades\Hash::make(getenv("ADMIN_PASSWORD"))])->save(); echo "Admin password set.\n";'
echo 'Production database ready.'
