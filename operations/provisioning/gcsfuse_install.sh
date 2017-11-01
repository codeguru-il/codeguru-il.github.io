LSB_RELEASE=$(lsb_release -c -s)
if [ -z "${LSB_RELEASE}" ]; then
    LSB_RELEASE=jessie
fi
export GCSFUSE_REPO=gcsfuse-"${LSB_RELEASE}"

echo "deb http://packages.cloud.google.com/apt $GCSFUSE_REPO main" | sudo tee /etc/apt/sources.list.d/gcsfuse.list
curl https://packages.cloud.google.com/apt/doc/apt-key.gpg | sudo apt-key add -
sudo apt-get update
sudo apt-get -f install gcsfuse
