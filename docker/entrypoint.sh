#!/bin/sh

set -e

echo "🚀 Starting ABCDips & Treats Production Container..."

# ─── 1. Ensure storage directories exist with correct permissions ─────────────
# storage/app is a Docker volume and starts empty on first deploy.
# We must create all required subdirectories at runtime.
mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/app/public \
    storage/app/private \
    storage/app/livewire-tmp \
    storage/logs \
    bootstrap/cache


chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ─── 2. Bind Nginx to Railway / Cloud PaaS $PORT dynamically (defaults to 80) ─
TARGET_PORT="${PORT:-80}"
echo "🌐 Configuring Nginx to listen on port ${TARGET_PORT}..."
sed -i "s/listen [0-9]\{1,5\} default_server;/listen ${TARGET_PORT} default_server;/g" /etc/nginx/sites-enabled/default

# ─── 3. Resolve .env file ──────────────────────────────────────────────────────
if [ ! -f .env ]; then
    echo "⚠️  No .env file found. Initializing from .env.example..."
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        touch .env
    fi
fi

# ─── 4. Generate APP_KEY if missing ───────────────────────────────────────────
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null && [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# ─── 5. Connect public storage symlink safely ─────────────────────────────────
php artisan storage:link --force || true

# ─── 6. Run Database Migrations SAFELY ────────────────────────────────────────
# IMPORTANT: We ONLY run `migrate --force` (incremental).
# We NEVER run migrate:fresh, migrate:reset, or db:wipe.
# The `down()` methods in migrations (dropIfExists) are NEVER called here.
echo "📦 Running database migrations (safe incremental update only)..."
php artisan migrate --force

# ─── 7. First-run seeders — GUARDED to never overwrite production data ─────────
# We use a reliable PHP-based check against the migrations table to determine
# if this is a brand-new database (no users seeded yet).
# This replaces the fragile `db:table users --count` command.
IS_FIRST_RUN=$(php -r "
    try {
        \$pdo = new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 3306) . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        \$count = \$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        echo (\$count == 0) ? 'yes' : 'no';
    } catch (Exception \$e) {
        echo 'yes'; // If table doesn't exist yet, treat as first run
    }
" 2>/dev/null || echo "no")

if [ "$IS_FIRST_RUN" = "yes" ]; then
    echo "🌱 First-run detected: seeding Roles, Permissions, Settings & Admin account..."
    # Only safe idempotent seeders run here:
    #   - RolesAndPermissionsSeeder: uses firstOrCreate for roles/permissions
    #   - SettingsSeeder:            uses firstOrCreate, never overwrites
    #   - AdminUserSeeder:           uses firstOrCreate, never overwrites
    # ProductSeeder and PurchasingAndPayrollSeeder are EXCLUDED (local/testing only)
    php artisan db:seed --force || true
else
    echo "✅ Existing database detected — skipping seeders to protect production data."
    # Still sync roles/permissions schema in case new permissions were added in code,
    # but use a safe idempotent-only run that won't overwrite existing data.
    php artisan db:seed --class=RolesAndPermissionsSeeder --force || true
    php artisan db:seed --class=SettingsSeeder --force || true
fi

# ─── 8. Clear Spatie permission cache (safe, no data risk) ────────────────────
php artisan permission:cache-reset || true

# ─── 9. Optimize caches for production ────────────────────────────────────────
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimizing Laravel & Filament caches for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache || true
    php artisan filament:cache-components || true
else
    echo "🧹 Clearing caches for development environment..."
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
fi

echo "✅ ABCDips application initialized successfully on port ${TARGET_PORT}!"

# ─── 10. Start Supervisord ────────────────────────────────────────────────────
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
