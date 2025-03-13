import httpService from './service'

const resourcePath = '/spa/participant-crm'

export const getList = (...args) => httpService.getHttp().get(resourcePath, ...args)

export const postData = (...args) => httpService.getHttp().post(resourcePath, ...args)

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath+'-detail' + `/${id}`)
}

export const deleteData = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const uploadMiladPhotos = (...args) => httpService.getHttp().post(resourcePath + '/upload-milad-photo', ...args)

export const getFiles = (id) => {
    return httpService.getHttp().get(resourcePath + `/files/${id}`)
}

export const getCertificateImage = (id) => {
    return httpService.getHttp().get(resourcePath + `/getCertificateImage/${id}`)
}

export const getParticipantCertificate = (id, args) => {
    return httpService.getHttp().get(resourcePath + '/certificate'+ `/${id}`,{ responseType: 'blob', params: args })
}

export const getParticipantCertificatePreview = (id) => {
    // return httpService.getHttp().get(resourcePath + '/preview-certificate'+ `/${id}`,{ responseType: "application/pdf", params: args })
    return resourcePath + '/preview-certificate'+ `/${id}`
}

export const downloadCertificate = (args) => {
    return httpService.getHttp().get(resourcePath + '/download-certificate', { params: args })
}

export const deleteFile = (...args) => httpService.getHttp().post(resourcePath + '/delete-photo-milad', ...args)

export const exportParticipant = (args) => {
    return httpService.getHttp().get(resourcePath + '/export', { responseType: 'blob', params: args })
}

export const importData = (...args) => {
    return httpService.getHttp().post(resourcePath + '/import', ...args)
}

export const getTripSearch = (args) => {
    return httpService.getHttp().get(resourcePath + '/trip-search', {params: args})
}

export const getListParticipantMerge = (args) => {
    return httpService.getHttp().get(resourcePath + '/list-participant-merge', {params: args})
}

export const postMergeParticipant = (...args) => httpService.getHttp().post(resourcePath + '/merge-participant', ...args)

export const postRemoveMergeParticipant = (...args) => httpService.getHttp().post(resourcePath + '/remove-merge-participant', ...args)

export const getFilterCities = (args) => {
    return httpService.getHttp().get(resourcePath + '/filter-cities', {params: args})
}

export const getFilterProvinces = (args) => {
    return httpService.getHttp().get(resourcePath + '/filter-provinces', {params: args})
}

export const getFilterTotalAccount = (args) => {
    return httpService.getHttp().get(resourcePath + '/filter-total-account', {params: args})
}

export const getFilterPackages = (args) => {
    return httpService.getHttp().get(resourcePath + '/filter-packages', {params: args})
}

export const getFilterJobs = (args) => {
    return httpService.getHttp().get(resourcePath + '/filter-jobs', {params: args})
}

export const getTransactions = (args) => {
    return httpService.getHttp().get(resourcePath + '/transactions', {params: args})
}

export const setParentAccount = (...args) => httpService.getHttp().post(resourcePath + '/set-parent-account', ...args)

export const viewParentAccount = (id, args) => {
    return httpService.getHttp().get(resourcePath + '/view-parent-account/' + `${id}`, {params: args})
}

export const updateDatabaseCRM = (args) => {
    return httpService.getHttp().get('/spa/refine-participant-crm-manual', {params: args})
}