<?php


$turtlefile = 'masks.ttl';
$prefixes = 
"@prefix dc: <http://purl.org/dc/elements/1.1/> .
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .
@prefix wdt: <http://www.wikidata.org/prop/direct/> .
@prefix geo: <http://www.opengis.net/ont/geosparql#> .
@prefix dct: <http://purl.org/dc/terms/> . \n\n";

file_put_contents($turtlefile, $prefixes);



$file = fopen("../data/maps.ndjson","r");

while(! feof($file)){
	$json = fgets($file);
	$data = json_decode($json,true);
	

	$geometry = $data['geometry'];
	$wkt = convertGeoJSONToWKT(json_encode($geometry));

	
	$statements = "<http://beeldbank.amsterdam.nl/afbeelding/" . $data['data']['imageId'] . ">\n";
	$statements .= "   dct:spatial [\n";
	$statements .= "      dc:identifier \"" . $data['id'] . "\"^^xsd:string ;\n";
	$statements .= "      geo:hasGeometry [ geo:asWKT \"" . $wkt . "\" ] ;\n";
	$statements .= "      dc:type \"outline\"^^xsd:string ;\n";
	$statements .= "      wdt:P2046 \"" . $data['data']['area'] . "\"^^xsd:float ;\n";
	$statements .= "   ] . \n\n";
	file_put_contents($turtlefile, $statements, FILE_APPEND);

	echo ". ";
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