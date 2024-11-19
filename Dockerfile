FROM php:8.0.21-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Create var directory and set permissions
RUN mkdir -p var
RUN chmod -R 777 var

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Apache config
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Set permissions for Apache
RUN chown -R www-data:www-data .

EXPOSE 80

CMD ["apache2-foreground"]