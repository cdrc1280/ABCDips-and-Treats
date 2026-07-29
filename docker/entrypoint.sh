#!/bin/sh

set -e

echo "Starting ABCDips & Treats Laravel App..."

# Create Laravel directories
mkdir -p \
    storage/framework/cache/data \
    storage/framework/views \
    storage/framework/sessions \
    storage/logs \
    bootstrap/cache

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Run migrations + seed ONCE
php artisan migrate --force
php artisan db:seed --force

# Create storage symlink
php artisan storage:link || true

# Clear caches
php artisan optimize:clear

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions again
chown -R www-data:www-data storage bootstrap/cache

# Start Supervisor
exec /usr/bin/supervisord -n
