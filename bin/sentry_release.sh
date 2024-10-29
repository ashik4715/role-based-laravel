#!/usr/bin/env bash
. ./bin/parse_env.sh

if [ "${APP_ENV}" = production ]; then
  VERSION=$(sentry-cli releases propose-version)
  sentry-cli releases new -p admin "$VERSION"
  sentry-cli releases set-commits --auto "$VERSION"
  sentry-cli releases finalize "$VERSION"
  php artisan set-release-number --release="$VERSION"
fi
