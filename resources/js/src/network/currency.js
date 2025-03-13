import httpService from './service'

const resourcePath = '/spa/currency'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}
export const getAllList = (...args) => {
    return httpService.getHttp().get(resourcePath+"-all", ...args)
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

export const getCurrencySearch = (args) => {
    return httpService.getHttp().get(resourcePath + '-search', {params: args})
}
