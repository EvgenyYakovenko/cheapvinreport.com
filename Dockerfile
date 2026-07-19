# ------------------------------------------------------------------------------
# Dockerfile — STAGING build for cheapvinreport.com (Laravel 12 / PHP 8.4)
#
# RU (Yevhen): описывает, как Railway собирает и запускает копию сайта. Менять не надо.
# EN (dev handoff): staging image. PHP extensions are installed via the well-known
#   mlocati/install-php-extensions helper, which auto-resolves the required system
#   libraries (avoids fragile manual apt lib lists). Serves via `php artisan serve`
#   (fine for low-traffic staging; use php-fpm+nginx or Octane for production).
# ------------------------------------------------------------------------------
FROM php:8.4-cli-bookworm

# --- PHP extensions (helper pulls all needed system libs automatically) ---
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_sqlite pdo_mysql redis intl zip gd bcmath exif pcntl

# --- Runtime tools + Node 22 (for building Vite/Tailwind assets) ---
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip ca-certificates curl \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . /app

ENV COMPOSER_ALLOW_SUPERUSER=1

# --- PHP deps (production set) + front-end build ---
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
RUN npm ci && npm run build

# --- Writable dirs for Laravel ---
RUN mkdir -p storage/framework/views storage/framework/cache/data storage/framework/sessions storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Railway injects $PORT at runtime; entrypoint binds to it (default 8080).
EXPOSE 8080
CMD ["/usr/local/bin/entrypoint.sh"]
