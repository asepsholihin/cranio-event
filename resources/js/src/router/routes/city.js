export default [
    {
      path: '/city',
      name: 'city',
      component: () => import('@/views/pages/city/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'City',
              active: true,
            },
          ],
      },
    },

    {
      path: '/city/:id',
      name: 'city-detail',
      component: () => import('@/views/pages/city/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'City',
              to: '/city',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
