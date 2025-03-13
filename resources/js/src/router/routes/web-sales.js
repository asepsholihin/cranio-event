export default [
    {
      path: '/web-sales',
      name: 'web-sales',
      component: () => import('@/views/pages/web-sales/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Web Sales',
              active: true,
            },
          ],
      },
    },

    {
      path: '/web-sales/:id',
      name: 'web-sales-detail',
      component: () => import('@/views/pages/web-sales/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Web Sales',
              to: '/web-sales',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
