import httpService from './service'

const resourcePath = '/spa/chart-of-account'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}
export const getListParent = (...args) => {
    return httpService.getHttp().get(resourcePath+'-parent-list', ...args)
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
export const getUserSearch = (args) => {
    return httpService.getHttp().get('/spa/user-sales-search', { params: args })
}
