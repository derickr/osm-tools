<?php
require dirname( __FILE__ ) . '/../lib/convert.php';

$f = simplexml_load_file($argv[1]);

$points = array();

// collect points
foreach ($f->modify as $modify)
{
	foreach ( $modify->node as $node )
	{
		$points[] = array( (float) $node['lat'], (float) $node['lon'] );
	}
}
foreach ($f->create as $create)
{
	foreach ( $create->node as $node )
	{
		$points[] = array( (float) $node['lat'], (float) $node['lon'] );
	}
}

// points to tiles
$expireTiles = array();
foreach ( $points as $point )
{
	list( $x, $y ) = tile_number( 14, $point[0], $point[1] );
	$expireTiles["14;$x;$y"] = true;
}

$tiles = array();
foreach ( $expireTiles as $expire => $dummy )
{
	list( $zoom, $x, $y ) = explode( ';', $expire );
	$tiles = array_merge( $tiles, tile_pyramid( $zoom, $x-1, $y-1, 1, 18 ) );
	$tiles = array_merge( $tiles, tile_pyramid( $zoom, $x  , $y-1, 1, 18 ) );
	$tiles = array_merge( $tiles, tile_pyramid( $zoom, $x+1, $y-1, 1, 18 ) );
	$tiles = array_merge( $tiles, tile_pyramid( $zoom, $x-1, $y  , 1, 18 ) );
	$tiles = array_merge( $tiles, tile_pyramid( $zoom, $x  , $y  , 1, 18 ) );
	$tiles = array_merge( $tiles, tile_pyramid( $zoom, $x+1, $y  , 1, 18 ) );
	$tiles = array_merge( $tiles, tile_pyramid( $zoom, $x-1, $y+1, 1, 18 ) );
	$tiles = array_merge( $tiles, tile_pyramid( $zoom, $x  , $y+1, 1, 18 ) );
	$tiles = array_merge( $tiles, tile_pyramid( $zoom, $x+1, $y+1, 1, 18 ) );
}

foreach ( $tiles as $tile )
{
	echo $tile, "\n";
}
