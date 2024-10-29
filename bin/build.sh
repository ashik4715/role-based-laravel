#!/usr/bin/env bash
. ./bin/parse_env.sh

docker build -f docker/Dockerfile.production -t wegro-farmer-new-service:latest .
