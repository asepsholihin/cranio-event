import httpService from './service'

const resourcePath = '/spa/marketplace-transaction'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const deleteData = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const deleteItemData = (id) => {
    return httpService.getHttp().get(resourcePath +'-delete-item'+ `/${id}`)
}

export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}

export const postAction = (...args) => {
    return httpService.getHttp().post(resourcePath + '/action', ...args)
}

export const getMarketplaceTransactionPDF = (id) => {
    return resourcePath + '-document-pdf/' + id
}
