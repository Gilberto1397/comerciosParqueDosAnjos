FROM php:8.3-apache

# pdo_sqlite e fileinfo já vêm compiladas na imagem oficial; só garantimos a lib do SQLite
RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_sqlite

# Apache: DocumentRoot em /public e roteamento via front controller
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
# PHP: limites de upload e erros fora da tela
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html
COPY . /var/www/html

# Pastas graváveis (banco SQLite e uploads)
RUN mkdir -p storage public/uploads \
    && chown -R www-data:www-data storage public/uploads \
    && chmod -R 775 storage public/uploads

EXPOSE 80
