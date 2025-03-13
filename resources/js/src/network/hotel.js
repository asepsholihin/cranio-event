import httpService from './service'

const resourcePath = '/spa/hotel'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const deleteData = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}

export const getHotelSearch = (args) => {
    return httpService.getHttp().get(resourcePath + '-search', {params: args})
}

export const getHotelByCity = (id) => {
    return httpService.getHttp().get(resourcePath + '-by-city' + `/${id}`)
}

export const getHotelGraphicUrl = (id) => {
    return resourcePath + '-graphic/' + id
}

export const postHotelRate = (...args) => {
    return httpService.getHttp().post(resourcePath + '/rate', ...args)
}

export const getHotelRateHistories = (id) => {
    return httpService.getHttp().get(resourcePath + '-rate-histories' + `/${id}`)
}
