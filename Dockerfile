FROM serversideup/php:8.3-fpm-nginx

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 8080
