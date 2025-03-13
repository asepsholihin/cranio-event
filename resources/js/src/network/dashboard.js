import httpService from './service'

const resourcePath = '/spa'

export const getData = (...args) => {
    return httpService.getHttp().get(resourcePath + '/dashboard', ...args)
}

export const getCountBadge = () => {
    return httpService.getHttp().get(resourcePath + '/count-badge-nav', {timeout: 3000})
}