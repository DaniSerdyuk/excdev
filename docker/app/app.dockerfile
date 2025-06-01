FROM php:8.2-fpm-alpine

WORKDIR /var/www/app

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER 1

ARG PHP_EXTENSIONS="zip intl mysqli pdo_mysql opcache bcmath mbstring exif pcntl sysvmsg sysvsem sysvshm"
ARG user
ARG group
ARG gid
ARG uid

RUN set -ex \
  && apk add --no-cache --virtual .build-deps \
    build-base \
    g++ \
    make \
    automake \
    autoconf \
    libzip-dev \
    icu-dev \
    icu-libs \
    zlib-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
  && apk add --update \
    busybox-suid \
    bash \
    git \
    wget \
    curl \
    icu \
    icu-data-full \
    freetype-dev \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
  && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg && docker-php-ext-install -j$(nproc) gd \
  && docker-php-ext-install $PHP_EXTENSIONS \
  && pecl install redis && docker-php-ext-enable redis.so \
  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
  && apk del .build-deps \
  && rm -rf /tmp/* /var/cache/apk/*

RUN addgroup -g $gid $group && adduser -S $user -u $uid -G $group

USER $user
