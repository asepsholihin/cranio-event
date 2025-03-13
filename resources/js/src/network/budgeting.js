import httpService from './service'

const resourcePath = '/spa/budgeting'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getListBudgeting = (...args) => {
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

export const postCopyData = (...args) => {
    return httpService.getHttp().post(resourcePath + '/copy-budgeting', ...args)
}

export const getBudgetingSearch = (args) => {
    return httpService.getHttp().get(resourcePath + '-search', {params: args})
}

export const postBudgetTicket = (...args) => {
    return httpService.getHttp().post(resourcePath + '/budget-ticket', ...args)
}

export const postBudgetHotel = (...args) => {
    return httpService.getHttp().post(resourcePath + '/budget-hotel', ...args)
}

export const postBudgetMutawwif = (...args) => {
    return httpService.getHttp().post(resourcePath + '/budget-mutawwif', ...args)
}

export const postBudgetTourLeader = (...args) => {
    return httpService.getHttp().post(resourcePath + '/budget-tour-leader', ...args)
}

export const postBudgetEquipment = (...args) => {
    return httpService.getHttp().post(resourcePath + '/budget-equipment', ...args)
}

export const postBudgetGeneral = (...args) => {
    return httpService.getHttp().post(resourcePath + '/budget-general', ...args)
}
