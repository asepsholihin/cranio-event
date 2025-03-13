import httpService from './service'

const resourcePath = '/spa/web-navbar'
const resourceParentPath = '/spa/web-navbar-parent'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const deleteData = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}

export const getCategoriesSearch = (args) => {
    return httpService.getHttp().get(resourcePath + '-search', {params: args})
}

export const getParentNavbarSearch = (args) => {
    return httpService.getHttp().get(resourceParentPath + '-search', {params: args})
}
