#!/bin/sh
set -e

# Render provides PORT at runtime; Docker Compose keeps Apache's default port 80.
APACHE_PORT="${PORT:-80}"
if [ "$APACHE_PORT" != "80" ]; then
  sed -ri "s/^Listen 80$/Listen ${APACHE_PORT}/" /etc/apache2/ports.conf
  sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${APACHE_PORT}>/" /etc/apache2/sites-available/000-default.conf
fi

composer install --no-interaction --prefer-dist
if [ ! -f .env ]; then cp .env.example .env; fi
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi
php artisan migrate --seed --force
php artisan schedule:work >/dev/null 2>&1 &
exec "$@"
