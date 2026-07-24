#!/bin/bash
set -e

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Cache config & routes for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Set correct permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Start supervisor (manages nginx + php-fpm)
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
