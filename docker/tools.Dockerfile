# Dev toolbox: PHP 8.3 (same extensions as production) + Composer + Node 22, for running
# tests, Wayfinder generation and Vite builds without PHP/Node on the host.
#   docker build -t abcdips-tools -f docker/tools.Dockerfile docker
#   scripts/t.sh php artisan test
FROM php:8.3-cli-bookworm
RUN rm -rf /var/lib/apt/lists/* && apt-get update && apt-get install -y --no-install-recommends ca-certificates xz-utils git unzip libzip-dev libicu-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libsqlite3-dev libpq-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql pdo_sqlite intl zip exif gd bcmath
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=node:22-bookworm-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-bookworm-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx
ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /app
