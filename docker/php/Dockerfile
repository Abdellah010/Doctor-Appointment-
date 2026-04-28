FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    bash curl git zip unzip \
    libpng-dev libjpeg-dev freetype-dev \
    oniguruma-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
