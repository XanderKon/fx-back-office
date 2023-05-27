FROM php:8.2-fpm

LABEL maintainer="Kondratenko Alexander <kondratenko.xander@yandex.ru>"

# replace shell with bash so we can source files
RUN rm /bin/sh && ln -s /bin/bash /bin/sh

RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y; \
    apt-get install -y --no-install-recommends \
            curl \
            nano \
            vim \
            libmemcached-dev \
            libz-dev \
            libpq-dev \
            libjpeg-dev \
            libpng-dev \
            libfreetype6-dev \
            libssl-dev \
            libwebp-dev \
            libxpm-dev \
            libmcrypt-dev \
            libonig-dev; \
    rm -rf /var/lib/apt/lists/*

RUN set -eux; \
    docker-php-ext-install pdo_mysql; \
    docker-php-ext-install pdo_pgsql; \
    docker-php-ext-configure gd \
            --prefix=/usr \
            --with-jpeg \
            --with-webp \
            --with-xpm \
            --with-freetype; \
    docker-php-ext-install gd; \
    php -r 'var_dump(gd_info());'

RUN set -xe; \
    apt-get update -yqq && \
    pecl channel-update pecl.php.net && \
    apt-get install -yqq \
      apt-utils \
      gnupg2 \
      git \
      libzip-dev zip unzip && \
      docker-php-ext-configure zip; \
      docker-php-ext-install zip && \
      php -m | grep -q 'zip'

# Install Composer

ARG COMPOSER_VERSION

RUN if [ ${COMPOSER_VERSION} = latest ]; then \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer ; else \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=${COMPOSER_VERSION} \
;fi

###########################################################################
# Set Timezone
###########################################################################

ARG TIMEZONE
ENV TZ ${TIMEZONE}

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

###########################################################################
# Install intl extension
###########################################################################

RUN apt-get -y update \
    && apt-get install -y libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl


# Install some php extensions
RUN docker-php-ext-install pdo pgsql pdo_pgsql zip pcntl bcmath

###########################################################################
# xDebug:
###########################################################################

ARG INSTALL_XDEBUG

RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    pecl install xdebug-3.2.0; \
    docker-php-ext-enable xdebug \
;fi

COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

###########################################################################
# Redis:
###########################################################################
RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

#####################################
# NodeJS & NPM
#####################################
ARG INSTALL_NODE

# nvm environment variables
ARG NODE_VERSION
ARG NVM_DIR

ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION ${NODE_VERSION}

WORKDIR $NVM_DIR

RUN if [ ${INSTALL_NODE} = true ]; then \
    # https://github.com/creationix/nvm#install-script
    curl --silent -o- https://raw.githubusercontent.com/creationix/nvm/v0.38.0/install.sh | bash \
    && source $NVM_DIR/nvm.sh \
    && nvm ls-remote \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default \
;fi

# add node and npm to path so the commands are available
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

#####################################

#
#--------------------------------------------------------------------------
# Final Touch
#--------------------------------------------------------------------------
#

COPY ./symfony.ini /usr/local/etc/php/conf.d

USER root

# Clean up
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

# Configure locale.
ARG LOCALE=POSIX
ENV LC_ALL ${LOCALE}

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000

