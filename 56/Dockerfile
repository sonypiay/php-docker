FROM php:5.6-apache

ENV TZ="Asia/Jakarta"
ENV DIRECTORY_SITES="/var/www/html"
ENV DIRECTORY_APACHE="${APACHE_CONFDIR}/sites-available"
ENV CONFIG_APACHE="${DIRECTORY_APACHE}/000-default.conf"
ENV CONFIG_APACHE_SSL="${DIRECTORY_APACHE}/default-ssl.conf"
ENV PHP_CONF="${PHP_INI_DIR}/conf.d"

RUN apt-get upgrade

RUN apt-get update && apt-get install --no-install-recommends -y \
		apt-utils \
        wget \
        gnupg \
        git \
        zip \
        unzip \
        curl \
        apt-transport-https

RUN apt-get update && apt-get -y install build-essential \
    libpng-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    jpegoptim optipng pngquant gifsicle \
    vim \
    supervisor \
    cron \
    unixodbc-dev \
    freetds-dev \
    freetds-bin \
    freetds-common \
    tdsodbc \
    libct4 \
    libsybdb5 \
    libmcrypt-dev

RUN ln -s /usr/lib/x86_64-linux-gnu/libsybdb.a /usr/lib/ \
    && ln -s /usr/lib/x86_64-linux-gnu/libsybdb.so /usr/lib/

RUN docker-php-ext-configure gd \
        --with-gd \
        --with-freetype-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ \
        --with-png-dir=/usr/include/ \
    && docker-php-ext-configure mssql \
    && docker-php-ext-configure pdo_dblib \
    && docker-php-ext-configure mcrypt \
    && docker-php-ext-configure iconv \
    && docker-php-ext-install \
        iconv \
        pdo \
        pdo_dblib \
        mssql \
        mcrypt \
        zip \
        mbstring \
        exif \
        pcntl \
        gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable apache2 modules
RUN a2enmod rewrite \
    ssl

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