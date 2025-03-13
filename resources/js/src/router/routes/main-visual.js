export default [
    {
      path: '/images-slider/main-visual',
      name: 'main-visual',
      component: () => import('@/views/pages/images-slider/main-visual/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Main Visual',
              active: true,
            },
          ],
      },
    },

    {
      path: '/images-slider/main-visual/:id',
      name: 'main-visual-detail',
      component: () => import('@/views/pages/images-slider/main-visual/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Main Visual',
              to: '/images-slider/main-visual',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
