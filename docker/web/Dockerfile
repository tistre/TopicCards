FROM php:7.4-apache

RUN a2enmod rewrite

RUN apt-get update

# Install PostgreSQL PDO
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Install intl
RUN apt-get install -y libicu-dev\
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl