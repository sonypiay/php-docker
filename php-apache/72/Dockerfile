FROM php:7.2-apache

ENV TZ="Asia/Jakarta"
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

ENV DIRECTORY_SITES="/var/www/html"
ENV DIRECTORY_APACHE="${APACHE_CONFDIR}/sites-available"
ENV CONFIG_APACHE="${DIRECTORY_APACHE}/000-default.conf"
ENV CONFIG_APACHE_SSL="${DIRECTORY_APACHE}/default-ssl.conf"
ENV PHP_CONF="${PHP_INI_DIR}/conf.d"

RUN apt-get update \
    && apt-get -y upgrade

RUN apt-get update && apt-get install --no-install-recommends -y \
        apt-utils \
        wget \
        gnupg \
        git \
        zip \
        unzip \
        curl \
        apt-transport-https \
        vim \
        supervisor \
        cron

RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
RUN curl https://packages.microsoft.com/config/debian/9/prod.list > /etc/apt/sources.list.d/mssql-release.list

RUN apt-get update && apt-get -y install build-essential \
    libpng-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    locales-all \
    jpegoptim optipng pngquant gifsicle \
    unixodbc-dev \
    freetds-dev \
    libssl-dev \
    libxml2-dev \
    libzip-dev \
    libmagickwand-dev \
    imagemagick

RUN apt-get -y update && apt-get clean
RUN ACCEPT_EULA=Y apt-get install -y msodbcsql17 mssql-tools

RUN docker-php-ext-configure gd \
        --with-gd \
        --with-freetype-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ \
        --with-png-dir=/usr/include/ \
    && docker-php-ext-configure pdo_dblib --with-libdir=/lib/x86_64-linux-gnu \
    && docker-php-ext-configure intl \
    && pecl install sqlsrv-5.6.0 \
    && pecl install pdo_sqlsrv-5.6.0 \
    && pecl install imagick \
    && docker-php-ext-install \
        iconv \
        intl \
        pdo \
        pdo_mysql \
        pdo_dblib \
        mysqli \
        zip \
        mbstring \
        exif \
        pcntl \
        gd \
    && docker-php-ext-enable \
        sqlsrv \
        pdo_sqlsrv \
        imagick

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable apache2 modules
RUN a2enmod rewrite

# change directory to apache configuration
WORKDIR ${DIRECTORY_APACHE}
COPY apache2/000-default.conf ${CONFIG_APACHE}
COPY apache2/default-ssl.conf ${CONFIG_APACHE_SSL}

WORKDIR ${PHP_CONF}
COPY phpconf/php.ini ${PHP_CONF}/php.ini

RUN rm -rf apache2
RUN rm -rf Dockerfile
RUN rm -rf phpconf

# Change directory to sites folder and copy all
WORKDIR ${DIRECTORY_SITES}
COPY src/* ${DIRECTORY_SITES}