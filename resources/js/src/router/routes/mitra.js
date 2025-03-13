export default [
    {
      path: '/mitra',
      name: 'mitra',
      component: () => import('@/views/pages/mitra/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Mitra',
              active: true,
            },
          ],
      },
    },

    {
      path: '/mitra/:id',
      name: 'mitra-detail',
      component: () => import('@/views/pages/mitra/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Mitra',
              to: '/mitra',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

    {
      path: '/mitra-media',
      name: 'mitra-media',
      component: () => import('@/views/pages/mitra/media.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Mitra',
              active: true,
            },
          ],
      },
    },

    {
      path: '/mitra-media/:id',
      name: 'mitra-media-detail',
      component: () => import('@/views/pages/mitra/media-detail.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Mitra Media',
              to: '/mitra-media',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

    {
      path: '/mitra-withdrawal',
      name: 'mitra-withdrawal',
      component: () => import('@/views/pages/mitra/withdrawal.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Mitra',
              active: true,
            },
          ],
      },
    },

  ]
