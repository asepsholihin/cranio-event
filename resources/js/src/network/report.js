import httpService from './service'

const resourcePath = '/spa/report'

export const getTabulationData = (...args) => {
    return httpService.getHttp().get(resourcePath + '/tabulation-data', ...args)
}

export const downloadTabulationUrl = () => {
    return httpService.getHttp().post(resourcePath + '/download-tabulation-data')
}

export const downloadTabulationDetail = (args) => {
    return httpService.getHttp().get(resourcePath + '/download-tabulation-data-detail', { responseType: 'blob', params: args })
}

export const getResumeDeparture = (...args) => {
    return httpService.getHttp().get(resourcePath + '/resume-departure', ...args)
}

export const getTabulationDetail = (id, ...args) => {
    return httpService.getHttp().get(resourcePath + '/tabulation-detail/'+id, ...args)
}

export const getResumePackage = (...args) => {
    return httpService.getHttp().get(resourcePath + '/resume-package', ...args)
}

export const getControlBudget = (...args) => {
    return httpService.getHttp().get(resourcePath + '/control-budget', ...args)
}

export const getControlBudgetSummary = (id) => {
    return httpService.getHttp().get(resourcePath + '/control-budget-summary/'+id)
}

export const getControlBudgetDetail = (...args) => {
    return httpService.getHttp().get(resourcePath + '/control-budget-detail', ...args)
}