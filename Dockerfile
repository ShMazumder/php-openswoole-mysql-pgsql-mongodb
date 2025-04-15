FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    curl \
    gnupg \
    && pecl install openswoole \
    && docker-php-ext-enable openswoole \
    && docker-php-ext-install pdo pdo_mysql zip

WORKDIR /var/www

CMD ["php", "/var/www/min.php"]
