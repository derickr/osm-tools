<?php
class Gpx
{
	static function getPoints( $filename, $filter )
	{
		$sxe = simplexml_load_file( $filename );
		$uid = (int) preg_replace( '@\.gpx$@', '', preg_replace( '@^.*/@', '', $filename ) );

		$points = array();
		foreach ( $sxe->trk as $trk )
		{
			foreach ( $trk->trkseg as $seg )
			{
				foreach( $seg->trkpt as $pt )
				{
					$point = array(
						'uid' => $uid,
						'lat' => (float) $pt['lat'],
						'lon' => (float) $pt['lon'],
						'ts'  => strtotime( $pt->time )
					);
					if (
						( (float) $pt['lat'] < $filter['N'] && (float) $pt['lat'] > $filter['S'] ) &&
						( (float) $pt['lon'] < $filter['E'] && (float) $pt['lon'] > $filter['W'] )
					) {
						$points[] = $point;
					}
				}
			}
		}
		return $points;
	}
}
?>
