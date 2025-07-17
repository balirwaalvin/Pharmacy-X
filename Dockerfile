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

# Enable Apache modules
RUN a2enmod rewrite deflate headers

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock* ./

# Install PHP dependencies (if any)
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader --no-interaction; fi

# Copy Apache configuration
COPY apache.conf /etc/apache2/conf-available/pharmacy.conf
RUN a2enconf pharmacy

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

# Set ServerName to suppress Apache warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Expose port 80 (standard Apache port)
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
