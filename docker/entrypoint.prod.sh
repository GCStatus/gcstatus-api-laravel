#!/usr/bin/env bash

composer install --no-dev --optimize-autoloader
php artisan migrate --force
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
