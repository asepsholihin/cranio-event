import httpService from './service'

const resourcePath = '/spa/sales-lead'
const resourcePerformanceInvoicePath = '/spa/log-performance-invoices'
const resourcePathDownload = '/spa/log-performance-invoices-download'

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

export const postPerformanceInvoice = (...args) => {
    return httpService.getHttp().post(resourcePath + '/performance-invoice', ...args)
}

export const postBookingOrder = (...args) => {
    return httpService.getHttp().post(resourcePath + '/booking-order', ...args)
}

export const getLogPerformanceInvoices = (salesLeadId, ...args) => {
    return httpService.getHttp().get(resourcePerformanceInvoicePath +"/"+ salesLeadId, ...args)
}

export const getLogPerformanceInvoiceDetail = (salesLeadId) => {
    return httpService.getHttp().get(resourcePerformanceInvoicePath +"-detail/"+ salesLeadId)
}

export const getInvoicePDF = (id) => {
    return resourcePathDownload + '/' + id
}

export const importData = (...args) => {
    return httpService.getHttp().post(resourcePath + '/import', ...args)
}

export const exportData = (...args) => {
    return httpService.getHttp().get(resourcePath + '-export', ...args)
}

export const getCities = (...args) => {
    return httpService.getHttp().get(resourcePath + '-cities', ...args)
}