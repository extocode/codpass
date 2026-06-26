# codPass local development image
# PHP 8.4 + Apache. Docroot is the repo root (index.php / api.php front controllers).
FROM php:8.4-apache

# System libs needed by the PHP extensions below.
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libxml2-dev \
        libzip-dev \
        zlib1g-dev \
        libldap2-dev \
        gettext \
        libicu-dev \
        unzip \
        git \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions required by codPass (composer.json) plus ldap (per dev setup).
# dom, json, libxml, mbstring, fileinfo are bundled/enabled in the base image.
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure ldap --with-libdir=lib/$(dpkg-architecture -q DEB_HOST_MULTIARCH) \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        gd \
        gettext \
        ldap

# Composer (copied from official image).
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apache: docroot is the project root. Real files (assets under public/) are
# served directly; everything else falls back to index.php — mirrors router.php.
ENV APACHE_DOCUMENT_ROOT=/var/www/html
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php/codpass.ini /usr/local/etc/php/conf.d/zz-codpass.ini
RUN a2enmod rewrite

WORKDIR /var/www/html

# Source is bind-mounted in dev (see docker-compose.yml); nothing copied here.
# The writable dirs are owned by the Apache user via the entrypoint.
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
