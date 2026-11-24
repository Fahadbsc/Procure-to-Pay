FROM php:8.2-apache
RUN apt-get update && apt-get install -y ca-certificates git unzip libzip-dev libpng-dev libonig-dev libxml2-dev && docker-php-ext-install pdo_mysql mbstring zip bcmath && a2enmod rewrite && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
WORKDIR /var/www/html
COPY . .
COPY docker/entrypoint.sh /usr/local/bin/p2p-entrypoint
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && php artisan package:discover --ansi \
    && chmod +x /usr/local/bin/p2p-entrypoint \
    && chown -R www-data:www-data storage bootstrap/cache
ENTRYPOINT ["/usr/local/bin/p2p-entrypoint"]
CMD ["apache2-foreground"]
