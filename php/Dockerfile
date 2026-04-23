FROM php:8.4-fpm-alpine AS base

RUN apk add --no-cache \
    bash \
    icu-dev \
    libpq-dev \
    mariadb-dev \
    libzip-dev \
    unzip

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install intl pdo pdo_mysql pdo_pgsql zip \
    && apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/lara

# -----------------------------------
FROM base AS dev

RUN apk add --no-cache $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del $PHPIZE_DEPS linux-headers

RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN chown -R www-data:www-data /var/www/lara
EXPOSE 9000
CMD ["php-fpm"]


# ----------------------------
FROM base AS prod

# disable php-fpm logs. Use only symfony json logs on prod
COPY ./docker/docker.conf /usr/local/etc/php-fpm.d/docker.conf

COPY lara/ /var/www/lara/

ARG DEFAULT_URI
ARG DATABASE_URL
ARG REDIS_HOST
ARG REDIS_PORT

ENV APP_ENV=prod
RUN composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader --no-dev
RUN php bin/console asset-map:compile

RUN chown -R www-data:www-data /var/www/lara
EXPOSE 9000
CMD ["php-fpm"]
