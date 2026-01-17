#!/bin/bash
set -e

# 1. Jalankan Migrasi Database (RESET TOTAL)
echo "Wiping and Refreshing Database..."
php artisan migrate:fresh --force

# 2. Jalankan Seeder (Super Admin)
echo "Running Seeder..."
php artisan db:seed --class=SuperAdminSeeder --force

# 3. Optimasi Config & Route (Biar Ringan)
echo "Caching Configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Jalankan Web Server Apache (Production Grade)
echo "Starting Apache..."
exec apache2-foreground
