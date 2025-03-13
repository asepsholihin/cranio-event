import httpService from './service'

const resourcePath = '/spa/umroh-booking-seat'
const resourceOrderPath = '/spa/umroh-booking-order'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}

export const postDataBadal = (...args) => {
    return httpService.getHttp().post(resourcePath + `/badal`, ...args)
}

export const postUpdateBadal = (...args) => {
    return httpService.getHttp().post(resourcePath + `/update-badal`, ...args)
}

export const deleteData = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const postSendMail = (id) => {
    return httpService.getHttp().post(resourcePath + `/send-invoice-email/${id}`)
}

export const getInvoicePDF = (id) => {
    return httpService.getHttp().get(resourcePath + '/download-invoice'+ `/${id}`,{ responseType: 'blob' })
}

export const getRoomTypes = (...args) => {
    return httpService.getHttp().get(resourcePath + '-room-types', ...args)
}

export const getHotels = (...args) => {
    return httpService.getHttp().get(resourcePath + '-hotels', ...args)
}

export const getBookingSearch = (...args) => {
    return httpService.getHttp().get(resourcePath + '-search', ...args)
}

export const getBookingOrderSearch = (...args) => {
    return httpService.getHttp().get(resourceOrderPath + '-search', ...args)
}

export const getAssignedParticipant = (id, ...args) => {
    return httpService.getHttp().get(resourcePath + `/${id}/`+ 'assigned-participant', ...args)
}

export const postUpgradeHotel = (...args) => {
    return httpService.getHttp().post(resourcePath + '-upgrade-hotel', ...args)
}