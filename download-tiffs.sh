#!/bin/bash

while read uuid ; do
  if [ ! -f tiffs/$uuid.tiff ]; then
    url="http://geoserver.memorix.nl/geoserver/ams/wcs?service=WCS&version=2.0.1&request=GetCoverage&format=image/tiff&CoverageId=ams:$uuid"
    wget -O tiffs/$uuid.original.tiff $url
    gdal_translate -of GTiff -outsize 1600 0 tiffs/$uuid.original.tiff tiffs/$uuid.tiff

    file tiffs/$uuid.original.tiff > tiffs/$uuid.info.txt
    rm tiffs/$uuid.original.tiff
  fi
done < <( cat links.ndjson | jq -r '.uuid' )
