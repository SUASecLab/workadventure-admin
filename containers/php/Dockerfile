FROM php:8.1-fpm
RUN apt-get update
RUN apt-get update -y
RUN apt-get install openssl libssl-dev libcurl4-openssl-dev -y
RUN pecl install mongodb
RUN docker-php-ext-enable mongodb
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
