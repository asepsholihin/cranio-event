export default [
    {
      path: '/web-settings/navbar',
      name: 'web-settings-navbar',
      component: () => import('@/views/pages/web-navbar/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Navbar',
              active: true,
            },
          ],
      },
    },

    {
      path: '/web-settings/navbar/:id',
      name: 'web-settings/navbar-detail',
      component: () => import('@/views/pages/web-navbar/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Navbar',
              to: '/web-settings/navbar',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
