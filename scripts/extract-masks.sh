#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

regex='tiffs/(.*).tiff'

for file in $DIR/../tiffs/*.tiff
do
  if [[ $file != *".original."* ]]; then
    [[ $file =~ $regex ]]
    uuid=${BASH_REMATCH[1]}
    if [ ! -f $DIR/../masks/$uuid.geojson ]; then
      rio shapes --mask --precision 6 $file | simplify-geojson -t 0.0005 | python -mjson.tool > $DIR/../masks/$uuid.geojson
    fi
  fi
done
