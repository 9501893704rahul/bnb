# Dockerfile for BnB Housekeeping Laravel Application
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy package files
COPY package.json package-lock.json ./

# Install Node.js dependencies
RUN npm ci

# Copy all application files
COPY . .

# Run composer scripts after copying all files
RUN composer dump-autoload --optimize

# Build frontend assets
RUN npm run build

# Create required directories
RUN mkdir -p storage/framework/{cache,sessions,views} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && mkdir -p storage/app/public

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Create startup script
COPY <<EOF /start.sh
#!/bin/bash
set -e

echo "Running migrations..."
php artisan migrate --force || echo "Migration failed, continuing..."

echo "Seeding roles..."
php artisan db:seed --class=SetupRolesAndPermissionsSeeder --force || echo "Seeding skipped"

echo "Caching config..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Creating storage link..."
php artisan storage:link || true

echo "Starting server on port \${PORT:-8080}..."
php artisan serve --host=0.0.0.0 --port=\${PORT:-8080}
EOF

RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
