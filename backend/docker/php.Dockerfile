FROM php:8.3-apache
RUN docker-php-ext-install pdo pdo_mysql
COPY php/ /var/www/html/
RUN a2enmod rewrite
