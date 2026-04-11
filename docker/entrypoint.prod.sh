#!/bin/bash
set -e

echo "=== G-REQUETES :: Production startup ==="

echo "[1/7] Fixing storage permissions..."
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "[2/7] Waiting for MySQL..."
until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --skip-ssl -e "SELECT 1" > /dev/null 2>&1; do
    sleep 2
    echo "  MySQL not ready yet, retrying..."
done
echo "  MySQL is ready!"

echo "[3/7] Running migrations..."
php artisan migrate --force

echo "[4/7] Seeding initial data (skipped if already seeded)..."
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "  Seeding database..."
    php artisan db:seed --force
else
    echo "  Already seeded (${USER_COUNT} users found). Skipping."
fi

echo "[5/7] Creating storage symlink..."
php artisan storage:link || true

echo "[6/7] Caching config, routes and views..."
php artisan package:discover --ansi || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[7/7] Starting application server..."
exec php artisan serve --host=0.0.0.0 --port=8080
