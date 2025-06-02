FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
        git \
        unzip \
        zip \
        libicu-dev \
        libzip-dev \
        libonig-dev \
        libxml2-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        curl \
    && docker-php-ext-install intl zip opcache pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony \
    && rm -rf /root/.symfony*

WORKDIR /var/www/symfony