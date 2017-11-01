#!/bin/bash

if ! which gcsfuse; then
	~/scripts/provisioning/gcsfuse_install
fi

set -x
UID_STORED=$(id -u)
GID_STORED=$(id -g)
sudo gcsfuse --implicit-dirs --uid ${UID_STORED} --gid ${GID_STORED} -o allow_other static.codeguru.co.il ~/fuse_gcs
