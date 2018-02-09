const fs = require('fs')
const H = require('highland')
const turf = {
  area: require('@turf/area')
}

function readMask (uuid) {
  try {
    return JSON.parse(fs.readFileSync(`masks/${uuid}.geojson`))
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
  return Math.round(number * n) /n
}

H(fs.createReadStream('links.ndjson'))
  .split()
  .compact()
  .map(JSON.parse)
  .map((data) => ({
    ...data,
    mask: readMask(data.uuid)
  }))
  .filter((data) => data.mask)
  // .map((data) => ({
  //   ...data,
  //   dimensions: readDimensions(data.uuid)
  // }))
  .map((data) => {
    // Pak jaartal uit triple store?
    const geometry = data.mask.features[0].geometry

    const area = Math.round(turf.area(geometry))
    const kmSquared = roundDecimals(area * 0.000001, 5)


    return {
      id: data.uuid,
      type: 'st:Map',
      // name: 'name',
      // validSince: ,
      // validUntil: ,
      data: {
        imageId: data.id,
        // http://geoserver.memorix.nl/geoserver/ams/wms?SERVICE=WMS&REQUEST=GetMap&VERSION=1.1.1&LAYERS=ams%3Adabef258-08a5-4057-b154-acc4c112c678&STYLES=&FORMAT=image%2Fpng&TRANSPARENT=true&HEIGHT=256&WIDTH=256&ZINDEX=1&SRS=EPSG%3A4326&BBOX=4.85595703125,52.36218321674427,4.8779296875,52.3755991766591
        area: kmSquared
      },
      geometry
    }
  })
  .map(JSON.stringify)
  .intersperse('\n')
  .pipe(fs.createWriteStream('maps.ndjson'))
