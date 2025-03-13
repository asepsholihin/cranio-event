import httpService from './service'

const resourcePath = '/spa/milad-crm'

export const getList = (...args) => httpService.getHttp().get(resourcePath, ...args)

export const downloadMiladCard = (id, previewMode) => httpService.getHttp().get(resourcePath + `/download-milad-card/${id}/?mode=` + previewMode, { responseType: 'blob' })

export const downloadWhenPost = (...args) => {
    return httpService.getHttp().post(resourcePath+'-post-download-milad-card', ...args, { responseType: 'blob' })
}

export const sendMiladCard = (id, previewMode) => httpService.getHttp().get(resourcePath + `/send-milad-card/${id}/?mode=` + previewMode)
// POSTED METHOD POST MILAD CARD
export const sendPostMiladCard = (...args) => {
    return httpService.getHttp().post(resourcePath+'-post-send-milad-card', ...args)
}
// ------
export const getFiles = (id) => {
    return httpService.getHttp().get(resourcePath + `/files/${id}`)
}
export const changeOurPhoto = (...args) => {
    return httpService.getHttp().post(resourcePath+'-change-our-photo', ...args, { responseType: 'blob' })
}

export const getMiladCardWithPhoto = (id) => {
    return httpService.getHttp().get(resourcePath + `/getMiladCardWithPhoto/${id}`, { responseType: 'blob' })
}

export const getMiladCardWithoutPhoto = (id) => {
    return httpService.getHttp().get(resourcePath + `/getMiladCardWithoutPhoto/${id}`, { responseType: 'blob' })
}

export const deleteFile = (...args) => httpService.getHttp().post(resourcePath + '/delete-photo-milad', ...args)

export const activationReminderMilad = (...args) => httpService.getHttp().post(resourcePath + '/activation-reminder-milad', ...args)

export const getMiladCardWithPhotoUrl = (id) => {
    return resourcePath + `/getMiladCardWithPhoto/${id}`
}
export const getMiladCardWithoutPhotoUrl = (id) => {
    return resourcePath + `/getMiladCardWithoutPhoto/${id}`
}

export const getImages = (id) => {
    return httpService.getHttp().get(resourcePath + `/images-milad-card/${id}`)
}
