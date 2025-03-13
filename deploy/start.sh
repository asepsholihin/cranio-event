#!/bin/bash
set -e
php /var/www/artisan optimize:clear
php /var/www/artisan optimize

php /var/www/artisan migrate --force
php /var/www/artisan permission:config-sync
php /var/www/artisan permission:cache-reset

# Update nginx to match worker_processes to no. of cpu's
procs=$(cat /proc/cpuinfo | grep processor | wc -l)
sed -i -e "s/worker_processes  1/worker_processes $procs/" /etc/nginx/nginx.conf

# Always chown webroot for better mounting
chown -Rf nginx:nginx /var/www

# Start supervisord and services
/usr/local/bin/supervisord -n -c /etc/supervisord.conf