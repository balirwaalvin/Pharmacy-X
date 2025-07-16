# Use the official PHP image with Apache (or CLI if you prefer)
FROM php:8.2-apache

# Copy your source code into the container
COPY . /var/www/html/

# If you use Composer, install it and your dependencies
RUN apt-get update && apt-get install -y unzip git \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

# Enable Apache rewrite module (optional, useful for frameworks like Laravel)
RUN a2enmod rewrite

# Expose port 80 for the web server
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
