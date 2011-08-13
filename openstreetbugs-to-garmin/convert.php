<?php
require '../lib/osm-writer.php';
require '../lib/gpx-to-osm.php';

$osm = new OSMWriter;
$data = GpxToOsm::convert( 'osb.gpx', $osm, 'OSB' );
file_put_contents( 'osb.osm', $data );
?>
