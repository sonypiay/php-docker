FROM php:7.4-apache

COPY src/ /var/www/html
WORKDIR /var/www/html/

# Install dependecies
RUN apt-get update && apt-get upgrade -y \
    build-essential \
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
    supervisor

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

# Add group
RUN groupadd -g 1000 www

RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy application folder
COPY . /var/www/html

# Copy existing permission from folder to Docker
COPY --chown=www:www . /var/www/html
RUN chown -R www-data:www-data /var/www/html

# Enable apache2 modules
RUN a2enmod proxy \
    proxy_http \
    headers \
    env \
    rewrite \
    proxy_balancer \
    proxy_connect \
    ssl

# CMD [ "sudo", "/etc/init.d/apache2", "reload" ]

# Change current user to www
USER www