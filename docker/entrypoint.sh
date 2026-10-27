#!/bin/sh
set -eu

cd /var/www/html

mkdir -p database storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache
touch database/database.sqlite
chown -R www-data:www-data database storage bootstrap/cache

php artisan migrate --force
if [ "$(php artisan tinker --execute='echo App\Models\Product::count();')" = "0" ]; then
    php artisan products:import
fi
php artisan storage:link --relative || true
php artisan optimize

exec /usr/bin/supervisord -c /etc/supervisord.conf
