import httpService from './service'

const resourcePath = '/spa/participant'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getRawList = (...args) => {
    return httpService.getHttp().get(resourcePath + '-raw-data', ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const deleteData = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const getFiles = (id) => {
    return httpService.getHttp().get(resourcePath + `/files/${id}`)
}

export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}

export const postUpdateDomisili = (...args) => {
    return httpService.getHttp().post(resourcePath + '/update-domisili', ...args)
}

export const postUpdateData = (...args) => {
    return httpService.getHttp().post(resourcePath + '/update-data', ...args)
}

export const getParticipantSearch = (args) => {
    return httpService.getHttp().get(resourcePath + '/search', {params: args})
}

export const getParticipantForBookingSearch = (args) => {
    return httpService.getHttp().get(resourcePath + '/search-for-booking', {params: args})
}

export const uploadPasPhoto = (...args) => {
    return httpService.getHttp().post(resourcePath + '/upload-pas-photo', ...args)
}

export const uploadFile = (...args) => {
    return httpService.getHttp().post(resourcePath + '/upload-verification-file', ...args)
}

export const importData = (...args) => {
    return httpService.getHttp().post(resourcePath + '/import', ...args)
}

export const deleteFile = (...args) => {
    return httpService.getHttp().post(resourcePath + '/delete-file', ...args)
}

export const deleteSpecificFile = (...args) => {
    return httpService.getHttp().post(resourcePath + '/delete-specific-file', ...args)
}

export const createAccessLogin = (...args) => {
    return httpService.getHttp().post(resourcePath + '/create-access-login', ...args)
}

export const createNameInCertificate = (...args) => {
    return httpService.getHttp().post(resourcePath + '/create-name-in-certificate', ...args)
}

export const exportParticipant = (args) => {
    return httpService.getHttp().get(resourcePath + '/export', {responseType: 'blob', params: args})
}

export const exportParticipantAddress = (args) => {
    return httpService.getHttp().get(resourcePath + '/export-participant-address', {responseType: 'blob', params: args})
}

export const importParticipantAddress = (...args) => {
    return httpService.getHttp().post(resourcePath + '/import-participant-address', ...args)
}

export const uploadReceiveDocument = (...args) => {
    return httpService.getHttp().post(resourcePath + '/upload-receive-document', ...args)
}

export const getMedicalRecordSearch = (...args) => {
    return httpService.getHttp().get(resourcePath + '/medical-record-search', ...args)
}

export const convertToBlob = (...args) => {
    return httpService.getHttp().post(resourcePath + '/convert-to-blob', ...args)
}

export const getJobSearch = (...args) => {
    return httpService.getHttp().get(resourcePath + '/job-search', ...args)
}

export const getChartGender = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-gender', ...args)
}

export const getChartPoloSize = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-polo-size', ...args)
}
export const postAction = (...args) => {
    return httpService.getHttp().post(resourcePath + '/action', ...args)
}
