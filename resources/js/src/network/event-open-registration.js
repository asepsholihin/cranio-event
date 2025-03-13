import httpService from './service'

const resourcePath = '/spa/event-open-registration'
const resourcePathAttendee = '/spa/attendee-open-registration'
const resourcePathPublic = '/public/event-open-registration-form'
const resourcePathSeat = '/spa/event-open-settled-seats'
const resourcePathCheckin = '/spa/checkin-event-open-seat'



export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getListAttendee = (...args) => {
    return httpService.getHttp().get(resourcePathAttendee, ...args)
}

export const postAttendee = (...args) => {
    return httpService.getHttp().post(resourcePathAttendee, ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const getPublicDetail = (id) => {
    return httpService.getHttp().get(resourcePathPublic + `/${id}`)
}

export const postPublic = (...args) => {
    return httpService.getHttp().post(resourcePathPublic, ...args)
}

export const getBarcode = (id) => {
    return httpService.getHttp().get(resourcePathAttendee + '/barcode'+ `/${id}`,{ responseType: 'blob' })
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

export const generateOpenRegistrationLink = (id) => {
    return httpService.getHttp().get(resourcePath + '/generate-event-registration-link'+ `/${id}`,{ responseType: 'blob' })
}

export const getChartAttendance = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-attendance', ...args)
}

export const getChartParticipant = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-participant', ...args)
}

export const sendBarcode = (...args) => {
    return httpService.getHttp().post(resourcePathAttendee + '/send-barcode', ...args)
}

export const getMappingSeatList = (args) => {
    return httpService.getHttp().get(resourcePathAttendee + '-seats', { params: args })
}

export const getSeatSettledList = (args) => {
    return httpService.getHttp().get(resourcePathSeat, { params: args })
}

export const postMappingSeat = (...args) => {
    return httpService.getHttp().post(resourcePathAttendee + '-seat', ...args)
}

export const postCheckinEventOpenSeat = (...args) => {
    return httpService.getHttp().post(resourcePathCheckin, ...args)
}

export const postAddAttendee = (...args) => {
    return httpService.getHttp().post(resourcePathAttendee + '/add', ...args)
}

export const downloadBarcode = (...args) => {
    return httpService.getHttp().post(resourcePathAttendee + '/download-barcode', ...args)
}

export const getCheckinEventOpenSeatReport = (args) => {
    return httpService.getHttp().get(resourcePathCheckin + '-report', { params: args })
}