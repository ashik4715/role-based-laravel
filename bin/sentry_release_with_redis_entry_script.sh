#!/usr/bin/env bash
. ./bin/parse_env.sh

# shellcheck disable=SC2124
sentry_release_with_redis_entry_script="docker exec -it --user www-data ${CONTAINER_NAME} sh ./bin/sentry_release.sh"
eval "${sentry_release_with_redis_entry_script}"
