ARG REDIS_VERSION

FROM redis:${REDIS_VERSION}

LABEL maintainer="Kondratenko Alexander <kondratenko.xander@yandex.ru>"

VOLUME /data

EXPOSE 6379

CMD ["redis-server",  "--appendonly", "yes" ]
