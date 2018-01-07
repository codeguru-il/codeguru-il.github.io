#!/bin/bash

MY_DIR=$(dirname "$0")

apt-get install -y software-properties-common dirmngr
apt-key add "${MY_DIR}/shaggytwodope.gpg"
apt-add-repository 'deb http://shaggytwodope.github.io/repo ./'
# sudo apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 7086E9CC7EC3233B
apt-key update
apt-get update
apt-get install -y drive
