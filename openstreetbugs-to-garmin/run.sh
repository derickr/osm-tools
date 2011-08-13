#!/bin/bash

wget -O osb.gpx 'http://openstreetbugs.schokokeks.org/api/0.1/getGPX?b=49.162&t=60.85&l=-13.41&r=1.769&open=yes&limit=100000'
php convert.php
./convert.sh
cp images/gmapsupp.img .
