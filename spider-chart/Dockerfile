FROM php:7.2.6-apache

RUN apt-get update -y && apt-get install -y libcurl4-gnutls-dev curl libpng-dev zlib1g-dev && apt-get clean -y

RUN docker-php-ext-install curl

RUN apt-get install -y libfreetype6-dev libmcrypt-dev libjpeg-dev

RUN docker-php-ext-configure gd --enable-gd-native-ttf --with-freetype-dir=/usr/include/freetype2 --with-png-dir=/usr/include --with-jpeg-dir=/usr/include && docker-php-ext-install gd

RUN docker-php-ext-install json

COPY .  /var/www/html

EXPOSE 80

COPY apache.conf /etc/apache2/conf-available/z-app.conf

RUN a2enconf z-app