FROM php:8.4-apache
RUN docker-php-ext-install pdo pdo_mysql
COPY config/php.ini "$PHP_INI_DIR/php.ini"
EXPOSE 80
