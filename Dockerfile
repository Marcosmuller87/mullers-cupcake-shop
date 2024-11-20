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

# Copy files
COPY . .

# Create var directory with permissions
RUN mkdir -p var && chmod 777 -R var
RUN mkdir -p public/uploads && chmod 777 -R public/uploads

# Install dependencies and generate autoload
RUN composer install --no-dev --optimize-autoloader
RUN composer dump-autoload --optimize

# Apache config
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Set final permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD apache2-foreground