import httpService from './service'

const resourcePath = '/spa/inquiry'

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

export const getEquipmentSearch = (args) => {
    return httpService.getHttp().get(resourcePath + '/search', {params: args})
}
export const getEquipmentGroupBy = (args) => {
    return httpService.getHttp().get(resourcePath + '/get-inquiry-group', {params: args})
}
export const exportInquiry = (...args) => {
    return httpService.getHttp().post(resourcePath + '/export', ...args)
}
