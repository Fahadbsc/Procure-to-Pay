#!/bin/sh
set -e
composer install --no-interaction --prefer-dist
if [ ! -f .env ]; then cp .env.example .env; fi
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi
php artisan migrate --seed --force
php artisan schedule:work >/dev/null 2>&1 &
exec "$@"
