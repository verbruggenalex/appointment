#!/bin/bash -x

# Set variables.
git fetch --tags
LATEST_TAG=$(git describe --tags --abbrev=0)

mkdir .tmp
if [ ! -f .tmp/$LATEST_TAG.tar.gz ]
  then
  wget https://github.com/verbruggenalex/appointment/releases/download/$LATEST_TAG/dist.tar.gz -O .tmp/$LATEST_TAG.tar.gz
fi

if [ ! -f .tmp/$LATEST_TAG.sql.gz ]
  then
  wget https://github.com/verbruggenalex/appointment/releases/download/$LATEST_TAG/clean.sql.gz -O .tmp/$LATEST_TAG.sql.gz
fi

if [ ! -d build/dist/$LATEST_TAG ]
  then
  mkdir -p build/dist/$LATEST_TAG
  tar -zxf .tmp/$LATEST_TAG.tar.gz -C build/dist/$LATEST_TAG
  ln -sfn $APACHE_DOCUMENT_ROOT/build/dist/$LATEST_TAG $APACHE_DOCUMENT_ROOT/build/production
fi

if [ -f .tmp/$LATEST_TAG.sql.gz ]
  then
  drush cc drush
  drush @prod sql-create -y
  drush @prod sql-drop -y
  drush @prod sqlq --file=$APACHE_DOCUMENT_ROOT/.tmp/$LATEST_TAG.sql.gz
  drush @prod en app_default_content -y
fi
