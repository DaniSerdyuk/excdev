#!/bin/bash

set -e

echo "Ensuring .env exists..."
cp .env.example .env

echo "Starting Docker containers..."
docker compose up --build -d

echo "Installing Composer dependencies..."
docker exec -ti app composer install

echo "Running migrations..."
docker exec -ti app php artisan migrate --seed

echo "Done."
