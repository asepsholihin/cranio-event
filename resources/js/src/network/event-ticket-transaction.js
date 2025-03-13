import httpService from './service'

const resourcePath = '/spa/event-ticket-transaction'


export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}

export const deleteData = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const resendTicket = (...args) => {
    return httpService.getHttp().post(resourcePath + `/resend-ticket`, ...args)
}