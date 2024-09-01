#!/bin/bash
[ "$1" = -x ] && shift && set -x
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

set -e

cd "${DIR}/.."

bundle exec jekyll build

echo -e "\n\nBuild complete:\n"
ls -al ./_site
