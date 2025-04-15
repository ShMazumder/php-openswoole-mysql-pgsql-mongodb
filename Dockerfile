# Use official PHP image with Alpine for a smaller image size
FROM php:8.2-fpm-alpine

# Install OpenSwoole and dependencies
RUN apk --no-cache add --virtual .build-deps \
        gcc \
        g++ \
        make \
        autoconf \
        libc-dev \
        bash \
        && pecl install openswoole \
        && docker-php-ext-enable openswoole \
        && apk del .build-deps

# Install PHP extensions required for database drivers
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Set working directory
WORKDIR /var/www

# Copy PHP files into the container
COPY ./src /var/www

# Expose OpenSwoole default HTTP port
EXPOSE 9501

# Command to run OpenSwoole
CMD ["php", "server.php"]