FROM php:8.2-fpm-alpine

ARG INSTALL_DEP="g++ make build-base autoconf"
ARG DEPS="libmcrypt-dev libxml2-dev libzip-dev freetype-dev libtool pcre-dev"
ARG PHP_EXTENSIONS="pdo_mysql mbstring exif pcntl bcmath"
ARG user
ARG group
ARG gid
ARG uid

WORKDIR /var/www/app

RUN set -ex \
  && apk add --no-cache $INSTALL_DEP $DEPS \
  && apk add --update \
    bash \
    curl \
    git \
    grep \
    oniguruma-dev \
    libmcrypt \
    libxslt \
    && docker-php-ext-install $PHP_EXTENSIONS \
    && pecl install redis && docker-php-ext-enable redis.so \
    && rm -rf /tmp/* /var/cache/apk/*

RUN addgroup -g $gid $group && adduser -S $user -u $uid -G $group

USER $user

ENTRYPOINT ["php", "artisan", "horizon"]
