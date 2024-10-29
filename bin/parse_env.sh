#!/usr/bin/env bash

# CHECK ANY ENV FILE LIKE master.env OR development.env FILE PRESENT
# IF EXIST, REMOVE
# shellcheck disable=SC2012
# shellcheck disable=SC2035
count=$(ls -1 *.env 2>/dev/null | wc -l)
if [ "$count" != 0 ]; then
  mv *.env .env
fi

# shellcheck disable=SC2046
export $(egrep -v '^#' .env | xargs)
