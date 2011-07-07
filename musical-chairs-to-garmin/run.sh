#!/bin/bash

wget -O matches-latest.csv.gz http://ris.dev.openstreetmap.org/oslmusicalchairs/data/matchdumps/matches-latest.csv.gz
php create-osm.php
./convert.sh
cp images/gmapsupp.img .
