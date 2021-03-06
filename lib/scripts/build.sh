#!/bin/bash -x

# Set variables.
TAG=$1

mkdir -p .tmp
if [ ! -d build/dist/$TAG ] && [ ! -f .tmp/$TAG.tar.gz ]
  then
  wget https://github.com/verbruggenalex/appointment/releases/download/$TAG/dist.tar.gz -O .tmp/$TAG.tar.gz
fi

if [ ! -f .tmp/$TAG.sql.gz ] && [ ! -f .tmp/$TAG.sql ]
  then
  wget https://github.com/verbruggenalex/appointment/releases/download/$TAG/clean.sql.gz -O .tmp/$TAG.sql.gz
  gunzip .tmp/$TAG.sql.gz
fi

if [ ! -d build/dist/$TAG ]
  then
  mkdir -p build/dist/$TAG
  tar -zxf .tmp/$TAG.tar.gz -C build/dist/$TAG
  ln -sfn $APACHE_DOCUMENT_ROOT/build/dist/$TAG $APACHE_DOCUMENT_ROOT/build/production
  rm -rf $APACHE_DOCUMENT_ROOT/build/dist/$TAG/web/sites/default/files
  ln -sf $APACHE_DOCUMENT_ROOT/build/files/public $APACHE_DOCUMENT_ROOT/build/dist/$TAG/web/sites/default/files
  composer reset-permissions -d $APACHE_DOCUMENT_ROOT/build/production
fi

if [ -f .tmp/$TAG.sql ]
  then
  drush cc drush
  drush @pre-prod sql-create -y
  drush @pre-prod sql-drop -y
  drush @pre-prod sqlc < $APACHE_DOCUMENT_ROOT/.tmp/$TAG.sql
  drush @pre-prod cr
  drush @pre-prod en app_default_content -y
  drush @prod sql-create -y
  drush @prod sql-drop -y
  drush @prod sqlc < $APACHE_DOCUMENT_ROOT/.tmp/$TAG.sql
  drush @prod cr
  drush @prod en app_default_content -y
fi
