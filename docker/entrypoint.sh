#!/bin/sh
set -e

# Render provides PORT at runtime; Docker Compose keeps Apache's default port 80.
APACHE_PORT="${PORT:-80}"
if [ "$APACHE_PORT" != "80" ]; then
  sed -ri "s/^Listen 80$/Listen ${APACHE_PORT}/" /etc/apache2/ports.conf
  sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${APACHE_PORT}>/" /etc/apache2/sites-available/000-default.conf
fi

if [ ! -f .env ]; then cp .env.example .env; fi
if [ -z "${APP_KEY:-}" ]; then
  php artisan key:generate --force
fi
php artisan migrate --seed --force
if [ "${QUEUE_CONNECTION:-database}" = "database" ]; then
  php artisan queue:work database --sleep=3 --tries=3 --timeout=90 >/dev/null 2>&1 &
fi
php artisan schedule:work >/dev/null 2>&1 &
exec "$@"
