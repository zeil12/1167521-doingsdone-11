FROM php:7.2-apache-stretch

RUN docker-php-ext-install mysqli
COPY . /var/www/html