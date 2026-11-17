#!/bin/sh

set -e

echo "Starting ABCDips & Treats Production Container..."

# ─── 0. Set UTF-8 locale to prevent encoding issues in PHP and the database ──────────────
export LC_ALL=C.UTF-8
export LANG=C.UTF-8

# ─── 1. Ensure storage directories exist with correct permissions ─────────────
mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/app/public \
    storage/app/private \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ─── 2. Bind Nginx to PaaS $PORT dynamically (defaults to 80) ─────────────────
TARGET_PORT="${PORT:-80}"
echo "Configuring Nginx to listen on port ${TARGET_PORT}..."
sed -i "s/listen [0-9]\{1,5\} default_server;/listen ${TARGET_PORT} default_server;/g" /etc/nginx/sites-enabled/default

# ─── 3. Resolve .env file ──────────────────────────────────────────────────────
if [ ! -f .env ]; then
    echo "No .env file found. Initializing from .env.example..."
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        touch .env
    fi
fi

# ─── 4. Generate APP_KEY if missing ───────────────────────────────────────────
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null && [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    # key:generate only replaces an existing APP_KEY= line, so make sure there is one.
    grep -q "^APP_KEY=" .env 2>/dev/null || echo "APP_KEY=" >> .env
    php artisan key:generate --force
fi

# ─── 5. Connect public storage symlink safely ─────────────────────────────────
php artisan storage:link --force || true

# ─── 6. Ensure supervisor log directory exists ────────────────────────────────
mkdir -p /var/log/supervisor

# ─── 7. Run Database Migrations SAFELY ────────────────────────────────────────
# Wait for the database to be truly ready (any driver, incl. DB_URL; healthcheck may pass before full init)
DB_READY=0
for i in 1 2 3 4 5 6 7 8 9 10; do
    if php -r "
        try {
            require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php'; \$app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();
            Illuminate\\Support\\Facades\\DB::connection()->getPdo();
            echo 'ok';
        } catch (Throwable \$e) {
            echo 'fail';
        }
    " 2>/dev/null | grep -q "ok"; then
        DB_READY=1
        break
    fi
    echo "Waiting for database... attempt ${i}/10"
    sleep 2
done

if [ "$DB_READY" = "0" ]; then
    echo "ERROR: Database connection failed after 10 attempts. Check DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD."
    # Continue startup anyway — the app will show a proper error page
fi

echo "Running database migrations (safe incremental update only)..."
php artisan migrate --force || echo "WARNING: Migration failed — check database connectivity."

# ─── 8. First-run seeders — GUARDED to never overwrite production data ─────────
IS_FIRST_RUN=$(php -r "
    try {
        require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php'; \$app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();
        echo Illuminate\\Support\\Facades\\DB::table('users')->count() === 0 ? 'yes' : 'no';
    } catch (Throwable \$e) {
        echo 'yes';
    }
" 2>/dev/null || echo "no")

if [ "$IS_FIRST_RUN" = "yes" ]; then
    echo "First-run detected: seeding Roles, Permissions, Settings & Admin account..."
    php artisan db:seed --force || true
else
    echo "Existing database detected — skipping seeders to protect production data."
    php artisan db:seed --class=AccessSeeder --force || true
    php artisan db:seed --class=SettingsSeeder --force || true
fi

# ─── 9. Clear Spatie permission cache (safe, no data risk) ────────────────────
php artisan permission:cache-reset || true

# ─── 10. Optimize caches for production ───────────────────────────────────────
if [ "$APP_ENV" = "production" ]; then
    echo "Optimizing Laravel caches for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache || true
else
    echo "Clearing caches for development environment..."
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
fi

echo "ABCDips application initialized successfully on port ${TARGET_PORT}!"

# ─── 11. Start Supervisord ───────────────────────────────────────────────────
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
