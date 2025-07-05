#!/bin/bash

# VehicleFlow PHP Server Startup Script
# This script starts the PHP development server for XAMPP compatibility

echo "Starting VehicleFlow PHP Server..."
echo "Backend running on PHP CodeIgniter 3"
echo "Database: MySQL (XAMPP compatible)"
echo "Port: 5000"
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "Error: PHP is not installed"
    exit 1
fi

# Check if api directory exists
if [ ! -d "api" ]; then
    echo "Error: API directory not found"
    exit 1
fi

# Start PHP development server
echo "Starting PHP server at http://0.0.0.0:5000"
php -S 0.0.0.0:5000 -t api/public