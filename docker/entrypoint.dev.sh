#!/bin/sh
set -e

echo "Installing Composer dependencies..."
if ! composer install --no-interaction --prefer-dist 2>/dev/null; then
    echo "Lock file incompatible, running composer update..."
    composer update --no-interaction --prefer-dist
fi

echo "Installing npm dependencies..."
npm install

echo "Clearing Laravel caches..."
php artisan config:clear || true

echo "Starting Laravel development server on port 8000..."
php artisan serve --host=0.0.0.0 --port=8000 &

echo "Starting Vite dev server with HMR on port 5173..."
exec npm run dev -- --host 0.0.0.0
