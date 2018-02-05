const fs = require('fs')
const H = require('highland')
const JSONStream = require('JSONStream')

H(fs.createReadStream('maps.json')
  .pipe(JSONStream.parse('*')))
  .map((map) => {
    const uuid = map.html.filter((s) => s)[0]

    // http://geoserver.memorix.nl/geoserver/ams/wcs?service=WCS&version=2.0.1&request=GetCoverage&format=image/tiff&CoverageId=
    return {
      permalink: map.permalink,
      coverageId: `ams:${uuid}`,
    }
  })
  .map(JSON.stringify)
  .intersperse('\n')
  .pipe(fs.createWriteStream('links.ndjson'))
