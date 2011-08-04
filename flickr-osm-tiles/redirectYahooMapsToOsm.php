#!/usr/local/php/5.3dev/bin/php
<?php
do {
    // Open the log file
    $a = fopen( '/tmp/test.log', 'a' );

    // Read the URL line
    $input = fgets( STDIN );
    if ( $input == '' )
    {
        continue;
    }

    // Split the data so that we can access the host and
    // query string elements
    $parts = explode( ' ', $input );
    $urlParts = parse_url( $parts[0] );
    $queryParts = array();
    if ( isset( $urlParts['query'] ) )
    {
        parse_str( $urlParts['query'], $queryParts );
    }

    // The block to test for Yahoo! Maps:
    if ( preg_match( '@maps\d?.yimg.com@', $urlParts['host'] ) )
    {
        $range = range('a', 'c');
        $server = $range[rand(0, sizeof($range)-1)];
        if ( !isset( $queryParts['r'] ) || $queryParts['r'] == 0 )
        {
            // This is the format that Flickr uses

            // Do the math to calculate the OSM tile
            // coordinates from the Yahoo!Maps one
            $z = 18 - $queryParts['z'];
            $x = $queryParts['x'];
            $y = pow(2, $z-1) - $queryParts['y'] - 1;

            // Assemble new URL and write log line
            $newUrl = "http://{$server}.tile.openstreetmap.org/$z/$x/$y.png";
            fwrite( $a, "REDIR: $parts[0] => $newUrl\n" );
        }
        else
        {
            // This is the format that Yahoo!Maps uses

            // Do the math to calculate the OSM tile
            // coordinates from the Yahoo!Maps one
            $z = $queryParts['z'] - 1;
            $x = $queryParts['x'];
            $y = pow( 2, $z - 1 ) - $queryParts['y'] - 1;

            // Assemble new URL and write log line
            $newUrl = "http://{$server}.tile.openstreetmap.org/$z/$x/$y.png";
            fwrite( $a, "REDIR: $parts[0] => $newUrl\n" );
        }
    } else {
        $newUrl = $parts[0];
        fwrite($a, "NORMAL: $newUrl\n");
    }

    // Output the rewritten (or original) URL
    echo $newUrl, "\n";
} while( true );
?>
