<?php
namespace OSM;

class getInfo
{
	public static function getEditPoints( \SimpleXMLElement $sxe )
	{
		$points = array();
		$meta = array( 'latMin' => 90, 'latMax' => -90, 'lonMin' => 180, 'lonMax' => -90, 'tsMin' => PHP_INT_MAX, 'tsMax' => 0 );

		foreach ($sxe->modify as $modify)
		{
			foreach ( $modify->node as $node )
			{
				$points[] = array( (float) $node['lat'], (float) $node['lon'], strtotime( $node['timestamp'] ) );
				if ( (float) $node['lon'] < $meta['lonMin'] ) { $meta['lonMin'] = (float) $node['lon']; }
				if ( (float) $node['lon'] > $meta['lonMax'] ) { $meta['lonMax'] = (float) $node['lon']; }
				if ( (float) $node['lat'] < $meta['latMin'] ) { $meta['latMin'] = (float) $node['lat']; }
				if ( (float) $node['lat'] > $meta['latMax'] ) { $meta['latMax'] = (float) $node['lat']; }
				if ( (float) $node['timestamp'] < $meta['tsMin'] ) { $meta['tsMin'] = strtotime( $node['timestamp'] ); }
				if ( (float) $node['timestamp'] > $meta['tsMax'] ) { $meta['tsMax'] = strtotime( $node['timestamp'] ); }
			}
		}
		foreach ($sxe->create as $create)
		{
			foreach ( $create->node as $node )
			{
				$points[] = array( (float) $node['lat'], (float) $node['lon'], strtotime( $node['timestamp'] ) );
				if ( (float) $node['lon'] < $meta['lonMin'] ) { $meta['lonMin'] = (float) $node['lon']; }
				if ( (float) $node['lon'] > $meta['lonMax'] ) { $meta['lonMax'] = (float) $node['lon']; }
				if ( (float) $node['lat'] < $meta['latMin'] ) { $meta['latMin'] = (float) $node['lat']; }
				if ( (float) $node['lat'] > $meta['latMax'] ) { $meta['latMax'] = (float) $node['lat']; }
				if ( (float) $node['timestamp'] < $meta['tsMin'] ) { $meta['tsMin'] = strtotime( $node['timestamp'] ); }
				if ( (float) $node['timestamp'] > $meta['tsMax'] ) { $meta['tsMax'] = strtotime( $node['timestamp'] ); }
			}
		}
		return array( 'points' => $points, 'meta' => $meta );
	}
}
?>
