FROM php:8.4-fpm

RUN groupadd -r php && useradd -r -g php php

RUN apt-get update -y
RUN apt-get install -y openssl libssl-dev libcurl4-openssl-dev
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN pecl config-set php_ini "${PHP_INI_DIR}/php.ini"
RUN pecl install mongodb
RUN docker-php-ext-enable mongodb

USER php
