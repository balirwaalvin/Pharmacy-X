# Use the official PHP image with Apache
FROM php:8.1-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy all project files into the Apache web root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Install dependencies (optional: unzip and git)
RUN apt-get update && apt-get install -y unzip git \
    # Install Composer (optional, in case you need it later)
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    # && composer install --no-dev --optimize-autoloader  # ❌ Commented out: causes error if no composer.json

# Set permissions (optional, for better compatibility)
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 (default for Apache)
EXPOSE 80
