FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring xml zip bcmath ctype \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction
COPY . .
RUN composer run-script post-autoload-dump 2>/dev/null || true
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chmod -R 775 storage bootstrap/cache

# Fix PHP 8.4 type error in ServeCommand (string + int)
RUN sed -i 's/\$port + \$this->portOffset/(int)$port + $this->portOffset/' vendor/laravel/framework/src/Illuminate/Foundation/Console/ServeCommand.php

CMD ["sh", "-c", "php artisan migrate --force; exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
