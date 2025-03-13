import httpService from './service'

const resourcePath = '/spa/sales-report-old'

export const getAllSales = (...args) => {
    return httpService.getHttp().get(resourcePath + '/all-sales', ...args)
}

export const getGroupSales = (...args) => {
    return httpService.getHttp().get(resourcePath + '/group-sales', ...args)
}

export const getTopSalesman = (...args) => {
    return httpService.getHttp().get(resourcePath + '/top-salesman', ...args)
}

export const getChartOmzet = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-omzet', ...args)
}

export const getChartSalesByCategory = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-sales-category', ...args)
}

export const getChartSalesByLeadsource = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-leadsource', ...args)
}

export const getPerformanceMonthly = (...args) => {
    return httpService.getHttp().get(resourcePath + '/performance-monthly', ...args)
}

export const getChartPerformanceMonthly = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-performance-monthly', ...args)
}

export const getPerformanceDaily = (...args) => {
    return httpService.getHttp().get(resourcePath + '/performance-daily', ...args)
}

export const exportPerformanceDaily = (args) => {
    return httpService.getHttp().get(resourcePath + '/performance-daily-export', { params: args, responseType: 'blob' })
}

export const getChartSalesSubCategory = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-sales-subcategory', ...args)
}

export const getTopUmrohTrip = (...args) => {
    return httpService.getHttp().get(resourcePath + '/top-umroh-trip', ...args)
}

export const getTopPackageUmrohTrip = (...args) => {
    return httpService.getHttp().get(resourcePath + '/top-package-umroh-trip', ...args)
}

export const getSalesHajiFuroda = (...args) => {
    return httpService.getHttp().get(resourcePath + '/sales-haji-furoda', ...args)
}

export const getChartSalesHaji = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-sales-haji', ...args)
}

export const getDetailRecommendation = (...args) => {
    return httpService.getHttp().get(resourcePath + '/detail-recommendation', ...args)
}

export const getChartLeadsBySource = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-leadsource', ...args)
}

export const getChartLeadsByStatus = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-leadstatus', ...args)
}

export const getChartLeadsByCity = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-leadcity', ...args)
}

export const getChartLeadsByPackage = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-leadpackage', ...args)
}

export const getChartOrderBySales = (...args) => {
    return httpService.getHttp().get(resourcePath + '/chart-order-cso', ...args)
}

export const exportPerformanceMonthly = (args) => {
    return httpService.getHttp().get(resourcePath + '/performance-monthly-export', { params: args, responseType: 'blob' })
}

export const exportTopTrip = (args) => {
    return httpService.getHttp().get(resourcePath + '/top-trip-export', { params: args, responseType: 'blob' })
}

export const exportTopTripByMonth = (args) => {
    return httpService.getHttp().get(resourcePath + '/top-trip-monthly-export', { params: args, responseType: 'blob' })
}

export const exportTopPackageByMonth = (args) => {
    return httpService.getHttp().get(resourcePath + '/top-package-monthly-export', { params: args, responseType: 'blob' })
}

export const getLogCsoClosingList = (...args) => {
    return httpService.getHttp().get(resourcePath + '/log-cso-closings', ...args)
}

export const refineLogCsoClosingPax = (...args) => {
    return httpService.getHttp().post(resourcePath + '/log-cso-closings/refine-pax', ...args)
}

export const postCreateClosing = (...args) => {
    return httpService.getHttp().post(resourcePath + '/log-cso-closings/create-closing', ...args)
}