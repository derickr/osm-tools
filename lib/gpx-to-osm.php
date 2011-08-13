<?php
class GpxToOsm
{
	static function convert( $filename, OSMWriter $osm, $namePrefix = '' )
	{
		$osm->xmlHeader();
		$osm->openOsm("0.6", "osm-tools");

		$s = simplexml_load_file( $filename );
		foreach ( $s->wpt as $point )
		{
			$osm->openNode( $point['lat'], $point['lon'] );
			$osm->writeTag( 'osmanalysis', 'mismatch' );
			$prefix = "OSB {$point->extensions->id}: ";
			$osm->writeTag( 'name', $prefix . htmlspecialchars(( string) $point->desc, ENT_QUOTES, 'UTF-8' ) );
			$osm->closeNode();
		}
		$osm->closeOsm();

		return $osm->get();
	}
}
?>
