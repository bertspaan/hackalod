# HackaLOD 2018

Plan voor http://hackalod.com/:

- Scrape [alle gegeorefereerde kaarten uit Beeldbank](http://beeldbank.amsterdam.nl/beeldbank/indeling/grid?f_string_geoserver_store%5B0%5D=%2A)
- Sla permalink en Geoserver Layer ID op
- Download alle GeoTIFF's, maak GeoJSON van mask
- Maak simpel website'je om masks te verbeteren
- Kopieër Maps by Decade!

Scripts:

1. [`scrape-beeldbank.js`](scrape-beeldbank.js)
2. [`create-links.js`](create-links.js)
3. [`download-tiffs.sh`](download-tiffs.sh)
4. [`extract-masks.sh`](extract-masks.sh)
5. [`to-spacetime.js`](to-spacetime.js)

Example links:

  - Thumbnails:
    - http://images.memorix.nl/ams/thumb/140x140/de61435f-d219-0f32-8930-3ec48422fcea.jpg
    - http://images.memorix.nl/ams/thumb/1000x1000/1fc4dfdc-e92c-6f65-2f3a-e960f9e52222.jpg
  - GeoTIFF:
    - http://geoserver.memorix.nl/geoserver/ams/wcs?service=WCS&version=2.0.1&request=GetCoverage&format=image/tiff&CoverageId=
  - JPG:
    - http://beeldbank.amsterdam.nl/component/ams_memorixbeeld_download/?view=download&format=download&id=DUIZ00345000001
  - WMS:
    - http://geoserver.memorix.nl/geoserver/ams/wms?SERVICE=WMS&REQUEST=GetMap&VERSION=1.1.1&LAYERS=ams%3Adabef258-08a5-4057-b154-acc4c112c678&STYLES=&FORMAT=image%2Fpng&TRANSPARENT=true&HEIGHT=256&WIDTH=256&ZINDEX=1&SRS=EPSG%3A4326&BBOX=4.85595703125,52.36218321674427,4.8779296875,52.3755991766591
