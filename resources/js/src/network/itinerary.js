import httpService from './service'

const resourcePath = '/spa/itinerary'
const resourceCategoryPath = '/spa/itinerary-category'

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

export const getItinerarySearch = (args) => {
    return httpService.getHttp().get(resourcePath + '/search', {params: args})
}

export const importData = (...args) => {
    return httpService.getHttp().post(resourcePath + '/import', ...args)
}

export const postCategory = (...args) => {
    return httpService.getHttp().post(resourceCategoryPath, ...args)
}

export const getItineraryCategorySearch = (args) => {
    return httpService.getHttp().get(resourceCategoryPath + '/search', {params: args})
}

export const deleteItineraryCategory = (id) => {
    return httpService.getHttp().delete(resourceCategoryPath + `/${id}`)
}