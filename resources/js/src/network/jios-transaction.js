import httpService from './service'

const resourcePath = '/spa/jios-transaction'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}