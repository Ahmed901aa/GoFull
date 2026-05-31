FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring xml zip bcmath ctype \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first (for caching)
COPY composer.json ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy rest of the application
COPY . .

# Run post-install scripts
RUN composer run-script post-autoload-dump 2>/dev/null || true

# Create storage directories
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 8000

# Start command - use PHP built-in server directly (avoids ServeCommand type bug)
CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force; exec php -S 0.0.0.0:${PORT:-8000} -t public"]
