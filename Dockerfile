ARG PHP_VERSION=8.1

FROM composer:2 as composer

FROM php:${PHP_VERSION}-cli-alpine3.15 as builder

ARG RR_VERSION=2.8.0

ENV TZ Europe/Prague
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN echo http://nl.alpinelinux.org/alpine/edge/community > /etc/apk/repositories \
  && echo http://nl.alpinelinux.org/alpine/edge/main >> /etc/apk/repositories \
  && apk update && apk upgrade \
  && apk add --no-cache --update \
  libzip-dev=1.8.0-r1 \
  zlib-dev=1.2.11-r3 \
  unzip=6.0-r9 \
  libaio-dev=0.3.112-r1 \
  tzdata=2021e-r0 \
  vim=8.2.4350-r0 \
  bash=5.1.16-r0 \
  && rm -rf /var/cache/apk/*

ARG OPCACHE_INI=/usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

RUN apk add --no-cache --update autoconf gcc make g++ \
  && docker-php-ext-install -j "$(nproc)" \
    bcmath \
    opcache \
    pcntl \
    sockets \
    zip \
  && apk del autoconf gcc make g++ \
  && rm -rf /var/cache/apk/* \
  && echo "opcache.enable_cli=1" >> "${OPCACHE_INI}" \
  && echo "opcache.jit_buffer_size=50M" >> "${OPCACHE_INI}" \
  && echo "opcache.jit=tracing" >> "${OPCACHE_INI}"

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# PHP RoadRunner
RUN mkdir /tmp/rr \
  && cd /tmp/rr \
  && echo "{\"require\":{\"spiral/roadrunner\":\"${RR_VERSION}\"}}" > composer.json \
  && composer install \
  && vendor/bin/rr get-binary -l /usr/local/bin \
  && cd / \
  && rm -rf /tmp/rr \
  && composer clearcache \
  && chmod +x /usr/local/bin/rr

# merge layers above
FROM scratch

ARG PROJECT_ID=geo-api

COPY --from=builder / /

WORKDIR /opt/${PROJECT_ID}

COPY docker/ /
COPY run.sh ./run.sh
COPY phpstan.neon ./phpstan.neon
COPY composer.json composer.lock auth.jso* ./

RUN mkdir -p -m 777 /etc/${PROJECT_ID} \
  && chmod +x ./run.sh


# composer deps
ARG COMPOSER_DEV=--no-dev
RUN composer install -d ./ --no-progress --no-interaction $COMPOSER_DEV --no-ansi --no-autoloader \
  && composer clearcache

COPY src ./src

# dump classmap
RUN composer dump-autoload -d ./ --no-ansi --no-interaction --optimize \
  && composer clearcache

ENTRYPOINT ["./run.sh"]
