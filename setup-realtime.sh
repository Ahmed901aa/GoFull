#!/bin/bash
# ─── GoFull Real-Time Setup Script ───────────────────────────
# Run this from the GoFull project root directory

set -e

echo "📦 Installing broadcasting dependencies..."
composer require laravel/reverb pusher/pusher-php-server

echo ""
echo "🔧 Installing Reverb..."
php artisan install:broadcasting

echo ""
echo "🗄️ Running migrations (Reverb tables)..."
php artisan migrate

echo ""
echo "🌱 Seeding test orders & ratings..."
php artisan db:seed --class=TestOrdersAndRatingsSeeder

echo ""
echo "✅ Real-time setup complete!"
echo ""
echo "To start everything, run:"
echo "  composer run dev"
echo ""
echo "Or start Reverb separately:"
echo "  php artisan reverb:start --debug"
echo ""
echo "WebSocket server will be available at: ws://0.0.0.0:8080"
echo "Flutter app should connect to: ws://<YOUR_IP>:8080"
