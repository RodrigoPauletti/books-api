FROM php:8.3-apache

RUN apt update && \
    apt install -y \
        g++ \
        libicu-dev \
        libpq-dev \
        libzip-dev \
        zip \
        zlib1g-dev \
        libpng-dev

RUN docker-php-ext-install \
        intl \
        opcache \
        pdo \
        pdo_mysql \
        mysqli \
        zip \
        gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

STOPSIGNAL SIGKILL

# Start application server (if the vendor folder don't exists, execute 'composer install')
# CMD . "$HOME/.bashrc"; test -d ./vendor || composer install; php artisan serve --host=0.0.0.0
