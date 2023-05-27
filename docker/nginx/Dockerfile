FROM nginx:alpine

LABEL maintainer="Kondratenko Alexander <kondratenko.xander@yandex.ru>"

ARG PHP_UPSTREAM_CONTAINER
ARG PHP_UPSTREAM_PORT

RUN apk update \
    && apk upgrade \
    && apk add --no-cache bash

COPY nginx.conf /etc/nginx/
COPY site.conf /etc/nginx/sites-available/
COPY site.conf /etc/nginx/sites-enabled/

RUN apk add --no-cache curl

RUN set -x ; \
    addgroup -g 82 -S www-data ; \
    adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1

RUN echo "upstream php-upstream { server ${PHP_UPSTREAM_CONTAINER}:${PHP_UPSTREAM_PORT}; }" > /etc/nginx/conf.d/upstream.conf \
    && rm /etc/nginx/conf.d/default.conf

CMD ["nginx"]

EXPOSE 80 443
