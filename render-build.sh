#!/usr/bin/env bash
# Render Build Script for Laravel Application

set -e

echo "==> Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "==> Installing Node.js dependencies..."
npm ci

echo "==> Building frontend assets..."
npm run build

echo "==> Setting up storage directories..."
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

echo "==> Setting permissions..."
chmod -R 775 storage bootstrap/cache

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Seeding roles and permissions..."
php artisan db:seed --class=SetupRolesAndPermissionsSeeder --force

echo "==> Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Creating storage link..."
php artisan storage:link || true

echo "==> Build complete!"
