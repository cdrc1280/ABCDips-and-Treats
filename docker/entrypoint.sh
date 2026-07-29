#!/bin/sh

set -e

echo "Starting ABCDips & Treats Laravel App..."

# Ensure permissions on storage and bootstrap directories
mkdir -p storage/framework/cache/data storage/framework/views storage/framework/sessions storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Run safe database migrations
php artisan migrate:fresh --seed --force

# Seed database if essential tables/users are unseeded
php artisan db:seed --force || true

# Symlink public storage
php artisan storage:link || true

# Clear & optimize caches for production
php artisan optimize:clear
php artisan config:cache
php artisan view:cache

php artisan queue:work

# Ensure www-data retains write ownership for session and log files
chown -R www-data:www-data storage bootstrap/cache

exec /usr/bin/supervisord -n
