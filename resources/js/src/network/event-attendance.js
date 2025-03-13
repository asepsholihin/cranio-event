import httpService from './service'

const resourcePath = '/spa/event-attendance'
const resourcePathAttendee = '/spa/event-attendee'
const resourcePathParticipant = '/spa/participant'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getUmrohTrip = (args) => {
    return httpService.getHttp().get(resourcePath + '/umroh-trip-search', { params: args })
}

export const getListAttendee = (...args) => {
    return httpService.getHttp().get(resourcePathAttendee, ...args)
}

export const postAttendee = (...args) => {
    return httpService.getHttp().post(resourcePathAttendee, ...args)
}

export const getDetail = (id, args) => {
    return httpService.getHttp().get(resourcePath + `/${id}`, { params: args })
}

export const getParticipantSearch = (args) => {
    return httpService.getHttp().get(resourcePathParticipant + '/search', { params: args })
}

export const getParticipantBarcode = (id) => {
    return httpService.getHttp().get(resourcePathParticipant + '/barcode' + `/${id}`, { responseType: 'blob' })
}

export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}

export const deleteEventNetwork = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const deleteAttendeeNetwork = (id) => {
    return httpService.getHttp().delete(resourcePathAttendee + `/${id}`)
}

export const deleteAttendee = (...args) => {
    return httpService.getHttp().post(resourcePathAttendee + `/delete`, ...args)
}

export const getParticipantDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/participant-detail` + `/${id}`)
}

export const updateManasikTable = (...args) => {
    return httpService.getHttp().post(resourcePath + `/update-manasik-table`, ...args)
}

export const departureConfirmation = (...args) => {
    return httpService.getHttp().post(resourcePath + `/departure-confirmation`, ...args)
}

export const departureConfirmationByAdmin = (...args) => {
    return httpService.getHttp().post(resourcePath + `/departure-confirmation-admin`, ...args)
}

export const getListAttendance = (id) => {
    return httpService.getHttp().get(resourcePath + `/list-participant-unattendee` + `/${id}`)
}

export const generateEventLink = (...args) => {
    return httpService.getHttp().post(resourcePath + `/generate-event-link`, ...args)
}

export const generateDepartureConfirmationLink = (id) => {
    return httpService.getHttp().get(resourcePath + '/generate-departure-confirmation-link'+ `/${id}`,{ responseType: 'blob' })
}

export const getChartAttendance = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-attendance', ...args)
}

export const getChartAttendanceConfirmation = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-attendance-confirmation', ...args)
}

export const getChartAttendanceConfirmationByPackage = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-attendance-confirmation-by-package', ...args)
}

export const importDeparture = (...args) => {
    return httpService.getHttp().post(resourcePath + '/import-departure', ...args)
}

export const exportDeparture = (...args) => {
    return httpService.getHttp().post(resourcePath + '/export-departure', ...args)
}

export const exportDepartureUpdate = (...args) => {
    return httpService.getHttp().post(resourcePath + '/export-departure-update', ...args)
}

export const exportData = (args) => {
    return httpService.getHttp().get(resourcePath + '-export-report', { params: args, responseType: 'blob' })
}

export const sendParticipantBarcode = (args) => {
    return httpService.getHttp().get(resourcePath + '/send-barcode', { params: args })
}

export const getChartManasikOnline = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-manasik-online', ...args)
}

export const resetDepartureConfirmation = (...args) => {
    return httpService.getHttp().post(resourcePathAttendee + `/reset-departure-confirmation`, ...args)
}