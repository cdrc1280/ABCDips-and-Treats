# Build from the repo root: docker build -f docker/app.Dockerfile .  (the app lives in src/)

# ==========================================
# Stage 1 - PHP dependencies
# ==========================================

FROM php:8.3-fpm AS vendor

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1

# Stale apt lists are cleared first (a half-written InRelease file breaks apt-get update);
# no recommended packages keeps the image small and skips openssh-client.
RUN rm -rf /var/lib/apt/lists/* && apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    xz-utils \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

RUN docker-php-ext-install \
    pdo_pgsql \
    intl \
    zip \
    exif \
    gd \
    bcmath \
    opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY src/composer.json src/composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction

COPY src/ .

# Rebuild the package manifest without dev packages (a dev-generated one would load
# providers that --no-dev left out).
RUN rm -f bootstrap/cache/*.php \
    && composer dump-autoload --optimize \
    && php artisan package:discover --ansi


# ==========================================
# Stage 2 - Frontend (needs PHP: the Wayfinder Vite plugin
# runs `php artisan wayfinder:generate` to build typed routes)
# ==========================================

FROM vendor AS frontend

COPY --from=node:22-bookworm-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-bookworm-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm

WORKDIR /app

COPY src/package*.json ./

RUN npm ci --no-audit --no-fund

COPY src/ .

# Route generation boots the app without a database (services read settings lazily).
RUN APP_KEY=base64:YnVpbGRvbmx5YnVpbGRvbmx5YnVpbGRvbmx5YnVpbGQ= DB_CONNECTION=sqlite DB_DATABASE=:memory: \
    CACHE_STORE=array SESSION_DRIVER=array npm run build


# ==========================================
# Stage 3 - Runtime
# ==========================================

FROM php:8.3-fpm

ARG APP_ENV=production
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    APP_ENV=${APP_ENV}

RUN rm -rf /var/lib/apt/lists/* && apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    xz-utils \
    nginx \
    supervisor \
    unzip \
    curl \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

RUN docker-php-ext-install \
    pdo_pgsql \
    intl \
    zip \
    exif \
    gd \
    bcmath \
    opcache

# phpredis — docker-compose uses Redis for cache, sessions and the queue.
RUN pecl install redis && docker-php-ext-enable redis && rm -rf /tmp/pear

WORKDIR /var/www/html

COPY src/ .

COPY --from=vendor /app/vendor ./vendor
COPY --from=vendor /app/bootstrap/cache/packages.php ./bootstrap/cache/packages.php
COPY --from=vendor /app/bootstrap/cache/services.php ./bootstrap/cache/services.php
COPY --from=frontend /app/public/build ./public/build

COPY docker/nginx.conf /etc/nginx/sites-enabled/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom-php.ini
COPY docker/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

RUN mkdir -p \
    storage/framework/cache \
    storage/framework/views \
    storage/framework/sessions \
    storage/logs \
    bootstrap/cache

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 CMD curl -f http://localhost:${PORT:-80}/up || exit 1

ENTRYPOINT ["/entrypoint.sh"]
