const fs = require('fs')
const H = require('highland')

H(fs.createReadStream('links.ndjson'))
  .split()
  .compact()
  .map(JSON.parse)
  .map((link) => {
    const coverageId = link.coverageId
    const url = `http://geoserver.memorix.nl/geoserver/ams/wcs?service=WCS&version=2.0.1&request=GetCoverage&format=image/tiff&CoverageId=${coverageId}`
    return url
  })
  .intersperse('\n')
  .pipe(fs.createWriteStream('tiffs.txt'))
