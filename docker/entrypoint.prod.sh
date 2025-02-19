#!/usr/bin/env bash

composer install --no-dev --no-interaction --optimize-autoloader
php artisan migrate --force
php artisan optimize
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
