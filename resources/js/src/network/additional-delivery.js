import httpService from './service'

const resourcePath = '/spa/additional-delivery'

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

export const getEquipmentSearch = (...args) => {
    return httpService.getHttp().get(resourcePath + '-search', ...args)
}

export const getParticipantEquipment = (...args) => {
    return httpService.getHttp().get(resourcePath + '/participant', ...args)
}

export const postProcessEquipment = (...args) => {
    return httpService.getHttp().post(resourcePath + '/process', ...args)
}

export const postDeliveryEquipment = (...args) => {
    return httpService.getHttp().post(resourcePath + '/delivery', ...args)
}

export const getAdditionalDelivery = (...args) => {
    return httpService.getHttp().get(resourcePath + '/participants', ...args)
}

export const getShipmentLabelsUrl = (umrohTripId) => {
    return resourcePath + '/shipment-labels/' + umrohTripId
}