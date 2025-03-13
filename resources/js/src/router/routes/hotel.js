export default [
    {
      path: '/hotel',
      name: 'hotel',
      component: () => import('@/views/pages/hotel/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Hotel',
              active: true,
            },
          ],
      },
    },
    {
      path: '/hotel/add',
      name: 'hotel-add',
      component: () => import('@/views/pages/hotel/add.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Hotel',
              to: '/hotel',
            },
            {
              text: 'Add',
              active: true,
            },
          ],
      },
    },
    {
      path: '/hotel/:id',
      name: 'hotel-detail',
      component: () => import('@/views/pages/hotel/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Hotel',
              to: '/hotel',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
