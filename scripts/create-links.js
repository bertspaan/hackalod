#!/usr/bin/env node

const fs = require('fs')
const path = require('path')
const H = require('highland')
const JSONStream = require('JSONStream')
const csv = require('csv-parser')

function getYear (date) {
  return parseInt(date.split('-')[0])
}

function readCsv () {
  return new Promise((resolve, reject) => {
    const mapsByPermalink = {}

    fs.createReadStream(path.join(__dirname, '..', 'data', 'years-and-titles.csv'))
      .pipe(csv({
        separator: ',',
        quote: '"',
        escape: '"'
      }))
      .on('data', (map) => {
        const permalink = map.map
        mapsByPermalink[permalink] = {
          title: map.title,
          years: [
            getYear(map.begin),
            getYear(map.end)
          ]
        }
      })
      .on('finish', () => {
        resolve(mapsByPermalink)
      })
  })
}

readCsv().then((mapsByPermalink) => {
  H(fs.createReadStream(path.join(__dirname, '..', 'data', 'scraped.json'))
    .pipe(JSONStream.parse('*')))
    .map((map) => {
      const imageId = map.permalink.replace('http://beeldbank.amsterdam.nl/afbeelding/', '')

      const imageUrl = map.imageUrl

      const memorixUuid = /250\/(.*)\.jpg/.exec(imageUrl)[1]
      const memorixGeotiffUuid = map.js.filter((s) => s)[0]
      const geotiffUrl = `http://geoserver.memorix.nl/geoserver/ams/wcs?service=WCS&version=2.0.1&request=GetCoverage&format=image/tiff&CoverageId=ams:${memorixGeotiffUuid}`

      const fromCsv = mapsByPermalink[map.permalink]

      let years
      let title

      if (!fromCsv) {
        console.error(`No CSV data found for map: ${map.permalink}`)
      } else {
        years = fromCsv.years
        title = fromCsv.title
      }

      return {
        imageId,
        imageUrl,
        geotiffUrl,
        permalink: map.permalink,
        memorixUuid,
        memorixGeotiffUuid,
        years,
        title
      }
    })
    .map(JSON.stringify)
    .intersperse('\n')
    .pipe(fs.createWriteStream(path.join(__dirname, '..', 'data', 'links.ndjson')))
})
