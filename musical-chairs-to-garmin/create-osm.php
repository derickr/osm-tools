<?php
require '../lib/osm-writer.php';

$osm = new OSMWriter;
$osm->xmlHeader();
$osm->openOsm("0.6", "osm-tools");

$f = fopen("compress.zlib://matches-latest.csv.gz", 'r');

while( false !== ($line = fgetcsv($f) ) )
{
	if ( $line[1] == '' )
	{
		continue;
	}
	if ( strlen( trim( $line[6] ) ) > 0 && $line[7] == 0 )
	{
		continue;
	}
	$osm->openNode( $line[3], $line[2] );
	if ( strlen( trim( $line[6] ) ) == 0 )
	{
		$osm->writeTag( 'osmanalysis', 'missing' );
		$osm->writeTag( "name", htmlspecialchars( "Missing: {$line[1]}", ENT_QUOTES ) );
	}
	else
	{
		$osm->writeTag( 'osmanalysis', 'mismatch' );
		$osm->writeTag( 'osmanalysislevel', $line[7] );
		$osm->writeTag( "name", htmlspecialchars( "No match: {$line[1]} -> {$line[6]}", ENT_QUOTES ) );
	}
	$osm->closeNode();
}

$osm->closeOsm();
file_put_contents('compress.zlib://musical-chairs.osm.gz', $osm->get());
