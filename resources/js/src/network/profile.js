import httpService from './service'

const updatePasswordPath = '/user/password'

export const updatePassword = (...args) => {
    return httpService.getHttp().put(updatePasswordPath, ...args)
}
