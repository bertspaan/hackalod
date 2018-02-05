#!/bin/bash

regex='ams:(.*)'

for file in tiffs/*
do
  [[ $file =~ $regex ]]
  uuid=${BASH_REMATCH[1]}
  rio shapes --mask --precision 6 $file | python -mjson.tool > masks/$uuid.geojson
done
