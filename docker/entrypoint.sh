#!/usr/bin/env bash
# ------------------------------------------------------------------------------
# entrypoint.sh — runs every time the staging container starts.
#
# RU: при первом запуске копирует чистую staging-базу на постоянный диск (volume),
#     применяет миграции и поднимает сайт. При следующих запусках базу не трогает
#     (данные сохраняются между деплоями).
# EN: seeds the SQLite DB onto the mounted volume on first boot, migrates, serves.
# ------------------------------------------------------------------------------
set -e
cd /app

# DB_DATABASE must point at the mounted volume, e.g. /data/database.sqlite
DB_PATH="${DB_DATABASE:-/data/database.sqlite}"
mkdir -p "$(dirname "$DB_PATH")"

if [ ! -f "$DB_PATH" ]; then
    echo "[entrypoint] First boot — seeding staging DB to $DB_PATH"
    cp database/staging-seed.sqlite "$DB_PATH"
else
    echo "[entrypoint] Existing DB found at $DB_PATH — keeping it"
fi

# Ensure app key exists (should come from env; generate if missing).
php artisan key:generate --force >/dev/null 2>&1 || true

# Apply any new migrations (idempotent).
php artisan migrate --force || true

# Clear any stale caches (we intentionally do NOT cache on staging).
php artisan optimize:clear || true

PORT="${PORT:-8080}"
echo "[entrypoint] Starting server on 0.0.0.0:$PORT"
exec php artisan serve --host 0.0.0.0 --port "$PORT"
