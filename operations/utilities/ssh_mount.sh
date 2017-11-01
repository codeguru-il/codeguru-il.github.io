set -x
START_UID=$(id -u)
START_GID=$(id -g)
if ! which sshfs; then
	sudo apt-get -f install sshfs
fi

if [ "$1" == "-d" ]; then
	DEBUG=",ssh_command=ssh -vv,sshfs_debug,debug"
fi

sudo sshfs uri@forum.codeguru.co.il: ~/fuse_ssh -o "IdentityFile=/home/uri/.ssh/web_provisional,uid=${START_UID},gid=${START_GID},allow_other$DEBUG"
