# ==========================================================
# CONFIGURATION
# ==========================================================
ARG PHP_VERSION=8.5
ARG DEBIAN_VERSION=bookworm

# ==========================================================
# STAGE 1: BUILD
# ==========================================================
FROM php:${PHP_VERSION}-fpm-${DEBIAN_VERSION} AS builder

RUN apt-get -y update && apt-get -y install --no-install-recommends \
    gnupg \
    zip \
    unzip \
    ca-certificates \
    curl

RUN mkdir -p /etc/apt/keyrings
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg
RUN curl https://packages.microsoft.com/config/debian/12/prod.list | tee /etc/apt/sources.list.d/mssql-release.list

RUN apt-get -y update && apt-get -y install --no-install-recommends \
    libfreetype6-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libmagickwand-dev \
    libmemcached-dev \
    libpng-dev \
    libpq-dev \
    libwebp-dev \
    libzip-dev \
    zlib1g-dev \
    unixodbc-dev \
    $PHPIZE_DEPS

RUN ACCEPT_EULA=Y apt-get install -y --no-install-recommends msodbcsql18
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# install pie
RUN curl -fL --output /tmp/pie.phar https://github.com/php/pie/releases/latest/download/pie.phar
RUN mv /tmp/pie.phar /usr/local/bin/pie && chmod +x /usr/local/bin/pie

RUN docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
        --with-webp=/usr/include/ \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql

# install php native extensions
RUN docker-php-ext-install -j"$(nproc)" \
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
    sockets

# install additional extensions
RUN pie install microsoft/sqlsrv:5.13.2 --no-dev --no-interaction \
    && pie install microsoft/pdo_sqlsrv:5.13.2 --no-dev --no-interaction \
    && pie install imagick/imagick --no-dev --no-interaction \
    && pie install phpredis/phpredis --no-dev --no-interaction \
    && pie install php-memcached/php-memcached --no-dev --no-interaction \
    && pie install apcu/apcu --no-dev --no-interaction

# ==========================================================
# STAGE 2: PRODUCTION
# ==========================================================
FROM php:${PHP_VERSION}-fpm-${DEBIAN_VERSION} AS production

ENV ACCEPT_EULA=Y
ENV TZ="Asia/Jakarta"
ENV PHP_FPM_DIR="/usr/local/etc/php-fpm.d"
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

# Copy Microsoft repository configuration
COPY --from=builder /etc/apt/keyrings/microsoft.gpg /usr/share/keyrings/microsoft-prod.gpg
COPY --from=builder /etc/apt/sources.list.d/mssql-release.list /etc/apt/sources.list.d/mssql-release.list

# Install runtime dependencies only
RUN apt-get update && apt-get install -y --no-install-recommends \
    tzdata \
    ca-certificates \
    libfreetype6 \
    libicu72 \
    libjpeg62-turbo \
    libmagickwand-6.q16-6 \
    libmemcached11 \
    libpng16-16 \
    libpq5 \
    libwebp7 \
    libzip4 \
    unixodbc \
    msodbcsql18 \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    poppler-utils \
    && rm -rf /var/lib/apt/lists/*

# Copy PHP extensions
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
    
# Copy PHP extension configurations
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# PHP Configuration
COPY phpconf/php.ini ${PHP_INI_DIR}/php.ini

# PHP-FPM Configuration
COPY phpconf/www.conf ${PHP_FPM_DIR}/www.conf
    
WORKDIR ${PHP_INI_DIR}

RUN mkdir -p ${PHP_INI_DIR}/session
RUN chown -R www-data:www-data ${PHP_INI_DIR}/session
RUN mkdir -p ${PHP_INI_DIR}/temp
RUN chown -R www-data:www-data ${PHP_INI_DIR}/temp
RUN mkdir -p ${PHP_INI_DIR}/opcache
RUN chown -R www-data:www-data ${PHP_INI_DIR}/opcache

EXPOSE 9000

CMD ["php-fpm"]