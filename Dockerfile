FROM php:8.2-cli

# Install system dependencies and PHP extensions
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
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        zip \
        # Optional:
        pdo_pgsql

# Set the working directory
WORKDIR /var/www

# Copy project files (optional – only if you're building the image with code)
# COPY . .

# Set default command
CMD ["php", "/var/www/server.php"]
