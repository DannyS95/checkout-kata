FROM php:8.3.2RC1-fpm-alpine3.18

RUN docker-php-ext-install bcmath && \
    docker-php-ext-install pdo pdo_mysql

WORKDIR /app

COPY . /app/

COPY --from=composer:2.6.6 /usr/bin/composer /usr/bin/composer

RUN composer --version
