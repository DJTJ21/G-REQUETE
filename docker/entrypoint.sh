#!/bin/bash
set -e

echo "Fixing storage permissions..."
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "Waiting for MySQL to be ready..."
until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --skip-ssl -e "SELECT 1" > /dev/null 2>&1; do
    sleep 2
    echo "MySQL not ready yet, retrying..."
done
echo "MySQL is ready!"

echo "Running migrations..."
php artisan migrate --force

echo "Checking if seeding is needed..."
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "Seeding database..."
    php artisan db:seed --force
fi

echo "Creating storage link..."
php artisan storage:link || true

echo "Discovering packages..."
php artisan package:discover --ansi || true

echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear

echo "Starting application server..."
exec php artisan serve --host=0.0.0.0 --port=8080
