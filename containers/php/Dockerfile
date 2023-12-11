FROM php:8.3-fpm

RUN groupadd -r php && useradd -r -g php php

RUN apt-get update -y
RUN apt-get install -y openssl libssl-dev libcurl4-openssl-dev
RUN pecl install mongodb
RUN docker-php-ext-enable mongodb
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

USER php
