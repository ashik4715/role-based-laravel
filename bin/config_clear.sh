#!/usr/bin/env bash

. ./bin/parse_env.sh

# shellcheck disable=SC2124
config_clear_script="docker exec -it --user www-data ${CONTAINER_NAME} php artisan config:clear"
eval "${config_clear_script}"
