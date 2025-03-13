export default [
    {
      path: '/hotel-room-type',
      name: 'hotel-room-type',
      component: () => import('@/views/pages/hotel-room-type/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Hotel Room Type',
              active: true,
            },
          ],
      },
    },

    {
      path: '/hotel-room-type/:id',
      name: 'hotel-room-type-detail',
      component: () => import('@/views/pages/hotel-room-type/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Hotel Room Type',
              to: '/hotel-room-type',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
