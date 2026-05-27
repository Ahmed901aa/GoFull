#!/bin/bash
# ──────────────────────────────────────────────────
# GO FULL - Quick Start Script
# Run: bash start.sh
# ──────────────────────────────────────────────────

set -e

echo "🚀 Starting GO FULL Backend..."
echo ""

# 1. Check PHP
if ! command -v php &> /dev/null; then
    echo "❌ PHP not found. Install it first:"
    echo "   brew install php"
    exit 1
fi
echo "✅ PHP found: $(php -v | head -1)"

# 2. Check MySQL
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL not found. Install it first:"
    echo "   brew install mysql && brew services start mysql"
    exit 1
fi

# 3. Check if MySQL is running
if ! mysqladmin ping -u root --silent 2>/dev/null; then
    echo "⚠️  MySQL is not running. Starting it..."
    brew services start mysql
    sleep 2
fi
echo "✅ MySQL is running"

# 4. Create database if it doesn't exist
mysql -u root -e "CREATE DATABASE IF NOT EXISTS \`go-full\`;" 2>/dev/null
echo "✅ Database 'go-full' ready"

# 5. Install composer dependencies if needed
if [ ! -d "vendor" ]; then
    echo "📦 Installing dependencies..."
    composer install --no-interaction
fi
echo "✅ Dependencies installed"

# 6. Run migrations
echo "📋 Running migrations..."
php artisan migrate --no-interaction
echo "✅ Migrations done"

# 7. Seed the database
echo "🌱 Seeding database..."
php artisan db:seed --no-interaction
echo "✅ Database seeded"

# 8. Create storage symlink
php artisan storage:link 2>/dev/null || true
echo "✅ Storage link created"

# 9. Get local IP for mobile testing
LOCAL_IP=$(ipconfig getifaddr en0 2>/dev/null || echo "127.0.0.1")
echo ""
echo "──────────────────────────────────────────────"
echo "🟢 Server starting on:"
echo "   Local:   http://127.0.0.1:8000"
echo "   Network: http://${LOCAL_IP}:8000"
echo ""
echo "   API:     http://${LOCAL_IP}:8000/api"
echo "──────────────────────────────────────────────"
echo ""
echo "⚠️  Make sure your Flutter app's base URL matches:"
echo "   http://${LOCAL_IP}:8000/api"
echo ""

# 10. Start the server (accessible from network)
php artisan serve --host=0.0.0.0 --port=8000
