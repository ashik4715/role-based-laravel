#!/usr/bin/env bash

# shellcheck disable=SC2006
dangling_images_cmd=`docker images --filter "dangling=true" -q --no-trunc`

if [[ $dangling_images_cmd ]]
then
    docker rmi "$dangling_images_cmd"
fi
