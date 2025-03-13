export default [
    {
      path: '/images-slider/partners',
      name: 'partners',
      component: () => import('@/views/pages/images-slider/partner/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Partners',
              active: true,
            },
          ],
      },
    },

    {
      path: '/images-slider/partner/:id',
      name: 'partner-detail',
      component: () => import('@/views/pages/images-slider/partner/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Partners',
              to: '/partners',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
