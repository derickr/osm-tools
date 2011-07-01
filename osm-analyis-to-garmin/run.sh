#!/bin/sh

mkdir -p ito-fetch
mkdir -p images

for i in `cat names.txt`; do
	php -dxdebug.max_nesting_level=200 fetch.php $i
done

php process-names.php > template.args

./convert.sh

cp images/gmapsupp.img .
