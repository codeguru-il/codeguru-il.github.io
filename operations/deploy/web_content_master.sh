#!/bin/bash

set -x
cd /tmp

MASTER="On branch master"
HTTPS_URL="https://source.developers.google.com/p/cdgws-183419/r/web_content"

if [ -d web_content ]; then
	pushd web_content
	SHOULD_BE_MASTER=`git status | grep "${MASTER}"`
	HTTPS_URL_COUNT=`git remote -v | grep "${HTTPS_URL}" | wc -l`
	popd
fi

if [ "${SHOULD_BE_MASTER}" = "${MASTER}" ] && [ "${HTTPS_URL_COUNT}" -eq 2 ]; then
	pushd web_content
	git checkout master
	git clean -d -x -f
	git pull origin master
	popd
else
	rm -rf web_content
	gcloud source repos clone web_content
fi
gsutil rsync -c -d -r -x '^[.]git.*' web_content/static gs://static.codeguru.co.il/
echo "Done"
