#!/usr/bin/env node

const fs = require('fs')
const path = require('path')
const H = require('highland')
const groupMaps = require('spacetime-group-maps')
const config = require('./group-maps-config')

const mapsNdjson = path.join(__dirname, '..', 'data', 'maps.ndjson')
const streams = groupMaps(mapsNdjson, config)

streams.all
  .errors((err) => {
    console.error(err.message)
  })
  .pipe(fs.createWriteStream(path.join(__dirname, '..', 'grouped-maps', 'maps-by-decade.all.json')))

streams.grouped
  .errors((err) => {
    console.error(err.message)
  })
  .pipe(fs.createWriteStream(path.join(__dirname, '..', 'grouped-maps', 'maps-by-decade.grouped.json')))
