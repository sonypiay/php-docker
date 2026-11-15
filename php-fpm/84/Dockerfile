# ==========================================================
# CONFIGURATION
# ==========================================================
ARG PHP_VERSION=8.4
ARG DEBIAN_VERSION=bookworm
ARG PHP_NAME=fpm
ARG MICROSOFT_PACKAGE_URL=https://packages.microsoft.com
ARG MICROSOFT_KEYS_PATH=/usr/share/keyrings/microsoft-prod.gpg
ARG MICROSOFT_RELEASE_PACKAGE_PATH=/etc/apt/sources.list.d/mssql-release.list
ARG MICROSOFT_OS_PACKAGE=debian/12
ARG MICROSOFT_SQLSRV_VERSION=5.13.0
ARG MICROSOFT_ODBCSQL_NAME=msodbcsql17

# ==========================================================
# STAGE 1: BUILD
# ==========================================================
FROM php:${PHP_VERSION}-${PHP_NAME}-${DEBIAN_VERSION} AS builder

ENV ACCEPT_EULA=Y

# Inherit global scope variable
ARG MICROSOFT_PACKAGE_URL
ARG MICROSOFT_KEYS_PATH
ARG MICROSOFT_RELEASE_PACKAGE_PATH
ARG MICROSOFT_OS_PACKAGE
ARG MICROSOFT_SQLSRV_VERSION
ARG MICROSOFT_ODBCSQL_NAME

RUN apt-get -y update && apt-get -y install --no-install-recommends \
    gnupg \
    zip \
    unzip \
    ca-certificates \
    curl

RUN mkdir -p /etc/apt/keyrings
RUN curl -fsSL $MICROSOFT_PACKAGE_URL/keys/microsoft.asc | gpg --dearmor -o $MICROSOFT_KEYS_PATH
RUN curl $MICROSOFT_PACKAGE_URL/config/$MICROSOFT_OS_PACKAGE/prod.list | tee $MICROSOFT_RELEASE_PACKAGE_PATH

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

RUN ACCEPT_EULA=Y apt-get install -y --no-install-recommends $MICROSOFT_ODBCSQL_NAME
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
    sockets \
    opcache

# install additional extensions
RUN pie install microsoft/sqlsrv:$MICROSOFT_SQLSRV_VERSION --no-dev --no-interaction \
    && pie install microsoft/pdo_sqlsrv:$MICROSOFT_SQLSRV_VERSION --no-dev --no-interaction \
    && pie install imagick/imagick --no-dev --no-interaction \
    && pie install phpredis/phpredis --no-dev --no-interaction \
    && pie install php-memcached/php-memcached --no-dev --no-interaction \
    && pie install apcu/apcu --no-dev --no-interaction

# ==========================================================
# STAGE 2: PRODUCTION
# ==========================================================
FROM php:${PHP_VERSION}-${PHP_NAME}-${DEBIAN_VERSION} AS production

# inherit global scope variable
ARG MICROSOFT_KEYS_PATH
ARG MICROSOFT_RELEASE_PACKAGE_PATH
ARG MICROSOFT_ODBCSQL_NAME

ENV ACCEPT_EULA=Y
ENV TZ="Asia/Jakarta"
ENV PHP_FPM_DIR="/usr/local/etc/php-fpm.d"
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

# Copy Microsoft repository configuration
COPY --from=builder $MICROSOFT_KEYS_PATH $MICROSOFT_KEYS_PATH
COPY --from=builder $MICROSOFT_RELEASE_PACKAGE_PATH $MICROSOFT_RELEASE_PACKAGE_PATH

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
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    poppler-utils

RUN ACCEPT_EULA=Y apt-get install -y --no-install-recommends $MICROSOFT_ODBCSQL_NAME
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

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