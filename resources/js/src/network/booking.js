import httpService from './service'

const resourcePath = '/spa/booking'

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

export const postAction = (...args) => {
    return httpService.getHttp().post(resourcePath + '/action', ...args)
}

export const exportData = (args) => {
    return httpService.getHttp().get(resourcePath + '-export', { responseType: 'blob', params: args })
}

export const getInvoicePDF = (id) => {
    return httpService.getHttp().get(resourcePath + '/download-invoice/'+ `${id}`,{ responseType: 'blob' })
    // return resourcePath + '/download-invoice/' + id
}

export const getReceiptPDF = (id) => {
    return httpService.getHttp().get(resourcePath + '/download-receipt'+ `/${id}`,{ responseType: 'blob' })
    // return resourcePath + '/download-receipt/' + id
}