import { $themeBreakpoints } from '@themeConfig'

export default {
  namespaced: true,
  state: {
    windowWidth: 0,
    shallShowOverlay: false,
    badgeCreditReceipt: 0,
    badgeOrderChangeRequest: 0,
    badgePaymentApproval: 0
  },
  getters: {
    currentBreakPoint: state => {
      const { windowWidth } = state
      if (windowWidth >= $themeBreakpoints.xl) return 'xl'
      if (windowWidth >= $themeBreakpoints.lg) return 'lg'
      if (windowWidth >= $themeBreakpoints.md) return 'md'
      if (windowWidth >= $themeBreakpoints.sm) return 'sm'
      return 'xs'
    },
  },
  mutations: {
    UPDATE_WINDOW_WIDTH(state, val) {
      state.windowWidth = val
    },
    UPDATE_BADGE_CREDIT_RECEIPT(state, val) {
      state.badgeCreditReceipt = val
    },
    UPDATE_BADGE_ORDER_CHANGE_REQUEST(state, val) {
      state.badgeOrderChangeRequest = val
    },
    UPDATE_BADGE_PAYMENT_APPROVAL(state, val) {
      state.badgePaymentApproval = val
    },
    TOGGLE_OVERLAY(state, val) {
      state.shallShowOverlay = val !== undefined ? val : !state.shallShowOverlay
    },
  },
  actions: {},
}
