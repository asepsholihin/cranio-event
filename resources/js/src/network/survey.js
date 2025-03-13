import httpService from './service'

const resourcePath = '/spa/survey'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const getSummary = (id, args) => {
    return httpService.getHttp().get(resourcePath + `/${id}/summary`, { params: args })
}

export const deleteData = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}

export const getTrips = (args) => {
    return httpService.getHttp().get(resourcePath + `-trip-search`, { params: args })
}

export const exportSummaryQuestion = (args) => {
    return httpService.getHttp().get(resourcePath + '-export-summary-question', { params: args, responseType: 'blob' })
}

export const exportSummary = (args) => {
    return httpService.getHttp().get(resourcePath + '-export-summary', { params: args, responseType: 'blob' })
}

export const updateAnswerSection = (...args) => {
    return httpService.getHttp().post(resourcePath + `/update-answer-section`, ...args)
}

export const updateAnswer = (...args) => {
    return httpService.getHttp().post(resourcePath + `/update-answer`, ...args)
}