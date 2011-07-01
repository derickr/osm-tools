<?php
require '../lib/convert.php';
require '../lib/osm-writer.php';

$dom = new DomDocument;
//@$dom->loadHTMLFile( "/tmp/{$argv[1]}.html" );
echo "Processing {$argv[1]}: ";
@$dom->loadHTMLFile( "http://www.itoworld.com/product/data/osm_analysis/area?name={$argv[1]}" );
echo "fetched ";
$missing = $dom->getElementByID('roadsMissingBody');
$missing = simplexml_import_dom($missing);

$result = array();
@process_list($result, $missing->table);
render_to_osm( $result, "ito-fetch/{$argv[1]}.osm" );
echo "written\n";

function process_list(&$result, $node)
{
	foreach($node->tr as $row) {
		$result[] = process_way($row->td->a);
	}
	foreach($node->i as $row) {
		process_list($result, $row);
	}
}

function process_way($node)
{
	$name = $node[0];
	$href = $node['href'];
	parse_str(preg_replace('@^map_browser\?@', '', $href), $result);
	list($left, $top, $right, $bottom) = explode(',', $result['bbox']);
	$horizontal = ($left + $right) / 2;
	$vertical = ($top + $bottom) / 2;

	// convert grid to lat/lon
	$convert = new OSConvert;
	$coords = $convert->convertOSGB36toWGS84( $convert->OSGridToLatLong( $horizontal, $vertical ) );
	return array(
		'name' => (string) $name,
		'lat' => $coords->lat,
		'lon' => $coords->lon
	);
}

function render_to_osm($nodeList, $file)
{
	$osm = new OSMWriter;
	$osm->xmlHeader();
	$osm->openOsm("0.6", "osm-tools");

	foreach( $nodeList as $node )
	{
		$osm->openNode( $node['lat'], $node['lon'] );
		$osm->writeTag( 'osmanalysis', 'missing' );
		$osm->writeTag( "name", htmlspecialchars( $node['name'], ENT_QUOTES ) );
		$osm->closeNode();
	}

	$osm->closeOsm();
	file_put_contents($file, $osm->get());
}
