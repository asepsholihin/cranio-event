import httpService from './service'

const resourcePath = '/spa/jios'

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

export const getItemSearch = (args) => {
    return httpService.getHttp().get(resourcePath + '-search', {params: args})
}

export const generateLinkJios = (...args) => {
    return httpService.getHttp().post(resourcePath + '/generate-link', ...args)
}

export const resumeJios = (umrohTripId) => {
    return httpService.getHttp().get(resourcePath + `/resume/${umrohTripId}`)
}
