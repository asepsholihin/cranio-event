export default [
    {
      path: '/booking-hotel-event',
      name: 'booking-hotel-event',
      component: () => import('@/views/pages/event-attendance/booking-hotel-event/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Booking Hotel',
              active: true,
            },
          ],
      },
    },

  ]
