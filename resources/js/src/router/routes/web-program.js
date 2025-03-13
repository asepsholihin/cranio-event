export default [
    {
      path: '/images-slider/programs',
      name: 'programs',
      component: () => import('@/views/pages/images-slider/program/index.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Programs',
              active: true,
            },
          ],
      },
    },

    {
      path: '/images-slider/program/:id',
      name: 'program-detail',
      component: () => import('@/views/pages/images-slider/program/edit.vue'),
      meta: {
          pageTitle: '',
          breadcrumb: [
            {
              text: 'Programs',
              to: '/programs',
            },
            {
              text: 'Detail',
              active: true,
            },
          ],
      },
    },

  ]
