import httpService from './service'

const resourcePath = '/spa/target-achivement'

export const getDetail = (...args) => {
    return httpService.getHttp().get(resourcePath+'-detail', ...args)
}
export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}
