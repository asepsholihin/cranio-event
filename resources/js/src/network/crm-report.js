import httpService from './service'

const resourcePath = '/spa/crm-report'

export const getChartTotalTrip = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-total-trip', ...args)
}

export const getChartTotalTransaction = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-total-transaction', ...args)
}

export const getChartPackage = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-package', ...args)
}

export const getChartTopTrip = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-top-trip', ...args)
}

export const getChartGender = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-gender', ...args)
}

export const getChartAge = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-age', ...args)
}

export const getChartJob = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-job', ...args)
}

export const getChartEducation = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-education', ...args)
}

export const getChartTopCity = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-city', ...args)
}

export const getChartTopProvince = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-province', ...args)
}

export const getChartTotalAccount = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-total-account', ...args)
}

export const getChartHasAccount = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-has-account', ...args)
}

export const getChartHasPhone = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-has-phone', ...args)
}

export const getChartHasInstagram = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-has-instagram', ...args)
}

export const getChartHasLinkedin = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-has-linkedin', ...args)
}

export const getChartOther = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-other', ...args)
}

export const getChartParticipantGrowth = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-participant-growth', ...args)
}

export const getChartRepetisi = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-repetisi', ...args)
}

// Trend
export const getTrendParticipant = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-trend-participant', ...args)
}

export const getTrendPackage = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-trend-package', ...args)
}

export const getTrendGrowth = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-trend-growth', ...args)
}

export const getTrendProduct = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-trend-product', ...args)
}
