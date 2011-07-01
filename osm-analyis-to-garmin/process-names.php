<?php
$baseMapId = 63249000;

$file = file('names.txt');

foreach ($file as $id => $area) {
	$area = trim( $area );
	$mapId = $baseMapId + $id;
	$niceName = substr(urldecode( $area ), 0, 36);
	echo <<<ENDF
mapname: $mapId
description: OSM Analysis: $niceName
input-file: ito-fetch/$area.osm


ENDF;
}
