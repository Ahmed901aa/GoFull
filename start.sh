#!/bin/sh

# Fix PHP 8.4 type error
sed -i 's/$port + $this->portOffset/(int)$port + $this->portOffset/' vendor/laravel/framework/src/Illuminate/Foundation/Console/ServeCommand.php 2>/dev/null

# Run migrations and seed
php artisan migrate --force 2>/dev/null
php artisan db:seed --force 2>/dev/null
php artisan route:clear 2>/dev/null
php artisan config:clear 2>/dev/null

# Start server
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
