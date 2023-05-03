FROM php:8.1-fpm-alpine AS php
WORKDIR /app
RUN apk add --update linux-headers
RUN apk add icu-libs icu-data-full jpeg-dev freetype-dev libjpeg-turbo-dev libpng-dev icu-dev libsodium-dev libzip-dev zlib-dev libpng-dev libxml2-dev libxslt-dev libmcrypt-dev imap-dev krb5-dev openssl-dev
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug redis mcrypt-1.0.5 \
    && docker-php-ext-enable redis
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl
RUN docker-php-ext-install -j$(nproc) mysqli zip pdo pdo_mysql gd bcmath imap
RUN docker-php-ext-enable xdebug mcrypt imap
COPY conf/development/php/php.ini /usr/local/etc/php/php.ini
COPY conf/development/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini