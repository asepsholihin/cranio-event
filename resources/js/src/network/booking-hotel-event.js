import httpService from './service'

const resourcePath = '/spa/booking-hotel-event'

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

export const exportData = (args) => {
    return httpService.getHttp().get(resourcePath + '-export', { responseType: 'blob', params: args })
}

export const getReceiptPDF = (id) => {
    //return httpService.getHttp().get(resourcePath + '/download-receipt'+ `/${id}`,{ responseType: 'blob' })
    return resourcePath + '/download-receipt/' + id
}

export const getHotelSearch = (args) => {
    return httpService.getHttp().get(resourcePath + '/hotel-search', { params: args })
}

export const getRoomTypeSearch = (args) => {
    return httpService.getHttp().get(resourcePath + '/room-type-search', { params: args })
}

export const reCreateInvoice = (...args) => {
    return httpService.getHttp().post(resourcePath + '/recreate-invoice', ...args)
}