#!/bin/bash
[ "$1" = -x ] && shift && set -x
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

set -e

cd "${DIR}/.."

BUILD_VERSION=$(date -u '+%Y-%m-%d')-$(git rev-parse --short HEAD)

rm ./_config-build.yml || true
echo -e "build_version: ${BUILD_VERSION}" > ./_config-build.yml

JEKYLL_ENV=production bundle exec jekyll build \
  --config _config.yml,_config-build.yml

echo -e "\n\nBuild complete:\n"
ls -al ./_site
