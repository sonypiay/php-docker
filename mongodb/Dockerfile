FROM mongo:5.0.5

ENV TZ="Asia/Jakarta"
ENV MONGO_INITDB_ROOT_USERNAME=eannovate
ENV MONGO_INITDB_ROOT_PASSWORD=eannovate88!
ENV MONGO_INITDB_DATABASE=sample_database

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

EXPOSE 27017