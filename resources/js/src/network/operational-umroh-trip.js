import httpService from './service'

const resourcePath = '/spa/operational-umroh-trip'
const resourceReservationPath = '/spa/operational-hotel-reservation'
const resourcePaymentPath = '/spa/operational-hotel-payment'
const resourceItineraryPath = '/spa/operational-hotel-itinerary'
const resourceStatisItineraryPath = '/spa/operational-hotel-statis-itinerary'
const resourceBadalPath = '/spa/badal-umroh-haji'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getAllList = (id) => {
    return httpService.getHttp().get(resourceReservationPath+"-list"+`/${id}`)
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

// Reservation Info
export const getHotelReservations = (...args) => {
    return httpService.getHttp().get(resourceReservationPath, ...args)
}

export const getHotelReservation = (id) => {
    return httpService.getHttp().get(resourceReservationPath + `/${id}`)
}

export const deleteHotelReservation = (id) => {
    return httpService.getHttp().delete(resourceReservationPath + `/${id}`)
}

export const postDataReservation = (...args) => {
    return httpService.getHttp().post(resourcePath + `/add-reservation`, ...args)
}

export const postUploadRoomlistByHandling = (...args) => {
    return httpService.getHttp().post(resourcePath + `/upload-roomlist-by-handling`, ...args)
}

// Payment Info
export const getHotelPayments = (...args) => {
    return httpService.getHttp().get(resourcePaymentPath, ...args)
}

export const getHotelPayment = (id) => {
    return httpService.getHttp().get(resourcePaymentPath + `/${id}`)
}

export const deleteHotelPayment = (id) => {
    return httpService.getHttp().delete(resourcePaymentPath + `/${id}`)
}

export const postDataPayment = (...args) => {
    return httpService.getHttp().post(resourcePath + `/add-payment`, ...args)
}

// Itinerary
export const getItineraries = (...args) => {
    return httpService.getHttp().get(resourceItineraryPath, ...args)
}

export const getStatisItineraries = (...args) => {
    return httpService.getHttp().get(resourceStatisItineraryPath, ...args)
}

export const getItinerary = (id) => {
    return httpService.getHttp().get(resourceItineraryPath + `/${id}`)
}

export const deleteItinerary = (id) => {
    return httpService.getHttp().delete(resourceItineraryPath + `/${id}`)
}

export const postItinerary = (...args) => {
    return httpService.getHttp().post(resourcePath + `/add-itinerary`, ...args)
}

export const postChecklistItinerary = (...args) => {
    return httpService.getHttp().post(resourceItineraryPath + `/checklist-itinerary`, ...args)
}

export const postUpdateBadal = (...args) => {
    return httpService.getHttp().post(resourceBadalPath + `/update-badal`, ...args)
}

export const postBadalCertificate = (...args) => {
    return httpService.getHttp().post(resourceBadalPath + `/post-badal-certificate`, ...args)
}

export const downloadBadalCertificate = (args) => {
    return httpService.getHttp().get(resourceBadalPath + `/download-badal-certificate`, { params: args, responseType: 'blob' })
}

export const getShipmentLabelsUrl = () => {
    return resourceBadalPath + '/shipment-labels'
}

// badal get asatidz
export const getAssatidz = () => {
    return httpService.getHttp().get(resourceBadalPath + '/assatidz')
}

export const getDetailBadal = (id) => {
    return httpService.getHttp().get(resourceBadalPath + `/${id}`)
}
