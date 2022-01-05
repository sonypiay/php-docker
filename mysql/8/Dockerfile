FROM mysql:8

ENV TZ="Asia/Jakarta"

# update packages
RUN apt-get -y update && apt-get -y upgrade

RUN apt-get -y install build-essential \
    libzip-dev \
    locales \
    zip \
    unzip \
    vim \
    git \
    curl \
    wget \
    supervisor \
    cron

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN apt-get autoremove -y

COPY local.cnf /etc/mysql/conf.d/local.cnf
RUN chmod 644 /etc/mysql/conf.d/local.cnf