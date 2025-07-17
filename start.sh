#!/bin/bash

# DigitalOcean App Platform startup script
echo "Starting Pharmacy-X application..."

# Set ServerName to suppress Apache warning
echo "ServerName pharmacy-x.ondigitalocean.app" >> /etc/apache2/apache2.conf

# Enable required Apache modules
a2enmod rewrite
a2enmod deflate
a2enmod headers

# Set proper permissions for upload directories
chmod 755 /var/www/html/Images/PaymentSlips
chmod 755 /var/www/html/Images/PrescriptionMessage
chmod 755 /var/www/html/Images/PrescriptionOrders
chmod 755 /var/www/html/Images/Profile_Pics

# Change ownership to www-data
chown -R www-data:www-data /var/www/html

# Start Apache
echo "Starting Apache server on port 80..."
exec apache2-foreground
