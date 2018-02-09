const Xray = require('x-ray')

const x = Xray({
  filters: {
    getLayer: (html) => {
      const match = /'ams:([\w-]+)',/g.exec(html)

      if (match) {
        return match[1]
      }
    }
  }
})

const url = 'http://beeldbank.amsterdam.nl/beeldbank/indeling/detail/start/1?f_string_geoserver_store%5B0%5D=%2A'

x(url, 'body@html', {
  permalink: '.permalink input@value',
  html: ['.detailresult script@html | getLayer']
})
  .paginate('.pagination-next a@href')
  .write('scraped.json')
