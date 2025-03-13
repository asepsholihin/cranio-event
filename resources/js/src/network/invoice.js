import httpService from './service'

const resourcePath = '/spa/invoice'
const resourcePathParticipant = '/spa/participant'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const deleteData = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const postPayment = (...args) => {
    return httpService.getHttp().post(resourcePath + '/add-payment', ...args)
}

export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}

export const uploadInvoiceCreditReceipt = (id, ...args) => {
    return httpService.getHttp().post(resourcePath + `/upload-credit-receipt/${id}`, ...args)
}

export const getInvoicePDF = (id) => {
    //return httpService.getHttp().get(resourcePath + '/download-invoice'+ `/${id}`,{ responseType: 'blob' })
    return resourcePath + '/download-invoice/' + id
}

export const getReceiptPDF = (id) => {
    //return httpService.getHttp().get(resourcePath + '/download-receipt'+ `/${id}`,{ responseType: 'blob' })
    return resourcePath + '/download-receipt/' + id
}

export const getParticipantSearch = (args) => {
    return httpService.getHttp().get(resourcePathParticipant + '/search', {params: args})
}

export const uploadPasPhoto = (...args) => {
    return httpService.getHttp().post(resourcePath + '/upload-pas-photo', ...args)
}