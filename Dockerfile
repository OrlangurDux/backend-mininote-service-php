FROM php:8.1-fpm-alpine AS service

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=caddy:2.5.0 /usr/bin/caddy /usr/local/bin/caddy

WORKDIR /app

RUN apk --update add icu-libs icu-data-full jpeg-dev freetype-dev libjpeg-turbo-dev libpng-dev icu-dev libsodium-dev libzip-dev zlib-dev libpng-dev libxml2-dev libxslt-dev libmcrypt-dev npm supervisor
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis mcrypt-1.0.5 \
    && docker-php-ext-enable redis
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/
RUN docker-php-ext-install -j$(nproc) mysqli zip pdo pdo_mysql gd bcmath
RUN docker-php-ext-enable mcrypt

COPY conf/production/php/php.ini /usr/local/etc/php/php.ini
COPY conf/production/caddy /etc/caddy
COPY conf/production/supervisor.conf /etc/supervisord.conf
COPY src /app

RUN cp .env.example .env
RUN composer --ignore-platform-reqs install

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
