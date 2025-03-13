export default [
    {
      path: '/user-platform',
      name: 'user-platform',
      component: () => import('@/views/pages/user-platform/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'User Platform',
              active: true,
            },
          ],
      },
    },

    {
      path: '/user-platform/:id',
      name: 'user-platform-detail',
      component: () => import('@/views/pages/user-platform/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'User Platform',
              to: '/user-platform',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
