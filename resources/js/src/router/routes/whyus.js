export default [
    {
      path: '/images-slider/whyus',
      name: 'whyus',
      component: () => import('@/views/pages/images-slider/whyus/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Why Us',
              active: true,
            },
          ],
      },
    },

    {
      path: '/images-slider/whyus/:id',
      name: 'whyus-detail',
      component: () => import('@/views/pages/images-slider/whyus/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Why Us',
              to: '/whyus',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
