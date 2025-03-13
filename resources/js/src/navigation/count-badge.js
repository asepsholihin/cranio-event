import store from '@/store'
import { hasPermission } from '@/auth/utils'
import { getCountBadge } from '@/network/dashboard'

export default function refetchCountBadgeNav() {
    if (hasPermission('payment-invoicing')) {
        getCountBadge().then(response => {
            const { creditReceipt } = response.data
            store.commit('app/UPDATE_BADGE_CREDIT_RECEIPT', creditReceipt)
        })
    }
    if (hasPermission('booking-order-change-trip-approval')) {
        getCountBadge().then(response => {
            const { orderChangeRequest } = response.data
            store.commit('app/UPDATE_BADGE_ORDER_CHANGE_REQUEST', orderChangeRequest)
        })
    }
    if (hasPermission('payment-approval')) {
        getCountBadge().then(response => {
            const { paymentApproval } = response.data
            store.commit('app/UPDATE_BADGE_PAYMENT_APPROVAL', paymentApproval)
        })
    }
}