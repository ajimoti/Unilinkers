#!/bin/bash

# Get user input for database configuration
read -p "Enter the database name (DB_DATABASE): " db_database
read -p "Enter the database username (DB_USERNAME): " db_username
read -s -p "Enter the database password (DB_PASSWORD): " db_password
echo ""

# Copy the .env.example file
cp .env.example .env

# Set the database configuration in the .env file
sed -i '' "s/DB_DATABASE=.*/DB_DATABASE=${db_database}/" .env
sed -i '' "s/DB_USERNAME=.*/DB_USERNAME=${db_username}/" .env
sed -i '' "s/DB_PASSWORD=.*/DB_PASSWORD=${db_password}/" .env

# Generate an application key
php artisan key:generate

# Run database migrations and seed the database
php artisan migrate

# Display a message indicating setup completion
echo "Setup complete!"

# Run the application
php artisan serve
