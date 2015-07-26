#!/bin/sh

if [ -z "$1" ]; then
  echo "Usage: $0 module-name"
  echo "Example: $0 Aitoc_Aitcheckoutfields"
  exit 1
fi

if [ ! -e "_aitoc_filelists/$1.filelist" ]; then
  echo "Unknown module: $1"
  exit 1
fi

cat "_aitoc_filelists/$1.filelist" | xargs -r rm -f
