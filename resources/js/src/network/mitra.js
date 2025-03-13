import httpService from './service'

const resourcePath = '/spa/mitra'
const resourcePathParticipant = '/spa/participant'
const resourcePathTransaction = '/spa/mitra-transaction'
const resourcePathWithdrawal = '/spa/mitra-withdraw'
const resourcePathMedia = '/spa/mitra-media'

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

export const getMitraSearch = (args) => {
    return httpService.getHttp().get(resourcePath + '/search', {params: args})
}

export const getParticipantSearch = (args) => {
    return httpService.getHttp().get(resourcePathParticipant + '/search', {params: args})
}

export const uploadPasPhoto = (...args) => {
    return httpService.getHttp().post(resourcePath + '/upload-pas-photo', ...args)
}

export const importData = (...args) => {
    return httpService.getHttp().post(resourcePath + '/import', ...args)
}

export const exportMitra = (...args) => {
    return httpService.getHttp().post(resourcePath + '/export', ...args)
}

export const getTransactionList = (...args) => {
    return httpService.getHttp().get(resourcePathTransaction, ...args)
}

export const getMediaList = (...args) => {
    return httpService.getHttp().get(resourcePathMedia, ...args)
}

export const downloadMitraMedia = (id) => {
    return resourcePathMedia + '/download' + `/${id}`
}

export const getWithdrawList = (...args) => {
    return httpService.getHttp().get(resourcePathWithdrawal, ...args)
}

export const postWithdraw = (...args) => {
    return httpService.getHttp().post(resourcePathWithdrawal + '/submit', ...args)
}