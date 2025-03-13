import httpService from './service'

const resourcePath = '/spa/media-marketing'

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

export const postDeleteImage = (...args) => {
    return httpService.getHttp().post(resourcePath + '/delete-image', ...args)
}

export const getPreviewFlyerPDF = (id) => {
    return resourcePath + '-preview-flyer/' + id
}

export const getDownloadFlyerPDF = (id, args) => {
    return httpService.getHttp().get(resourcePath + '-download-flyer/' + id,{ params: args, responseType: 'blob' })
}
