#! /bin/bash

target=$1
upper=`echo $target | sed 's/^\w/\U&/g'`

target_file="$target".class.php

cp template.class.php $target_file

sed -i -e 's/template/'$target'/g' $target_file
sed -i -e 's/Template/'$upper'/g' $target_file

