#!/bin/bash
#
# author: Samuel Amaziro

argLength=2
testPath=$2
basePath=$1
behat=vendor/behat/behat/bin/behat


if [ "$#" -ne "$argLength" ]
then
	echo "Invalid number of arguments"
else

	cd "$testPath"
	if [ "$?" -ne 0 ] 
	
	then 
		exit $?

	else

	testOutput=$(php $basePath$behat)
	echo "$testOutput"
	exit $?

	fi

fi
