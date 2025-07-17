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

# Set ServerName to suppress Apache warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock* ./

# Install PHP dependencies (if any)
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader --no-interaction; fi

# Copy all project files
COPY . .

# Replace default Apache configuration with our custom configuration
COPY ports.conf /etc/apache2/ports.conf
COPY apache.conf /etc/apache2/conf-available/pharmacy.conf

# Update default site to use port 8080
RUN sed -i 's/:80/:8080/g' /etc/apache2/sites-available/000-default.conf

# Enable our custom configuration
RUN a2enconf pharmacy

# Create upload directories and set proper permissions
RUN mkdir -p Images/PaymentSlips \
    Images/PrescriptionMessage \
    Images/PrescriptionOrders \
    Images/Profile_Pics \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 Images/

# Make start script executable
RUN chmod +x start.sh

# Expose port 8080 (DigitalOcean App Platform standard)
EXPOSE 8080

# Use our startup script
CMD ["./start.sh"]
