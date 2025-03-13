import httpService from './service'

const resourcePath = '/spa/address'

export const getProvinces = (...args) => {
    return httpService.getHttp().get(resourcePath + '/provinces', ...args)
}

export const getCities = (province) => {
    return httpService.getHttp().get(resourcePath + '/cities/?province=' + province)
}

export const getDistricts = (city) => {
    return httpService.getHttp().get(resourcePath + '/districts?city=' + city)
}

export const getSubdistricts = (district) => {
    return httpService.getHttp().get(resourcePath + '/subdistricts?district=' + district)
}

export const getPostalcodes = (subdistrict, district = '') => {
    return httpService.getHttp().get(resourcePath + '/postalcodes?subdistrict=' + subdistrict + '&district=' + district)
}

export const getCitiesSearch = (args) => {
    return httpService.getHttp().get(resourcePath + '/city-search', { params: args })
}
