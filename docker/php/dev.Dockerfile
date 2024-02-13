FROM ghcr.io/roadrunner-server/roadrunner:2023.3.1 AS roadrunner
FROM php:8.3-alpine

RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        linux-headers

RUN apk add --update --no-cache \
        openssl-dev \
        pcre-dev \
        icu-dev \
        icu-data-full \
        git \
        libzip-dev \
        bash \
        postgresql-dev \
        autoconf

RUN docker-php-ext-install \
        intl \
        opcache \
        zip \
        sockets \
        pdo_pgsql

# Install XDEBUG
RUN pecl install xdebug-3.3.1 && \
    docker-php-ext-enable xdebug

# Copy configs
COPY ./docker/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY ./docker/php/dev.php.ini /usr/local/etc/php/php.ini

# Clear Cache
RUN pecl clear-cache \
    && apk del --purge .build-deps

WORKDIR /app
COPY --from=roadrunner /usr/bin/rr /usr/local/bin
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV APP_ENV=dev

EXPOSE 8080 9000

USER root

COPY ./docker/php/bin/enter_dev /usr/local/bin/enter_dev.sh
RUN chmod +x /usr/local/bin/enter_dev.sh
ENTRYPOINT [ "enter_dev.sh" ]
