FROM php:8.0.21-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first
COPY composer.json composer.lock ./

# Set environment variable
ENV APP_ENV=prod
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install dependencies (without dev)
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-scripts

# Copy the rest of the application
COPY . .

# Generate autoload files
RUN composer dump-autoload --optimize

# Fix permissions
RUN chown -R www-data:www-data /var/www/html

# Apache config
RUN a2enmod rewrite
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]