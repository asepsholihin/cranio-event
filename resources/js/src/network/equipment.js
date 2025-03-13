import httpService from './service'

const resourcePath = '/spa/equipment'
const respurceCheckShippingRatePath = 'spa/check-shipping-rates'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getListCategory = (...args) => {
    return httpService.getHttp().get(resourcePath+'-category-list', ...args)
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

export const getEquipmentSearch = (...args) => {
    return httpService.getHttp().get(resourcePath + '-search', ...args)
}

export const getParticipantEquipment = (...args) => {
    return httpService.getHttp().get(resourcePath + '-participant', ...args)
}

export const getReceivedEquipment = (equipmentDeliveryId) => {
    return httpService.getHttp().get(resourcePath + '-received' + `/${equipmentDeliveryId}`)
}

export const getDeliveryLog = (equipmentDeliveryId) => {
    return httpService.getHttp().get(resourcePath + '-delivery-log' + `/${equipmentDeliveryId}`)
}

export const getShipmentLabelUrl = (id) => {
    return resourcePath + '-delivery/shipment-label/' + id
}
// MULTIPLE FILTER
export const getShipmentLabelsUrl = (umrohTripId, multiple="") => {
    if(multiple!=""){
        return resourcePath + '-delivery/shipment-labels-multiple'
    }else{
        return resourcePath + '-delivery/shipment-labels/' + umrohTripId
    }
}

export const getNameLabelUrl = (id) => {
    return resourcePath + '-delivery/name-label/' + id
}
// MULTIPLE FILTER
export const getNameLabelsUrl = (umrohTripId, multiple="") => {
    if(multiple!=""){
        return resourcePath + '-delivery/name-labels-multiple'
    }
    else{
        return resourcePath + '-delivery/name-labels/' + umrohTripId
    }
}

export const downloadLuggageTag = (umrohTripId, args) => {
    return httpService.getHttp().get('/spa/download-luggage-tag/'+umrohTripId, { responseType: 'blob', params: args })
}

export const downloadKoperTag = (umrohTripId, ...args) => {
    return httpService.getHttp().get('/spa/download-koper-tag/'+umrohTripId, { responseType: 'blob', params: args })
}

export const downloadIDCard = (umrohTripId, args) => {
    return httpService.getHttp().get('/spa/download-id-card/'+umrohTripId, { responseType: 'blob', params: args })
}

export const downloadCertificate = (umrohTripId, args) => {
    return httpService.getHttp().get('/spa/download-certificate/'+umrohTripId, { responseType: 'blob', params: args })
}

export const downloadDirectCertificate = (umrohTripId, args) => {
    return httpService.getHttp().get('/spa/download-direct-certificate/'+umrohTripId, { responseType: 'blob', params: args })
}

export const downloadTableNumber = (umrohTripId, args) => {
    return httpService.getHttp().get('/spa/download-table-number/'+umrohTripId, { responseType: 'blob', params: args })
}
// MULTIPLE FILTER
export const downloadLabelPassport = (umrohTripId, args, multiple="") => {
    if(multiple!=""){
        return httpService.getHttp().get('/spa/download-label-passport-multiple', { responseType: 'blob', params: args })
    }
    else{
        return httpService.getHttp().get('/spa/download-label-passport/'+umrohTripId, { responseType: 'blob', params: args })
    }
}

export const downloadLabelName = (umrohTripId, args) => {
    return httpService.getHttp().get('/spa/download-label-name/'+umrohTripId, { responseType: 'blob', params: args })
}

export const downloadAttendanceTag = (umrohTripId, args) => {
    return httpService.getHttp().get('/spa/download-attendance-tag/'+umrohTripId, { params: args })
}

export const exportDeliveryData = (args) => {
    return httpService.getHttp().get('spa/equipment-delivery-export', { responseType: 'blob', params: args })
}

export const exportJNE = (args) => {
    return httpService.getHttp().get('spa/equipment-export-jne', { responseType: 'blob', params: args })
}

export const exportCeklistEquipmentDelivery = (args) => {
    return httpService.getHttp().get('spa/checklist-equipment-delivery-export', { responseType: 'blob', params: args })
}

export const exportEquipment = (args) => {
    return httpService.getHttp().get(resourcePath + '-export', { responseType: 'blob', params: args })
}

export const importEquipment = (...args) => {
    return httpService.getHttp().post(resourcePath + '/import', ...args)
}

export const downloadAlbumKepulangan = (args) => {
    return httpService.getHttp().get('/spa/download-album-kepulangan', { responseType: 'blob', params: args })
}

// LOG REPORT
export const getLogReportList = (...args) => {
    return httpService.getHttp().get(resourcePath + '-log-report', ...args)
}

export const checkShippingRates = (...args) => {
    return httpService.getHttp().post(respurceCheckShippingRatePath, ...args)
}
