FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
        git unzip libzip-dev libpng-dev libonig-dev default-mysql-client \
    && docker-php-ext-install pcntl pdo_mysql mbstring zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
