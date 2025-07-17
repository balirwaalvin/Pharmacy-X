# Use official PHP with Apache
FROM php:8.1-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install required PHP extensions
RUN docker-php-ext-install mysqli

# Optional: Install unzip, git, and composer (commented to avoid composer.json error)
RUN apt-get update && apt-get install -y unzip git \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    # && composer install --no-dev --optimize-autoloader

# Set working directory
WORKDIR /var/www/html

# Copy everything into web root
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
