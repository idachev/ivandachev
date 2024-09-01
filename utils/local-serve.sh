#!/bin/bash
[ "$1" = -x ] && shift && set -x
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

set -e

cd "${DIR}/.."

BUILD_VERSION=$(date -u '+%Y-%m-%d')-$(git rev-parse --short HEAD)

rm ./_config-build.yml || true
echo -e "build_version: ${BUILD_VERSION}" > ./_config-build.yml

bundle exec jekyll serve \
  --config _config.yml,_config-build.yml

