<?php
class LatLon
{
	public $lat;
	public $lon;
	public $height;

	function __construct( $lat, $lon, $height = 0 )
	{
		$this->lat = $lat;
		$this->lon = $lon;
		$this->height = $height;
	}
}

class OSConvert {
/*
 * convert geodesic co-ordinates to OS grid reference
 */
function LatLongToOSGrid(LatLon $p)
{
  $lat = deg2rad($p->lat);
  $lon = deg2rad($p->lon);
  
  $a = 6377563.396; $b = 6356256.910;          // Airy 1830 major & minor semi-axes
  $F0 = 0.9996012717;                         // NatGrid scale factor on central meridian

  $lat0 = deg2rad(49);  // NatGrid true origin
  $lon0 = deg2rad(-2);

  $N0 = -100000; $E0 = 400000;                // northing & easting of true origin, metres
  $e2 = 1 - ($b*$b)/($a*$a);                      // eccentricity squared
  $n = ($a-$b)/($a+$b); $n2 = $n*$n; $n3 = $n*$n*$n;

  $cosLat = cos($lat); $sinLat = sin($lat);
  $nu = $a*$F0 / sqrt(1-$e2*$sinLat*$sinLat);              // transverse radius of curvature
  $rho = $a*$F0*(1-$e2)/pow(1-$e2*$sinLat*$sinLat, 1.5);  // meridional radius of curvature
  $eta2 = $nu/$rho-1;

  $Ma = (1 + $n + (5/4)*$n2 + (5/4)*$n3) * ($lat-$lat0);
  $Mb = (3*$n + 3*$n*$n + (21/8)*$n3) * sin($lat-$lat0) * cos($lat+$lat0);
  $Mc = ((15/8)*$n2 + (15/8)*$n3) * sin(2*($lat-$lat0)) * cos(2*($lat+$lat0));
  $Md = (35/24)*$n3 * sin(3*($lat-$lat0)) * cos(3*($lat+$lat0));
  $M = $b * $F0 * ($Ma - $Mb + $Mc - $Md);              // meridional arc

  $cos3lat = $cosLat*$cosLat*$cosLat;
  $cos5lat = $cos3lat*$cosLat*$cosLat;
  $tan2lat = tan($lat)*tan($lat);
  $tan4lat = $tan2lat*$tan2lat;

  $I = $M + $N0;
  $II = ($nu/2)*$sinLat*$cosLat;
  $III = ($nu/24)*$sinLat*$cos3lat*(5-$tan2lat+9*$eta2);
  $IIIA = ($nu/720)*$sinLat*$cos5lat*(61-58*$tan2lat+$tan4lat);
  $IV = $nu*$cosLat;
  $V = ($nu/6)*$cos3lat*($nu/$rho-$tan2lat);
  $VI = ($nu/120) * $cos5lat * (5 - 18*$tan2lat + $tan4lat + 14*$eta2 - 58*$tan2lat*$eta2);

  $dLon = $lon-$lon0;
  $dLon2 = $dLon*$dLon; $dLon3 = $dLon2*$dLon; $dLon4 = $dLon3*$dLon; $dLon5 = $dLon4*$dLon; $dLon6 = $dLon5*$dLon;

  $N = $I + $II*$dLon2 + $III*$dLon4 + $IIIA*$dLon6;
  $E = $E0 + $IV*$dLon + $V*$dLon3 + $VI*$dLon5;

  return array( $E, $N );
//  return $this->gridrefNumToLet($E, $N, 10);
}


/*
 * convert OS grid reference to geodesic co-ordinates
 */
function OSGridToLatLong($E, $N) {
//  $gr = $this->gridrefLetToNum($gridRef);
//  $E = $gr[0]; $N = $gr[1];

  $a = 6377563.396; $b = 6356256.910;              // Airy 1830 major & minor semi-axes
  $F0 = 0.9996012717;                             // NatGrid scale factor on central meridian
  $lat0 = 49*M_PI/180; $lon0 = -2*M_PI/180;  // NatGrid true origin
  $N0 = -100000; $E0 = 400000;                     // northing & easting of true origin, metres
  $e2 = 1 - ($b*$b)/($a*$a);                          // eccentricity squared
  $n = ($a-$b)/($a+$b); $n2 = $n*$n; $n3 = $n*$n*$n;

  $lat=$lat0; $M=0;
  do {
    $lat = ($N-$N0-$M)/($a*$F0) + $lat;

    $Ma = (1 + $n + (5/4)*$n2 + (5/4)*$n3) * ($lat-$lat0);
    $Mb = (3*$n + 3*$n*$n + (21/8)*$n3) * sin($lat-$lat0) * cos($lat+$lat0);
    $Mc = ((15/8)*$n2 + (15/8)*$n3) * sin(2*($lat-$lat0)) * cos(2*($lat+$lat0));
    $Md = (35/24)*$n3 * sin(3*($lat-$lat0)) * cos(3*($lat+$lat0));
    $M = $b * $F0 * ($Ma - $Mb + $Mc - $Md);                // meridional arc

  } while ($N-$N0-$M >= 0.00001);  // ie until < 0.01mm

  $cosLat = cos($lat); $sinLat = sin($lat);
  $nu = $a*$F0/sqrt(1-$e2*$sinLat*$sinLat);              // transverse radius of curvature
  $rho = $a*$F0*(1-$e2)/pow(1-$e2*$sinLat*$sinLat, 1.5);  // meridional radius of curvature
  $eta2 = $nu/$rho-1;

  $tanLat = tan($lat);
  $tan2lat = $tanLat*$tanLat; $tan4lat = $tan2lat*$tan2lat; $tan6lat = $tan4lat*$tan2lat;
  $secLat = 1/$cosLat;
  $nu3 = $nu*$nu*$nu; $nu5 = $nu3*$nu*$nu; $nu7 = $nu5*$nu*$nu;
  $VII = $tanLat/(2*$rho*$nu);
  $VIII = $tanLat/(24*$rho*$nu3)*(5+3*$tan2lat+$eta2-9*$tan2lat*$eta2);
  $IX = $tanLat/(720*$rho*$nu5)*(61+90*$tan2lat+45*$tan4lat);
  $X = $secLat/$nu;
  $XI = $secLat/(6*$nu3)*($nu/$rho+2*$tan2lat);
  $XII = $secLat/(120*$nu5)*(5+28*$tan2lat+24*$tan4lat);
  $XIIA = $secLat/(5040*$nu7)*(61+662*$tan2lat+1320*$tan4lat+720*$tan6lat);

  $dE = ($E-$E0); $dE2 = $dE*$dE; $dE3 = $dE2*$dE; $dE4 = $dE2*$dE2; $dE5 = $dE3*$dE2; $dE6 = $dE4*$dE2; $dE7 = $dE5*$dE2;
  $lat = $lat - $VII*$dE2 + $VIII*$dE4 - $IX*$dE6;
  $lon = $lon0 + $X*$dE - $XI*$dE3 + $XII*$dE5 - $XIIA*$dE7;

  return new LatLon(rad2deg($lat), rad2deg($lon));
}


/* 
 * convert standard grid reference ('SU387148') to fully numeric ref ([438700,114800])
 *   returned co-ordinates are in metres, centred on grid square for conversion to lat/long
 *
 *   note that northern-most grid squares will give 7-digit northings
 *   no error-checking is done on gridref (bad input will give bad results or NaN)
 */
function gridrefLetToNum($gridref) {
  // get numeric values of letter references, mapping A->0, B->1, C->2, etc:
  $l1 = ord(strtoupper($gridref[0])) - ord('A');
  $l2 = ord(strtoupper($gridref[1])) - ord('A');

  // shuffle down letters after 'I' since 'I' is not used in grid:
  if ($l1 > 7) $l1--;
  if ($l2 > 7) $l2--;

  // convert grid letters into 100km-square indexes from false origin (grid square SV):
  $e = (($l1-2)%5)*5 + ($l2%5);
  $n = (19-floor($l1/5)*5) - floor($l2/5);

  // skip grid letters to get numeric part of ref, stripping any spaces:
  $gridref = substr(preg_replace( '/ /', '', $gridref ), 2 );

  // append numeric part of references to grid index:
  $len = strlen( $gridref );
  $e .= substr( $gridref, 0, $len / 2);
  $n .= substr( $gridref, $len / 2 );

  // normalise to 1m grid, rounding up to centre of grid square:
  switch ($len) {
    case 6: $e .= '50'; $n .= '50'; break;
    case 8: $e .= '5'; $n .= '5'; break;
    // 10-digit refs are already 1m
  }

  return array( $e, $n );
}


/*
 * convert numeric grid reference (in metres) to standard-form grid ref
 */
function gridrefNumToLet($e, $n, $digits) {
  // get the 100km-grid indices
  $e100k = floor($e/100000); $n100k = floor($n/100000);
  
  if ($e100k<0 || $e100k>6 || $n100k<0 || $n100k>12) return '';

  // translate those into numeric equivalents of the grid letters
  $l1 = (19-$n100k) - (19-$n100k)%5 + floor(($e100k+10)/5);
  $l2 = (19-$n100k)*5%25 + $e100k%5;

  // compensate for skipped 'I' and build grid letter-pairs
  if ($l1 > 7) $l1++;
  if ($l2 > 7) $l2++;
  $letPair = chr($l1 + ord('A') ) . chr( $l2 + ord ( 'A' ) );

  // strip 100km-grid indices from easting & northing, and reduce precision
  $e = floor(($e%100000)/pow(10,5-$digits/2));
  $n = floor(($n%100000)/pow(10,5-$digits/2));

$dl = $digits / 2;
  $gridRef = $letPair . sprintf("%0{$dl}d%0{$dl}d", $e, $n);

  return $gridRef;
}

/* --------------  the following are duplicated from latlong-convert-coords.html   -------------- */

// ellipse parameters
private $e;
private $h;

function __construct() {
$this->e = array(
	'WGS84' => array( 'a' => 6378137, 'b' => 6356752.3142, 'f' => 1/298.257223563 ),
	'Airy1830' => array( 'a' => 6377563.396, 'b' => 6356256.910,  'f' => 1/299.3249646 )
);

// helmert transform parameters
$this->h = array( 'WGS84toOSGB36' => array( 'tx' => -446.448,  'ty' =>  125.157,   'tz' => -542.060,   // m
                           'rx' =>   -0.1502, 'ry' =>   -0.2470,  'rz' =>   -0.8421,  // sec
                           's' =>    20.4894 ),                               // ppm
          'OSGB36toWGS84' => array( 'tx' =>  446.448,  'ty' => -125.157,   'tz' =>  542.060,
                           'rx' =>    0.1502, 'ry' =>    0.2470,  'rz' =>    0.8421,
                           's' =>   -20.4894 ) );
}
                 
function convertOSGB36toWGS84($p1) {
  $p2 = $this->convert($p1, $this->e['Airy1830'], $this->h['OSGB36toWGS84'], $this->e['WGS84'] );
  return $p2;
}


function convertWGS84toOSGB36($p1) {
  $p2 = $this->convert($p1, $this->e['WGS84'], $this->h['WGS84toOSGB36'], $this->e['Airy1830']);
  return $p2;
}


function convert(LatLon $p, $e1, $t, $e2)
{
  // -- convert polar to cartesian coordinates (using ellipse 1)
  
  $p1 = new LatLon($p->lat, $p->lon, $p->height);  // to avoid modifying passed param
  $p1->lat = deg2rad($p->lat); $p1->lon = deg2rad($p->lon);

  $a = $e1['a']; $b = $e1['b'];

  $sinPhi = sin($p1->lat); $cosPhi = cos($p1->lat);
  $sinLambda = sin($p1->lon); $cosLambda = cos($p1->lon);
  $H = $p1->height;

  $eSq = ($a*$a - $b*$b) / ($a*$a);
  $nu = $a / sqrt(1 - $eSq*$sinPhi*$sinPhi);

  $x1 = ($nu+$H) * $cosPhi * $cosLambda;
  $y1 = ($nu+$H) * $cosPhi * $sinLambda;
  $z1 = ((1-$eSq)*$nu + $H) * $sinPhi;


  // -- apply helmert transform using appropriate params
  
  $tx = $t['tx']; $ty = $t['ty']; $tz = $t['tz'];
  $rx = $t['rx']/3600 * M_PI/180;  // normalise seconds to radians
  $ry = $t['ry']/3600 * M_PI/180;
  $rz = $t['rz']/3600 * M_PI/180;
  $s1 = $t['s']/1e6 + 1;              // normalise ppm to (s+1)

  // apply transform
  $x2 = $tx + $x1*$s1 - $y1*$rz + $z1*$ry;
  $y2 = $ty + $x1*$rz + $y1*$s1 - $z1*$rx;
  $z2 = $tz - $x1*$ry + $y1*$rx + $z1*$s1;


  // -- convert cartesian to polar coordinates (using ellipse 2)

  $a = $e2['a']; $b = $e2['b'];
  $precision = 1 / $a;  // results accurate to around 1 metres

  $eSq = ($a*$a - $b*$b) / ($a*$a);
  $p = sqrt($x2*$x2 + $y2*$y2);
  $phi = atan2($z2, $p*(1-$eSq)); $phiP = 2*M_PI;
  while (abs($phi-$phiP) > $precision) {
    $nu = $a / sqrt(1 - $eSq*sin($phi)*sin($phi));
    $phiP = $phi;
    $phi = atan2($z2 + $eSq*$nu*sin($phi), $p);
  }
  $lambda = atan2($y2, $x2);
  $H = $p/cos($phi) - $nu;

  return new LatLon(rad2deg($phi), rad2deg($lambda), $H);
}

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */

}


function lon2x( $lon, $pixels = 32768 )
{
	return (($lon + 180) / 360) * $pixels;
}

function lat2y( $lat, $pixels = 32768 )
{
	return ((atanh(sin(deg2rad(-$lat))) / M_PI) + 1) * ($pixels / 2);
}
