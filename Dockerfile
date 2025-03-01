FROM php:5.6-apache-stretch

ENV PORT=8080
ENV APP_ENV=prod
ENV SYMFONY_ENV=prod
ENV HTTPDUSER="www-data"
ENV COMPOSER_ALLOW_SUPERUSER=1

# add archived debian stretch repository for the time being to install php5.6
# @see: https://unix.stackexchange.com/questions/743839
RUN echo "deb http://archive.debian.org/debian stretch main" > /etc/apt/sources.list
RUN apt-get update && apt-get install -y \
    acl \
    wget \
    git  \
    libzip-dev && \
    rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-configure zip --with-libzip
RUN docker-php-ext-install -j "$(nproc)" pdo_mysql opcache zip

COPY docker/php/cloud.ini "$PHP_INI_DIR/conf.d/cloud.ini"

RUN sed -i "s/80/${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/project

COPY docker/apache/000-default.conf /etc/apache2/sites-available/

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
RUN composer self-update 1.9.0

COPY --link composer.* ./

RUN set -eux; \
	composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

COPY --link . /var/www/project/

RUN set -eux; \
	mkdir -p /var/www/project/var/logs/ /var/www/project/var/cache/; \
    composer dump-autoload --optimize --classmap-authoritative --no-dev; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync;

RUN setfacl -dR -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX var; \
    setfacl -R -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX var; \
    usermod -u 1000 www-data; \
    chown -R www-data:www-data /var/www/; \
    a2enmod rewrite; \
    a2enmod headers;
USER www-data

CMD ["apache2-foreground"]
