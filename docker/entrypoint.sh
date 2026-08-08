#!/bin/sh

set -e

echo "Starting ABCDips..."

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

php artisan storage:link --force || true

if [ "$APP_ENV" = "production" ] && [ -f .env ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

exec /usr/bin/supervisord -n
