FROM php:7.2-apache

ENV TZ="Asia/Jakarta"
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

ENV DIRECTORY_SITES="/var/www/html"
ENV DIRECTORY_APACHE="${APACHE_CONFDIR}/sites-available"
ENV CONFIG_APACHE="${DIRECTORY_APACHE}/000-default.conf"
ENV CONFIG_APACHE_SSL="${DIRECTORY_APACHE}/default-ssl.conf"

RUN apt-get -y update && apt-get -y upgrade
RUN apt-get -y install --no-install-recommends \
        apt-utils \
        wget \
        gnupg \
        zip \
        unzip \
        curl \
        apt-transport-https \
        vim \
        supervisor

RUN curl https://packages.microsoft.com/keys/microsoft.asc | tee /etc/apt/trusted.gpg.d/microsoft.asc
RUN curl https://packages.microsoft.com/config/debian/9/prod.list | tee /etc/apt/sources.list.d/mssql-release.list

RUN apt-get -y update && apt-get -y install build-essential \
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
    libpq-dev \
    poppler-utils

RUN ACCEPT_EULA=Y apt-get install -y msodbcsql17 mssql-tools
RUN apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

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

# Enable apache2 modules
RUN a2enmod rewrite \
    ssl \
    headers \
    expires

# change directory to apache configuration
WORKDIR ${DIRECTORY_APACHE}
COPY apache2/000-default.conf ${CONFIG_APACHE}
COPY apache2/default-ssl.conf ${CONFIG_APACHE_SSL}

WORKDIR ${PHP_INI_DIR}
COPY phpconf/php.ini ${PHP_INI_DIR}/php.ini

RUN rm -rf apache2
RUN rm -rf Dockerfile
RUN rm -rf phpconf

# Change directory to sites folder and copy all
WORKDIR ${DIRECTORY_SITES}
COPY src/* ${DIRECTORY_SITES}