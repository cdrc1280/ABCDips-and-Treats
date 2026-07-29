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

php artisan key:generate --force || true

php artisan migrate:fresh --seed --force

php artisan storage:link || true

php artisan optimize:clear

php artisan config:cache

php artisan route:cache

php artisan view:cache

exec /usr/bin/supervisord -n
