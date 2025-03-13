import httpService from './service'

const resourcePath = '/spa/transaction'

export const getListTransaction = (args) => {
    return httpService.getHttp().get(resourcePath+'-list', {params: args})
}
export const postDataTransaction = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}
export const deleteTransaction = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}