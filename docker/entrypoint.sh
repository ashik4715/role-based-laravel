#!/usr/bin/env bash

php artisan optimize
php artisan config:cache
php artisan view:cache
php artisan route:clear

supervisord
service nginx start
service cron start
php-fpm
