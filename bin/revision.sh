#!/usr/bin/env bash
. ./bin/parse_env.sh

# shellcheck disable=SC2124
script="docker exec ${CONTAINER_NAME} bash -c "
# shellcheck disable=SC2016
script+='"echo $(expr $(cat revision) + 1) > revision"'

eval "${script}"
