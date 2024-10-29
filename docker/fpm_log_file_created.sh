#!/usr/bin/env bash
dirname=/var/log/php

if [ ! -d "$dirname" ]
then
    mkdir -p $dirname
    touch $dirname/fpm-access.log
    touch $dirname/fpm-error.log
    chown -R www-data:www-data $dirname
else
    echo "File exists"
fi
