FROM php:8.2-apache

ENV TZ="Asia/Jakarta"
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

ENV DIRECTORY_SITES="/var/www/html"
ENV DIRECTORY_APACHE="${APACHE_CONFDIR}/sites-available"
ENV CONFIG_APACHE="${DIRECTORY_APACHE}/000-default.conf"
ENV CONFIG_APACHE_SSL="${DIRECTORY_APACHE}/default-ssl.conf"

RUN apt-get -y update
RUN apt-get -y install --no-install-recommends \
        gnupg \
        zip \
        unzip \
        curl \
        vim

RUN curl https://packages.microsoft.com/keys/microsoft.asc | tee /etc/apt/trusted.gpg.d/microsoft.asc
RUN curl https://packages.microsoft.com/config/debian/11/prod.list | tee /etc/apt/sources.list.d/mssql-release.list

RUN apt-get -y update && apt-get -y install \
    libpng-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
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

RUN ACCEPT_EULA=Y apt-get -y install msodbcsql17 mssql-tools unixodbc-dev
RUN apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && pecl install sqlsrv-5.11.1 \
    && pecl install pdo_sqlsrv-5.11.1 \
    && pecl install imagick \
    && pecl install redis \
    && pecl install memcached \
    && docker-php-ext-install \
        iconv \
        intl \
        pdo \
        pdo_mysql \
        pdo_pgsql \
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