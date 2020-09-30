#!/bin/bash

# Script to run a file full of BASH commands in parallel
# Usage :
# multiprocess file-full-of-bash-commands.sh"
# multiprocess  file-full-of-bash-commands.sh 16

if [[ $2 ]]; then
    NUM_PROCS=$2
    < $1 xargs -d '\n' -P $NUM_PROCS -I {} /bin/bash -c "{}"
else
    NUM_PROCS=`cat /proc/cpuinfo | awk '/^processor/{print $3}'| wc -l`
    < $1 xargs -d '\n' -P $NUM_PROCS -I {} /bin/bash -c "{}"
fi