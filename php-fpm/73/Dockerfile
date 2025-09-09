FROM php:7.3-fpm

ENV TZ="Asia/Jakarta"
ENV PHP_FPM_DIR="/usr/local/etc/php-fpm.d"
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

RUN apt-get -y update && apt-get -y install --no-install-recommends \
        gnupg \
        zip \
        unzip \
        curl \
        vim

RUN curl https://packages.microsoft.com/keys/microsoft.asc | tee /etc/apt/trusted.gpg.d/microsoft.asc
RUN curl https://packages.microsoft.com/config/debian/11/prod.list | tee /etc/apt/sources.list.d/mssql-release.list

RUN apt-get -y update && apt-get -y install build-essential \
    libpng-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    locales \
    jpegoptim optipng pngquant gifsicle \
    libxml2-dev \
    libmagickwand-dev \
    imagemagick \
    libz-dev \
    libmemcached-tools \
    libmemcached-dev \
    libpq-dev \
    poppler-utils

RUN ACCEPT_EULA=Y apt-get install -y msodbcsql17 mssql-tools unixodbc-dev
RUN apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-configure gd \
        --with-gd \
        --with-freetype-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ \
        --with-png-dir=/usr/include/ \
        --with-webp-dir=/usr/include/ \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && pecl install sqlsrv-5.8.1 \
    && pecl install pdo_sqlsrv-5.8.1 \
    && pecl install imagick-3.4.4 \
    && pecl install redis-5.3.7 \
    && pecl install memcached \
    && docker-php-ext-install \
        iconv \
        intl \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        mysqli \
        zip \
        mbstring \
        exif \
        pcntl \
        gd \
        pgsql \
        opcache \
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

WORKDIR ${PHP_INI_DIR}
COPY phpconf/php.ini ${PHP_INI_DIR}/php.ini
COPY phpconf/www.conf ${PHP_FPM_DIR}/www.conf