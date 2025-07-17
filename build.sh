#!/bin/bash

# DigitalOcean App Platform Build Script
echo "Starting build process for Pharmacy-X..."

# Install PHP dependencies if composer.json exists
if [ -f "composer.json" ]; then
    echo "Installing PHP dependencies..."
    composer install --no-dev --optimize-autoloader
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
chmod 755 Images/PaymentSlips
chmod 755 Images/PrescriptionMessage
chmod 755 Images/PrescriptionOrders
chmod 755 Images/Profile_Pics

echo "Build completed successfully!"
