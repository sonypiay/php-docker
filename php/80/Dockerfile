FROM php:8.0

ENV TZ="Asia/Jakarta"
ENV PHP_CONF="${PHP_INI_DIR}"
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

RUN apt-get update && apt-get install --no-install-recommends -y \
        apt-utils \
        wget \
        gnupg \
        zip \
        unzip \
        curl \
        vim

RUN curl https://packages.microsoft.com/keys/microsoft.asc | tee /etc/apt/trusted.gpg.d/microsoft.asc
RUN curl https://packages.microsoft.com/config/debian/11/prod.list | tee /etc/apt/sources.list.d/mssql-release.list

RUN apt-get update && apt-get -y install build-essential \
    libpng-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    jpegoptim optipng pngquant gifsicle \
    libxml2-dev \
    freetds-dev \
    libssl-dev \
    openssl \
    libmagickwand-dev \
    imagemagick \
    libz-dev \
    libmemcached-tools \
    libmemcached-dev \
    libpq-dev

RUN apt-get -y update && apt-get clean
RUN ACCEPT_EULA=Y apt-get install -y \
        msodbcsql17 \
        mssql-tools \
        unixodbc-dev

RUN docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
    && docker-php-ext-configure pdo_dblib --with-libdir=/lib/x86_64-linux-gnu \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && pecl install sqlsrv-5.10.1 \
    && pecl install pdo_sqlsrv-5.10.1 \
    && pecl install imagick \
    && pecl install redis \
    && pecl install memcached \
    && docker-php-ext-install \
        iconv \
        intl \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        pdo_dblib \
        mysqli \
        zip \
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