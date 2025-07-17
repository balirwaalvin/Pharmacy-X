#!/bin/bash

# DigitalOcean App Platform Build Script
echo "Starting build process for Pharmacy-X..."

# Install PHP dependencies if composer.json exists
if [ -f "composer.json" ]; then
    echo "Installing PHP dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
else
    echo "No composer.json found, skipping dependency installation"
fi

# Set proper permissions for upload directories
echo "Setting up directories..."
mkdir -p Images/PaymentSlips
mkdir -p Images/PrescriptionMessage
mkdir -p Images/PrescriptionOrders
mkdir -p Images/Profile_Pics

# Make directories writable
chmod -R 755 Images/

# Make scripts executable
chmod +x start.sh
chmod +x build.sh

# Set proper ownership (if running as root)
if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data Images/
fi

echo "Build completed successfully!"
