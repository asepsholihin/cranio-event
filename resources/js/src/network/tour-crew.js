import httpService from './service'

const resourcePath = '/spa/tour-crew'
const resourcePathParticipant = '/spa/participant'

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

export const getCrewSearch = (args) => {
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

export const exportCrew = (...args) => {
    return httpService.getHttp().post(resourcePath + '/export', ...args)
}