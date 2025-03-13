import httpService from './service'

const resourcePath = '/spa/order-change-request'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const approvalRequest = (...args) => {
    return httpService.getHttp().post(resourcePath + '/approval', ...args)
}

export const deleteData = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}
