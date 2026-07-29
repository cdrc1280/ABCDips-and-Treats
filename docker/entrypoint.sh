#!/bin/sh

set -e

echo "Starting Laravel..."

php artisan optimize:clear

php artisan migrate:fresh --seed --force

php artisan storage:link || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec /usr/bin/supervisord -n
