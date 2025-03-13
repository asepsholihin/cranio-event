import httpService from './service'

const resourcePath = '/spa/log-accurate-activity'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}
