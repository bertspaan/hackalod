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
    imageId: object.data.imageId,
    name: object.name,
    year: object.validSince
  }),
  geometry: {
    type: 'Polygon',
    coordinates:  [
      [
        [
          5.3063969257812635,
          52.05417909696046
        ],
        [
          5.3063969257812635,
          52.63472952447904
        ],
        [
          4.383544925781263,
          52.63472952447904
        ],
        [
          4.383544925781263,
          52.05417909696046
        ],
        [
          5.3063969257812635,
          52.05417909696046
        ]
      ]
    ]
  },
  yearMin: 1000,
  yearMax: 2020,
  maxArea: 10000000,
  buffer: 25,
  simplifyTolerance: 0.00005
}
