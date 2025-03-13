import httpService from './service'

const resourcePath = '/spa/log-qontak-activity'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}
