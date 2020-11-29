#!/bin/bash

# Set variables.
BACKUP_TIME=$(date +'%y%m%d%H%M%S')
BACKUP_DATE=$(date +'%y%m%d')
BACKUP_LOC=${APACHE_DOCUMENT_ROOT}/build/bak/${BACKUP_DATE}
TAG=$1

# Build codebase and set pre-production environment
rm -rf build/dist/${TAG} && mkdir -p build/dist/${TAG}
if [ ! -f dist.tar.gz ]; then wget https://github.com/verbruggenalex/appointment/releases/download/${TAG}/dist.tar.gz; fi
tar -zxf dist.tar.gz -C build/dist/${TAG}
ln -sfn dist/${TAG}/ ${PWD}/build/pre-production

# Backup production and import on pre-production
mkdir -p ${BACKUP_LOC}
drush @prod sql-dump --result-file=${BACKUP_LOC}/production-${BACKUP_TIME}.sql
ln -sfn ${BACKUP_LOC}/production-${BACKUP_TIME}.sql $(dirname ${BACKUP_LOC})/production-latest.sql
drush @pre-prod sql-drop -y
drush @pre-prod sql-create -y
drush @pre-prod sqlc < ${BACKUP_LOC}/production-${BACKUP_TIME}.sql
drush @pre-prod cache-rebuild

# Cleanup when successful.
rm -rf dist.tar.gz
