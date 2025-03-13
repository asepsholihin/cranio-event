import httpService from './service'

const resourcePath = '/spa/lead-source'
const resourcePathSearch = '/spa/lead-source-search'

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

export const getLeadSourcesSearch = (args) => {
    return httpService.getHttp().get(resourcePathSearch, {params: args})
}
