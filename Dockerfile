FROM php:8.3-cli

# Prevent Composer warnings
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    libpq-dev \
    libxslt1-dev \
    libwebp-dev \
    libxpm-dev \
    libexif-dev \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

RUN docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    intl \
    zip \
    exif \
    gd \
    bcmath \
    pcntl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy project
COPY . .

# Install Composer dependencies
RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction

# Install Node dependencies
RUN npm install

# Build Vite assets
RUN npm run build

# Optimize Laravel
# Optimize Laravel
RUN php artisan package:discover --ansi || true

EXPOSE 8080

CMD php artisan migrate --force && \
    php artisan storage:link || true && \
    php artisan optimize:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
