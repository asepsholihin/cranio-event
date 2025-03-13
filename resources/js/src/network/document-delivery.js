import httpService from './service'

const resourcePath = '/spa/document-delivery'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const deleteData = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const deleteMultipleData = (...args) => {
    return httpService.getHttp().post(resourcePath + '/deletes', ...args)
}

export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}

export const postDataAddress = (...args) => {
    return httpService.getHttp().post(resourcePath + '/address', ...args)
}

export const postPickup = (...args) => {
    return httpService.getHttp().post(resourcePath + '/pickup', ...args)
}

export const postDelivery = (...args) => {
    return httpService.getHttp().post(resourcePath + '/delivery', ...args)
}

export const getInfoDelivery = (...args) => {
    return httpService.getHttp().get(resourcePath + '/participants', ...args)
}

export const exportData = (args) => {
    return httpService.getHttp().get(resourcePath + '-export', { responseType: 'blob', params: args })
}

export const exportShipmentData = (args) => {
    return httpService.getHttp().get(resourcePath + '-shipment-export', { responseType: 'blob', params: args })
}

export const getShipmentLabelUrl = (id) => {
    return resourcePath + '/shipment-label/' + id
}

export const getShipmentLabelsUrl = () => {
    return resourcePath + '/shipment-labels'
}