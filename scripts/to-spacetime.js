#!/usr/bin/env node

const fs = require('fs')
const path = require('path')
const H = require('highland')
const turf = {
  area: require('@turf/area')
}

function readMask (uuid) {
  try {
    return JSON.parse(fs.readFileSync(path.join(__dirname, '..', 'masks', `${uuid}.geojson`)))
  } catch (err) {
    // do nothing!
  }
}

// function readDimensions (uuid) {
//   try {
//     const info = fs.readFileSync(`tiffs/${uuid}.info.txt`, 'utf8')
//
//     const widthMatch = /width=(\d+)/g.exec(info)
//     const heightMatch = /height=(\d+)/g.exec(info)
//
//     return [
//       parseInt(widthMatch[1]),
//       parseInt(heightMatch[1])
//     ]
//   } catch (err) {
//     // do nothing!
//   }
// }

function roundDecimals (number, decimals) {
  const n = Math.pow(10, decimals)
  return Math.round(number * n) / n
}

H(fs.createReadStream(path.join(__dirname, '..', 'data', 'links.ndjson')))
  .split()
  .compact()
  .map(JSON.parse)
  .map((data) => ({
    ...data,
    mask: readMask(data.memorixGeotiffUuid)
  }))
  .filter((data) => data.mask && data.mask.features && data.mask.features.length)
  // .map((data) => ({
  //   ...data,
  //   dimensions: readDimensions(data.uuid)
  // }))
  .map((data) => {
    const geometry = data.mask.features[0].geometry

    if (!geometry) {
      return
    }

    const area = Math.round(turf.area(geometry))
    const kmSquared = roundDecimals(area * 0.000001, 5)

    const years = data.years

    return {
      id: data.imageId,
      type: 'st:Map',
      name: data.title,
      validSince: years && years[0],
      validUntil: years && years[1],
      data: {
        imageId: data.imageId,
        memorixGeotiffUuid: data.memorixGeotiffUuid,
        memorixUuid: data.memorixUuid,        
        area: kmSquared
      },
      geometry
    }
  })
  .compact()
  .map(JSON.stringify)
  .intersperse('\n')
  .pipe(fs.createWriteStream(path.join(__dirname, '..', 'data', 'maps.ndjson')))
