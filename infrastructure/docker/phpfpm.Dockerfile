FROM php:8.3-fpm-alpine3.18

RUN apk add --no-cache pcre-dev $PHPIZE_DEPS linux-headers autoconf libxml2 libxml2-dev groff freetype-dev bash icu-dev php82-pecl-apcu \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli soap intl \
    && pecl install redis \
    && docker-php-ext-enable intl redis.so \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.2.17
