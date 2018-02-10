<?php



$sparqlquery = "

PREFIX dc: <http://purl.org/dc/elements/1.1/>
PREFIX dct: <http://purl.org/dc/terms/>
PREFIX geo: <http://www.opengis.net/ont/geosparql#>
PREFIX sem: <http://semanticweb.cs.vu.nl/2009/11/sem/>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX wdt: <http://www.wikidata.org/prop/direct/>
select ?kaart ?img ?x ?y {
  <https://adamlink.nl/geo/building/algemeen-handelsblad/500> geo:hasGeometry/geo:asWKT ?wktbuilding .
  ?kaart dct:spatial ?spatial . 
  ?kaart foaf:depiction ?img .
  ?spatial dc:type \"outline\"^^xsd:string .
  ?spatial geo:hasGeometry/geo:asWKT ?wktmap .
  ?spatial wdt:P2046 ?km2 .
  bind (bif:st_geomfromtext(\"POINT(4.917047 52.365802)\") as ?x)
  bind (bif:st_geomfromtext(?wktmap) as ?y)
  FILTER (bif:st_intersects(?x, ?y))
}
ORDER BY ASC(?km2)
limit 14

";


$url = "https://api.data.adamlink.nl/datasets/menno/alles/services/alles/sparql?default-graph-uri=&query=" . urlencode($sparqlquery) . "&format=application%2Fsparql-results%2Bjson&timeout=120000&debug=on";

//echo '<a target="_blank" href="' . $url . '">' . $url . '</a>';

$json = file_get_contents($url);

$data = json_decode($json,true);

$fc = array("type"=>"FeatureCollection","features"=>array());


$occs = array();

foreach ($data['results']['bindings'] as $row) {
	$street = array("type"=>"Feature");
	$props = array(
		"image" => $row['img']['value'],
		"permalink" => $row['uri']['value']
	);
	$street['geometry'] = wkt2geojson($row['y']['value']);
	$street['properties'] = $props;
	$fc['features'][] = $street;
}


$json = json_encode($fc);

header('Content-Type: application/json');
die($json);

//echo "Total number of countries:" . $result->numRows();

function wkt2geojson($wkt){
	$coordsstart = strpos($wkt,"(");
	$type = trim(substr($wkt,0,$coordsstart));
	$coordstring = substr($wkt, $coordsstart);

	switch ($type) {
	    case "LINESTRING":
	    	$geom = array("type"=>"LineString","coordinates"=>array());
			$coordstring = str_replace(array("(",")"), "", $coordstring);
	    	$pairs = explode(",", $coordstring);
	    	foreach ($pairs as $k => $v) {
	    		$coords = explode(" ", $v);
	    		$geom['coordinates'][] = array((double)$coords[0],(double)$coords[1]);
	    	}
	    	return $geom;
	    	break;
	    case "POLYGON":
	    	$geom = array("type"=>"Polygon","coordinates"=>array());
			$coordstring = str_replace(array("(",")"), "", $coordstring);
	    	$pairs = explode(",", $coordstring);
	    	foreach ($pairs as $k => $v) {
	    		$coords = explode(" ", $v);
	    		$geom['coordinates'][0][] = array((double)$coords[0],(double)$coords[1]);
	    	}
	    	return $geom;
	    	break;
	    case "MULTILINESTRING":
	    	$geom = array("type"=>"MultiLineString","coordinates"=>array());
	    	preg_match_all("/\([0-9. ,]+\)/",$coordstring,$matches);
	    	//print_r($matches);
	    	foreach ($matches[0] as $linestring) {
	    		$linestring = str_replace(array("(",")"), "", $linestring);
		    	$pairs = explode(",", $linestring);
		    	$line = array();
		    	foreach ($pairs as $k => $v) {
		    		$coords = explode(" ", $v);
		    		$line[] = array((double)$coords[0],(double)$coords[1]);
		    	}
		    	$geom['coordinates'][] = $line;
	    	}
	    	return $geom;
	    	break;
	    case "POINT":
			$coordstring = str_replace(array("(",")"), "", $coordstring);
	    	$coords = explode(" ", $coordstring);
	    	print_r($coords);
	    	$geom = array("type"=>"Point","coordinates"=>array((double)$coords[0],(double)$coords[1]));
	    	return $geom;
	        break;
	}
}
