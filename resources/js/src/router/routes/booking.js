export default [
    {
      path: '/booking',
      name: 'booking',
      component: () => import('@/views/pages/booking/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Booking',
              active: true,
            },
          ],
      },
    },

  ]
