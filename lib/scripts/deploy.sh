#!/bin/bash -x

# Set variables.
BACKUP_TIME=$(date +'%y%m%d%H%M%S')
BACKUP_DATE=$(date +'%y%m%d')
BUILD_DIR=${APACHE_DOCUMENT_ROOT}/build
BACKUP_DIR=${BUILD_DIR}/bak/${BACKUP_DATE}
TAG=$1

# Build codebase and set pre-production environment
rm -rf build/dist/${TAG} && mkdir -p build/dist/${TAG}
if [ ! -f dist.tar.gz ]; then wget https://github.com/verbruggenalex/appointment/releases/download/${TAG}/dist.tar.gz; fi
tar -zxf dist.tar.gz -C build/dist/${TAG}
ln -sfn ${BUILD_DIR}/dist/${TAG}/ ${BUILD_DIR}/pre-production

# Backup production and import on pre-production
mkdir -p ${BACKUP_DIR}
drush @prod sql-dump --result-file=${BACKUP_DIR}/production-${BACKUP_TIME}.sql
ln -sfn ${BACKUP_DIR}/production-${BACKUP_TIME}.sql $(dirname ${BACKUP_DIR})/production-latest.sql
drush @pre-prod sql-drop -y
drush @pre-prod sql-create -y
drush @pre-prod sqlc < ${BACKUP_DIR}/production-${BACKUP_TIME}.sql
drush @pre-prod cache-rebuild

# Cleanup when successful.
rm -rf dist.tar.gz
