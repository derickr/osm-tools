<?php
require '../lib/osm-writer.php';

$osm = new OSMWriter;
$osm->xmlHeader();
$osm->openOsm("0.6", "osm-tools");

$f = fopen("compress.zlib://matches-latest.csv.gz", 'r');

while( false !== ($line = fgetcsv($f) ) )
{
	if ( $line[1] == '' && $line[2] == '' )
	{
		continue;
	}
	if ( ( strlen( trim( $line[8] ) ) > 0 || strlen( trim( $line[9] ) ) > 0 ) && $line[5] == 0 )
	{
		continue;
	}
	if ( strlen( trim( $line[8] ) ) == 0 && strlen( trim( $line[9] ) ) == 0 )
	{
		$osm->openNode( $line[4], $line[3] );
		$missing = strlen( $line[1] ) ? $line[1] : $line[2];
		$osm->writeTag( 'osmanalysis', 'missing' );
		$osm->writeTag( "name", htmlspecialchars( "{$missing}", ENT_QUOTES ) );
		$osm->writeTag( "addr:street", htmlspecialchars( "OSM has nothing", ENT_QUOTES ) );
		$osm->closeNode();
		continue;
	}
	else if ( strlen( trim( $line[2] ) ) != 0 && strlen( trim( $line[9] ) ) == 0 )
	{
		$osm->openNode( $line[4], $line[3] );
		$osm->writeTag( 'osmanalysis', 'missing' );
		$osm->writeTag( "name", htmlspecialchars( "{$line[2]}", ENT_QUOTES ) );
		$osm->writeTag( "addr:street", htmlspecialchars( "OSM has nothing", ENT_QUOTES ) );
		$osm->closeNode();
		continue;
	}
	else if ( strlen( trim( $line[1] ) ) != 0 && strlen( trim( $line[8] ) ) == 0 )
	{
		$osm->openNode( $line[4], $line[3] );
		$osm->writeTag( 'osmanalysis', 'missing' );
		$osm->writeTag( "name", htmlspecialchars( "{$line[1]}", ENT_QUOTES ) );
		$osm->writeTag( "addr:street", htmlspecialchars( "OSM has nothing", ENT_QUOTES ) );
		$osm->closeNode();
		continue;
	}
	if ( strlen( trim( $line[2] ) ) != 0 && ( $line[2] != $line[9] ) )
	{
		$osm->openNode( $line[4], $line[3] );
		$osm->writeTag( 'osmanalysis', 'missing' );
		$osm->writeTag( "name", htmlspecialchars( "{$line[2]}", ENT_QUOTES ) );
		$osm->writeTag( "addr:street", htmlspecialchars( "OSM has nothing", ENT_QUOTES ) );
		$osm->closeNode();

		$osm->openNode( $line[4], $line[3] );
		$osm->writeTag( 'osmanalysis', 'missing' );
		$osm->writeTag( "name", htmlspecialchars( "{$line[9]}", ENT_QUOTES ) );
		$osm->writeTag( "addr:street", htmlspecialchars( "OSM has nothing", ENT_QUOTES ) );
		$osm->closeNode();
	}

	{
		$expected = strlen( $line[1] ) ? $line[1] : $line[2];
		$real     = strlen( $line[8] ) ? $line[8] : $line[9];
		$osm->openNode( $line[4], $line[3] );
		$osm->writeTag( 'osmanalysis', 'mismatch' );
		$osm->writeTag( 'osmanalysislevel', $line[5] );
		$osm->writeTag( "name", htmlspecialchars( "{$expected}", ENT_QUOTES ) );
		$osm->writeTag( "addr:street", htmlspecialchars( "OSM has {$real}", ENT_QUOTES ) );
		$osm->closeNode();
		$osm->openNode( $line[4], $line[3] );
		$osm->writeTag( 'osmanalysis', 'mismatch' );
		$osm->writeTag( 'osmanalysislevel', $line[5] );
		$osm->writeTag( "name", htmlspecialchars( "{$real}", ENT_QUOTES ) );
		$osm->writeTag( "addr:street", htmlspecialchars( "OSL has {$expected}", ENT_QUOTES ) );
		$osm->closeNode();
	}
}

$osm->closeOsm();
file_put_contents('compress.zlib://musical-chairs.osm.gz', $osm->get());
