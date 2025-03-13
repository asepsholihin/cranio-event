import httpService from './service'

const resourcePath = '/spa/user-platform'
const resourcePathDepartment = '/spa/department'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getListDepartment = (...args) => {
    return httpService.getHttp().get(resourcePathDepartment, ...args)
}

export const getDepartmentSearch = (...args) => {
    return httpService.getHttp().get(resourcePathDepartment+'-search', ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}
export const addDepartment = (...args) => {
    return httpService.getHttp().post(resourcePathDepartment, ...args)
}
export const changeStatusDepartment = (...args) => {
    return httpService.getHttp().post(resourcePathDepartment+'-status', ...args)
}
export const getUserSearch = (args) => {
    return httpService.getHttp().get('/spa/user-sales-search', { params: args })
}
