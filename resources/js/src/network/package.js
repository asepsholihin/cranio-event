import httpService from './service'

const resourcePath = '/spa/package'
const resourcePathSearch = '/spa/packages'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getListAll = (...args) => {
    return httpService.getHttp().get(resourcePath+"-list", ...args)
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

export const getPackageSearch = (args) => {
    return httpService.getHttp().get(resourcePathSearch + '/search', {params: args})
}
