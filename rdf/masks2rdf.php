<?php


$turtlefile = 'masks.ttl';
$prefixes = 
"@prefix dc: <http://purl.org/dc/elements/1.1/> .
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .
@prefix geo: <http://www.opengis.net/ont/geosparql#> .
@prefix dct: <http://purl.org/dc/terms/> . \n\n";

file_put_contents($turtlefile, $prefixes);



$file = fopen("../links.ndjson","r");

while(! feof($file)){
	$json = fgets($file);
	$data = json_decode($json,true);

	$maskid = str_replace("ams:", "", $data['coverageId']);
	$maskfile = "../masks/" . $maskid . ".geojson";
	//echo $maskid . " ... \n\n";


	if( file_exists($maskfile) ){

		$geojson = file_get_contents($maskfile);
		$geodata = json_decode($geojson,true);

		$geometry = $geodata['features'][0]['geometry'];
		$wkt = convertGeoJSONToWKT(json_encode($geometry));

		$bb = $geodata['bbox'];
		$rdsw = wgs2rd($bb[1],$bb[0]);
		$rdne = wgs2rd($bb[3],$bb[2]);
		$eastwestmeters = floor($rdne['x']-$rdsw['x']);
		$northsouthmeters = floor($rdne['y']-$rdsw['y']);

		$statements = "<" . $data['permalink'] . ">\n";
		$statements .= "   dct:spatial [\n";
		$statements .= "      dc:identifier \"" . $maskid . "\"^^xsd:string ;\n";
		$statements .= "      geo:hasGeometry [ geo:asWKT \"" . $wkt . "\" ] ;\n";
		//$statements .= "      dc:source \"url van geotif?\" ;\n";
		$statements .= "      dc:type \"outline\"^^xsd:string ;\n";
		$statements .= "      dinges:dinges " . $eastwestmeters . "^^xsd:integer ;\n";
		$statements .= "   ] . \n\n";
		file_put_contents($turtlefile, $statements, FILE_APPEND);


	}
}

fclose($file);



function convertGeoJSONToWKT(string $geoJson){

    $geometry = json_decode($geoJson, true);
    $wktType = strtoupper($geometry['type']);

    if ($wktType == "MULTIPOLYGON" || $wktType == "MULTILINESTRING" || $wktType == "POLYGON") {
        $linesOrPolygons = array();
        foreach ($geometry['coordinates'] as $lineOrPolygon) {
            $points = array();
            foreach ($lineOrPolygon as $point) {
                $points[] = $point[0] . " " . $point[1];
            }
            $linesOrPolygons[] = "(" . implode(",", $points) . ")";
        }
        $wktGeometry = $wktType . "(" . implode(",", $linesOrPolygons) . ")";
    } elseif ($wktType == "POLYGON") {
        $points = array();
        foreach ($geometry['coordinates'][0] as $point) {
            $points[] = $point[0] . " " . $point[1];
        }
        $wktGeometry = $wktType . "(" . implode(",", $points) . ")";

    } elseif ($wktType == "POINT") {
        $wktGeometry = $wktType . "(" . $geometry['coordinates'][0] . " " . $geometry['coordinates'][1] . ")";
    } elseif ($wktType == "LINESTRING") {
        $points = array();
        foreach ($geometry['coordinates'] as $point) {
            $points[] = $point[0] . " " . $point[1];
        }
        $wktGeometry = $wktType . "(" . implode(",", $points) . ")";
    }

    return $wktGeometry;
}



function wgs2rd( $lat, $lon ){ // the schrama one
	
	// Fixed constants / coefficients
	$x0      = 155000;
	$y0      = 463000;
	$k       = 0.9999079;
	$bigr    = 6382644.571;
	$m       = 0.003773954;
	$n       = 1.000475857;
	$lambda0 = 0.094032038;
	$phi0    = 0.910296727;
	$l0      = 0.094032038;
	$b0      = 0.909684757;
	$e       = 0.081696831;
	$a       = 6377397.155;
	
	// wgs84 to bessel
	$dphi = $lat - 52;
	$dlam = $lon - 5;
		
	$phicor = ( -96.862 - $dphi * 11.714 - $dlam * 0.125 ) * 0.00001;
	$lamcor = ( $dphi * 0.329 - 37.902 - $dlam * 14.667 ) * 0.00001;

	$phibes = $lat - $phicor;
	$lambes = $lon - $lamcor;

	// bessel to rd
	$phi		= $phibes / 180 * pi();
	$lambda		= $lambes / 180 * pi();
	$qprime		= log( tan( $phi / 2 + pi() / 4 ));  
	$dq			= $e / 2 * log(( $e * sin($phi) + 1 ) / ( 1 - $e * sin( $phi ) ) );
	$q			= $qprime - $dq;
		
	$w			= $n * $q + $m;
	$b			= atan( exp( $w ) ) * 2 - pi() / 2;
	$dl			= $n * ( $lambda - $lambda0 );

	$d_1		= sin( ( $b - $b0 ) / 2 );
	$d_2		= sin( $dl / 2 );
	
	$s2psihalf	= $d_1 * $d_1 + $d_2 * $d_2 * cos( $b ) * cos ( $b0 );
	$cpsihalf	= sqrt( 1 - $s2psihalf );
	$spsihalf	= sqrt( $s2psihalf );
	$tpsihalf	= $spsihalf / $cpsihalf;
	
	$spsi		= $spsihalf * 2 * $cpsihalf;
	$cpsi		= 1 - $s2psihalf * 2;
	
	$sa			= sin( $dl ) * cos( $b ) / $spsi;
	$ca			= ( sin( $b ) - sin( $b0 ) * $cpsi ) / ( cos( $b0 ) * $spsi );
	
	$r			= $k * 2 * $bigr * $tpsihalf;
	$x			= $r * $sa + $x0;
	$y			= $r * $ca + $y0;

	return array('x'=>$x, 'y'=>$y);
}

?>