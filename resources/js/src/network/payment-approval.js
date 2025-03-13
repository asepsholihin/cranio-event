import httpService from './service'

const resourcePath = '/spa/payment-approval'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const getListPoNumber = (...args) => {
    return httpService.getHttp().get(resourcePath+'-list-po', ...args)
}

export const getFormPayment = (...args) => {
    return httpService.getHttp().get(resourcePath+'-payment-header', ...args)
}

export const deleteData = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const deleteItemData = (id) => {
    return httpService.getHttp().get(resourcePath +'-delete-item'+ `/${id}`)
}

export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}

// DONE APPROVAL PAYEMENT
export const donePayment = (...args) => {
    return httpService.getHttp().post(resourcePath+'-done-payement', ...args)
}

export const postSumbit = (...args) => {
    return httpService.getHttp().post(resourcePath+'-post-submit', ...args)
}

export const postApprovalItem = (...args) => {
    return httpService.getHttp().post(resourcePath+'/approval-item', ...args)
}

export const postApprovalParent = (...args) => {
    return httpService.getHttp().post(resourcePath+'/approval-parent', ...args)
}

export const getPaNumber = (...args) => {
    return httpService.getHttp().post(resourcePath+'-pa-number', ...args)
}

export const removeItem = (...args) => {
    return httpService.getHttp().post(resourcePath+'-remove-item', ...args)
}

export const postAction = (...args) => {
    return httpService.getHttp().post(resourcePath + '/action', ...args)
}

export const getPaymentApprovalPDF = (id) => {
    return resourcePath + '-document-pdf/' + id
}
