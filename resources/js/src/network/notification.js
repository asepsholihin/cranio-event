import httpService from './service'

const resourcePath = '/spa/notifications'

export const getNotifications = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const readNotification = (...args) => {
    return httpService.getHttp().post(resourcePath + '/read', ...args)
}

export const readAllNotification = (...args) => {
    return httpService.getHttp().post(resourcePath + '/read-all', ...args)
}