FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

FROM php:8.2-fpm-alpine

RUN apk add --no-cache nginx supervisor sqlite-dev libzip-dev oniguruma-dev libpng-dev libjpeg-turbo-dev freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_sqlite mbstring zip exif gd \
    && mkdir -p /run/nginx /var/log/supervisor /var/www/html/storage/app/public /var/www/html/database \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/database

WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint \
    && chown -R www-data:www-data storage bootstrap/cache database

EXPOSE 8080
ENTRYPOINT ["/usr/local/bin/entrypoint"]
