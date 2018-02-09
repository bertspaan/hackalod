#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

regex='tiffs/(.*).tiff'

for file in $DIR/../tiffs/*.tiff
do
  [[ $file =~ $regex ]]
  uuid=${BASH_REMATCH[1]}
  rio shapes --mask --precision 6 $file | simplify-geojson -t 0.001 | python -mjson.tool > $DIR/../masks/$uuid.geojson
done
