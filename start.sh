#!/bin/bash

# DigitalOcean App Platform startup script
echo "Starting Pharmacy-X application..."

# Ensure Apache modules are enabled
a2enmod rewrite deflate headers

# Set proper permissions for upload directories
chmod -R 755 /var/www/html/Images/
chown -R www-data:www-data /var/www/html

# Verify Apache configuration
echo "Testing Apache configuration..."
apache2ctl configtest

# Start Apache
echo "Starting Apache server on port 8080..."
exec apache2-foreground
