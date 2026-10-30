FROM php:8.3-apache
 
RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_sqlite
 
WORKDIR /var/www/html

EXPOSE 80
 