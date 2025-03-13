export default [
    {
      path: '/web-link-text',
      name: 'web-link-text',
      component: () => import('@/views/pages/web-link-text/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Web Link Text',
              active: true,
            },
          ],
      },
    },

    {
      path: '/web-link-text/:id',
      name: 'web-link-text-detail',
      component: () => import('@/views/pages/web-link-text/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Web Link Text',
              to: '/web-link-text',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
