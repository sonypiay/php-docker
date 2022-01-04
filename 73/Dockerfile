FROM php:7.3-apache

ENV TZ="Asia/Jakarta"
RUN date

ENV DIRECTORY_SITES="/var/www/html"
ENV DIRECTORY_APACHE="${APACHE_CONFDIR}/sites-available"
ENV CONFIG_APACHE="${DIRECTORY_APACHE}/000-default.conf"
ENV CONFIG_APACHE_SSL="${DIRECTORY_APACHE}/default-ssl.conf"

# Change directory to sites folder and copy all
WORKDIR ${DIRECTORY_SITES}
COPY src/* ${DIRECTORY_SITES}

# change directory to apache configuration
WORKDIR ${DIRECTORY_APACHE}
COPY apache2/000-default.conf ${CONFIG_APACHE}
COPY apache2/default-ssl.conf ${CONFIG_APACHE_SSL}

RUN rm -rf apache2
RUN rm -rf Dockerfile

# update packages
RUN apt-get -y update && apt-get -y upgrade

# install packages
RUN apt-get -y install build-essential \
    libpng-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    unzip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    git \
    curl \
    wget \
    supervisor \
    systemd

# Clean cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# install extension
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    zip \
    mbstring \
    exif \
    pcntl \
    gd

RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable apache2 modules
RUN a2enmod rewrite \
    ssl