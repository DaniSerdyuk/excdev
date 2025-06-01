#!/bin/bash

set -e

echo "Starting Docker containers..."
docker compose up --build -d

echo "Ensuring .env exists..."
docker exec -ti app php -r "file_exists('.env') || copy('.env.example', '.env');"

echo "Installing Composer dependencies..."
docker exec -ti app composer install

echo "Running migrations..."
docker exec -ti app php artisan migrate --seed

echo "Done."
