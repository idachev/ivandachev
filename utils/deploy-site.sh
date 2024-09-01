#!/bin/bash
[ "$1" = -x ] && shift && set -x
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

set -e

cd "${DIR}/.."

LOCAL_SOURCE_DIR=./_site

ENV_FILE="${DIR}/.env"
if [ -f "${ENV_FILE}" ]; then
  set -a
  source "${ENV_FILE}"
  set +a
fi

if [ -z "${SFTP_HOST}" ]; then
  echo "SFTP_HOST is not set"
  exit 1
fi

echo "SFTP_HOST: ${SFTP_HOST}"

if [ -z "${SFTP_USER}" ]; then
  echo "SFTP_USER is not set"
  exit 1
fi

echo "SFTP_USER: ${SFTP_USER}"

if [ -z "${SFTP_KEY_FILE}" ]; then
  if [ "${SFTP_KEY_VALUE}" ]; then
    SFTP_KEY_FILE="/tmp/random_$(date +%s%N)"

    echo "${SFTP_KEY_VALUE}" > "${SFTP_KEY_FILE}"

    chmod 600 "${SFTP_KEY_FILE}"
  else
    echo "SFTP_KEY_VALUE or SFTP_KEY_FILE is not set"
    exit 1
  fi
fi

echo "SFTP_KEY_FILE: ${SFTP_KEY_FILE}"

if [ -z "${SFTP_TARGET_DIR}" ]; then
  echo "SFTP_TARGET_DIR is not set"
  exit 1
fi

echo "SFTP_TARGET_DIR: ${SFTP_TARGET_DIR}"

cp -a ./static/. ${LOCAL_SOURCE_DIR}/

echo -e "\n\nFiles and folders to upload: ${LOCAL_SOURCE_DIR}\n"

ls -al ./_site

echo -e "\n\nUploading...\n"

lftp -v -c 'set sftp:connect-program "ssh -a -x -o StrictHostKeyChecking=no -p 10022 -i '"${SFTP_KEY_FILE}"'"; \
  connect sftp://'"${SFTP_USER}"':@'"${SFTP_HOST}"'; \
  mirror -vv -R --delete '${LOCAL_SOURCE_DIR}' '"${SFTP_TARGET_DIR}"'; \
  exit;'