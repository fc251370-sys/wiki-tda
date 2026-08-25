FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libpq-dev \
    git \
    pkg-config \
    libssl-dev \
    && pecl install mongodb redis \
    && docker-php-ext-enable mongodb redis \
    && docker-php-ext-install pdo pdo_mysql mysqli pdo_pgsql pgsql zip gd intl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]