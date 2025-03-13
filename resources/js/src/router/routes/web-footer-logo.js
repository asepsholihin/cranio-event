export default [
    {
      path: '/images-slider/footer-logo',
      name: 'footer-logo',
      component: () => import('@/views/pages/images-slider/footer-logo/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Footer Logo',
              active: true,
            },
          ],
      },
    },

    {
      path: '/images-slider/footer-logo/:id',
      name: 'footer-logo-detail',
      component: () => import('@/views/pages/images-slider/footer-logo/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Footer Logo',
              to: '/footer-logo',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
