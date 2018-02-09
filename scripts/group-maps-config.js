module.exports = {
  groupBy: (object) => {
    const getDate = (str) => new Date(`${str}`)
    const bandSize = 10
    const validSince = object.validSince
    const validUntil = object.validUntil
    const year = (getDate(validSince).getFullYear() + getDate(validUntil).getFullYear()) / 2
    return Math.floor(year / bandSize) * bandSize
  },
  properties: (object) => ({
    id: object.id,
    uuid: object.data.uuid,
    imageId: object.data.imageId,
    name: object.name,
    year: object.validSince
  }),
  geometry: {
    type: 'Polygon',
    coordinates: [
      [
        [
          -73.424377,
          40.436495
        ],
        [
          -73.424377,
          41.15591
        ],
        [
          -74.347229,
          41.15591
        ],
        [
          -74.347229,
          40.436495
        ],
        [
          -73.424377,
          40.436495
        ]
      ]
    ]
  },
  yearMin: 1850,
  yearMax: 1949,
  maxArea: 5000000,
  buffer: 25,
  simplifyTolerance: 0.00005
}
