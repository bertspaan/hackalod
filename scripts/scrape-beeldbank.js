#!/usr/bin/env node

const path = require('path')
const Xray = require('x-ray')

const x = Xray({
  filters: {
    getLayer: (js) => {
      const match = /'ams:([\w-]+)',/g.exec(js)

      if (match) {
        return match[1]
      }
    }
  }
})

const url = 'http://beeldbank.amsterdam.nl/beeldbank/indeling/detail/start/1?f_string_geoserver_store%5B0%5D=%2A'

x(url, 'body@html', {
  permalink: '.permalink input@value',
  js: ['.detailresult script@html | getLayer'],
  imageUrl: '.hoofdafbeelding img@src'
})
  .paginate('.pagination-next a@href')
  .write(path.join(__dirname, '..', 'data', 'scraped.json'))
