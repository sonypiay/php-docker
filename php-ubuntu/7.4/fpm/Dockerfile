FROM composer:latest AS composer

FROM ubuntu:22.04

ENV PHP_VERSION=7.4
ENV TZ="Asia/Jakarta"
ENV WWWROOT="/var/www/html"
ENV PHP_CONF_DIR="/etc/php/${PHP_VERSION}"
ENV PHP_CONF_CLI="${PHP_CONF_DIR}/cli"
ENV PHP_CONF_FPM="${PHP_CONF_DIR}/fpm"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get -y update && apt-get -y upgrade

RUN apt-get -y install \
    software-properties-common \
    ca-certificates \
    lsb-release \
    apt-transport-https \
    zip \
    imagemagick \
    vim \
    libpng-dev \
    libzip-dev \
    libfreetype6-dev \
    locales \
    locales-all \
    jpegoptim optipng pngquant gifsicle \
    libmcrypt-dev \
    wget \
    apt-utils \
    gnupg
    
# add repository php
RUN add-apt-repository -y ppa:ondrej/php
RUN apt-get -y update && apt-get -y upgrade
RUN apt-get -y install php${PHP_VERSION} \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-bz2 \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-mysqli \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-xmlrpc \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-iconv \
    php${PHP_VERSION}-pdo \
    php${PHP_VERSION}-exif \
    php${PHP_VERSION}-interbase \
    php${PHP_VERSION}-imagick \
    php${PHP_VERSION}-tidy \
    php${PHP_VERSION}-pdo-mysql \
    php${PHP_VERSION}-redis \
    php${PHP_VERSION}-memcached \
    php${PHP_VERSION}-fpm

RUN apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

# Backup origin configuration
RUN mv ${PHP_CONF_CLI}/php.ini ${PHP_CONF_CLI}/php.ini.origin
RUN mv ${PHP_CONF_FPM}/php.ini ${PHP_CONF_FPM}/php.ini.origin
RUN mv ${PHP_CONF_FPM}/pool.d/www.conf ${PHP_CONF_FPM}/pool.d/www.conf.origin
RUN mv ${PHP_CONF_FPM}/php-fpm.conf ${PHP_CONF_FPM}/php-fpm.conf.origin
# Backup origin configuration

RUN mkdir -p /var/log/php-fpm
RUN chown -R www-data:www-data /var/log/php-fpm

COPY ./cli/php.ini ${PHP_CONF_CLI}/php.ini
COPY ./fpm/php.ini ${PHP_CONF_FPM}/php.ini
COPY ./fpm/pool.d/. ${PHP_CONF_FPM}/pool.d/.
COPY ./fpm/php-fpm.conf ${PHP_CONF_FPM}/php-fpm.conf

RUN mkdir -p ${WWWROOT}

COPY ./docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT [ "/usr/local/bin/docker-entrypoint.sh" ]

EXPOSE 9000/tcp