FROM php:7.2

ENV TZ="Asia/Jakarta"
ENV PHP_CONF="${PHP_INI_DIR}/conf.d"
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

RUN apt update \
    && apt -y upgrade

RUN apt -y update && apt install --no-install-recommends -y \
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

RUN apt -y update && apt -y install build-essential \
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
    imagemagick \
    libz-dev \
    libmemcached-tools \
    libmemcached-dev \
    libpq-dev

RUN curl https://packages.microsoft.com/keys/microsoft.asc | tee /etc/apt/trusted.gpg.d/microsoft.asc
RUN curl https://packages.microsoft.com/config/debian/9/prod.list | tee /etc/apt/sources.list.d/mssql-release.list

RUN apt -y update && apt clean

RUN ACCEPT_EULA=Y apt-get install -y msodbcsql17 mssql-tools

RUN docker-php-ext-configure gd \
        --with-gd \
        --with-freetype-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ \
        --with-png-dir=/usr/include/ \
    && docker-php-ext-configure pdo_dblib --with-libdir=/lib/x86_64-linux-gnu \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && pecl install sqlsrv-5.6.1 \
    && pecl install pdo_sqlsrv-5.6.1 \
    && pecl install imagick \
    && pecl install redis \
    && pecl install memcached \
    && docker-php-ext-install \
        iconv \
        intl \
        pdo \
        pdo_mysql \
        pdo_dblib \
        pdo_pgsql \
        mysqli \
        zip \
        mbstring \
        exif \
        pcntl \
        gd \
        pgsql \
    && docker-php-ext-enable \
        sqlsrv \
        pdo_sqlsrv \
        imagick \
        redis \
        memcached

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN rm -rf Dockerfile
RUN rm -rf phpconf

WORKDIR ${PHP_CONF}
COPY phpconf/php.ini ${PHP_CONF}/php.ini