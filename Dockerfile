FROM php:8.4-fpm
LABEL authors="iwakur"
RUN  docker-php-ext-install \
    pdo_mysql \
   mysqli
WORKDIR /var/www/html
COPY . .
CMD ["php-fpm"]