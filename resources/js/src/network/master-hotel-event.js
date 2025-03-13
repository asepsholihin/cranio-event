import httpService from './service'

const resourcePath = '/spa/master-hotel-event'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const priceCategoryAll = (...args) => {
    return httpService.getHttp().get(resourcePath+'-price-category', ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const deleteData = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const getTransitHotel = () => {
    return httpService.getHttp().get(resourcePath+'-transit-hotel')
}

export const getManasikHotel = () => {
    return httpService.getHttp().get(resourcePath+'-manasik-hotel')
}

export const getCounterItem = (id) => {
    return httpService.getHttp().get(resourcePath + '-get-counter' +`/${id}`)
}

export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}

export const postDataCategory = (...args) => {
    return httpService.getHttp().post(resourcePath+'-add-category', ...args)
}

export const prefer = ($id, ...args) => {
    return httpService.getHttp().post(resourcePath+'-prefer/'+$id, ...args)
}

export const deleteItem = (...args) => {
    return httpService.getHttp().post(resourcePath+'-delete-item', ...args)
}
