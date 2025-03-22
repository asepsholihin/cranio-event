export default [
  {
    path: '/booking-receipt',
    name: 'booking-receipt',
    component: () => import('@/views/pages/booking-receipt/index.vue'),
    meta: {
        pageTitle: '',
        breadcrumb: [
          {
            text: 'Booking Temporary',
            active: true,
          },
        ],
    },
  },

]
