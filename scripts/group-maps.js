#!/usr/bin/env node

const fs = require('fs')
const path = require('path')
const H = require('highland')
const groupMaps = require('spacetime-group-maps')

// function allGroups (dir) {
//   const suffix = '.config.js'
//
//   return fs.readdirSync(dir)
//     .filter((filename) => filename.endsWith(suffix))
//     .map((filename) => ({
//       group: filename.replace(suffix, ''),
//       config: require(path.join(dir, filename))
//     }))
// }
//
// function destFilename (dirs, group, stream) {
//   return path.join(dirs.current, `${group.group}.${stream}.json`)
// }
//
// function writeToFile (dirs, grouped, callback) {
//   console.log(`Grouping maps: ${grouped.group}`)
//
//   grouped.streams.all
//     .pipe(fs.createWriteStream(destFilename(dirs, grouped, 'all')))
//
//   grouped.streams.grouped
//     .pipe(fs.createWriteStream(destFilename(dirs, grouped, 'grouped')))
//     .on('finish', callback)
// }
//
// function aggregate (config, dirs, tools, callback) {
//   const groupsDir = path.join(__dirname, 'groups')
//   const mapsNdjson = path.join(dirs.current, '..', '..', 'transform/mapwarper/mapwarper.objects.ndjson')
//
//   H(allGroups(groupsDir))
//     .map((group) => Object.assign(group, {
//       streams: groupMaps(mapsNdjson, group.config)
//     }))
//     .map(H.curry(writeToFile, dirs))
//     .nfcall([])
//     .series()
//     .done(callback)
// }
