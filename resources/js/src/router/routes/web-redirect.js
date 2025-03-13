export default [
    {
      path: '/web-settings/redirect',
      name: 'web-settings-redirect',
      component: () => import('@/views/pages/web-redirect/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Redirect URL',
              active: true,
            },
          ],
      },
    },

    {
      path: '/web-settings/redirect/:id',
      name: 'web-settings/redirect-detail',
      component: () => import('@/views/pages/web-redirect/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Redirect URL',
              to: '/web-settings/redirect',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
