#!/bin/bash

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Cache config & routes for performance
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Run migrations (non-fatal if nothing to migrate)
php artisan migrate --force || true

# Set correct permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Ensure log directories exist
mkdir -p /var/log/nginx /var/log/php-fpm /var/log/supervisor

# Render provides $PORT — substitute it into nginx config
export NGINX_PORT="${PORT:-10000}"
envsubst '${NGINX_PORT}' < /etc/nginx/sites-available/default > /tmp/nginx_rendered.conf
cp /tmp/nginx_rendered.conf /etc/nginx/sites-available/default

# Start supervisor (manages nginx + php-fpm)
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
