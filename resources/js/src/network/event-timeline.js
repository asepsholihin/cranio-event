import httpService from './service'

const resourcePath = '/spa/event-timeline'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const deleteData = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const updateData = (...args) => {
    return httpService.getHttp().post(resourcePath + '/update', ...args)
}

