import httpService from './service'

const resourcePath = '/spa/ticketing'
const resourcePathManifest = '/spa/ticketing/download-manifest'

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

export const postUpdateData = (...args) => {
    return httpService.getHttp().post(resourcePath + '/update-data', ...args)
}

export const downloadManifest = (args) => {
    return httpService.getHttp().get(resourcePathManifest, { responseType: 'blob', params: args })
}

export const copyData = (...args) => {
    return httpService.getHttp().post(resourcePath+'-copy', ...args)
}
export const getTripSearch = (args) => {
    return httpService.getHttp().get(resourcePath + '-trip-search', { params: args })
}

export const getTicketingSearch = (args) => {
    return httpService.getHttp().get(resourcePath + '-search', { params: args })
}

export const getListTicketing = (args) => {
    return httpService.getHttp().get(resourcePath + '-list', { params: args })
}