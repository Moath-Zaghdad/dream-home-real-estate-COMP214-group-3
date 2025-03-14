FROM php:8.2-apache
    MAINTAINER Moath Zaghdad <moath.zaghdad@pm.me>

RUN apt-get update && apt-get install -y \
    libaio1 \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html


COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 755 /var/www/html/

EXPOSE 80
