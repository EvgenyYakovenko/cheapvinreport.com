# ------------------------------------------------------------------------------
# Dockerfile — STAGING build for cheapvinreport.com (Laravel 12 / PHP 8.4)
#
# RU (Yevhen): этот файл описывает, как Railway собирает и запускает копию сайта.
#   Ставит PHP + расширения, Composer, Node (для сборки Vite/Tailwind), тянет
#   зависимости, собирает фронт и запускает сайт. Тебе тут ничего менять не надо.
#
# EN (dev handoff): Container image for a staging deploy on Railway.
#   - Uses the SQLite DB seeded from database/staging-seed.sqlite onto a volume.
#   - Serves via `php artisan serve` (fine for low-traffic staging; swap for
#     php-fpm+nginx or Octane/FrankenPHP for production).
#   - No config/route/view caching on boot — staging is meant to be edited and
#     redeployed frequently, so we keep it un-cached for instant feedback.
# ------------------------------------------------------------------------------
FROM php:8.4-cli-bookworm

# --- System packages + PHP extensions the app needs ---
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip ca-certificates curl \
        libicu-dev libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j"$(nproc)" pdo_sqlite intl zip gd bcmath exif pcntl \
    && rm -rf /var/lib/apt/lists/*

# --- Composer (copied from the official image) ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- Node 22 (only used at build time to compile front-end assets) ---
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . /app

# --- PHP dependencies (production set) ---
# (scripts left ON so Laravel package discovery runs during the build)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# --- Front-end build (Vite + Tailwind) ---
RUN npm ci && npm run build

# --- Writable dirs for Laravel ---
RUN chmod -R 775 storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Railway injects $PORT at runtime; entrypoint binds to it.
EXPOSE 8080
CMD ["/usr/local/bin/entrypoint.sh"]
