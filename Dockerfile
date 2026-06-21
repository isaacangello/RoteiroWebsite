FROM wordpress:7.0.0-php8.3-apache

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libzip-dev \
        unzip \
        git \
        subversion \
    ; \
    docker-php-ext-install zip; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

RUN { \
    echo 'upload_max_filesize = 128M'; \
    echo 'post_max_size = 128M'; \
    echo 'memory_limit = 256M'; \
    echo 'max_execution_time = 300'; \
    echo 'date.timezone = America/Sao_Paulo'; \
} > /usr/local/etc/php/conf.d/roteiro.ini

RUN chown -R www-data:www-data /var/www/html

COPY --chown=www-data:www-data ./wp-content /var/www/html/wp-content
