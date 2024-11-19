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

# Create non-root user
RUN useradd -G www-data,root -u 1000 -d /home/symfony symfony
RUN mkdir -p /home/symfony/.composer && \
    chown -R symfony:symfony /home/symfony

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY --chown=symfony:symfony . .

# Switch to non-root user
USER symfony

# Install dependencies
RUN composer install --no-scripts --no-dev

USER root
# Apache config
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/var

EXPOSE 80

CMD ["apache2-foreground"]