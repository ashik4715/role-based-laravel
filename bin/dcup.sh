#!/usr/bin/env bash
compose_folder="./docker/composes"

compose="docker-compose --env-file=./.env -f $compose_folder/docker-compose.yml"

if [[ $1 = "prod" ]]; then
  extra="-f $compose_folder/docker-compose.prod.yml"
elif [[ $1 = "dev" ]]; then
  extra="-f $compose_folder/docker-compose.dev.yml"
elif [[ $1 = "stage" ]]; then
  extra="-f $compose_folder/docker-compose.stage.yml"
elif [[ $1 = "local" ]]; then
  extra="-f $compose_folder/docker-compose.local.yml"
fi

compose="$compose $extra up"

# shellcheck disable=SC2206
all_args=($@)
len=${#all_args[@]}
# shellcheck disable=SC2124
other_args=${all_args[@]:1:${len}-1}
compose="$compose $other_args"

eval "${compose}"
