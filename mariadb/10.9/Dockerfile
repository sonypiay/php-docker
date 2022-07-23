FROM mariadb:10.9-rc

ENV TZ="Asia/Jakarta"
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

# update packages
RUN apt-get update \
    && apt-get -y upgrade

RUN apt-get update && apt-get install --no-install-recommends -y \
    build-essential \
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
    cron \
    libzip-dev

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN apt-get autoremove -y

COPY local.cnf /etc/mysql/conf.d/local.cnf
RUN chmod 644 /etc/mysql/conf.d/local.cnf