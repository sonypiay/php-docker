FROM ubuntu:20.04

ENV TZ="Asia/Jakarta"
ENV PHP_VERSION=7.4
ENV SQLSRV_DRIVER_VERSION=5.8.1
ENV PHP_CONF_DIR="/etc/php/${PHP_VERSION}"
ENV PHP_CONF_APACHE="${PHP_CONF_DIR}/apache2"
ENV PHP_CONF_CLI="${PHP_CONF_DIR}/cli"
ENV APACHE_CONFDIR="/etc/apache2"
ENV SITES_AVAILABLE="${APACHE_CONFDIR}/sites-available"
ENV DIRECTORY_SITES="/var/www/html"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN apt -y update && apt -y upgrade
RUN apt install -y software-properties-common

RUN apt -y update && apt install --no-install-recommends -y \
	apt-utils \
    wget \
    gnupg \
    zip \
    unzip \
    curl \
    apt-transport-https \
    libxml2-dev \
    apache2 \
    apache2-utils

RUN apt -y update && apt -y install build-essential \
    libpng-dev \
    libzip-dev \
    libfreetype6-dev \
    locales \
    locales-all \
    jpegoptim optipng pngquant gifsicle \
    vim \
    supervisor \
    cron \
    freetds-dev \
    freetds-bin \
    freetds-common \
    tdsodbc \
    libct4 \
    libsybdb5 \
    libmcrypt-dev \
    imagemagick \
    poppler-utils

RUN apt -y install php \
    php-dev \
    php-imap \
    php-cli \
    php-bz2 \
    php-zip \
    php-curl \
    php-gd \
    php-mysql \
    php-mysqli \
    php-bcmath \
    php-xml \
    php-xmlrpc \
    php-mbstring \
    php-iconv \
    php-pdo \
    php-exif \
    php-interbase \
    php-imagick \
    php-tidy \
    php-pdo-dblib \
    php-pdo-mysql \
    php-snmp \
    php-redis \
    php-memcached \
    php-pear

# install pre requisites
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
RUN curl https://packages.microsoft.com/config/ubuntu/$(lsb_release -rs)/prod.list > /etc/apt/sources.list.d/mssql-release.list
RUN apt update
RUN ACCEPT_EULA=Y apt install -y msodbcsql18 mssql-tools unixodbc-dev
RUN apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# install driver sqlsrv
RUN pecl install sqlsrv-${SQLSRV_DRIVER_VERSION}
RUN printf "; priority=20\nextension=sqlsrv.so\n" > /etc/php/${PHP_VERSION}/mods-available/sqlsrv.ini
RUN pecl install pdo_sqlsrv-${SQLSRV_DRIVER_VERSION}
RUN printf "; priority=30\nextension=pdo_sqlsrv.so\n" > /etc/php/${PHP_VERSION}/mods-available/pdo_sqlsrv.ini

# install ODBC Driver
RUN echo 'export PATH="$PATH:/opt/mssql-tools18/bin"' >> ~/.bash_profile
RUN echo 'export PATH="$PATH:/opt/mssql-tools18/bin"' >> ~/.bashrc
RUN exec bash

RUN phpenmod -v ${PHP_VERSION} sqlsrv pdo_sqlsrv imap redis memcached

RUN ln -s /usr/lib/x86_64-linux-gnu/libsybdb.a /usr/lib/ \
    && ln -s /usr/lib/x86_64-linux-gnu/libsybdb.so /usr/lib/

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable apache2 modules
RUN a2enmod rewrite \
    ssl \
    headers \
    expires

# change directory to apache configuration
WORKDIR ${SITES_AVAILABLE}
COPY apache2/000-default.conf "${SITES_AVAILABLE}/000-default.conf"
COPY apache2/default-ssl.conf "${SITES_AVAILABLE}/default-ssl.conf"

# copy php.ini configuration
WORKDIR ${PHP_CONF_APACHE}
COPY phpconf/apache2/php.ini ${PHP_CONF_APACHE}/php.ini

RUN rm -rf apache2
RUN rm -rf Dockerfile
RUN rm -rf phpconf

# Change directory to sites folder and copy all
WORKDIR ${DIRECTORY_SITES}
RUN rm -rf index.html
COPY src/* ${DIRECTORY_SITES}

# Start Apache2 on image start.
ENTRYPOINT ["apache2ctl", "-D", "FOREGROUND"]
