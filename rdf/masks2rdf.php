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

		$statements = "<" . $data['permalink'] . ">\n";
		$statements .= "   dct:spatial [\n";
		$statements .= "      dc:identifier \"" . $maskid . "\"^^xsd:string ;\n";
		$statements .= "      geo:hasGeometry [ geo:asWKT \"" . $wkt . "\" ] ;\n";
		//$statements .= "      dc:source \"url van geotif?\" ;\n";
		$statements .= "      dc:type \"outline\"^^xsd:string ;\n";
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


?>