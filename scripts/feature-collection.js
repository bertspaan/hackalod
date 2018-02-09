#!/usr/bin/env node

const fs = require('fs')
const path = require('path')
const H = require('highland')
const JSONStream = require('JSONStream')

H(fs.createReadStream(path.join(__dirname, '..', 'data', 'maps.ndjson')))
  .split()
  .compact()
  .map(JSON.parse)
  .map((map) => ({
    type: 'Feature',
    properties: map.data,
    geometry: map.geometry
  }))
  .pipe(JSONStream.stringify('{"type":"FeatureCollection","features":[', ',\n', ']}'))
  .pipe(fs.createWriteStream(path.join(__dirname, '..', 'data', 'all-masks.geojson')))
