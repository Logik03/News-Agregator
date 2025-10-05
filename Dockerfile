# Use official PHP image with Apache or CLI
FROM php:8.4-cli

# Arguments for environment
ARG USER=www-data
ARG UID=1000
ARG GID=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set permissions for Laravel storage and bootstrap/cache
RUN chown -R $USER:$USER /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port (for php artisan serve)
EXPOSE 8000

# Default command (can be overridden in docker-compose.yml)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
