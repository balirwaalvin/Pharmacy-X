FROM php:8.1-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock* ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy all project files
COPY . .

# Create upload directories and set permissions
RUN mkdir -p Images/PaymentSlips \
    Images/PrescriptionMessage \
    Images/PrescriptionOrders \
    Images/Profile_Pics \
    && chown -R www-data:www-data /var/www/html \
    && chmod 755 Images/PaymentSlips \
    Images/PrescriptionMessage \
    Images/PrescriptionOrders \
    Images/Profile_Pics

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
