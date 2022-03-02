FROM php:8.1-fpm

# Install "bcmath" extension for Neo4j Bolt
RUN docker-php-ext-install bcmath

# Install "zip" extension for Composer installs
RUN apt-get update && apt-get install -y libzip-dev \
    && docker-php-ext-install zip

# git and unzip for Composer installs
RUN apt-get update && apt-get install -y \
		git ssh unzip \
	--no-install-recommends && rm -r /var/lib/apt/lists/*

ENV COMPOSER_HOME /tmp
ENV COMPOSER_VERSION 2.0.11

# "chmod 0777 /tmp/cache" for non-root composer installs
RUN curl --silent --fail --location --retry 3 --output /tmp/installer.php --url https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer \
 && mkdir /tmp/cache \
 && chmod 0777 /tmp/cache \
 && php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer --version=${COMPOSER_VERSION} \
 && composer --ansi --version --no-interaction \
 && rm -f /tmp/installer.php \
 && composer self-update
