FROM serversideup/php:8.3-fpm-nginx

USER root

RUN install-php-extensions gd

USER www-data

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 8080
