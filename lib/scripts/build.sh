#!/bin/bash -x

# Set variables.
git fetch --tags
LATEST_TAG=$(git describe --tags --abbrev=0)

mkdir .tmp
if [ ! -f .tmp/$LATEST_TAG.tar.gz ]
  then
  wget https://github.com/verbruggenalex/appointment/releases/download/$LATEST_TAG/dist.tar.gz -O .tmp/$LATEST_TAG.tar.gz
fi

if [ ! -f .build/dist/$LATEST_TAG ]
  then
  mkdir -p build/dist/$LATEST_TAG
  tar -zxf .tmp/$LATEST_TAG.tar.gz -C build/dist/$LATEST_TAG
fi
