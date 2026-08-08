#!/bin/sh

set -e

echo "🚀 Starting ABCDips & Treats Production Container..."

# 1. Ensure storage and bootstrap cache directories exist with correct permissions
mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/app/public \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 2. Bind Nginx to Railway / Cloud PaaS $PORT dynamically (defaults to 80)
TARGET_PORT="${PORT:-80}"
echo "🌐 Configuring Nginx to listen on port ${TARGET_PORT}..."
sed -i "s/listen [0-9]\{1,5\} default_server;/listen ${TARGET_PORT} default_server;/g" /etc/nginx/sites-enabled/default

# 3. Check for .env file; create from .env.example if missing
if [ ! -f .env ]; then
    echo "⚠️  No .env file found in /var/www/html. Initializing from .env.example..."
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        touch .env
    fi
fi

# 4. Generate APP_KEY if missing in .env and not injected via environment variables
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null && [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# 5. Connect public storage symlink safely
php artisan storage:link --force || true

# 6. Run Database Migrations SAFELY (never drop tables or wipe production data!)
echo "📦 Running database migrations (safe incremental update)..."
php artisan migrate --force

# 7. Auto-seed initial roles & admin account ONLY if database users table is empty
USER_COUNT=$(php artisan db:table users --count 2>/dev/null || echo "0")
if [ "$USER_COUNT" = "0" ] || [ "$USER_COUNT" = "Count: 0" ]; then
    echo "🌱 Initializing first-run database seeders (Roles, Permissions & Settings)..."
    php artisan db:seed --force || true
fi

# 8. Optimize & cache routes, views, config, and Filament components for production
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimizing Laravel & Filament caches for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache || true
    php artisan filament:cache-components || true
else
    echo "🧹 Clearing configuration cache for development environment..."
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
fi

echo "✅ ABCDips application initialized successfully on port ${TARGET_PORT}!"

# 9. Start Supervisord
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
