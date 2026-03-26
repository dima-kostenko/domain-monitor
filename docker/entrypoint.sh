#!/usr/bin/env bash
set -euo pipefail

APP_DIR=/var/www/html

echo "[entrypoint] Starting Domain Monitor..."

# ─── 1. Generate APP_KEY if missing ──────────────────────────────────────────
if [ -z "${APP_KEY:-}" ]; then
    echo "[entrypoint] APP_KEY is not set — generating..."
    php "$APP_DIR/artisan" key:generate --force --ansi
fi

# ─── 2. Wait for MySQL ───────────────────────────────────────────────────────
echo "[entrypoint] Waiting for database at ${DB_HOST:-db}:${DB_PORT:-3306}..."
until php -r "new PDO('mysql:host=${DB_HOST:-db};port=${DB_PORT:-3306};dbname=${DB_DATABASE:-crm}', '${DB_USERNAME:-crm}', '${DB_PASSWORD:-secret}');" 2>/dev/null; do
    sleep 2
done
echo "[entrypoint] Database is ready."

# ─── 3. Run migrations ───────────────────────────────────────────────────────
echo "[entrypoint] Running migrations..."
php "$APP_DIR/artisan" migrate --force --no-interaction

# ─── 4. Clear & warm caches ──────────────────────────────────────────────────
echo "[entrypoint] Caching config, routes, views..."
php "$APP_DIR/artisan" config:cache
php "$APP_DIR/artisan" route:cache
php "$APP_DIR/artisan" view:cache

# ─── 5. Fix permissions ──────────────────────────────────────────────────────
chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

mkdir -p /var/log/supervisor /var/log/php
touch /var/log/scheduler.log
chown www-data:www-data /var/log/scheduler.log

echo "[entrypoint] Done. Handing off to: $*"
exec "$@"
