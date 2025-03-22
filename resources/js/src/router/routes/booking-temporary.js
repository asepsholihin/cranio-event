export default [
    {
      path: '/booking-temporary',
      name: 'booking-temporary',
      component: () => import('@/views/pages/booking-temporary/index.vue'),
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
