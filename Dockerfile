FROM php:8.0-apache

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY index.php /var/www/html/index.php

RUN chown -R www-data:www-data /var/www/html