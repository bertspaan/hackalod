#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
TIFF_DIR=$DIR/../tiffs

while read uuid ; do
  if [ ! -f $TIFF_DIR/$uuid.tiff ]; then
    url="http://geoserver.memorix.nl/geoserver/ams/wcs?service=WCS&version=2.0.1&request=GetCoverage&format=image/tiff&CoverageId=ams:$uuid"
    wget -O $TIFF_DIR/$uuid.original.tiff $url
    gdal_translate -of GTiff -outsize 1600 0 $TIFF_DIR/$uuid.original.tiff $TIFF_DIR/$uuid.tiff

    file $TIFF_DIR/$uuid.original.tiff > $TIFF_DIR/$uuid.info.txt
    rm $TIFF_DIR/$uuid.original.tiff
  fi
done < <( cat $DIR/../data/links.ndjson | jq -r '.uuid' )
