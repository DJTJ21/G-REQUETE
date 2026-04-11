# ── Stage 1 : Node – compile Vite assets ──────────────────────────
FROM node:20-alpine AS node_builder

WORKDIR /app

COPY package*.json ./
RUN npm ci --prefer-offline

COPY resources/ resources/
COPY vite.config.js ./
COPY tailwind.config.js ./
COPY postcss.config.js ./

RUN npm run build

# ── Stage 2 : PHP application ─────────────────────────────────────
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zip \
    unzip \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Inject compiled assets from Node stage
COPY --from=node_builder /app/public/build public/build

# Install PHP dependencies (--no-scripts : package:discover runs at entrypoint once .env is ready)
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts

# Bake php.ini into the image
COPY docker/php/php.ini /usr/local/etc/php/conf.d/local.ini

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Make both entrypoints executable
RUN chmod +x /var/www/html/docker/entrypoint.sh \
    && chmod +x /var/www/html/docker/entrypoint.prod.sh

EXPOSE 8080

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
