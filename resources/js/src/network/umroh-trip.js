import httpService from './service'

const resourcePath = '/spa/umroh-trip'
const resourcePathParticipantTab = '/spa/umroh-trip-participant'
const resourcePathCrewTab = '/spa/umroh-trip-crew'
const resourcePathParticipantEquipmentTab = '/spa/equipment-delivery-participant'
const resourcePathLetterInformation = '/spa/participant-letter-information'
const resourcePathChartDocumentParticipant = '/spa/chart-document-participant'
const resourcePathDocumentParticipant = '/spa/document-participant'
const resourcePathParticipantRoomListTab = '/spa/umroh-trip-participant-room-list'
const resourcePathBadal = '/spa/badal-prices'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const deleteTrip = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const getListParticipantTab = (...args) => {
    return httpService.getHttp().get(resourcePathParticipantTab, ...args)
}

export const getListParticipantEquipmentTab = (...args) => {
    return httpService.getHttp().get(resourcePathParticipantEquipmentTab, ...args)
}

export const postEquipmentAction = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantEquipmentTab + '/action', ...args)
}

export const postProcessEquipment = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantEquipmentTab + '/process', ...args)
}

export const postDeliveryEquipment = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantEquipmentTab + '/delivery', ...args)
}

export const postBulkDeliveryEquipment = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantEquipmentTab + '/bulk-delivery', ...args)
}

export const postAction = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + '/action', ...args)
}

export const postActionPDF = (args) => {
    return httpService.getHttp().get(resourcePathParticipantTab + '-pdf', { responseType: 'blob', params: args })
}

export const postVerification = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + '/verification', ...args)
}

export const postParticipant = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab, ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}

export const postTourCrew = (...args) => {
    return httpService.getHttp().post(resourcePath + '/add-tour-crew', ...args)
}

export const getTourCrewTab = (...args) => {
    return httpService.getHttp().get(resourcePath + '-tour-crew', ...args)
}

export const getParticipantLetter = (id, args) => {
    return httpService.getHttp().get(resourcePathParticipantTab + '/letter' + `/${id}`, { responseType: 'blob', params: args })
}

export const sendParticipantLetter = (id, args) => {
    return httpService.getHttp().get(resourcePathParticipantTab + '/send-letter' + `/${id}`, { params: args } )
}

export const viewLetter = (id, args) => {
    return httpService.getHttp().get(resourcePathParticipantTab + '/view-letter' + `/${id}`, { params: args } )
}

export const postLetterSign = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + '/post-letter-sign', ...args)
}

export const listDocumentLetter = (id, args) => {
    return httpService.getHttp().get(resourcePathParticipantTab + '/list-document-letter' + `/${id}`, { params: args })
}

export const getParticipantCertificate = (id, args) => {
    return httpService.getHttp().get(resourcePathParticipantTab + '/certificate' + `/${id}`, { responseType: 'blob', params: args })
}

export const importSiskopatuh = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + '/import-siskopatuh', ...args)
}

export const importSeat = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + '/import-seat', ...args)
}

export const downloadAllDocuments = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + '/download-documents', ...args)
}

export const downloadPassportDocuments = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + '/download-passport-documents', ...args)
}

export const downloadParticipantDocuments = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + '/download-participant-documents', ...args)
}

export const downloadLetters = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + '/download-letters', ...args)
}

export const downloadPasPhotos = (args) => {
    return httpService.getHttp().get(resourcePathParticipantTab + '/download-pas-photos', { responseType: 'blob', params: args })
}

export const getParticipantUmrohTripSearch = (args) => {
    return httpService.getHttp().get(resourcePathParticipantTab + '/search', { params: args })
}

export const getTourCrewSearch = (args) => {
    return httpService.getHttp().get(resourcePathCrewTab + '/search', { params: args })
}

export const getBusGroupSearch = (args) => {
    return httpService.getHttp().get(resourcePathParticipantTab + '/bus-search', { params: args })
}

export const getAirlines = (...args) => {
    return httpService.getHttp().get(resourcePath + '-airlines', ...args)
}

export const getUmrohTripSearch = (...args) => {
    return httpService.getHttp().get(resourcePath + '-search', ...args)
}

export const copyUmrohTrip = (...args) => {
    return httpService.getHttp().post(resourcePath + '/copy-umroh-trip', ...args)
}

export const getParticipantDetail = (participantId, ...args) => {
    return httpService.getHttp().get(resourcePathParticipantTab + `/${participantId}`, ...args)
}

export const getLetterInformation = (participantId, ...args) => {
    return httpService.getHttp().get(resourcePathLetterInformation + `/${participantId}`, ...args)
}

export const getChartDocumentParticipant = (umrohTripid, ...args) => {
    return httpService.getHttp().get(resourcePathChartDocumentParticipant + `/${umrohTripid}`, ...args)
}

export const getDocumentParticipant = (umrohTripid, ...args) => {
    return httpService.getHttp().get(resourcePathDocumentParticipant + `/${umrohTripid}`, ...args)
}

export const addPackage = (...args) => {
    return httpService.getHttp().post(resourcePath + '/add-package', ...args)
}

export const deletePackage = (...args) => {
    return httpService.getHttp().post(resourcePath  + '/delete-package', ...args)
}

export const addHotel = (...args) => {
    return httpService.getHttp().post(resourcePath + '/add-hotel', ...args)
}

export const deleteHotel = (...args) => {
    return httpService.getHttp().post(resourcePath  + '/delete-hotel', ...args)
}

export const updateWithoutBedParticipant = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + `/update-without-bed`, ...args)
}

export const updateInfantParticipant = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + `/update-infant`, ...args)
}

export const downloadAttendanceTagImages = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + '/download-attendance-tags', ...args)
}

export const generateNoUrut = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + `/generate-no-urut`, ...args)
}

export const getHotelUmrohTrip = (args) => {
    return httpService.getHttp().get(resourcePath + '-hotels', { params: args })
}

export const postAddRoom = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + '/add-room-group', ...args)
}

export const postResetRoom = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + '/reset-room-group', ...args)
}


export const postRemoveRoom = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + '/remove-room-group', ...args)
}

export const postCopyRoom = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + '/copy-room-group', ...args)
}

export const postAddSeat = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantTab + '/add-seat', ...args)
}

export const updateManifestfile = (...args) => {
    return httpService.getHttp().post(resourcePath + '/ready-manifest', ...args)
}

export const getParticipantFiles = (args) => {
    return httpService.getHttp().get(resourcePathParticipantTab + `/files`, { params: args })
}

export const getParticipantRoomListSearch = (args) => {
    return httpService.getHttp().get(resourcePathParticipantRoomListTab + '/search', { params: args })
}

export const getResumeDeparture = (args) => {
    return httpService.getHttp().get(resourcePath + '/resume', { responseType: 'blob', params: args })
}

export const addBadalPrice = (...args) => {
    return httpService.getHttp().post(resourcePath + '/add-badal-price', ...args)
}

export const getBadalPriceList = (args) => {
    return httpService.getHttp().get(resourcePathBadal, { params: args })
}

export const deleteBadalPrice = (id) => {
    return httpService.getHttp().delete(resourcePathBadal + `/${id}`)
}

export const postDeleteDeliveryEvidence = (...args) => {
    return httpService.getHttp().post(resourcePathParticipantEquipmentTab + '/delete-delivery-evidence', ...args)
}

export const getAbsensiKepulangan = (args) => {
    return httpService.getHttp().get(resourcePath + '/absensi-kepulangan', { responseType: 'blob', params: args })
}

export const getRoomComposition = (id) => {
    return httpService.getHttp().get(resourcePath + `/room-composition/` + id)
}

export const getTourCrewLetter = (id, args) => {
    return httpService.getHttp().get(resourcePathParticipantTab + '/tour-crew-letter' + `/${id}`, { responseType: 'blob', params: args })
}

// GENERATE NO LINK
export const generateLinkDocument = (...args) => {
    return httpService.getHttp().post(resourcePath + `/generate-link-document`, ...args)
}

// UPLOAD TRIP DOCUMENT
export const uploadTripDocument = (...args) => {
    return httpService.getHttp().post(resourcePath + '/upload-trip-document', ...args)
}

// DOWNLOAD FILE TRIP
export const downloadDocumentUmrohTrip = (id) => {
    return httpService.getHttp().get(resourcePath + '/download-trip-document' + `/${id}`, { responseType: 'blob' })
}

export const getDocumentUmrohTrips = (umrohTripId, args) => {
    return httpService.getHttp().get(resourcePath + '/trip-documents' + `/${umrohTripId}`, { params: args })
}

export const deleteDocumentUmrohTrip = (id) => {
    return httpService.getHttp().delete(resourcePath + '/trip-document' + `/${id}`)
}

export const getAvailableSeats = (...args) => {
    return httpService.getHttp().get(resourcePath + '/available-seats', ...args)
}