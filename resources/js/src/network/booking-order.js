import httpService from './service'

const resourcePath = '/spa/booking-order'

export const getList = (...args) => {
    return httpService.getHttp().get(resourcePath, ...args)
}

export const getListCreditReceipt = (...args) => {
    return httpService.getHttp().get(resourcePath + '/credit-receipt', ...args)
}

export const getDetail = (id) => {
    return httpService.getHttp().get(resourcePath + `/${id}`)
}

export const getDetailInvoice = (id) => {
    return httpService.getHttp().get(resourcePath + `/invoice/${id}`)
}

export const getDetailRefund = (id) => {
    return httpService.getHttp().get(resourcePath + `/refund/${id}`)
}

export const getDetailOrderItem = (id) => {
    return httpService.getHttp().get(resourcePath + `/order-item/${id}`)
}

export const postInvoice = (id, ...args) => {
    return httpService.getHttp().post(resourcePath + `/invoice/${id}`, ...args)
}

export const createInvoice = (id, ...args) => {
    return httpService.getHttp().post(resourcePath + `/create-invoice/${id}`, ...args)
}

export const editInvoice = (id, ...args) => {
    return httpService.getHttp().post(resourcePath + `/edit-invoice/${id}`, ...args)
}

export const createRefund = (id, ...args) => {
    return httpService.getHttp().post(resourcePath + `/create-refund/${id}`, ...args)
}

export const createOrderItem = (id, ...args) => {
    return httpService.getHttp().post(resourcePath + `/create-order-item/${id}`, ...args)
}

export const uploadInvoiceCreditReceipt = (id, ...args) => {
    return httpService.getHttp().post(resourcePath + `/upload-credit-receipt/${id}`, ...args)
}

export const postAckCreditReceipt = (id, ...args) => {
    return httpService.getHttp().post(resourcePath + `/ack-credit-receipt/${id}`, ...args)
}

export const deleteCreditReceipt = (id) => {
    return httpService.getHttp().delete(resourcePath + `/delete-credit-receipt/${id}`)
}

export const deletePayment = (id) => {
    return httpService.getHttp().delete(resourcePath + `/delete-payment/${id}`)
}

export const deleteInvoiceNetwork = (id) => {
    return httpService.getHttp().delete(resourcePath + `/delete-invoice/${id}`)
}

export const orderItemDelete = (id) => {
    return httpService.getHttp().delete(resourcePath + `/delete-order-item/${id}`)
}

export const orderItemPaxDelete = (id) => {
    return httpService.getHttp().delete(resourcePath + `/delete-item-pax/${id}`)
}

export const refundDelete = (id) => {
    return httpService.getHttp().delete(resourcePath + `/delete-refund/${id}`)
}

export const postData = (...args) => {
    return httpService.getHttp().post(resourcePath, ...args)
}

export const postEditItemPax = (...args) => {
    return httpService.getHttp().post(resourcePath + '/edit-item-pax', ...args)
}

export const postChangeItemPax = (...args) => {
    return httpService.getHttp().post(resourcePath + '/change-item-pax', ...args)
}

export const deleteData = (id) => {
    return httpService.getHttp().delete(resourcePath + `/${id}`)
}

export const postSendMail = (id) => {
    return httpService.getHttp().post(resourcePath + `/send-invoice-email/${id}`)
}

export const postSendInvoiceWhatsapp = (id, ...args) => {
    return httpService.getHttp().post(resourcePath + `/send-invoice-whatsapp/${id}`, ...args)
}

export const postSendMailReceipt = (id) => {
    return httpService.getHttp().post(resourcePath + `/send-receipt-email/${id}`)
}

export const assignParticipant = (...args) => {
    return httpService.getHttp().post(resourcePath + `/assign-participant`, ...args)
}

export const assignNewParticipant = (id, ...args) => {
    return httpService.getHttp().post(resourcePath + `/assign-new-participant/${id}`, ...args)
}

export const postSendMailRefund = (id) => {
    return httpService.getHttp().post(resourcePath + `/send-refund-email/${id}`)
}

export const getInvoicePDF = (id) => {
    //return httpService.getHttp().get(resourcePath + '/download-invoice'+ `/${id}`,{ responseType: 'blob' })
    return resourcePath + '/download-invoice/' + id
}

export const getInvoicePDFParticipant = (id, participantId) => {
    //return httpService.getHttp().get(resourcePath + '/download-invoice'+ `/${id}`,{ responseType: 'blob' })
    return resourcePath + '/download-invoice-participant/' + id + "?participant=" + participantId
}

export const getEmptyInvoicePDF = (orderId) => {
    //return httpService.getHttp().get(resourcePath + '/download-empty-invoice'+ `/${orderId}`,{ responseType: 'blob' })
    return resourcePath + '/download-empty-invoice/' + orderId
}

export const getReceiptPDF = (id) => {
    //return httpService.getHttp().get(resourcePath + '/download-receipt'+ `/${id}`,{ responseType: 'blob' })
    return resourcePath + '/download-receipt/' + id
}

export const getRefundPDF = (id) => {
    // return httpService.getHttp().get(resourcePath + '/download-refund'+ `/${id}`,{ responseType: 'blob' })
    return resourcePath + '/download-refund/' + id
}

export const downloadRecapUrl = (id) => {
    return httpService.getHttp().post(resourcePath + '/download-recap-trip' + `/${id}`)
}

export const getRecapData = (id) => {
    return httpService.getHttp().get(resourcePath + '/recap-trip' + `/${id}`)
}

export const downloadTabulationUrl = () => {
    return httpService.getHttp().post(resourcePath + '/download-tabulation-trip')
}

export const getTabulationData = () => {
    return httpService.getHttp().get(resourcePath + '-tabulation-trip')
}

export const getDetailItemRoomPax = (id) => {
    return httpService.getHttp().get(resourcePath + '/item-room-pax' + `/${id}`)
}

export const getTripSearch = (args) => {
    return httpService.getHttp().get(resourcePath + '/trip-search', { params: args })
}

// new search packages with array
export const getPackagesArray = (args) => {
    return httpService.getHttp().get(resourcePath + '/packages-array', { params: args })
}

export const getPackages = (umrohTripId) => {
    return httpService.getHttp().get(resourcePath + '/packages' + `/${umrohTripId}`)
}

export const getDetailDiscount = (id) => {
    return httpService.getHttp().get(resourcePath + `/discount/${id}`)
}


export const postAddDiscount = (...args) => {
    return httpService.getHttp().post(resourcePath + '/add-discount', ...args)
}

export const postEditDiscount = (...args) => {
    return httpService.getHttp().post(resourcePath + '/edit-discount', ...args)
}

export const postEditOldDiscount = (...args) => {
    return httpService.getHttp().post(resourcePath + '/edit-old-discount', ...args)
}

export const deleteDiscount = (id) => {
    return httpService.getHttp().delete(resourcePath + `/delete-discount/${id}`)
}

export const deleteOldDiscount = (id) => {
    return httpService.getHttp().delete(resourcePath + `/delete-old-discount/${id}`)
}

export const postAddReference = (...args) => {
    return httpService.getHttp().post(resourcePath + '/add-reference', ...args)
}

export const getDetailSpecialRequest = (id) => {
    return httpService.getHttp().get(resourcePath + `/special-request/${id}`)
}

export const createSpecialRequest = (id, ...args) => {
    return httpService.getHttp().post(resourcePath + `/add-special-request/${id}`, ...args)
}

export const editSpecialRequest = (id, ...args) => {
    return httpService.getHttp().post(resourcePath + `/edit-special-request/${id}`, ...args)
}

export const deleteSpecialRequest = (id) => {
    return httpService.getHttp().delete(resourcePath + `/delete-special-request/${id}`)
}

export const editOrderInformation = (id, ...args) => {
    return httpService.getHttp().post(resourcePath + `/edit-order-information/${id}`, ...args)
}

export const postSetMahrom = (...args) => {
    return httpService.getHttp().post(resourcePath + '/set-mahrom', ...args)
}

export const postSetUmrohTrip = (...args) => {
    return httpService.getHttp().post(resourcePath + '/set-umroh-trip', ...args)
}

export const getListBadal = (...args) => {
    return httpService.getHttp().get(resourcePath + '/badal', ...args)
}

export const getBadalPriceList = (args) => {
    return httpService.getHttp().get(resourcePath + '/badal-price-list', { params: args })
}

export const getParticipantData = (args) => {
    return httpService.getHttp().get(resourcePath + '/get-participant-data', { params: args })
}

export const postOrderEquipment = (...args) => {
    return httpService.getHttp().post(resourcePath + '/order-equipment', ...args)
}

export const getDetailOrderEquipment = (id) => {
    return httpService.getHttp().get(resourcePath + `/order-equipment/${id}`)
}

export const orderEquipmentDelete = (id) => {
    return httpService.getHttp().delete(resourcePath + `/delete-order-equipment/${id}`)
}

export const getDetailBadal = (id) => {
    return httpService.getHttp().get(resourcePath + `/badal/${id}`)
}

export const getMutawwifForBadal = (args) => {
    return httpService.getHttp().get(resourcePath + `/mutawwif`, { params: args })
}

export const postEditParticipant = (...args) => {
    return httpService.getHttp().post(resourcePath + `/edit-assigned-participant`, ...args)
}

export const getInvoices = (args) => {
    return httpService.getHttp().get(resourcePath + '-invoice-search', { params: args })
}

export const exportData = (args) => {
    return httpService.getHttp().get(resourcePath + '-export', { responseType: 'blob', params: args })
}