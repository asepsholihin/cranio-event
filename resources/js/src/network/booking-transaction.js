import httpService from './service'

const resourcePath = '/spa/booking-transaction'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}