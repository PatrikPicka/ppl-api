FROM php:8.4-fpm

RUN apt-get update  \
    && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip openssl libssl-dev libcurl4-openssl-dev \
    && docker-php-ext-install intl opcache pdo pdo_mysql  \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

RUN docker-php-ext-install mysqli

WORKDIR /var/www/project

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
