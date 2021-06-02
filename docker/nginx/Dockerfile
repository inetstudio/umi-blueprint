FROM nginx:stable-alpine

RUN mkdir -p /var/www/${COMPOSE_PROJECT_NAME}

RUN set -x ; \
    addgroup -g 82 -S www-data ; \
    adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1

RUN chown www-data:www-data /var/www/${COMPOSE_PROJECT_NAME}
