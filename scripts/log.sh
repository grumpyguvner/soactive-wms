#!/bin/bash

# This script should be included at the top of BASH scripts to change to log folder
# e.g. write at the top of the script
# 	source ./log.sh

dir="$(dirname "$(pwd)")/logs"
if [ ! -d "$dir" ]; then
    mkdir -p $dir
fi
if [ ! -d "$dir" ]; then
    exit
fi

cd $dir
