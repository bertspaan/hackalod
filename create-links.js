const fs = require('fs')
const H = require('highland')
const JSONStream = require('JSONStream')

H(fs.createReadStream('scraped.json')
  .pipe(JSONStream.parse('*')))
  .map((map) => {
    const uuid = map.html.filter((s) => s)[0]
    const id = map.permalink.replace('http://beeldbank.amsterdam.nl/afbeelding/', '')

    return {
      id,
      permalink: map.permalink,
      uuid      
    }
  })
  .map(JSON.stringify)
  .intersperse('\n')
  .pipe(fs.createWriteStream('links.ndjson'))
