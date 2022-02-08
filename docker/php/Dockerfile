FROM php:7.4-fpm-alpine

RUN mkdir -p /var/www/${COMPOSE_PROJECT_NAME}

RUN chown www-data:www-data /var/www/${COMPOSE_PROJECT_NAME}

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions pdo_mysql mysqli zLib json libxml iconv mbstring xsl simplexml phar curl xmlreader redis bcmath gd imagick exif zip opcache xdebug

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
