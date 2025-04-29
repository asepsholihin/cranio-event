export default [
  {
    path: '/booking-receipt',
    name: 'booking-receipt',
    component: () => import('@/views/pages/booking-receipt/index.vue'),
    meta: {
        pageTitle: '',
        breadcrumb: [
          {
            text: 'Pembayaran',
            active: true,
          },
        ],
    },
  },

]
