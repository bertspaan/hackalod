#!/bin/bash

regex='/(.*).tiff'

for file in tiffs/*.tiff
do
  [[ $file =~ $regex ]]
  uuid=${BASH_REMATCH[1]}
  rio shapes --mask --precision 6 $file | simplify-geojson -t 0.001 | python -mjson.tool > masks/$uuid.geojson
done
